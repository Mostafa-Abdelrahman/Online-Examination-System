<?php
namespace Database\Factories;
use App\Models\ScheduleEvent;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class ScheduleEventFactory extends Factory
{
    protected $model = ScheduleEvent::class;
    public function definition(): array
    {
        $course = Course::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'start_time' => $this->faker->dateTime(),
            'end_time' => $this->faker->dateTime(),
            'course_id' => $course ? $course->id : null,
            'created_by' => $doctor ? $doctor->id : null,
            'is_active' => true,
        ];
    }
} 