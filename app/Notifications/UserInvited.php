<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class UserInvited extends Notification
{
    use Queueable;
    public function __construct(public User $user) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        $link = url('/dashboard/'.$this->user->guid);

        return (new MailMessage)
            ->subject('Dein persönlicher Kalenderlink')
            ->greeting('Hallo '.$this->user->name.'!')
            ->line('hier ist dein persönlicher Link zum Schülerlotsen‑Kalender.')
            ->action('Zum Kalender', $link)
            ->line('Bitte speichere den Link. Er identifiziert dich automatisch.')
            ->line('Falls der Link einmal verloren geht, melde dich bei der Koordination.');
    }
}
