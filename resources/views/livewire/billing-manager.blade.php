<!-- resources/views/livewire/billing-manager.blade.php -->

<div class="min-h-screen bg-gradient-to-br from-blue-100 via-purple-100 to-pink-200 py-6 md:py-10 font-sans">
    <div class="max-w-7xl mx-auto rounded-3xl shadow-2xl p-4 md:p-5 bg-white/70 backdrop-blur-lg border border-blue-200">
        <h1 class="text-2xl md:text-4xl font-extrabold mb-6 md:mb-8 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-pink-400 drop-shadow-lg text-center">Billing Manager</h1>
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">PPPoE Accounts</h2>
            <div class="overflow-x-auto w-full">
                <table class="min-w-[600px] w-full bg-white/90 dark:bg-gray-900 rounded-2xl shadow-xl border border-blue-100 text-xs md:text-base">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-100 to-purple-100 text-blue-900">
                            <th class="px-6 py-4 text-lg font-semibold">Username</th>
                            <th class="px-6 py-4 text-lg font-semibold">Profile</th>
                            <th class="px-6 py-4 text-lg font-semibold">Status</th>
                            <th class="px-6 py-4 text-lg font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                        <tr class="border-t border-blue-100 hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-blue-900 font-medium">{{ $account->username }}</td>
                            <td class="px-6 py-4 text-center text-purple-700 font-semibold">{{ $account->profile }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $account->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $account->active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:modal.trigger name="billing-details">
                                    <button wire:click="loadBills({{ $account->id }})" class="bg-gradient-to-r from-purple-400 to-pink-400 hover:from-purple-500 hover:to-pink-500 text-white px-5 py-2 rounded-xl shadow-md text-sm font-bold transition-all duration-200">View Bills</button>
                                </flux:modal.trigger>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if ($selectedAccountId)
        <flux:modal name="billing-details" class="md:w-lg">
            <div class="mb-4">
                <flux:heading size="lg">Billing Details</flux:heading>
                <flux:text class="mt-2">Manage bills and payments for this account.</flux:text>
            </div>
            <form wire:submit.prevent="createBill">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-1">Amount</label>
                        <input type="number" wire:model="amount" class="w-full border-2 border-blue-300 rounded-xl px-4 py-2" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="block mb-1">Due Date</label>
                        <input type="date" wire:model="due_date" class="w-full border-2 border-blue-300 rounded-xl px-4 py-2">
                    </div>
                </div>
                <div class="flex justify-end mb-4">
                    <flux:button type="submit" variant="primary">Create Bill</flux:button>
                </div>
            </form>
            @if ($bills->count())
            <div class="overflow-x-auto w-full mb-4">
                <table class="min-w-[400px] w-full bg-white/90 dark:bg-gray-900 rounded-2xl shadow-xl border border-blue-100 text-xs md:text-base">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-100 to-purple-100 text-blue-900">
                            <th class="px-6 py-4 text-lg font-semibold">Amount</th>
                            <th class="px-6 py-4 text-lg font-semibold">Due Date</th>
                            <th class="px-6 py-4 text-lg font-semibold">Status</th>
                            <th class="px-6 py-4 text-lg font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills as $bill)
                        <tr class="border-t border-blue-100 hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-blue-900 font-medium">{{ number_format($bill->amount, 2) }}</td>
                            <td class="px-6 py-4 text-center text-purple-700 font-semibold">{{ $bill->due_date }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $bill->status == 'paid' ? 'bg-green-100 text-green-800' : ($bill->status == 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($bill->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="loadPayments({{ $bill->id }})" class="bg-gradient-to-r from-purple-400 to-pink-400 hover:from-purple-500 hover:to-pink-500 text-white px-5 py-2 rounded-xl shadow-md text-sm font-bold transition-all duration-200">View Payments</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @if ($payments->count())
            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2">Payments</h3>
                <table class="min-w-[400px] w-full bg-white/90 dark:bg-gray-900 rounded-2xl shadow-xl border border-purple-100 text-xs md:text-base mb-4">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-900">
                            <th class="px-6 py-4 text-lg font-semibold">Amount</th>
                            <th class="px-6 py-4 text-lg font-semibold">Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                        <tr class="border-t border-purple-100 hover:bg-purple-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-purple-900 font-medium">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-center">{{ $payment->paid_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block mb-1">Payment Amount</label>
                        <input type="number" wire:model="payment_amount" class="w-full border-2 border-purple-300 rounded-xl px-4 py-2" min="0" step="0.01">
                    </div>
                    <flux:button wire:click="recordPayment" class="bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white font-bold py-2 px-6 rounded-xl shadow-md transition-all duration-200">Record Payment</flux:button>
                </div>
            </div>
            @endif
        </flux:modal>
        @endif
        @if ($bills->count())
        <div class="overflow-x-auto w-full mb-6">
            <table class="min-w-[600px] w-full bg-white/90 dark:bg-gray-900 rounded-2xl shadow-xl border border-blue-100 text-xs md:text-base">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-100 to-purple-100 text-blue-900">
                        <th class="px-6 py-4 text-lg font-semibold">Amount</th>
                        <th class="px-6 py-4 text-lg font-semibold">Due Date</th>
                        <th class="px-6 py-4 text-lg font-semibold">Status</th>
                        <th class="px-6 py-4 text-lg font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bills as $bill)
                    <tr class="border-t border-blue-100 hover:bg-blue-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-blue-900 font-medium">{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-6 py-4 text-center text-purple-700 font-semibold">{{ $bill->due_date }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded text-xs {{ $bill->status == 'paid' ? 'bg-green-100 text-green-800' : ($bill->status == 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="loadPayments({{ $bill->id }})" class="bg-gradient-to-r from-purple-400 to-pink-400 hover:from-purple-500 hover:to-pink-500 text-white px-5 py-2 rounded-xl shadow-md text-sm font-bold transition-all duration-200">View Payments</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @if ($payments->count())
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Payments</h3>
            <table class="min-w-[400px] w-full bg-white/90 dark:bg-gray-900 rounded-2xl shadow-xl border border-purple-100 text-xs md:text-base mb-4">
                <thead>
                    <tr class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-900">
                        <th class="px-6 py-4 text-lg font-semibold">Amount</th>
                        <th class="px-6 py-4 text-lg font-semibold">Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                    <tr class="border-t border-purple-100 hover:bg-purple-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-purple-900 font-medium">{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">{{ $payment->paid_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block mb-1">Payment Amount</label>
                    <input type="number" wire:model="payment_amount" class="w-full border-2 border-purple-300 rounded-xl px-4 py-2" min="0" step="0.01">
                </div>
                <button wire:click="recordPayment" class="bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white font-bold py-2 px-6 rounded-xl shadow-md transition-all duration-200">Record Payment</button>
            </div>
        </div>
        @endif
        @if (session()->has('message'))
            <div class="fixed top-6 right-6 z-50 flex flex-col gap-2">
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
                    {{ session('message') }}
                </div>
            </div>
        @endif
    </div>
</div>
