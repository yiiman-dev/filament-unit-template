<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/19/25, 5:17 PM
 */

namespace Units\ActLog\Admin\Console;

use Illuminate\Console\Command;
use Units\ActLog\Admin\Services\ActLogService;


class VerifyLogChainCommand extends Command
{
    protected $signature = 'logs:admin-verify-chain';
    protected $description = 'Verify the integrity of log hash chains';
    public function handle()
    {
        $logService = app(ActLogService::class);
        $this->info('Starting hash chain verification...');

        $results = $logService->verifyChain();

        if ($results['is_valid']) {
            $this->info('✅ Hash chain is valid.');
        } else {
            $this->error('❌ Hash chain is invalid!');

            foreach ($results['errors'] as $error) {
                if ($error['type'] === 'hash_mismatch') {
                    $this->error("Change detected in record #{$error['index']}!");
                    $this->error("Calculated hash: {$error['calculated_hash']}");
                    $this->error("Stored hash: {$error['stored_hash']}");
                } elseif ($error['type'] === 'chain_break') {
                    $this->error("Chain break detected at record #{$error['index']}!");
                    $this->error("Expected previous hash: {$error['expected_previous_hash']}");
                    $this->error("Actual previous hash: {$error['actual_previous_hash']}");
                }
            }
        }
    }
}
