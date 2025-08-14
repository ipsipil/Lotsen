<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class BookingCancelled extends Notification
{
    use Queueable;
    public function __construct(public Booking $booking) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Buchung storniert: '.$this->booking->shift->name.' am '.$this->booking->date)
            ->greeting('Hallo '.$notifiable->name.'!')
            ->line('Deine Buchung wurde storniert.')
            ->line('Datum: '.$this->booking->date)
            ->line('Schicht: '.$this->booking->shift->name)
            ->action('Zum Kalender', url('/dashboard/'.$notifiable->guid))
            ->line('Danke fürs Aktualisieren!');
    }
}
