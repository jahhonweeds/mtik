<!-- resources/views/livewire/pppoe-manager.blade.php -->

<div class=" mx-auto p-6">
    <div class=" rounded-lg shadow-md p-6">
        
        <h1 class="text-2xl font-bold mb-6">MikroTik PPPoE Account Management</h1>
         <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-red-500 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Total Accounts</h3>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-10a2 2 0 01-2-2V8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2zM9 11h6m-3 3V8"></path>
                    </svg>
                </div>
                <h1 class="font-bold text-white">{{ count($accountsRaw) }}</h1>
            </div>
            <div class="bg-pink-500 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Total Connected Accounts</h3>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-10a2 2 0 01-2-2V8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2zM9 11h6m-3 3V8"></path>
                    </svg>
                </div>
                <input disabled class="text-6xl" wire:model.live="totalActiveConnections">
             </div>
        </div>
        <!-- Connection Status -->
        <div class="mb-6 flex items-center">
            <span class="mr-2">Connection Status:</span>
            <span class="px-3 py-1 rounded text-xs {{ $connectionStatus === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $connectionStatus === 'connected' ? 'Connected' : 'Disconnected' }}
            </span>
            <button wire:click="checkConnection" class="ml-4 bg-gray-500 hover:bg-gray-700 text-white text-sm px-3 py-1 rounded">
                Check Connection
            </button>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-6">
            <flux:modal.trigger name="create-pppoe-account">

            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Account
            </button>
            </flux:modal.trigger>
            
            <button wire:click="syncWithMikrotik" 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Sync with MikroTik
            </button>
            
            <button wire:click="loadActiveConnections" 
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                Refresh Active Connections
            </button>

           
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
        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Profile</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr class="border-t ">
                            <td class="px-4 py-2">{{ $account['name'] ?? 'N/A'  }}</td>
                            <td class="px-4 py-2 text-center" >{{ $account['profile'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">
                                <flux:badge color="{{ $account['disabled'] ? 'green' : 'red'}}"> {{ $account['disabled'] ? 'Active' : 'Disabled' }}</flux:badge>
                               
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button wire:click="editAccount({{ $account['.id'] }})" 
                                        class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">
                                    Edit
                                </button>
                               
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border px-4 py-2 text-center">No PPPoE accounts found. 
                                <button wire:click="syncWithMikrotik" class="text-blue-500 underline">Sync with MikroTik</button>
                            </td>
                        </tr>
                    @endforelse
                    <!-- Pagination Links -->
                   
                </tbody>
                
            </table>
             <div class="mt-4">
                    {{ $accounts->links() }}
            </div>
        </div>

        <!-- Create Modal -->
        @if ($showCreateForm)
        <flux:modal name="create-pppoe-account" :show="true" focusable class="max-w-2xl">          
        <div class="bg-white rounded-lg p-6 w-full ">
                    <h2 class="text-xl font-bold mb-4">Create PPPoE Account</h2>
                    
                    <form wire:submit.prevent="createAccount">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Username *</label>
                            <input type="text" wire:model="username" 
                                   class="w-full px-3 py-2 border rounded">
                            @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Password *</label>
                            <input type="password" wire:model="password" 
                                   class="w-full px-3 py-2 border rounded">
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Profile *</label>
                            <select wire:model="profile" class="w-full px-3 py-2 border rounded">
                                <option value="default">default</option>
                                @if(is_array($profiles))
                                    @foreach($profiles as $profile)
                                        @if(isset($profile['name']))
                                            <option value="{{ $profile['name'] }}">{{ $profile['name'] }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('profile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Service *</label>
                            <select wire:model="service" class="w-full px-3 py-2 border rounded">
                                <option value="pppoe">PPPoE</option>
                                <option value="pptp">PPTP</option>
                                <option value="l2tp">L2TP</option>
                                <option value="sstp">SSTP</option>
                                <option value="ovpn">OpenVPN</option>
                            </select>
                            @error('service') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Remote Address (IP)</label>
                            <input type="text" wire:model="remote_address" 
                                   class="w-full px-3 py-2 border rounded" placeholder="Leave empty for dynamic IP">
                            @error('remote_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Comment</label>
                            <textarea wire:model="comment" 
                                      class="w-full px-3 py-2 border rounded" rows="2"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="active" class="mr-2" checked>
                                <span class="text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="$set('showCreateForm', false)" 
                                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
        </flux:modal>       
        @endif

        <!-- Edit Modal -->
        @if ($showEditForm)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md">
                    <h2 class="text-xl font-bold mb-4">Edit PPPoE Account</h2>
                    
                    <form wire:submit.prevent="updateAccount">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Username</label>
                            <input type="text" wire:model="username" disabled
                                   class="w-full px-3 py-2 border rounded bg-gray-100">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Password *</label>
                            <input type="password" wire:model="password" 
                                   class="w-full px-3 py-2 border rounded">
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Profile *</label>
                            <select wire:model="profile" class="w-full px-3 py-2 border rounded">
                                <option value="default">default</option>
                                @if(is_array($profiles))
                                    @foreach($profiles as $profile)
                                        @if(isset($profile['name']))
                                            <option value="{{ $profile['name'] }}">{{ $profile['name'] }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('profile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Service *</label>
                            <select wire:model="service" class="w-full px-3 py-2 border rounded">
                                <option value="pppoe">PPPoE</option>
                                <option value="pptp">PPTP</option>
                                <option value="l2tp">L2TP</option>
                                <option value="sstp">SSTP</option>
                                <option value="ovpn">OpenVPN</option>
                            </select>
                            @error('service') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Remote Address (IP)</label>
                            <input type="text" wire:model="remote_address" 
                                   class="w-full px-3 py-2 border rounded" placeholder="Leave empty for dynamic IP">
                            @error('remote_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Comment</label>
                            <textarea wire:model="comment" 
                                      class="w-full px-3 py-2 border rounded" rows="2"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="active" class="mr-2">
                                <span class="text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="$set('showEditForm', false)" 
                                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>