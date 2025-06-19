<?php

namespace App\Services;

use App\Models\Planka\Card;
use App\Models\Planka\UserAccount;
use App\Notifications\PlankaCardNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class TelegramNotificationService
{
    protected ?string $chatId;

    public function __construct()
    {
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * Send a notification about a Planka card to all relevant users
     *
     * @param Card|string|int $card Card instance or card ID
     * @param string $message Optional custom message
     * @return void
     * @throws \Exception
     */
    public function notifyCard(Card|string|int $card, string $message = ''): void
    {
        // Ensure chat ID is configured
        if (!$this->chatId) {
            throw new \Exception('Telegram chat ID is not configured. Please set TELEGRAM_CHAT_ID in your .env file.');
        }

        // Get card instance if ID was provided
        if (!$card instanceof Card) {
            $card = Card::findOrFail($card);
        }

        // Load relationships for better notification content
        $card->load(['board', 'list', 'creatorUser', 'cardSubscriptions.user']);

        // Get users to notify
        $usersToNotify = $this->getUsersToNotify($card);

        if ($usersToNotify->isEmpty()) {
            return;
        }

        // Send notifications to each user
        Notification::send($usersToNotify, new PlankaCardNotification($card, $message));
    }

    /**
     * Send a notification about multiple cards
     *
     * @param array $cards Array of Card instances or IDs
     * @param string $message Optional custom message
     * @return void
     */
    public function notifyMultipleCards(array $cards, string $message = ''): void
    {
        foreach ($cards as $card) {
            $this->notifyCard($card, $message);
        }
    }

    /**
     * Get the users who should be notified about a card
     *
     * @param Card $card
     * @return Collection
     */
    protected function getUsersToNotify(Card $card): Collection
    {
        $users = collect();

        // Add the card creator
        if ($card->creatorUser) {
            $users->put($card->creatorUser->id, $card->creatorUser);
        }

        // Add all subscribers
        foreach ($card->cardSubscriptions as $subscription) {
            if ($subscription->user) {
                // Use put to avoid duplicate notifications if creator is also subscribed
                $users->put($subscription->user->id, $subscription->user);
            }
        }

        return $users->values();
    }

    /**
     * Send a notification to specific users about a card
     *
     * @param Card|string|int $card Card instance or card ID
     * @param array|Collection $users Array or Collection of UserAccount instances or user IDs
     * @param string $message Optional custom message
     * @return void
     */
    public function notifyCardToUsers(Card|string|int $card, array|Collection $users, string $message = ''): void
    {
        // Ensure chat ID is configured
        if (!$this->chatId) {
            throw new \Exception('Telegram chat ID is not configured. Please set TELEGRAM_CHAT_ID in your .env file.');
        }

        // Get card instance if ID was provided
        if (!$card instanceof Card) {
            $card = Card::findOrFail($card);
        }

        // Load relationships for better notification content
        $card->load(['board', 'list']);

        // Convert array to collection if needed
        if (is_array($users)) {
            $users = collect($users);
        }

        // Get user instances if IDs were provided
        $userInstances = $users->map(function ($user) {
            if ($user instanceof UserAccount) {
                return $user;
            }
            return UserAccount::find($user);
        })->filter();

        if ($userInstances->isNotEmpty()) {
            Notification::send($userInstances, new PlankaCardNotification($card, $message));
        }
    }

    /**
     * Set a different chat ID for this instance
     *
     * @param string $chatId
     * @return self
     */
    public function toChatId(string $chatId): self
    {
        $this->chatId = $chatId;
        return $this;
    }
}