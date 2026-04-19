<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
    'employee_id',
    'amount_spent',
    'reference_code',
    'spent_at',
    'notes'
];
}
