<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('MikroTik')" :subheading="__('Configure your MikroTik router connection')">
        @if (session('message'))
            <flux:callout variant="success" class="mb-4">{{ session('message') }}</flux:callout>
        @endif

        @if (session('error'))
            <flux:callout variant="danger" class="mb-4">{{ session('error') }}</flux:callout>
        @endif

        <form wire:submit="save" class="my-6 w-full space-y-6">
            <flux:input wire:model="host" :label="__('Host / IP Address')" type="text" required placeholder="192.168.88.1" />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input wire:model="port" :label="__('API Port')" type="number" min="1" max="65535" required />
                <flux:input wire:model="timeout" :label="__('Timeout (seconds)')" type="number" min="1" max="300" required />
            </div>

            <flux:input wire:model="username" :label="__('Username')" type="text" required />
            <flux:input wire:model="password" :label="__('Password')" type="password" placeholder="Leave blank to keep the saved password" />

            <div class="flex items-center gap-3">
                <flux:badge :color="$connectionStatus === 'Connected' ? 'green' : ($connectionStatus === 'Disconnected' ? 'red' : 'zinc')">
                    {{ $connectionStatus }}
                </flux:badge>
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                <flux:button type="button" wire:click="testConnection">{{ __('Test Connection') }}</flux:button>
            </div>
        </form>
    </x-settings.layout>
</section>
