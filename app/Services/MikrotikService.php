<?php
// app/Services/MikrotikService.php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use Exception;

class MikrotikService
{
    private $client;
    private $config;

    public function __construct(?array $config = null)
    {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = [
                'host' => \App\Models\Setting::getValue('mikrotik.host', config('mikrotik.host')),
                'port' => (int) \App\Models\Setting::getValue('mikrotik.port', config('mikrotik.port', 8728)),
                'username' => \App\Models\Setting::getValue('mikrotik.username', config('mikrotik.username')),
                'password' => \App\Models\Setting::getValue('mikrotik.password', config('mikrotik.password', '')),
                'timeout' => (int) \App\Models\Setting::getValue('mikrotik.timeout', config('mikrotik.timeout', 10)),
            ];
        }

        $this->connect();
    }

    public function getInterfaceStats($interface)
    {
        $query = new Query('/interface/monitor-traffic');
        $query->equal('interface', $interface);
        $query->equal('once', '');

        $result = $this->client->query($query)->read();
        // SFP stats: tx-power, rx-power (values may differ by device)
    $sfpQuery = new Query('/interface/ethernet/monitor');
    $sfpQuery->equal('.id', $interface);
    $sfpQuery->equal('once', '');
    $sfpResult = $this->client->query($sfpQuery)->read();
    // Try multiple possible keys for SFP Tx/Rx Power
    $txPower = $sfpResult[0]['sfp-tx-power'] ?? $sfpResult[0]['tx-power'] ?? $sfpResult[0]['sfp-tx-power-dbm'] ?? null;
    $rxPower = $sfpResult[0]['sfp-rx-power'] ?? $sfpResult[0]['rx-power'] ?? $sfpResult[0]['sfp-rx-power-dbm'] ?? null;
        if (!empty($result[0])) {
            return [
                'in' => isset($result[0]['rx-bits-per-second']) ? (int)$result[0]['rx-bits-per-second'] : 0,
                'out' => isset($result[0]['tx-bits-per-second']) ? (int)$result[0]['tx-bits-per-second'] : 0,
                'timestamp' => now()->toDateTimeString(),
                'sfp_tx_power' => $txPower,
                'sfp_rx_power' => $rxPower,
            ];
        }
        return [
            'in' => 0,
            'out' => 0,
            'timestamp' => now()->toDateTimeString(),
            'sfp_tx_power' => $txPower,
            'sfp_rx_power' => $rxPower,
        ];
    }
    private function connect()
    {
        try {
            $this->client = new Client([
                'host' => $this->config['host'],
                'user' => $this->config['username'],
                'pass' => $this->config['password'],
                'port' => $this->config['port'] ?? 8728,
                'timeout' => $this->config['timeout'] ?? 10,
            ]);
        } catch (Exception $e) {
            throw new Exception("MikroTik connection failed: " . $e->getMessage());
        }
    }

    public function createPppoeAccount($username, $password, $profile = 'default', $service = 'pppoe')
    {
        $query = new Query('/ppp/secret/add');
        $query->equal('name', $username);
        $query->equal('password', $password);
        $query->equal('profile', $profile);
        $query->equal('service', $service);

        $response = $this->client->query($query)->read();
        return $response[0]['ret'] ?? null;
    }

    public function updatePppoeAccount($id, $data)
    {
        $query = new Query('/ppp/secret/set');
        $query->equal('.id', $id);
        
        foreach ($data as $key => $value) {
            $query->equal($key, $value);
        }

        return $this->client->query($query)->read();
    }

    public function deletePppoeAccount($id)
    {
        $query = new Query('/ppp/secret/remove');
        $query->equal('.id', $id);

        return $this->client->query($query)->read();
    }

    public function enablePppoeAccount($id)
    {
        $query = new Query('/ppp/secret/enable');
        $query->equal('.id', $id);

        return $this->client->query($query)->read();
    }

    public function disablePppoeAccount($id)
    {
        $query = new Query('/ppp/secret/disable');
        $query->equal('.id', $id);

        return $this->client->query($query)->read();
    }

    public function getPppoeAccounts()
    {
        $query = new Query('/ppp/secret/getall');
        return $this->client->query('/ppp/secret/print')->read();
    }

    public function getPppoeActive()
    {
        $query = new Query('/ppp/active/print');
        return $this->client->query($query)->read();
    }

    public function getProfiles()
    {
        $query = new Query('/ppp/profile/print');
        return $this->client->query($query)->read();
    }

    public function testConnection()
    {
        try {
            $query = new Query('/system/resource/print');
            $this->client->query($query)->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function disconnectPppoeSessions($username)
    {
        $activeQuery = new Query('/ppp/active/print');
        $activeSessions = $this->client->query($activeQuery)->read();

        foreach ($activeSessions as $session) {
            if (($session['name'] ?? null) === $username && !empty($session['.id'])) {
                $removeQuery = new Query('/ppp/active/remove');
                $removeQuery->equal('.id', $session['.id']);
                $this->client->query($removeQuery)->read();
            }
        }

        return true;
    }
}