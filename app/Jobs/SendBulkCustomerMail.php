<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\BulkMailRecipient;
use App\Models\BulkMailLog;
use App\Models\User;
use App\Mail\TopluMusteriBilgilendirmeMail;

class SendBulkCustomerMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipientId;
    public $sender;
    public $targetUser;
    public $log;

    /**
     * Create a new job instance.
     */
    public function __construct($recipientId, User $sender, User $targetUser, BulkMailLog $log)
    {
        $this->recipientId = $recipientId;
        $this->sender = $sender;
        $this->targetUser = $targetUser;
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipient = BulkMailRecipient::find($this->recipientId);
        if (!$recipient) return;

        try {
            Mail::to($this->targetUser->email)->send(
                new TopluMusteriBilgilendirmeMail($this->log->subject, $this->log->body)
            );

            $recipient->update(['status' => 'sent']);
        } catch (\Exception $e) {
            $recipient->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
