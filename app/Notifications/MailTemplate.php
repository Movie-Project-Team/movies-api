<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Support\Constants;
use App\Mail\TemplateMailable;

class MailTemplate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $template;
    protected $params;

    /**
     * Create a new job instance.
     *
     * @param array $params
     *
     * @return void
     */

    public function __construct($template, $params = [])
    {
        $this->template = $template;
        $this->params = array_merge($params, [
            'serviceSupport' => config('setting.site.contact'),
            'serviceSiteUrl' => config('setting.site.url'),
        ]);

        $this->onQueue(Constants::QUEUE_NAME_MAIL);
    }

    /**
     * Get the notification channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $templateView = new TemplateMailable($this->template, $this->params);

        return (new MailMessage())
            ->subject($templateView->getSubject())
            ->markdown($this->template, $this->params);;
    }
}