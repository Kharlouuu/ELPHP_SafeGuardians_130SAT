<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Expense extends Model
{
    use HasFactory; protected $fillable = [ 'user_id', 'name', 'amount', 'type', ]; 
    // Define the relationship to User public function user() { return $this->belongsTo(User::class); } }
}
