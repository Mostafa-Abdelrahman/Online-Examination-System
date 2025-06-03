<?php
namespace Database\Factories;
use App\Models\ExamQuestion;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
class ExamQuestionFactory extends Factory
{
    protected $model = ExamQuestion::class;
    public function definition(): array
    {
        $exam = Exam::inRandomOrder()->first();
        $question = Question::inRandomOrder()->first();
        return [
            'exam_id' => $exam ? $exam->id : null,
            'question_id' => $question ? $question->id : null,
            'order' => $this->faker->numberBetween(1, 50),
            'points' => $this->faker->numberBetween(1, 10),
        ];
    }
} 