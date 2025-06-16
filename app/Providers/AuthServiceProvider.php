<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Planka Model Policies
        \App\Models\Planka\Action::class => \App\Policies\Planka\ActionPolicy::class,
        \App\Models\Planka\Attachment::class => \App\Policies\Planka\AttachmentPolicy::class,
        \App\Models\Planka\BackgroundImage::class => \App\Policies\Planka\BackgroundImagePolicy::class,
        \App\Models\Planka\BaseCustomFieldGroup::class => \App\Policies\Planka\BaseCustomFieldGroupPolicy::class,
        \App\Models\Planka\Board::class => \App\Policies\Planka\BoardPolicy::class,
        \App\Models\Planka\BoardMembership::class => \App\Policies\Planka\BoardMembershipPolicy::class,
        \App\Models\Planka\BoardSubscription::class => \App\Policies\Planka\BoardSubscriptionPolicy::class,
        \App\Models\Planka\Card::class => \App\Policies\Planka\CardPolicy::class,
        \App\Models\Planka\CardLabel::class => \App\Policies\Planka\CardLabelPolicy::class,
        \App\Models\Planka\CardMembership::class => \App\Policies\Planka\CardMembershipPolicy::class,
        \App\Models\Planka\CardSubscription::class => \App\Policies\Planka\CardSubscriptionPolicy::class,
        \App\Models\Planka\Comment::class => \App\Policies\Planka\CommentPolicy::class,
        \App\Models\Planka\CustomField::class => \App\Policies\Planka\CustomFieldPolicy::class,
        \App\Models\Planka\CustomFieldGroup::class => \App\Policies\Planka\CustomFieldGroupPolicy::class,
        \App\Models\Planka\CustomFieldValue::class => \App\Policies\Planka\CustomFieldValuePolicy::class,
        \App\Models\Planka\FileReference::class => \App\Policies\Planka\FileReferencePolicy::class,
        \App\Models\Planka\IdentityProviderUser::class => \App\Policies\Planka\IdentityProviderUserPolicy::class,
        \App\Models\Planka\Label::class => \App\Policies\Planka\LabelPolicy::class,
        \App\Models\Planka\ListModel::class => \App\Policies\Planka\ListModelPolicy::class,
        \App\Models\Planka\Notification::class => \App\Policies\Planka\NotificationPolicy::class,
        \App\Models\Planka\NotificationService::class => \App\Policies\Planka\NotificationServicePolicy::class,
        \App\Models\Planka\Project::class => \App\Policies\Planka\ProjectPolicy::class,
        \App\Models\Planka\ProjectFavorite::class => \App\Policies\Planka\ProjectFavoritePolicy::class,
        \App\Models\Planka\ProjectManager::class => \App\Policies\Planka\ProjectManagerPolicy::class,
        \App\Models\Planka\Session::class => \App\Policies\Planka\SessionPolicy::class,
        \App\Models\Planka\Task::class => \App\Policies\Planka\TaskPolicy::class,
        \App\Models\Planka\TaskList::class => \App\Policies\Planka\TaskListPolicy::class,
        \App\Models\Planka\UserAccount::class => \App\Policies\Planka\UserAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}