<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = 'reports';
    protected $fillable = [
        'id',
        'program_name',
        'beneficiaries',
        'province',
        'city',
        'district',
        'distribution_date',
        'proof_file',
        'additional_notes',
        'status',
        'rejection_reason',
    ];
    protected $casts = [
        'distribution_date' => 'date',
        'beneficiaries' => 'integer',
        'status' => 'string',
    ];
}
