<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// Продаваемая сущность, участвует в закупках и продажах.
// (товары/запчасти)
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id', 'name', 'product_description', 'price', 'image_url'];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = substr(Str::uuid()->toString(), 0, 15);
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
