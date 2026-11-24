<!-- resources/views/livewire/pppoe-manager.blade.php -->

<div class="min-h-screen bg-gradient-to-br from-blue-100 via-purple-100 to-pink-200 py-6 md:py-10 font-sans">
    <div class="max-w-7xl mx-auto rounded-3xl shadow-2xl p-4 md:p-5 bg-white/70 backdrop-blur-lg border border-blue-200">
        
        <h1 class="text-2xl md:text-4xl font-extrabold mb-6 md:mb-8 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-pink-400 drop-shadow-lg text-center">Accounts Management</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-8 mb-8 md:mb-10">
                {{-- <div class="bg-red-500 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Total Accounts</h3>
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-10a2 2 0 01-2-2V8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2zM9 11h6m-3 3V8"></path>
                        </svg>
                    </div>
                    <h1 class="font-bold text-white">{{ count($accountsRaw) }}</h1>
                </div> --}}
                <livewire:total-connected-accounts :total-active-connections="$totalActiveConnections" />
            </div>
            <!-- Connection Status -->
        <div class="mb-6 md:mb-8 flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4">
                <span class="mr-2">Connection Status:</span>
                <span class="px-3 py-1 rounded text-xs {{ $connectionStatus === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $connectionStatus === 'connected' ? 'Connected' : 'Disconnected' }}
                </span>
                <flux:button icon="arrow-path" wire:click="checkConnection" class="ml-4 dark:bg-white hover:bg-gray-700 text-white text-sm px-3 py-1 rounded">
                </flux:button>
            
            </div>

            <!-- Flash Messages (Top Right, Auto-hide) -->
            <div class="fixed top-6 right-6 z-50 flex flex-col gap-2">
                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

        
            <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row flex-wrap gap-2 md:gap-4 mb-6 md:mb-8">
            <flux:modal.trigger name="create-pppoe-account">

        <button wire:click="$set('showCreateForm', true)" 
            class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-2 px-6 rounded-xl shadow-md transition-all duration-200">
                Create New Account
            </button>
            </flux:modal.trigger>
            
        <button wire:click="syncWithMikrotik" 
            class="bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white font-bold py-2 px-6 rounded-xl shadow-md transition-all duration-200">
                Sync with MikroTik
            </button>
            
        <button wire:click="loadActiveConnections" 
            class="bg-gradient-to-r from-purple-400 to-pink-400 hover:from-purple-500 hover:to-pink-500 text-white font-bold py-2 px-6 rounded-xl shadow-md transition-all duration-200">
                Refresh Active Connections
            </button>
             <!-- Search Username --> 
        </div>
         <div class="mb-6 md:mb-8 flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 w-full">
                <input type="text" wire:model.debounce.500ms="searchUsername" placeholder="Search username..." class="border-2 border-blue-300 rounded-xl px-4 py-2 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 shadow-sm" />
                <flux:button wire:click="loadAccounts" class="bg-gradient-to-r from-blue-400 to-purple-400 hover:from-blue-500 hover:to-purple-500 text-white text-sm px-5 py-2 rounded-xl shadow-md transition-all duration-200 w-full md:w-auto">Search</flux:button>
        </div>

        <!-- Active Connections -->
        {{-- @if(isset($activeConnections) && count($activeConnections) > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-3">Active PPPoE Connections ({{ count($activeConnections) }})</h2>
            <div class="overflow-x-auto bg-gray-100 rounded-lg p-4">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">User</th>
                            <th class="px-4 py-2">Address</th>
                            <th class="px-4 py-2">Uptime</th>
                            <th class="px-4 py-2">Service</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeConnections as $connection)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $connection['name'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $connection['address'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $connection['uptime'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $connection['service'] ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif --}}

        <!-- Accounts Table -->
    <div class="overflow-x-auto w-full">
            <table class="min-w-[900px] w-full bg-white/90 dark:bg-gray-900 rounded-2xl shadow-xl border border-blue-100 text-xs md:text-base">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-100 to-purple-100 text-blue-900">
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer" wire:click="sortBy('username')">
                            Username
                            @if ($sortField === 'username')
                                <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-4 text-lg font-semibold">Profile</th>
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer" wire:click="sortBy('active')">
                            Status
                            @if ($sortField === 'active')
                                <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-4 text-lg font-semibold">Bill Amount</th>
                        <th class="px-6 py-4 text-lg font-semibold cursor-pointer" wire:click="sortBy('bill_due_date')">
                            Due Date
                            @if ($sortField === 'bill_due_date')
                                <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-4 text-lg font-semibold whitespace-nowrap">Connection</th>
                        <th class="px-6 py-4 text-lg font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr class="border-t border-blue-100 hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-blue-900 font-medium">{{ $account['username'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center text-purple-700 font-semibold">{{ $account['profile'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge color="{{ $account['active'] ? 'green' : 'red'}}"> {{ $account['active'] ? 'Active' : 'Disabled' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center text-blue-900 font-semibold">Php {{ $account['bill_amount'] ?? '0.00' }}</td>
                            <td class="px-6 py-4 text-center text-blue-900 font-semibold">
                                @if (!empty($account['bill_due_date']))
                                    @php
                                        $due = \Carbon\Carbon::parse($account['bill_due_date']);
                                        $day = $due->day;
                                        $nextDue = $due->copy()->addMonth()->toDateString();
                                    @endphp
                                    Every {{ $day }}th day of the month<br>
                                    <span class="text-xs text-purple-700">Next Due: {{ $nextDue }}</span>
                               @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <button type="button"
                                        wire:click="toggleStatus({{ $account['id'] }})"
                                        wire:confirm="{{ $account['active'] ? 'Disconnect this PPPoE account?' : 'Activate this PPPPoE account?' }}"
                                        style="{{ $account['active'] ? 'background-color:#dc2626;' : 'background-color:#16a34a;' }}color:#ffffff !important;"
                                        class="inline-flex items-center justify-center px-5 py-2 rounded-xl border-0 shadow-md text-sm font-bold text-white transition-colors duration-200 hover:opacity-90">
                                    {{ $account['active'] ? 'Disconnect' : 'Activate' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <flux:modal.trigger name="edit-pppoe-account">
                                    <button type="button" wire:click="editAccount({{ $account['id'] }})"
                                            class="bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-white px-5 py-2 rounded-xl shadow-md text-sm font-bold transition-all duration-200">
                                        Edit
                                    </button>
                                </flux:modal.trigger>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border px-6 py-4 text-center bg-white/70 rounded-xl">No PPPoE accounts found.
                                <button wire:click="syncWithMikrotik" class="text-blue-500 underline font-bold">Sync with MikroTik</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4 md:mt-6 flex justify-center">
                {{ $accounts->links() }}
            </div>
        </div>

            <!-- Create Modal -->

    @if ($showCreateForm)
            <flux:modal name="create-pppoe-account" class="md:w-lg">
                <div class="mb-4">
                    <flux:heading size="lg">Create PPPoE Accountd</flux:heading>
                    <flux:text class="mt-2">Create PPPOE personal details.</flux:text>
                </div>
                <form wire:submit.prevent="createAccount">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="col-span-4">
                            <flux:input label="Account Name" placeholder="account name" wire:model="account_name" class="w-full" />
                        </div>
                        <div class="col-span-2">
                            <flux:input label="Username" type="text" placeholder="username" wire:model="username" class="w-full" />
                        </div>
                        <div class="col-span-2 col-start-3">
                            <flux:input label="Password" viewable type="password" placeholder="password" wire:model="password" class="w-full" />
                        </div>
                        <div class="col-span-2">
                            <flux:select label="Profile" wire:model="profile" class="w-full">
                                <flux:select.option value="default">default</flux:select.option>
                                @if(is_array($profiles))
                                    @foreach($profiles as $profileOption)
                                        @if(isset($profileOption['name']))
                                            <flux:select.option value="{{ $profileOption['name'] }}">{{ $profileOption['name'] }}</flux:select.option>
                                        @endif
                                    @endforeach
                                @endif
                            </flux:select>
                        </div>
                        <div class="col-span-2">
                            <flux:select label="Service" wire:model="service" class="w-full">
                                <flux:select.option value="pppoe">PPPoE</flux:select.option>
                                <flux:select.option value="pptp">PPTP</flux:select.option>
                                <flux:select.option value="l2tp">L2TP</flux:select.option>
                                <flux:select.option value="sstp">SSTP</flux:select.option>
                                <flux:select.option value="ovpn">OpenVPN</flux:select.option>
                            </flux:select>
                        </div>
                        <div class="col-span-2 col-start-1">
                            <flux:input label="Bill Amount" type="number" step="0.01" placeholder="e.g., 49.99" wire:model="bill_amount" class="w-full" />
                        </div>
                        <div class="col-span-2 col-start-3">
                            <flux:input label="Bill Due Date" type="date" wire:model="bill_due_date" class="w-full" />
                        </div>
                        <div >
                            <flux:field variant="inline">
                                <flux:label>Status</flux:label>
                                <flux:switch color="green" wire:model.live="active" />
                                <flux:error name="status" />
                            </flux:field>
                        </div>
                        <div class="col-span-4">
                            <flux:input label="Remote Address (IP)" type="text" placeholder="Leave empty for dynamic IP" wire:model="remote_address" class="w-full" />
                        </div>
                        <div class="col-span-4">
                            <flux:input label="Comment" type="text" placeholder="comment" wire:model="comment" class="w-full" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </form>
            </flux:modal>
            @endif

            <!-- Edit Modal -->

            @if ($showEditForm)
                <flux:modal name="edit-pppoe-account" class="md:w-lg">
                    <div class="mb-4">
                        <flux:heading size="lg">Edit PPPoE Account</flux:heading>
                        <flux:text class="mt-2">Edit PPPoE personal details.</flux:text>
                    </div>
                    <form wire:submit.prevent="updateAccount">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-4">
                                <flux:input label="Account Name" placeholder="account name" wire:model="account_name" class="w-full"  />
                            </div>
                            <div class="col-span-2">
                                <flux:input label="Username" type="text" placeholder="username" wire:model="username" class="w-full" disabled />
                            </div>
                            <div class="col-span-2 col-start-3">
                                <flux:input label="Password"  viewable type="text" placeholder="password" wire:model="password" class="w-full" />
                            </div>
                            <div class="col-span-2">
                                <flux:select label="Profile" wire:model="profile" class="w-full">
                                    <flux:select.option value="default">default</flux:select.option>
                                    @if(is_array($profiles))
                                        @foreach($profiles as $profileOption)
                                            @if(isset($profileOption['name']))
                                                <flux:select.option value="{{ $profileOption['name'] }}">{{ $profileOption['name'] }}</flux:select.option>
                                            @endif
                                        @endforeach
                                    @endif
                                </flux:select>
                            </div>
                            <div class="col-span-2">
                                <flux:select label="Service" wire:model="service" class="w-full">
                                    <flux:select.option value="pppoe">PPPoE</flux:select.option>
                                    <flux:select.option value="pptp">PPTP</flux:select.option>
                                    <flux:select.option value="l2tp">L2TP</flux:select.option>
                                    <flux:select.option value="sstp">SSTP</flux:select.option>
                                    <flux:select.option value="ovpn">OpenVPN</flux:select.option>
                                </flux:select>
                            </div>
                            <div class="col-span-2 col-start-1">
                                <flux:input label="Bill Amount" type="number" step="0.01" placeholder="e.g., 49.99" wire:model="bill_amount" class="w-full" />
                            </div>
                            <div class="col-span-2 col-start-3">
                                <flux:input label="Bill Due Date" type="date" wire:model="bill_due_date" class="w-full" />
                            </div>
                             <div >
                                <flux:field variant="inline">
                                    <flux:label>Status</flux:label>
                                    <flux:switch color="green" wire:model.live="active" />
                                    <flux:error name="status" />
                                </flux:field>
                            </div>
                            <div class="col-span-4">
                                <flux:input label="Remote Address (IP)" type="text" placeholder="Leave empty for dynamic IP" wire:model="remote_address" class="w-full" />
                            </div>
                            <div class="col-span-4">
                                <flux:input label="Comment" type="text" placeholder="comment" wire:model="comment" class="w-full" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <flux:button type="button" variant="danger" wire:click="$set('showEditForm', false)">Cancel</flux:button>
                            <flux:button type="submit" variant="primary" class="ml-2">Update</flux:button>
                        </div>
                    </form>
                </flux:modal>
            @endif
        </div>
    </div>    
</div>