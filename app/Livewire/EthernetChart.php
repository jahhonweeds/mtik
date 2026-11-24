<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\MikrotikService;

class EthernetChart extends Component
{
    public $interface = 'ether2'; // default port
    public $traffic = [];
    public $labels = [];
    public $inData = [];
    public $outData = [];

    protected $listeners = ['updateChart' => 'fetchTraffic'];

    public function mount($interface = 'SFP-10G')
    {
        $this->interface = $interface;
        $this->fetchTraffic();
    }

    public function fetchTraffic()
    {
        $mikrotik = new MikrotikService();
        $stats = $mikrotik->getInterfaceStats($this->interface);
        // Example: $stats = ['in' => 123456, 'out' => 654321, 'timestamp' => now()];
        $this->labels[] = now()->format('H:i:s');
        $this->inData[] = $stats['in'] ?? 0;
        $this->outData[] = $stats['out'] ?? 0;
        $this->dispatch('updateChart');
        // Limit to last 20 points
        $this->labels = array_slice($this->labels, -20);
        $this->inData = array_slice($this->inData, -20);
        $this->outData = array_slice($this->outData, -20);
        $this->traffic = $stats;
    }

    public function render()
    {
        // Always pass non-empty arrays to Blade to avoid Chart.js errors
        $labels = !empty($this->labels) ? $this->labels : [now()->format('H:i:s')];
        $inData = !empty($this->inData) ? $this->inData : [0];
        $outData = !empty($this->outData) ? $this->outData : [0];
        $traffic = !empty($this->traffic) ? $this->traffic : [0];
        return view('livewire.ethernet-chart', [
            'labels' => $labels,
            'inData' => $inData,
            'outData' => $outData,
            'traffic' => $traffic,
        ]);
    }
}
