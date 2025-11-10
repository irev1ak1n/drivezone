<?php

namespace Database\Factories\Orders;

use App\Models\Orders\Review;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        // случайный рейтинг
        $rating = $this->faker->randomFloat(1, 1, 5);
        $subjectType = $this->faker->boolean(40) ? 'Сервис' : 'Товар';

        // списки комментариев
        $comments = [
            'service_negative' => [
                'Обслуживание оставило неприятное впечатление.',
                'Сотрудник опоздал и сделал работу плохо.',
                'Качество ремонта ниже ожиданий.',
                'Сервис слишком затянул сроки и не извинился.',
            ],
            'service_positive' => [
                'Быстро и качественно отремонтировали машину!',
                'Мастер всё подробно объяснил и сделал отлично.',
                'Очень доволен обслуживанием, приеду ещё.',
                'Приятная атмосфера и профессиональные сотрудники.',
            ],
            'product_negative' => [
                'Запчасть оказалась бракованной.',
                'Купленный товар не соответствует описанию.',
                'Качество материала слабое, быстро сломалось.',
                'Цена слишком высокая для такого качества.',
            ],
            'product_positive' => [
                'Отличное качество товара, полностью доволен.',
                'Купил деталь — идеально подошла!',
                'Цена и качество соответствуют, рекомендую.',
                'Товар надёжный, пользуюсь без проблем.',
            ],
        ];

        // выбор комментария
        if ($subjectType === 'Сервис') {
            $comment = $rating <= 3
                ? $this->faker->randomElement($comments['service_negative'])
                : $this->faker->randomElement($comments['service_positive']);
        } else {
            $comment = $rating <= 3
                ? $this->faker->randomElement($comments['product_negative'])
                : $this->faker->randomElement($comments['product_positive']);
        }

        return [
            'client_id' => User::where('role', 'customer')
                ->inRandomOrder()->first()?->id,
            'employee_id' => User::where('role', 'employee')
                    ->inRandomOrder()->first()?->id ?? null,

            'subject_id' => Str::uuid(),
            'subject_type' => $subjectType,

            'rating' => $rating,
            'comment' => $comment,
            'status' => $this->faker->boolean(90) ? 'Доступен к чтению' : 'Удалён',
        ];
    }
}
