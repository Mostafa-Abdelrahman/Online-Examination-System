<?php
namespace Database\Factories;
use App\Models\StudentExam;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;
class StudentExamFactory extends Factory
{
    protected $model = StudentExam::class;
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        $exam = Exam::inRandomOrder()->first();
        return [
            'student_id' => $student ? $student->id : null,
            'exam_id' => $exam ? $exam->id : null,
            'start_time' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'end_time' => $this->faker->dateTimeBetween('now', '+2 hours'),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'submitted']),
            'score' => $this->faker->randomFloat(2, 0, 100),
            'attempts' => $this->faker->numberBetween(1, 3),
            'is_late' => $this->faker->boolean(20),
            'submitted_at' => $this->faker->optional()->dateTimeBetween('-1 hour', 'now'),
        ];
    }
} 