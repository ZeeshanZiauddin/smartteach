<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roll_number',
        'address',
        'phone_no',
        'whatsapp_no',
        'guardian_name',
        'guardian_phone',
        'home_number',
        'guardian_accupation',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}