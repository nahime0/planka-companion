# Planka Companion

> **DISCLAIMER**: This project is currently in active development and is **NOT** suitable for production use. Features may be incomplete, APIs may change, and bugs may exist. Use at your own risk.

A Laravel-based companion application for [Planka](https://github.com/plankanban/planka) that extends its functionality with advanced features like expiration notifications, enhanced card management, and Telegram integration.

## Why Planka Companion?

While Planka is an excellent open-source project management tool, it lacks some essential features for teams that need:
- **Expiration Notifications**: Get notified before cards expire
- **External Notifications**: Telegram integration for real-time updates
- **Advanced Card Management**: Better visibility of expiring and overdue cards
- **Notification History**: Track all notifications sent for audit purposes

Planka Companion fills these gaps without modifying Planka's core database, ensuring compatibility and easy updates.

## Features

### Card Expiration Management
- **30-minute warnings** before card due dates
- **Daily reminders** for overdue cards (sent after 10 AM)
- **Smart notification system** that prevents duplicate alerts
- **Visual indicators** for overdue cards in the interface

### Telegram Integration
- Send notifications to card creators and subscribers
- Customizable notification messages
- Support for multiple notification scenarios
- Full notification history tracking

### Enhanced Card Views
- **Hierarchical display**: Project → Board → List → Card
- **Recently updated cards widget** on dashboard
- **Advanced filtering** by board, list, due date, and overdue status
- **Card statistics** at a glance (comments, attachments, members, tasks)

### Notification Tracking
- Complete history of all notifications sent
- View notification logs per card
- Track who was notified and when
- Prevent notification fatigue with intelligent deduplication

## Requirements

- PHP 8.2 or higher
- PostgreSQL (for Planka database connection)
- SQLite or MySQL/PostgreSQL (for companion app data)
- Composer
- A running Planka instance
- Telegram Bot Token (for notifications)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/nahime/planka-companion.git
   cd planka-companion
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up database connections**
   
   Edit `.env` and configure your Planka database connection:
   ```env
   # Planka Database Connection
   PLANKA_DB_HOST=your-planka-host
   PLANKA_DB_PORT=5432
   PLANKA_DB_DATABASE=planka
   PLANKA_DB_USERNAME=planka
   PLANKA_DB_PASSWORD=your-password
   ```

5. **Configure Telegram (optional)**
   ```env
   TELEGRAM_BOT_TOKEN=your-bot-token
   TELEGRAM_CHAT_ID=your-chat-id
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

8. **Set up the scheduler**
   
   Add this cron entry to run Laravel's scheduler:
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Usage

### Accessing the Dashboard

Navigate to `http://localhost:8000/admin` and log in using your Planka credentials.

### Viewing Cards

The dashboard shows recently updated cards. You can:
- Click on any card to view details
- See notification history for each card
- Filter cards by various criteria
- Access cards directly in Planka

### Notifications

Notifications are sent automatically based on the schedule:
- **Expiring cards**: Checked every minute, notified 30 minutes before due date
- **Expired cards**: Daily notifications sent after 10 AM

### Manual Notification Command

You can also run notifications manually:
```bash
php artisan cards:notify-expiring
```

## Architecture

Planka Companion follows a **read-only** approach to Planka's database:
- Never modifies Planka tables directly
- Maintains its own database for companion-specific data
- Uses separate database connections for clean separation

## Development

### Code Style
```bash
composer lint      # Run PHP linter
composer typecheck # Run static analysis
composer test      # Run tests
```

### Project Guidelines

## Important Notes

**Never modify Planka database tables directly** - This is a core principle of Planka Companion

**Telegram Notifications** - Currently, all users share the same Telegram chat ID. Individual user chat IDs will be supported in future versions.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source and available under the [MIT License](LICENSE).

## Acknowledgments

- [Planka](https://github.com/plankanban/planka) - The amazing project management tool this companion extends
- [Laravel](https://laravel.com) - The PHP framework
- [Filament](https://filamentphp.com) - The admin panel builder

## Support

If you find this project helpful, please give it a ⭐ on GitHub!