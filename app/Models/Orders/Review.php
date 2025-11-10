<?php

namespace App\Models\Orders;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'employee_id',
        'subject_id',
        'subject_type',
        'rating',
        'comment',
        'is_published'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Автоматическая генерация укороченного UUID при создании модели
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = substr(Str::uuid()->toString(), 0, 15);
            }
        });
    }

    // Клиент (User с ролью customer)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Сотрудник (User с ролью employee)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Полиморфная связь
    public function subject()
    {
        return $this->morphTo();
    }
}
