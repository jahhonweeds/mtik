<?php
// app/Http/Livewire/BillingManager.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PppoeAccount;
use Illuminate\Support\Carbon;

class BillingManager extends Component
{
    public $accounts;
    public $selectedAccountId;
    public $bills;
    public $payments;
    public $amount;
    public $due_date;
    public $bill_id;
    public $payment_amount;

    public function mount()
    {
        $this->accounts = PppoeAccount::orderBy('username')->get();
        $this->bills = collect();
        $this->payments = collect();
    }

    public function loadBills($accountId)
    {
        $this->selectedAccountId = $accountId;
        $this->bills = Bill::where('pppoe_account_id', $accountId)->orderByDesc('due_date')->get();
        $this->payments = collect();
    }

    public function loadPayments($billId)
    {
        $this->bill_id = $billId;
        $this->payments = Payment::where('bill_id', $billId)->orderByDesc('paid_at')->get();
    }

    public function createBill()
    {
        $this->validate([
            'selectedAccountId' => 'required|exists:pppoe_accounts,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);
        Bill::create([
            'pppoe_account_id' => $this->selectedAccountId,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'status' => 'unpaid',
        ]);
        $this->loadBills($this->selectedAccountId);
        $this->amount = null;
        $this->due_date = null;
        session()->flash('message', 'Bill created successfully!');
    }

    public function recordPayment()
    {
        $this->validate([
            'bill_id' => 'required|exists:bills,id',
            'payment_amount' => 'required|numeric|min:0',
        ]);
        $bill = Bill::findOrFail($this->bill_id);
        Payment::create([
            'bill_id' => $bill->id,
            'amount' => $this->payment_amount,
            'paid_at' => now(),
        ]);
        // Mark bill as paid if total payments >= bill amount
        $totalPaid = Payment::where('bill_id', $bill->id)->sum('amount');
        if ($totalPaid >= $bill->amount) {
            $bill->status = 'paid';
            $bill->save();
        }
        $this->loadPayments($bill->id);
        session()->flash('message', 'Payment recorded!');
    }

    public function render()
    {
        return view('livewire.billing-manager', [
            'accounts' => $this->accounts,
            'bills' => $this->bills,
            'payments' => $this->payments,
            'selectedAccountId' => $this->selectedAccountId,
        ]);
    }
}
