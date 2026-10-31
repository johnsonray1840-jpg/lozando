<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorTrackMail;

class SendVisitorEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

public function handle()
{
    $recipient = env('VISITOR_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS'));
    
    try {
        \Illuminate\Support\Facades\Log::info('SendVisitorEmail: Attempting to send email to ' . $recipient);
        
        \Illuminate\Support\Facades\Mail::to($recipient)
            ->send(new \App\Mail\VisitorTrackMail($this->data));
            
        \Illuminate\Support\Facades\Log::info('SendVisitorEmail: Email sent successfully');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('SendVisitorEmail: Failed to send email - ' . $e->getMessage());
    }
}
}