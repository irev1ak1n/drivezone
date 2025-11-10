<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'work_shift',
        'salary',
        'hire_date',
        'is_active',
        'skills',
        'rating',
        'experience_years',
        'supervisor_id',
        'notes',
        'note_added_at',
    ];

    // Связь: сотрудник относится к пользователю
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь: сотрудник может иметь руководителя (supervisor)
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    protected static function boot()
    {
        parent::boot();
    }

}
