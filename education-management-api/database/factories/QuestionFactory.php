<?php
namespace Database\Factories;
use App\Models\Question;
use App\Models\User;
use App\Models\QuestionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
class QuestionFactory extends Factory
{
    protected $model = Question::class;
    public function definition(): array
    {
        $category = QuestionCategory::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'content' => $this->faker->sentence(),
            'type' => $category ? $category->name : $this->faker->randomElement(['Multiple Choice', 'True/False', 'Short Answer', 'Programming', 'Problem Solving']),
            'difficulty' => $this->faker->numberBetween(1, 5),
            'points' => $this->faker->numberBetween(1, 10),
            'category_id' => $category ? $category->id : null,
            'created_by' => $doctor ? $doctor->id : null,
            'is_active' => true,
            'explanation' => $this->faker->paragraph(),
            'time_limit' => $this->faker->numberBetween(30, 300),
        ];
    }
} 