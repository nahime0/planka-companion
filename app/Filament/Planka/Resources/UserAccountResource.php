<?php

namespace App\Filament\Planka\Resources;

use App\Filament\Planka\Resources\UserAccountResource\Pages;
use App\Filament\Planka\Resources\UserAccountResource\RelationManagers;
use App\Models\Planka\UserAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\FontWeight;

class UserAccountResource extends Resource
{
    protected static ?string $model = UserAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Users';
    
    protected static ?string $label = 'User';
    
    protected static ?string $pluralLabel = 'Users';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('username')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\Select::make('role')
                                    ->options([
                                        'user' => 'User',
                                        'admin' => 'Admin',
                                    ])
                                    ->required()
                                    ->default('user'),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('organization')
                                    ->maxLength(255),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Preferences')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('language')
                                    ->options([
                                        'en' => 'English',
                                        'de' => 'German',
                                        'fr' => 'French',
                                        'es' => 'Spanish',
                                        'it' => 'Italian',
                                        'pt' => 'Portuguese',
                                        'ru' => 'Russian',
                                        'zh' => 'Chinese',
                                        'ja' => 'Japanese',
                                    ]),
                                Forms\Components\Select::make('default_editor_mode')
                                    ->options([
                                        'edit' => 'Edit',
                                        'preview' => 'Preview',
                                    ])
                                    ->default('edit')
                                    ->required(),
                                Forms\Components\Select::make('default_home_view')
                                    ->options([
                                        'projects' => 'Projects',
                                        'boards' => 'Boards',
                                    ])
                                    ->default('projects')
                                    ->required(),
                                Forms\Components\Select::make('default_projects_order')
                                    ->options([
                                        'created_at' => 'Created Date',
                                        'updated_at' => 'Updated Date',
                                        'name' => 'Name',
                                    ])
                                    ->default('created_at')
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('subscribe_to_own_cards')
                                    ->label('Subscribe to own cards')
                                    ->default(true),
                                Forms\Components\Toggle::make('subscribe_to_card_when_commenting')
                                    ->label('Subscribe to card when commenting')
                                    ->default(true),
                                Forms\Components\Toggle::make('turn_off_recent_card_highlighting')
                                    ->label('Turn off recent card highlighting')
                                    ->default(false),
                                Forms\Components\Toggle::make('enable_favorites_by_default')
                                    ->label('Enable favorites by default')
                                    ->default(false),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Account Status')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_sso_user')
                                    ->label('SSO User')
                                    ->disabled(),
                                Forms\Components\Toggle::make('is_deactivated')
                                    ->label('Deactivated')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_deactivated')
                    ->label('Deactivated')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('projectManagers_count')
                    ->counts('projectManagers')
                    ->label('Projects')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('boardMemberships_count')
                    ->counts('boardMemberships')
                    ->label('Boards')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdCards_count')
                    ->counts('createdCards')
                    ->label('Cards Created')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                    ]),
                Tables\Filters\TernaryFilter::make('is_deactivated')
                    ->label('Account Status')
                    ->placeholder('All users')
                    ->trueLabel('Deactivated only')
                    ->falseLabel('Active only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectManagersRelationManager::class,
            RelationManagers\BoardMembershipsRelationManager::class,
            RelationManagers\CreatedCardsRelationManager::class,
        ];
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),
                                TextEntry::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                                TextEntry::make('username')
                                    ->icon('heroicon-o-at-symbol')
                                    ->placeholder('Not set'),
                                TextEntry::make('role')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'admin' => 'danger',
                                        'user' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('organization')
                                    ->icon('heroicon-o-building-office')
                                    ->placeholder('Not set'),
                                TextEntry::make('phone')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('Not set'),
                            ]),
                    ]),
                
                Section::make('Statistics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('projectManagers_count')
                                    ->label('Projects Managed')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->state(fn ($record) => $record->projectManagers()->count()),
                                TextEntry::make('boardMemberships_count')
                                    ->label('Board Memberships')
                                    ->badge()
                                    ->color('info')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->state(fn ($record) => $record->boardMemberships()->count()),
                                TextEntry::make('createdCards_count')
                                    ->label('Cards Created')
                                    ->badge()
                                    ->color('warning')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->state(fn ($record) => $record->createdCards()->count()),
                                TextEntry::make('comments_count')
                                    ->label('Comments')
                                    ->badge()
                                    ->color('success')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->state(fn ($record) => $record->comments()->count()),
                            ]),
                    ]),
                
                Section::make('Preferences')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('language')
                                    ->label('Language')
                                    ->placeholder('Not set'),
                                TextEntry::make('default_editor_mode')
                                    ->label('Default Editor Mode')
                                    ->badge(),
                                TextEntry::make('default_home_view')
                                    ->label('Default Home View')
                                    ->badge(),
                                TextEntry::make('default_projects_order')
                                    ->label('Default Projects Order')
                                    ->badge(),
                                IconEntry::make('subscribe_to_own_cards')
                                    ->label('Subscribe to Own Cards')
                                    ->boolean(),
                                IconEntry::make('subscribe_to_card_when_commenting')
                                    ->label('Subscribe When Commenting')
                                    ->boolean(),
                                IconEntry::make('turn_off_recent_card_highlighting')
                                    ->label('Turn Off Card Highlighting')
                                    ->boolean(),
                                IconEntry::make('enable_favorites_by_default')
                                    ->label('Enable Favorites by Default')
                                    ->boolean(),
                            ]),
                    ]),
                
                Section::make('Account Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('is_sso_user')
                                    ->label('SSO User')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-shield-check')
                                    ->falseIcon('heroicon-o-key'),
                                IconEntry::make('is_deactivated')
                                    ->label('Account Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-x-circle')
                                    ->falseIcon('heroicon-o-check-circle')
                                    ->trueColor('danger')
                                    ->falseColor('success')
                                    ->label(fn (bool $state): string => $state ? 'Deactivated' : 'Active'),
                                TextEntry::make('created_at')
                                    ->label('Joined')
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime()
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('password_changed_at')
                                    ->label('Password Changed')
                                    ->dateTime()
                                    ->icon('heroicon-o-key')
                                    ->placeholder('Never changed'),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserAccounts::route('/'),
            'create' => Pages\CreateUserAccount::route('/create'),
            'view' => Pages\ViewUserAccount::route('/{record}'),
        ];
    }
}
