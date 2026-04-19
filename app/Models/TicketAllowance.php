<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAllowance extends Model
{
    use HasFactory;
    protected $fillable = [
    'employee_id',
    'monthly_limit',
    'current_balance',
    'year',
    'month'
];
}
