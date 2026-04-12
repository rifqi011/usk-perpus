<x-filament-panels::page>
    @if (!$isEditing)
        <div class="pointer-events-none opacity-75">
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        </div>
    @else
        <form wire:submit="save">
            {{ $this->form }}
        </form>
    @endif
</x-filament-panels::page>
