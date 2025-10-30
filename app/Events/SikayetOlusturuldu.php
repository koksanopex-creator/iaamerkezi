<?php

namespace App\Events;

use App\Models\MusteriSikayeti;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SikayetOlusturuldu implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sikayet;

    public function __construct(MusteriSikayeti $sikayet)
    {
        $this->sikayet = $sikayet;
    }

    /**
     * Olayın yayınlanacağı kanalı/kanalları alın.
     */
    public function broadcastOn(): array
    {
        // Herkesin dinleyebileceği public bir kanal
        return [
            new Channel('sikayet-raporlari'),
        ];
    }
}