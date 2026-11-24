<?php
// app/Models/Bill.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    protected $fillable = [
        'pppoe_account_id',
        'amount',
        'due_date',
        'status', // 'paid', 'unpaid', 'overdue'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PppoeAccount::class, 'pppoe_account_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
