<?php

namespace App\Console\Commands;

use App\Services\OffersSyncService;
use Illuminate\Console\Command;

class OffersSync extends Command
{
    protected $signature = 'offers:sync
        {--path=database/data/daily-offers.json : Snapshot JSON path (relative to base_path)}
        {--force : Apply without confirmation}';

    protected $description = 'Replace the daily_offers table with the committed snapshot JSON.';

    public function handle(OffersSyncService $service): int
    {
        $path = base_path($this->option('path'));

        if (! file_exists($path)) {
            $this->error("No snapshot found at {$path}.");

            return self::FAILURE;
        }

        $offers = $service->load($path);
        $this->info(sprintf('Snapshot contains %d offers.', count($offers)));

        if (! $this->option('force') && ! $this->confirm('Replace current daily_offers table with this snapshot?', false)) {
            $this->warn('Aborted.');

            return self::SUCCESS;
        }

        $result = $service->apply($offers);

        $this->info(sprintf(
            'Removed %d existing; inserted %d from snapshot.',
            $result['removed'],
            count($result['inserted']),
        ));

        return self::SUCCESS;
    }
}
