<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public string $to,
        public string $subject,
        public string $body,
    ) {}

    public function handle(): void
    {
        Mail::raw(
            $this->body,
            fn ($message) => $message
                ->to($this->to)
                ->subject($this->subject)
        );
    }
}
