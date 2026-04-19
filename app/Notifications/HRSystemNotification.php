<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;


class HRSystemNotification extends Notification
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->data['title'],
            'body'    => $this->data['body'],
            'type'    => $this->data['type'], // (salary, penalty, reward, contract)
            'link'    => $this->data['link'] ?? '#',
            'icon'    => $this->data['icon'] ?? 'fas fa-info-circle',
            'color'   => $this->data['color'] ?? 'text-primary',
    
               ];
    }
}   