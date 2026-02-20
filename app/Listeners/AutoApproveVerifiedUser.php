<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AutoApproveVerifiedUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // Kullanıcı e-postasını doğruladığı an 'onaylandi_mi' true olsun.
        $user = $event->user;

        if (!$user->onaylandi_mi) {
            $user->onaylandi_mi = true;
            $user->save();
        }
    }
}
