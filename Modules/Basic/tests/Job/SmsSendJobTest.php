<?php

namespace Modules\Basic\Tests\Job;

use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;
use Modules\Basic\Job\SmsSendJob;
use Tests\TestCase;

class SmsSendJobTest extends TestCase
{
    public function test_sms_job_sends_successfully()
    {
        // Mock the KavenegarApi
        $kavenegarMock = $this->createMock(KavenegarApi::class);
        $kavenegarMock->expects($this->once())
            ->method('Send')
            ->willReturn(true);

        // Bind the mock to the container
        $this->app->instance(KavenegarApi::class, $kavenegarMock);

        // Mock Log facade
        Log::shouldReceive('info')->withAnyArgs()->times(7);

        // Create job instance
        $job = new SmsSendJob(
            '09353466620',
            'Test message',
            null,
            null,
            null
        );

        // Execute the job
        $job->handle();
    }

    public function test_sms_job_handles_failure()
    {
        // Mock the KavenegarApi
        $kavenegarMock = $this->createMock(KavenegarApi::class);
        $kavenegarMock->expects($this->once())
            ->method('Send')
            ->willReturn(false);

        // Bind the mock to the container
        $this->app->instance(KavenegarApi::class, $kavenegarMock);

        // Mock Log facade
        Log::shouldReceive('error')
            ->with('SMS sent failed')
            ->once();

        // Create job instance
        $job = new SmsSendJob(
            '09123456789',
            'Test message',
            null,
            null,
            null
        );

        // Execute the job
        $job->handle();
    }
} 