<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProspectActivityController extends Controller
{
    public function store(Request $request, Prospect $prospect): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(Prospect::ACTIVITY_TYPES)), Rule::notIn(['stage_change'])],
            'summary' => ['required', 'string', 'max:4000'],
            'happened_at' => ['required', 'date'],
            'next_action' => ['nullable', 'string', 'max:255'],
            'next_action_at' => ['nullable', 'date', 'required_with:next_action'],
        ]);

        DB::transaction(function () use ($request, $prospect, $data): void {
            $prospect->activities()->create([...$data, 'user_id' => $request->user()->id]);
            $updates = [];
            if (in_array($data['type'], ['call', 'message', 'visit', 'follow_up'], true)) {
                $updates['last_contact_at'] = $data['happened_at'];
            }
            if (! empty($data['next_action_at'])) {
                $updates['next_follow_up_at'] = $data['next_action_at'];
            }
            if ($updates) {
                $prospect->update($updates);
            }
        });

        return back()->with('status', 'Actividad registrada.');
    }
}
