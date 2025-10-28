<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Message::class;

    public function definition(): array
    {
        $sender = User::inRandomOrder()->where('is_admin', 0)->first();
        $recevier = User::where('is_admin', 0)->where('id', '!=', $sender->id)->inRandomOrder()->first();


        return [
            'sender_id' => $sender->id,
            'receiver_id' => $recevier->id,
            'message' => $this->faker->sentence,
            'image_path' => null,
            'voice_note_path' => null,
            'video_path' => null,
            'is_read' => $this->faker->boolean(80),
        ];
    }
}
