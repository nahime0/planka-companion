<?php

namespace App\Services;

use App\Models\Planka\Board;
use App\Models\Planka\Card;

class PlankaUrlService
{
    protected string $baseUrl;

    public function __construct(array $config)
    {
        $this->baseUrl = rtrim($config['url'] ?? 'http://localhost:1337', '/');
    }

    /**
     * Get the base Planka URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Generate a URL for a board
     */
    public function boardUrl(string|int|Board $board): string
    {
        $boardId = $board instanceof Board ? $board->id : $board;
        
        return "{$this->baseUrl}/boards/{$boardId}";
    }

    /**
     * Generate a URL for a card
     */
    public function cardUrl(string|int|Card $card): string
    {
        $cardId = $card instanceof Card ? $card->id : $card;
        
        return "{$this->baseUrl}/cards/{$cardId}";
    }

    /**
     * Generate a URL for a project
     */
    public function projectUrl(string|int $projectId): string
    {
        return "{$this->baseUrl}/projects/{$projectId}";
    }

    /**
     * Generate a URL for a user
     */
    public function userUrl(string|int $userId): string
    {
        return "{$this->baseUrl}/users/{$userId}";
    }
}