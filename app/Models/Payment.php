<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable=[
        'id',
        'account_name',
        'account_number',
        'account_type',
        'fee',
        'created_at',
    ];
}
