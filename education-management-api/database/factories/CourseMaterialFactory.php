<?php
namespace Database\Factories;
use App\Models\CourseMaterial;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class CourseMaterialFactory extends Factory
{
    protected $model = CourseMaterial::class;
    public function definition(): array
    {
        $course = Course::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'file_url' => $this->faker->url(),
            'course_id' => $course ? $course->id : null,
            'created_by' => $doctor ? $doctor->id : null,
            'is_active' => true,
        ];
    }
} 