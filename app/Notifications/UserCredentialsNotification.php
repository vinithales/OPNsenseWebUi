<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $credentials;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
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
        $loginUrl = url('/login');

        return (new MailMessage)
                    ->subject('Suas Credenciais de Acesso - OPNsense Web UI')
                    ->greeting("Olá, {$this->credentials['ra']}!")
                    ->line('Seu acesso ao sistema OPNsense foi criado com sucesso!')
                    ->line('')
                    ->line('**Suas credenciais de acesso:**')
                    ->line("**Usuário (RA):** {$this->credentials['ra']}")
                    ->line("**Senha temporária:** {$this->credentials['password']}")
                    ->line("**E-mail:** {$this->credentials['email']}")
                    ->line("**Tipo:** " . ucfirst($this->credentials['user_type']))
                    ->line('')
                    ->action('Acessar Sistema', $loginUrl)
                    ->line('')
                    ->line('⚠️ **IMPORTANTE:**')
                    ->line('- Esta é uma senha temporária gerada automaticamente.')
                    ->line('- Por favor, altere sua senha no primeiro acesso.')
                    ->line('- Não compartilhe suas credenciais com terceiros.')
                    ->line('- Guarde esta senha em local seguro até realizar o primeiro acesso.')
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
            'ra' => $this->credentials['ra'],
            'email' => $this->credentials['email'],
            'user_type' => $this->credentials['user_type'],
        ];
    }
}
