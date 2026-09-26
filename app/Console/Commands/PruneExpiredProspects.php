<?php

namespace App\Console\Commands;

use App\Models\Prospect;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class PruneExpiredProspects extends Command
{
    protected $signature = 'prospects:prune {--dry-run : Cuenta los registros sin eliminarlos}';

    protected $description = 'Elimina prospectos no convertidos cuyo periodo de conservación terminó';

    public function handle(): int
    {
        $months = max(1, (int) config('sentriq.leads.retention_months', 3));
        $cutoff = now()->subMonthsNoOverflow($months);
        $query = Prospect::query()
            ->where('stage', '!=', 'won')
            ->whereDoesntHave('quotes', fn (Builder $query) => $query
                ->where('status', 'accepted')->orWhereNotNull('signed_at'))
            ->where(function (Builder $query) use ($cutoff): void {
                $query->where('last_contact_at', '<', $cutoff)
                    ->orWhere(function (Builder $query) use ($cutoff): void {
                        $query->whereNull('last_contact_at')->where('created_at', '<', $cutoff);
                    });
            });

        $count = (clone $query)->count();
        if ($this->option('dry-run')) {
            $this->info($count.' prospecto(s) cumplen los criterios de eliminación.');

            return self::SUCCESS;
        }

        $query->select('id')->chunkById(100, function ($prospects): void {
            Prospect::whereKey($prospects->pluck('id'))->delete();
        });
        $this->info($count.' prospecto(s) eliminado(s).');

        return self::SUCCESS;
    }
}
