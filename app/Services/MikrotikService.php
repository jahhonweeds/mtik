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

    public function __construct()
    {
        $this->config = config('mikrotik');
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->client = new Client([
                'host' => $this->config['host'],
                'user' => $this->config['username'],
                'pass' => $this->config['password'],
                'port' => $this->config['port'] ?? 8728,
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
}