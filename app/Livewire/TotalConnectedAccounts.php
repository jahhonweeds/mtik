<?php
namespace App\Livewire;

use Livewire\Component;

class TotalConnectedAccounts extends Component
{
    public $totalActiveConnections = 0;

    public function mount($totalActiveConnections = 0)
    {
        $this->totalActiveConnections = $totalActiveConnections;
    }

    public function render()
    {
        return view('livewire.total-connected-accounts');
    }
}
