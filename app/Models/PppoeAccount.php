<?php
// app/Models/PppoeAccount.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppoeAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'username',
        'password',
        'service',
        'profile',
        'remote_address',
        'comment',
        'active',
        'mikrotik_id',
        'bill_amount',
        'bill_due_date',
    ];
       

    protected $casts = [
        'active' => 'boolean',
    ];
}