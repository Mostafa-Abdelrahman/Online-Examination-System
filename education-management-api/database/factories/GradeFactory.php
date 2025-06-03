<?php
namespace Database\Factories;
use App\Models\Grade;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;
class GradeFactory extends Factory
{
    protected $model = Grade::class;
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        $exam = Exam::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'student_id' => $student ? $student->id : null,
            'exam_id' => $exam ? $exam->id : null,
            'score' => $this->faker->randomFloat(2, 0, 100),
            'total_marks' => 100,
            'percentage' => $this->faker->randomFloat(2, 0, 100),
            'grade_letter' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'F']),
            'status' => $this->faker->randomElement(['pending', 'graded', 'rejected']),
            'graded_at' => $this->faker->dateTime(),
            'graded_by' => $doctor ? $doctor->id : null,
            'feedback' => $this->faker->paragraph(),
        ];
    }
} 