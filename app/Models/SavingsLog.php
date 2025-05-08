<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsLog extends Model
{
    protected $table = 'savings_log';

    protected $fillable = [
        'user_id',
        'amount',
        'description',
    ];
}
