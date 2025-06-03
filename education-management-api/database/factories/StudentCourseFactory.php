<?php
namespace Database\Factories;
use App\Models\StudentCourse;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
class StudentCourseFactory extends Factory
{
    protected $model = StudentCourse::class;
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first();
        $course = Course::inRandomOrder()->first();
        return [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => $this->faker->randomElement(['enrolled', 'completed', 'dropped']),
            'enrollment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'completion_date' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'grade' => $this->faker->optional()->randomElement(['A', 'B', 'C', 'D', 'F']),
        ];
    }
} 