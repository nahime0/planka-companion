<div class="flex items-center gap-2">
    <span 
        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
        style="background-color: {{ $getRecord()->color }}20; color: {{ $getRecord()->color }}; ring-color: {{ $getRecord()->color }}30;"
    >
        <span class="h-2 w-2 rounded-full" style="background-color: {{ $getRecord()->color }}"></span>
        {{ $getRecord()->name ?: 'No name' }}
    </span>
</div>