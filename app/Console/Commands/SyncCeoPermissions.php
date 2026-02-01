<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Units\Corporates\Users\Common\Services\CeoPermissionSyncService;

class SyncCeoPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ceo:sync-permissions {--force : Force sync even if already run today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize CEO role permissions across all corporates to ensure they have all available permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CEO permissions synchronization...');

        $service = CeoPermissionSyncService::make();
        $result = $service->actSyncCeoPermissions();

        if ($result->hasErrors()) {
            $this->error('Error syncing CEO permissions: ' . $result->getFirstError());
            return 1;
        }

        $responseData = $result->getSuccessResponse()->getData();

        $this->info($responseData['message']);
        $this->info("Corporates synced: " . $responseData['corporates_synced']);
        $this->info("Permissions synced: " . $responseData['permissions_synced']);

        $this->info('CEO permissions synchronization completed successfully!');

        return 0;
    }
}
