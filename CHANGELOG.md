# Changelog

All notable changes to Planka Companion will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.0.1] - 2025-06-19

### Added

#### Core Features
- Initial Laravel 11 application setup for Planka integration
- Planka database connection configuration
- Authentication using Planka credentials
- Filament admin panel integration

#### Models & Resources
- Planka models for all core entities (Cards, Boards, Lists, Projects, Users, etc.)
- Filament resources for managing Planka data
- Card hierarchical view showing Project → Board → List → Card
- Recently updated cards widget for dashboard

#### Telegram Notifications
- Telegram notification service for Planka cards
- Automatic notifications for card creators and subscribers
- Notification history tracking with `NotificationLog` model
- Notification logs viewable in card detail pages
- Card expiration notifications:
  - 30-minute warning before due date
  - Daily reminders for overdue cards (sent after 10 AM)
  - Intelligent duplicate prevention using notification history

#### Card Management
- Card actions utility for quick access to Planka cards
- Card filtering and sorting
- Card statistics display (comments, attachments, members, tasks)
- Due date tracking with visual indicators for overdue cards

#### Commands
- `cards:notify-expiring` - Automated command for expiration notifications
  - Scheduled to run every minute
  - Checks for cards expiring in 30 minutes
  - Sends daily overdue reminders after 10 AM

### Configuration
- Environment variables for Planka database connection
- Telegram bot token and chat ID configuration
- Separate database connections for Planka and companion data

### Dependencies
- Laravel 11.x
- Filament 3.x
- Laravel Notification Channels - Telegram
- PostgreSQL driver for Planka connection
- SQLite for companion application data

[0.0.1]: https://github.com/nahime0/planka-companion/releases/tag/v0.0.1