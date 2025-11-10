<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ['name', 'country', 'description'];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Генерация короткого UUID при создании
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
