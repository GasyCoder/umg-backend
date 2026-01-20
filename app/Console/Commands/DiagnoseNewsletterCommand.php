<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Http\Controllers\Api\V1\Admin\NewsletterCampaignAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use App\Models\User;

class DiagnoseNewsletterCommand extends Command
{
    protected $signature = 'diagnose:newsletter';
    protected $description = 'Diagnose newsletter config and benchmark dispatch speed';

    public function handle()
    {
        $this->info('--- NEWSLETTER DIAGNOSTIC ---');
        
        // 1. Check Config
        $queueConn = config('queue.default');
        $mailDriver = config('mail.default');
        $this->line("QUEUE_CONNECTION: <comment>$queueConn</comment>");
        $this->line("MAIL_MAILER: <comment>$mailDriver</comment>");
        
        // Check DB connections for jobs
        if ($queueConn === 'database') {
            $pending = \DB::table('jobs')->count();
            $failed = \DB::table('failed_jobs')->count();
            $this->line("Pending Jobs in DB: <comment>$pending</comment>");
            $this->line("Failed Jobs in DB: <error>$failed</error>");
        }

        // 2. Measure Dispatch Speed
        // Create fakes
        $this->info("\n--- BENCHMARK ---");
        $count = 10;
        $this->line("Simulating send to $count fake subscribers...");
        
        // Create temporary subscribers
        $subs = [];
        for ($i=0; $i<$count; $i++) {
            $email = "test_diag_{$i}@example.com";
            $sub = NewsletterSubscriber::firstOrCreate(['email' => $email], [
                'status' => 'active', 
                'token' => 'bench_' . $i . '_' . uniqid()
            ]);
            $subs[] = $sub->id;
        }

        // Create temp campaign
        $campaign = NewsletterCampaign::create([
            'subject' => '[DIAGNOSTIC] Benchmark',
            'content_html' => '<p>Test</p>',
            'status' => 'draft',
            'created_by' => 1 // Assumption: user 1 exists, or we fake it
        ]);

        $start = microtime(true);
        
        // Simulate Controller Logic
        // We use the controller Instance logic directly (or dispatch manually)
        // Let's mimic controller send Logic manually to avoid auth/request mocking complexity
        
        $queuedCount = 0;
        \DB::transaction(function () use ($campaign, $subs, &$queuedCount) {
             // 1. Create Sends
             $rows = [];
             foreach($subs as $id) {
                 $rows[] = [
                     'newsletter_campaign_id' => $campaign->id,
                     'newsletter_subscriber_id' => $id,
                     'status' => 'queued',
                     'created_at' => now(),
                     'updated_at' => now(),
                 ];
             }
             \App\Models\NewsletterSend::insert($rows);
             $queuedCount = count($rows);

             // 2. Dispatch
             $sends = \App\Models\NewsletterSend::where('newsletter_campaign_id', $campaign->id)->get();
             foreach ($sends as $s) {
                 \App\Jobs\SendNewsletterToSubscriberJob::dispatch($s->id);
             }
        });

        $duration = microtime(true) - $start;
        
        $this->info("Dispatch Time for $count jobs: " . round($duration, 4) . "s");
        
        if ($queueConn === 'sync') {
            $this->error("WARNING: Queue is SYNC. Jobs executed immediately. 10 jobs took {$duration}s.");
            $est = ($duration / $count) * 513;
            $this->error("Estimation for 513 subs: " . round($est, 2) . "s (Timeout Risk!)");
        } else {
            $this->info("Queue is ASYNC ($queueConn). Dispatch fast.");
            // Check if jobs actually landed
             if ($queueConn === 'database') {
                $newPending = \DB::table('jobs')->count();
                $this->line("Jobs currently in DB: $newPending");
            }
        }

        // Cleanup
        $this->line("Cleaning up...");
        NewsletterCampaign::where('id', $campaign->id)->delete();
        \App\Models\NewsletterSend::where('newsletter_campaign_id', $campaign->id)->delete();
        NewsletterSubscriber::where('email', 'like', 'test_diag_%')->delete();
        
        $this->info("Done.");
    }
}
