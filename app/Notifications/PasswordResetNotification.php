<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $username;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $username)
    {
        $this->token = $token;
        $this->username = $username;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email ?? $notifiable
        ], false));

        return (new MailMessage)
                    ->subject('Redefinição de Senha - OPNsense Web UI')
                    ->greeting('Olá!')
                    ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.')
                    ->line("**Usuário:** {$this->username}")
                    ->action('Redefinir Senha', $resetUrl)
                    ->line('Este link de redefinição de senha expirará em 60 minutos.')
                    ->line('Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.')
                    ->salutation('Atenciosamente, Equipe ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'username' => $this->username,
            'token' => $this->token,
        ];
    }
}
