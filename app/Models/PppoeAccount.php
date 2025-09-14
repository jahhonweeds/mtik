<?php
// app/Models/PppoeAccount.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppoeAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'service',
        'profile',
        'remote_address',
        'comment',
        'active',
        'mikrotik_id'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}