<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Report::class;
    public function definition(): array
    {
        $user1 = User::inRandomOrder()->where('is_admin', 0)->first();
        $user2 = User::where('is_admin', 0)->where('id', '!=', $user1->id)->inRandomOrder()->first();

        return [
            'reported_id' => $user1->id,   // one user in the conversation
            'reported_by_id' =>  $user2->id,   // the other user
            'message' => $this->faker->text(),
            'is_processed' => 0,
        ];
    }
}
