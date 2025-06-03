<?php
namespace Database\Factories;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class NotificationFactory extends Factory
{
    protected $model = Notification::class;
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        return [
            'user_id' => $user ? $user->id : null,
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['info', 'success', 'warning', 'error']),
            'is_read' => $this->faker->boolean(30),
            'read_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'data' => json_encode([
                'action' => $this->faker->randomElement(['exam', 'course', 'grade', 'announcement']),
                'action_id' => $this->faker->numberBetween(1, 100),
            ]),
        ];
    }
} 