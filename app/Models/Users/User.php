<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|User where($column, $operator = null, $value = null, $boolean = 'and')
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Говорим Laravel, что id = UUID, а не автоинкремент
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Для автоматической генерирации UUID при создании модели
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

    /**
     * Поля, которые можно массово заполнять
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'photo_url',
        'gender',
        'email',
        'phone_number',
        'password',
        'role',
        'avatar_style',
        'birth_date',
    ];

    /**
     * Поля, скрытые при сериализации
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Преобразование типов
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function generateApiToken(): string
    {
        // создаём уникальный токен
        $token = Str::random(60);

        // сохраняем в БД
        $this->forceFill([
            'remember_token' => hash('sha256', $token),
        ])->save();

        // возвращаем исходный токен (до хеширования)
        return $token;
    }

    /** Форматируем даты без миллисекунд. */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /** Форматируем номер телефона при выводе. */
    public function getPhoneNumberAttribute($value): ?string
    {
        if (!$value) return null;

        $digits = preg_replace('/\D+/', '', $value);

        if (str_starts_with($digits, '8')) {
            $digits = '7' . substr($digits, 1);
        }

        if (strlen($digits) !== 11) {
            return '+' . $digits;
        }

        return sprintf('+%s (%s) %s-%s-%s',
            substr($digits, 0, 1),
            substr($digits, 1, 3),
            substr($digits, 4, 3),
            substr($digits, 7, 2),
            substr($digits, 9)
        );
    }

}
