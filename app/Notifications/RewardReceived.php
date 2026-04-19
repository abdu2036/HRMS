<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RewardReceived extends Notification
{
    use Queueable;

    protected $reward;

    public function __construct($reward)
    {
        $this->reward = $reward;
    }

    // نحدد أن الإشعار سيخزن في قاعدة البيانات
    public function via($notifiable)
    {
        return ['database'];
    }

    // البيانات التي ستظهر للموظف
    public function toArray($notifiable)
    {
        return [
            'title' => 'تهانينا! حصلت على مكافأة جديدة',
            'amount' => $this->reward->amount,
            'days' => $this->reward->days_count,
            'reason' => $this->reward->description,
            'date' => $this->reward->date,
        ];
    }
}