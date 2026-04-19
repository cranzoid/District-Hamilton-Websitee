<?php

namespace App\Console\Commands;

use App\Services\MenuSyncService;
use Illuminate\Console\Command;

class MenuSync extends Command
{
    protected $signature = 'menu:sync
        {--path=database/data/menu.json : Snapshot JSON path (relative to base_path)}
        {--force : Apply without confirmation}';

    protected $description = 'Apply the committed menu snapshot JSON to the database (idempotent).';

    public function handle(MenuSyncService $service): int
    {
        $path = base_path($this->option('path'));

        if (! file_exists($path)) {
            $this->error("No snapshot found at {$path}.");
            $this->line('Bootstrap it with `php artisan menu:export` or `php artisan menu:import <pdf>`.');

            return self::FAILURE;
        }

        $data = $service->load($path);
        $diff = $service->diff($data);

        $total = count($diff['addedCategories'])
            + count($diff['updatedCategories'])
            + count($diff['added'])
            + count($diff['updated'])
            + count($diff['deactivated']);

        $this->info('Pending changes:');
        $this->line('  Categories added:   '.count($diff['addedCategories']));
        $this->line('  Categories updated: '.count($diff['updatedCategories']));
        $this->line('  Items added:        '.count($diff['added']));
        $this->line('  Items updated:      '.count($diff['updated']));
        $this->line('  Items to hide:      '.count($diff['deactivated']));

        if ($total === 0) {
            $this->info('Menu is already in sync. Nothing to do.');

            return self::SUCCESS;
        }

        if ($this->getOutput()->isVerbose()) {
            foreach (['addedCategories', 'updatedCategories', 'added', 'updated', 'deactivated'] as $bucket) {
                if (! empty($diff[$bucket])) {
                    $this->line("  [{$bucket}] ".implode(', ', $diff[$bucket]));
                }
            }
        }

        if (! $this->option('force') && ! $this->confirm('Apply these changes?', false)) {
            $this->warn('Aborted.');

            return self::SUCCESS;
        }

        $result = $service->apply($data);

        $this->info(sprintf(
            'Applied: %d added, %d updated, %d hidden.',
            count($result['added']),
            count($result['updated']),
            count($result['hidden']),
        ));

        return self::SUCCESS;
    }
}
