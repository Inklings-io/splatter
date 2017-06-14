<?php

namespace App\Jobs;

use App\MentionSend;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWebmentions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mention_send_entry;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MentionSend $mention_send_entry)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        ////TODO send webmentions here
    }
}
