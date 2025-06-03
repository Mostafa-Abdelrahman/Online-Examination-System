<?php
namespace Database\Factories;
use App\Models\Choice;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
class ChoiceFactory extends Factory
{
    protected $model = Choice::class;
    public function definition(): array
    {
        $question = Question::inRandomOrder()->first();
        return [
            'question_id' => $question ? $question->id : null,
            'text' => $this->faker->sentence(),
            'is_correct' => $this->faker->boolean(20),
        ];
    }
} 