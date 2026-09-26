<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\User;
use App\Models\WhatsappClick;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProspectController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:160'],
            'stage' => ['nullable', Rule::in(array_keys(Prospect::STAGES))],
            'source' => ['nullable', Rule::in(array_keys(Prospect::SOURCES))],
            'service' => ['nullable', Rule::in(array_keys(config('sentriq.services')))],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'follow_up' => ['nullable', Rule::in(['due', 'today'])],
        ]);

        $query = Prospect::query()->with('assignedUser')->latest();
        $this->applyFilters($query, $filters);

        $from = isset($filters['from']) ? Carbon::parse($filters['from'])->startOfDay() : now()->subDays(29)->startOfDay();
        $to = isset($filters['to']) ? Carbon::parse($filters['to'])->endOfDay() : now()->endOfDay();
        $periodProspects = Prospect::query()->whereBetween('created_at', [$from, $to]);
        $sourceMetrics = (clone $periodProspects)->select('source', DB::raw('count(*) as total'))->groupBy('source')->pluck('total', 'source');
        $stageEvents = DB::table('prospect_activities')
            ->join('prospects', 'prospects.id', '=', 'prospect_activities.prospect_id')
            ->where('prospect_activities.type', 'stage_change')
            ->whereBetween('prospect_activities.happened_at', [$from, $to])
            ->whereIn('prospect_activities.new_stage', ['visit', 'quote', 'won'])
            ->select('prospects.source', 'prospect_activities.new_stage', DB::raw('count(distinct prospect_id) as total'))
            ->groupBy('prospects.source', 'prospect_activities.new_stage')->get();

        return view('admin.prospects.index', [
            'prospects' => $query->paginate(30)->withQueryString(),
            'filters' => $filters,
            'sourceMetrics' => $sourceMetrics,
            'stageMetrics' => $stageEvents->groupBy('source'),
            'whatsappClicks' => WhatsappClick::whereBetween('clicked_at', [$from, $to])->count(),
            'periodLabel' => $from->format('d/m/Y').' – '.$to->format('d/m/Y'),
        ]);
    }

    public function today(): View
    {
        $todayEnd = now()->endOfDay();

        return view('admin.prospects.today', [
            'newProspects' => Prospect::where('stage', 'new')->whereNull('last_contact_at')->oldest()->get(),
            'followUps' => Prospect::whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', $todayEnd)->orderBy('next_follow_up_at')->get(),
            'visits' => Prospect::where('stage', 'visit')->orderBy('next_follow_up_at')->get(),
            'quotes' => Prospect::where('stage', 'quote')->orderBy('next_follow_up_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.prospects.create', ['prospect' => new Prospect(['stage' => 'new'])] + $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $prospect = Prospect::create($data);
        $prospect->activities()->create([
            'user_id' => $request->user()->id,
            'type' => 'note',
            'summary' => 'Prospecto creado manualmente.',
            'happened_at' => now(),
        ]);

        return redirect()->route('admin.prospects.show', $prospect)->with('status', 'Prospecto creado correctamente.');
    }

    public function show(Prospect $prospect): View
    {
        $prospect->load(['activities.user', 'quotes', 'assignedUser']);
        $duplicates = collect();
        if ($prospect->phone || $prospect->email) {
            $duplicates = Prospect::query()->whereKeyNot($prospect->id)
                ->where(function (Builder $query) use ($prospect): void {
                    if ($prospect->phone) {
                        $query->orWhere('phone', $prospect->phone);
                    }
                    if ($prospect->email) {
                        $query->orWhere('email', $prospect->email);
                    }
                })->get();
        }

        return view('admin.prospects.show', compact('prospect', 'duplicates'));
    }

    public function edit(Prospect $prospect): View
    {
        return view('admin.prospects.edit', compact('prospect') + $this->formOptions());
    }

    public function update(Request $request, Prospect $prospect): RedirectResponse
    {
        $data = $this->validatedData($request);
        $oldStage = $prospect->stage;

        DB::transaction(function () use ($request, $prospect, $data, $oldStage): void {
            $prospect->update($data);
            if ($oldStage !== $prospect->stage) {
                $prospect->activities()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'stage_change',
                    'summary' => 'Etapa actualizada de '.Prospect::STAGES[$oldStage].' a '.Prospect::STAGES[$prospect->stage].'.',
                    'old_stage' => $oldStage,
                    'new_stage' => $prospect->stage,
                    'happened_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.prospects.show', $prospect)->with('status', 'Prospecto actualizado correctamente.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['nullable', 'required_without:business', 'string', 'max:160'],
            'business' => ['nullable', 'required_without:name', 'string', 'max:160'],
            'request_type' => ['required', Rule::in(array_keys(Prospect::REQUEST_TYPES))],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:40'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'service_interest' => ['nullable', Rule::in(array_keys(config('sentriq.services')))],
            'municipality' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:4000'],
            'source' => ['required', Rule::in(array_keys(Prospect::SOURCES))],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['required', Rule::in(array_keys(Prospect::STAGES))],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'next_follow_up_at' => ['nullable', 'date'],
            'last_contact_at' => ['nullable', 'date'],
        ]);

        foreach (['phone', 'email', 'business', 'service_interest', 'municipality', 'description', 'campaign', 'assigned_user_id', 'next_follow_up_at', 'last_contact_at'] as $key) {
            $data[$key] = ($data[$key] ?? null) ?: null;
        }

        return $data;
    }

    private function formOptions(): array
    {
        return ['users' => User::orderBy('name')->get()];
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $query, string $search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')->orWhere('business', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%');
            });
        })->when($filters['stage'] ?? null, fn (Builder $query, string $stage) => $query->where('stage', $stage))
            ->when($filters['source'] ?? null, fn (Builder $query, string $source) => $query->where('source', $source))
            ->when($filters['service'] ?? null, fn (Builder $query, string $service) => $query->where('service_interest', $service))
            ->when($filters['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date));

        if (($filters['follow_up'] ?? null) === 'due') {
            $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now()->startOfDay());
        } elseif (($filters['follow_up'] ?? null) === 'today') {
            $query->whereDate('next_follow_up_at', today());
        }
    }
}
