<?php

namespace App\Console\Commands;

use App\Services\MenuSyncService;
use Illuminate\Console\Command;

class MenuExport extends Command
{
    protected $signature = 'menu:export
        {--out=database/data/menu.json : Output JSON path (relative to base_path)}
        {--force : Overwrite existing snapshot without confirmation}';

    protected $description = 'Dump the current categories/menu_items tables to a snapshot JSON.';

    public function handle(MenuSyncService $service): int
    {
        $out = base_path($this->option('out'));

        if (file_exists($out) && ! $this->option('force')) {
            if (! $this->confirm("Overwrite existing snapshot at {$out}?", false)) {
                $this->warn('Aborted.');

                return self::SUCCESS;
            }
        }

        $data = $service->exportFromDb();
        $service->save($out, $data);

        $this->info(sprintf(
            'Wrote %d categories and %d items to %s',
            count($data['categories']),
            count($data['items']),
            $out,
        ));

        return self::SUCCESS;
    }
}
