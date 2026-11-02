<?php

namespace App\Console\Commands;

use App\Services\GoPosApiService;
use Illuminate\Console\Command;

class GoPosWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:go-pos-webhooks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(GoPosApiService $goPosApiService)
    {
        dd($goPosApiService->makeGetWebhookRequest(2)->json());
    }
}
