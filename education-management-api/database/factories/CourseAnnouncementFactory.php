<?php
namespace Database\Factories;
use App\Models\CourseAnnouncement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class CourseAnnouncementFactory extends Factory
{
    protected $model = CourseAnnouncement::class;
    public function definition(): array
    {
        $course = Course::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'course_id' => $course ? $course->id : null,
            'created_by' => $doctor ? $doctor->id : null,
            'is_active' => true,
        ];
    }
} 