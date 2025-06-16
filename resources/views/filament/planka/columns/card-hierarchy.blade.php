@php
    $card = $getRecord();
    $board = $card->board;
    $project = $board?->project;
    $list = $card->list;
@endphp

<div class="items-center gap-1 text-sm">
    @if ($project)
        <a href="{{ route('filament.planka.resources.projects.view', $project) }}"
            class="font-medium text-primary-600 hover:text-primary-500 hover:underline">
            {{ $project->name }}
        </a>
        <span class="text-gray-400">/</span>
    @endif

    @if ($board)
        <a href="{{ route('filament.planka.resources.boards.view', $board) }}"
            class="text-gray-700 hover:text-primary-600 hover:underline">
            {{ $board->name }}
        </a>
        <span class="text-gray-400">/</span>
    @endif

    @if ($list)
        <span class="text-gray-600">
            {{ $list->name }}
        </span>
    @endif
</div>
