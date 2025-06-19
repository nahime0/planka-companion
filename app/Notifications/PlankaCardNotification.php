<?php

namespace App\Notifications;

use App\Models\Planka\Card;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class PlankaCardNotification extends Notification
{
    use Queueable;

    protected Card $card;
    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Card $card, string $message = '')
    {
        $this->card = $card;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        $plankaUrl = planka()->cardUrl($this->card);
        
        // Build the message content
        $content = "📋 **Planka Card Update**\n\n";
        
        // Add custom message if provided
        if ($this->message) {
            $content .= $this->message . "\n\n";
        }
        
        // Add card details
        $content .= "**Card:** {$this->card->name}\n";
        
        if ($this->card->board) {
            $content .= "**Board:** {$this->card->board->name}\n";
        }
        
        if ($this->card->list) {
            $content .= "**List:** {$this->card->list->name}\n";
        }
        
        if ($this->card->due_date) {
            $dueDate = $this->card->due_date->format('M d, Y H:i');
            $content .= "**Due Date:** {$dueDate}\n";
        }
        
        // Create and return the message
        return TelegramMessage::create()
            ->content($content)
            ->button('View Card', $plankaUrl);
    }
}