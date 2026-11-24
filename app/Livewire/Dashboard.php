<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\MikrotikService;

class Dashboard extends Component
{
    public $totalActiveConnections = 0;

    public function mount()
    {
        // Fetch the total active connections from MikroTik
        $mikrotik = new MikrotikService();
        $active = $mikrotik->getPppoeActive();
        $this->totalActiveConnections = is_array($active) ? count($active) : 0;
    }

    public function render()
    {
        return view('dashboard');
    }
}
