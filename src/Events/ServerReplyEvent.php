<?php

namespace Pharaoh\Express\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServerReplyEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $params = [];

    /**
     * Create a new event instance.
     *
     * @param array $params
     * @param string $type
     */
    public function __construct(array $params, string $type)
    {
        $this->params = $params;
        $this->params['type'] = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
