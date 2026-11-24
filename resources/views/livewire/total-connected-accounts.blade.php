<div class="w-full h-full bg-gradient-to-br from-pink-400 via-pink-500 to-pink-600 rounded-xl shadow-xl p-4 md:p-8 text-white flex flex-col items-center justify-center hover:scale-105 transition-transform duration-300">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-white">Total Connected Accounts</h3>
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-10a2 2 0 01-2-2V8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2zM9 11h6m-3 3V8"></path>
        </svg>
    </div>
    <input disabled class="text-6xl font-extrabold bg-transparent border-none outline-none text-white text-center" wire:model.live="totalActiveConnections">
</div>
