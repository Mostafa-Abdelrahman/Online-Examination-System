<?php
namespace Database\Factories;
use App\Models\Exam;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class ExamFactory extends Factory
{
    protected $model = Exam::class;
    public function definition(): array
    {
        $course = Course::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'course_id' => $course ? $course->id : null,
            'created_by' => $doctor ? $doctor->id : null,
            'exam_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'duration' => $this->faker->numberBetween(60, 180),
            'total_marks' => $this->faker->numberBetween(50, 100),
            'passing_marks' => $this->faker->numberBetween(30, 50),
            'instructions' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
        ];
    }
} 