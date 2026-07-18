<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'message',
            'title'      => 'New message from ' . $this->message->sender->name,
            'body'       => \Str::limit($this->message->content, 80),
            'sender_id'  => $this->message->sender_id,
            'sender_name'=> $this->message->sender->name,
            'url'        => '/messages/' . $this->message->sender_id,
            'icon'       => 'fa-comment',
        ];
    }
}
