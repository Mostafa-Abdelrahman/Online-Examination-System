<?php
namespace Database\Factories;
use App\Models\Course;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class CourseFactory extends Factory
{
    protected $model = Course::class;
    public function definition(): array
    {
        $major = Major::inRandomOrder()->first();
        $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
        return [
            'name' => $this->faker->words(3, true),
            'code' => strtoupper($this->faker->bothify('???###')),
            'description' => $this->faker->sentence(),
            'credits' => $this->faker->numberBetween(2, 5),
            'major_id' => $major ? $major->id : null,
            'created_by' => $doctor ? $doctor->id : null,
            'is_active' => true,
            'semester' => $this->faker->numberBetween(1, 8),
            'academic_year' => '2024-2025',
        ];
    }
} 