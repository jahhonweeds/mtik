
<div class="p-4">
    <div class="mb-4 flex items-center gap-2">
        <label for="interface" class="font-semibold">Ethernet Port:</label>
        <input type="text" id="interface" wire:model.lazy="interface" wire:change="fetchTraffic" class="border rounded px-2 py-1 bg-gray-900 text-white" placeholder="ether1">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-gray-800 rounded-lg p-4 flex flex-col items-center">
            <span class="text-sm text-gray-400">Tx Rate</span>
            <span class="text-2xl font-bold text-green-400">{{ number_format(($traffic['out'] ?? 0) / 1000000, 2) }} Mbps</span>
            <span class="text-sm text-gray-400 mt-2">SFP Tx Power</span>
            <span class="text-lg font-semibold text-yellow-400">{{ $traffic['sfp_tx_power'] ?? 'N/A' }} dBm</span>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 flex flex-col items-center">
            <span class="text-sm text-gray-400">Rx Rate</span>
            <span class="text-2xl font-bold text-blue-400">{{ number_format(($traffic['in'] ?? 0) / 1000000, 2) }} Mbps</span>
            <span class="text-sm text-gray-400 mt-2">SFP Rx Power</span>
            <span class="text-lg font-semibold text-yellow-400">{{ $traffic['sfp_rx_power'] ?? 'N/A' }} dBm</span>
        </div>
    </div>
    {{-- <div class="mt-2 text-xs text-gray-600">Last updated: {{ now()->format('H:i:s') }}</div> --}}
</div>
