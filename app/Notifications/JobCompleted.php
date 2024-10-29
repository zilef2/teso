<?php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobCompleted extends Notification
{
    use Queueable;

    public function __construct($jobName)
    {
        $this->jobName = $jobName;
    }

    public function via($notifiable)
    {
        return ['mail']; // Puedes agregar otros canales si es necesario
    }

    public function toMail($notifiable)
    {
        return $this->subject($this->jobName)
            ->view('views.jobs.index');
    }
}
