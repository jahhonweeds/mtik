<?php
// app/Console/Commands/DisableUnpaidAccounts.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Services\MikrotikService;
use Carbon\Carbon;

class DisableUnpaidAccounts extends Command
{
    protected $signature = 'billing:disable-unpaid';
    protected $description = 'Disable PPPoE accounts with overdue bills on Mikrotik';

    public function handle()
    {
        $overdueBills = Bill::where('status', 'overdue')->with('account')->get();
        $mikrotik = new MikrotikService();
        $count = 0;
        foreach ($overdueBills as $bill) {
            $account = $bill->account;
            if ($account && $account->active) {
                $mikrotik->disablePppoeAccount($account->mikrotik_id);
                $account->active = false;
                $account->save();
                $count++;
            }
        }
        $this->info("Disabled $count overdue accounts.");
    }
}
