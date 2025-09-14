<?php
// app/Http/Livewire/PppoeManager.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PppoeAccount;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class PppoeManager extends Component
{
    public $perPage = 10; // Number of accounts per page
    public $accountsRaw = [];

    public $activeConnections;
    public $profiles;
    public $showCreateForm = false;
    public $showEditForm = false;
    public $editingAccount = null;
    public $connectionStatus = 'disconnected';
    public $syncStatus = '';

    public $username;
    public $password;
    public $profile = 'default';
    public $service = 'pppoe';
    public $remote_address;
    public $comment;
    public $active = true;

    protected $rules = [
        'username' => 'required|min:3|max:255',
        'password' => 'required|min:6|max:255',
        'profile' => 'required|max:255',
        'service' => 'required|max:255',
        'remote_address' => 'nullable|ip',
        'comment' => 'nullable|max:500',
    ];

    public function mount()
    {
        $this->checkConnection();
        $this->loadAccounts();
        $this->loadProfiles();
    }

    public function checkConnection()
    {
        try {
            $mikrotik = new MikrotikService();
            $this->connectionStatus = $mikrotik->testConnection() ? 'connected' : 'disconnected';
        } catch (\Exception $e) {
            $this->connectionStatus = 'disconnected';
        }
    }

    public function loadAccounts()
    {
        $mikrotik = new MikrotikService();
        
        $this->accountsRaw = $mikrotik->getPppoeAccounts();
   
    }

    public function loadActiveConnections()
    {
        try {
            $mikrotik = new MikrotikService();
            $this->activeConnections = $mikrotik->getPppoeActive();
        } catch (\Exception $e) {
            $this->activeConnections = [];
            session()->flash('error', 'Failed to load active connections: ' . $e->getMessage());
        }
    }

    public function loadProfiles()
    {
        try {
            $mikrotik = new MikrotikService();
            $this->profiles = $mikrotik->getProfiles();
        } catch (\Exception $e) {
            $this->profiles = [];
            session()->flash('error', 'Failed to load profiles: ' . $e->getMessage());
        }
    }

    public function syncWithMikrotik()
    {
        try {
            $mikrotik = new MikrotikService();
            $mikrotikAccounts = $mikrotik->getPppoeAccounts();
            
            foreach ($mikrotikAccounts as $account) {
                $localAccount = PppoeAccount::where('username', $account['name'])->first();
                
                if (!$localAccount) {
                    PppoeAccount::create([
                        'username' => $account['name'],
                        'password' => $account['password'] ?? '',
                        'service' => $account['service'] ?? 'pppoe',
                        'profile' => $account['profile'] ?? 'default',
                        'remote_address' => $account['remote-address'] ?? null,
                        'comment' => $account['comment'] ?? null,
                        'active' => !isset($account['disabled']),
                        'mikrotik_id' => $account['.id']
                    ]);
                } else if ($localAccount->mikrotik_id !== $account['.id']) {
                    $localAccount->update(['mikrotik_id' => $account['.id']]);
                }
            }
            
            $this->loadAccounts();
            $this->syncStatus = "Synced successfully. Found " . count($mikrotikAccounts) . " accounts on MikroTik.";
            session()->flash('message', $this->syncStatus);
        } catch (\Exception $e) {
            $this->syncStatus = 'Sync failed: ' . $e->getMessage();
            session()->flash('error', $this->syncStatus);
        }
    }

    public function createAccount()
    {
        $this->validate();

        try {
            $mikrotik = new MikrotikService();
            
            // Create on MikroTik
            $mikrotikId = $mikrotik->createPppoeAccount(
                $this->username,
                $this->password,
                $this->profile,
                $this->service
            );

            if (!$mikrotikId) {
                throw new Exception('Failed to create account on MikroTik');
            }

            // Create local record
            PppoeAccount::create([
                'username' => $this->username,
                'password' => $this->password,
                'profile' => $this->profile,
                'service' => $this->service,
                'remote_address' => $this->remote_address,
                'comment' => $this->comment,
                'active' => $this->active,
                'mikrotik_id' => $mikrotikId
            ]);

            $this->resetForm();
            $this->showCreateForm = false;
            $this->loadAccounts();
            
            session()->flash('message', 'PPPoE account created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create account: ' . $e->getMessage());
            Log::error('PPPoE creation error: ' . $e->getMessage());
        }
    }

    public function editAccount($accountId)
    {
        
        $account = PppoeAccount::findOrFail($accountId);
        $this->editingAccount = $account;
        
        $this->username = $account->username;
        $this->password = $account->password;
        $this->profile = $account->profile;
        $this->service = $account->service;
        $this->remote_address = $account->remote_address;
        $this->comment = $account->comment;
        $this->active = $account->active;
        
        $this->showEditForm = true;
    }

    public function updateAccount()
    {
        $this->validate();

        try {
            $mikrotik = new MikrotikService();
            
            // Update on MikroTik
            $updateData = [
                'password' => $this->password,
                'profile' => $this->profile,
                'service' => $this->service,
            ];
            
            if ($this->remote_address) {
                $updateData['remote-address'] = $this->remote_address;
            }
            
            if ($this->comment) {
                $updateData['comment'] = $this->comment;
            }
            
            if ($this->active) {
                $updateData['disabled'] = 'no';
            } else {
                $updateData['disabled'] = 'yes';
            }
            
            $mikrotik->updatePppoeAccount($this->editingAccount->mikrotik_id, $updateData);

            // Update local database
            $this->editingAccount->update([
                'password' => $this->password,
                'profile' => $this->profile,
                'service' => $this->service,
                'remote_address' => $this->remote_address,
                'comment' => $this->comment,
                'active' => $this->active,
            ]);

            $this->resetForm();
            $this->showEditForm = false;
            $this->loadAccounts();
            
            session()->flash('message', 'PPPoE account updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update account: ' . $e->getMessage());
            Log::error('PPPoE update error: ' . $e->getMessage());
        }
    }

    public function deleteAccount($accountId)
    {
        try {
            $account = PppoeAccount::findOrFail($accountId);
            
            $mikrotik = new MikrotikService();
            $mikrotik->deletePppoeAccount($account->mikrotik_id);
            
            $account->delete();
            $this->loadAccounts();
            
            session()->flash('message', 'PPPoE account deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete account: ' . $e->getMessage());
            Log::error('PPPoE deletion error: ' . $e->getMessage());
        }
    }

    public function toggleStatus($accountId)
    {
        try {
            $account = PppoeAccount::findOrFail($accountId);
            $mikrotik = new MikrotikService();
            
            if ($account->active) {
                $mikrotik->disablePppoeAccount($account->mikrotik_id);
                $account->active = false;
            } else {
                $mikrotik->enablePppoeAccount($account->mikrotik_id);
                $account->active = true;
            }
            
            $account->save();
            $this->loadAccounts();
            
            session()->flash('message', 'Account status updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update status: ' . $e->getMessage());
            Log::error('PPPoE status toggle error: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'username', 'password', 'profile', 'service', 'remote_address', 'comment', 'active'
        ]);
    }

    public function render()
{
    $collection = collect($this->accountsRaw);

    $perPage = $this->perPage;
    $currentPage = request()->get('page', 1); // ✅ Fetch page from query string

    $currentItems = $collection
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    $paginator = new LengthAwarePaginator(
        $currentItems,
        $collection->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('livewire.pppoe-manager', [
        'accounts' => $paginator
    ]);
}
}