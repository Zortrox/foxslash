<?php

namespace Database\Factories;

use App\Models\ConnectedAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ConnectedAccount>
 */
class ConnectedAccountFactory extends Factory
{
    protected $model = ConnectedAccount::class;

    public function definition(): array
    {
        return [
            'provider' => 'steam',
            'provider_id' => $this->faker->numerify('7656119#########'),
            'nickname' => $this->faker->userName(),
            'token' => Str::random(432),
            'refresh_token' => Str::random(432),
        ];
    }
}
