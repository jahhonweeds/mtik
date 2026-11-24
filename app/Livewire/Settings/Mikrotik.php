<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use App\Services\MikrotikService;
use Livewire\Component;

class Mikrotik extends Component
{
    public string $host = '';
    public int $port = 8728;
    public string $username = '';
    public string $password = '';
    public int $timeout = 10;
    public string $connectionStatus = 'Not tested';

    public function mount(): void
    {
        $this->host = (string) Setting::getValue('mikrotik.host', config('mikrotik.host'));
        $this->port = (int) Setting::getValue('mikrotik.port', config('mikrotik.port', 8728));
        $this->username = (string) Setting::getValue('mikrotik.username', config('mikrotik.username'));
        $this->password = (string) Setting::getValue('mikrotik.password', config('mikrotik.password', ''));
        $this->timeout = (int) Setting::getValue('mikrotik.timeout', config('mikrotik.timeout', 10));
    }

    public function save(): void
    {
        $validated = $this->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'timeout' => ['required', 'integer', 'between:1,300'],
        ]);

        foreach ([
            'mikrotik.host' => $validated['host'],
            'mikrotik.port' => $validated['port'],
            'mikrotik.username' => $validated['username'],
            'mikrotik.timeout' => $validated['timeout'],
        ] as $key => $value) {
            Setting::setValue($key, $value);
        }

        if ($this->password !== '') {
            Setting::setValue('mikrotik.password', $validated['password'] ?? '');
        }

        config([
            'mikrotik.host' => $validated['host'],
            'mikrotik.port' => $validated['port'],
            'mikrotik.username' => $validated['username'],
            'mikrotik.password' => $this->password,
            'mikrotik.timeout' => $validated['timeout'],
        ]);

        session()->flash('message', 'MikroTik settings saved successfully.');
    }

    public function testConnection(): void
    {
        $this->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'username' => ['required', 'string', 'max:255'],
            'timeout' => ['required', 'integer', 'between:1,300'],
        ]);

        try {
            $mikrotik = new MikrotikService([
                'host' => $this->host,
                'port' => $this->port,
                'username' => $this->username,
                'password' => $this->password,
                'timeout' => $this->timeout,
            ]);
            $this->connectionStatus = $mikrotik->testConnection() ? 'Connected' : 'Disconnected';
        } catch (\Throwable $e) {
            $this->connectionStatus = 'Disconnected';
            session()->flash('error', 'MikroTik connection failed: ' . $e->getMessage());
        }
    }
}
