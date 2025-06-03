<?php
namespace Database\Factories;
use App\Models\StudentAnswer;
use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
class StudentAnswerFactory extends Factory
{
    protected $model = StudentAnswer::class;
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        $question = Question::inRandomOrder()->first();
        return [
            'student_id' => $student ? $student->id : null,
            'question_id' => $question ? $question->id : null,
            'answer' => $this->faker->paragraph(),
            'is_correct' => $this->faker->boolean(),
            'score' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
} 