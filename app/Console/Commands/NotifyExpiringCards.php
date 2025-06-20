<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\Planka\Card;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyExpiringCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:notify-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify about cards that are expiring soon or already expired';

    /**
     * The Telegram notification service
     *
     * @var TelegramNotificationService
     */
    protected $telegramService;

    public function __construct(TelegramNotificationService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring and expired cards...');
        
        $aboutToExpireCount = $this->notifyAboutToExpireCards();
        $expiredCount = $this->notifyExpiredCards();
        
        $this->info("Notified about {$aboutToExpireCount} cards expiring soon");
        $this->info("Notified about {$expiredCount} expired cards");
        
        return Command::SUCCESS;
    }

    /**
     * Notify about cards that are expiring in the next 30 minutes
     *
     * @return int Number of cards notified
     */
    protected function notifyAboutToExpireCards(): int
    {
        $now = Carbon::now();
        $thirtyMinutesFromNow = $now->copy()->addMinutes(30);
        
        // Get cards that have a due date between now and 30 minutes from now
        $expiringCards = Card::whereBetween('due_date', [$now, $thirtyMinutesFromNow])
            ->with(['board', 'list', 'creatorUser', 'cardSubscriptions.user'])
            ->get();
        
        $notifiedCount = 0;
        
        foreach ($expiringCards as $card) {
            // Check if we already sent a "30 minutes before" notification for this card
            $existingNotification = NotificationLog::where('card_id', $card->id)
                ->where('custom_message', 'LIKE', '%expires in 30 minutes%')
                ->where('created_at', '>=', $now->copy()->subHours(2)) // Within last 2 hours
                ->exists();
            
            if (!$existingNotification) {
                try {
                    $this->telegramService->notifyCard(
                        $card,
                        "⏰ This card expires in 30 minutes!"
                    );
                    $notifiedCount++;
                    $this->info("Notified about card: {$card->name}");
                } catch (\Exception $e) {
                    $this->error("Failed to notify about card {$card->name}: " . $e->getMessage());
                }
            }
        }
        
        return $notifiedCount;
    }

    /**
     * Notify about cards that are already expired (once per day after 10 AM)
     *
     * @return int Number of cards notified
     */
    protected function notifyExpiredCards(): int
    {
        $now = Carbon::now();
        $startOfToday = $now->copy()->startOfDay();
        
        // Only send expired notifications after 10 AM
        if ($now->hour < 10) {
            $this->info('Skipping expired card notifications (before 10 AM)');
            return 0;
        }
        
        // Get cards that have already expired
        $expiredCards = Card::where('due_date', '<', $now)
            ->with(['board', 'list', 'creatorUser', 'cardSubscriptions.user'])
            ->get();
        
        $notifiedCount = 0;
        
        foreach ($expiredCards as $card) {
            // Check if we already sent an "expired" notification today
            $existingNotificationToday = NotificationLog::where('card_id', $card->id)
                ->where('custom_message', 'LIKE', '%has expired%')
                ->where('created_at', '>=', $startOfToday)
                ->exists();
            
            if (!$existingNotificationToday) {
                try {
                    $daysOverdue = ceil($card->due_date->diffInDays($now));
                    
                    if ($daysOverdue == 0) {
                        $hoursOverdue = ceil($card->due_date->diffInHours($now));
                        $message = "🚨 This card has expired! It's {$hoursOverdue} hour(s) overdue.";
                    } elseif ($daysOverdue == 1) {
                        $message = "🚨 This card has expired! It's 1 day overdue.";
                    } else {
                        $message = "🚨 This card has expired! It's {$daysOverdue} days overdue.";
                    }
                    
                    $this->telegramService->notifyCard($card, $message);
                    $notifiedCount++;
                    $this->info("Notified about expired card: {$card->name}");
                } catch (\Exception $e) {
                    $this->error("Failed to notify about expired card {$card->name}: " . $e->getMessage());
                }
            }
        }
        
        return $notifiedCount;
    }
}