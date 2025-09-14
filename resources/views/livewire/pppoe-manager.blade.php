<!-- resources/views/livewire/pppoe-manager.blade.php -->

<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">MikroTik PPPoE Account Management</h1>

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
            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Account
            </button>
            
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
        @if(isset($activeConnections) && count($activeConnections) > 0)
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
        @endif

        <!-- Accounts Table -->
        <h2 class="text-xl font-semibold mb-3">PPPoE Accounts {{ count($accountsRaw) }} </h2>
        <div class="overflow-x-auto">
            <table class="w-full bg-white">
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
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $account['name'] ?? 'N/A'  }}</td>
                            <td class="px-4 py-2">{{ $account['profile'] ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs {{ $account['disabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $account['disabled'] ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <button wire:click="editAccount({{ $account['.id'] }})" 
                                        class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">
                                    Edit
                                </button>
                                <button wire:click="toggleStatus({{ $account['.id'] }})" 
                                        class="bg-{{ $account['disabled'] ? 'red' : 'green' }}-500 hover:bg-{{ $account['disabled'] ? 'red' : 'green' }}-700 text-white px-3 py-1 rounded text-sm ml-2">
                                    {{ $account['disabled'] ? 'Disable' : 'Enable' }}
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
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md">
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
            </div>
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