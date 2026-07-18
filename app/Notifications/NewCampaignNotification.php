<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCampaignNotification extends Notification
{
    use Queueable;

    public function __construct(public Campaign $campaign) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'campaign',
            'title'        => 'New campaign: ' . $this->campaign->title,
            'body'         => 'A new campaign was launched for ' . $this->campaign->venture->title,
            'campaign_id'  => $this->campaign->id,
            'venture_name' => $this->campaign->venture->title,
            'url'          => '/investor/campaigns',
            'icon'         => 'fa-bullhorn',
        ];
    }
}
