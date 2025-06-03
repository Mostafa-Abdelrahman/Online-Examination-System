<?php
namespace Database\Factories;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;
class MajorFactory extends Factory
{
    protected $model = Major::class;
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Computer Science', 'Information Technology', 'Software Engineering', 'Computer Engineering',
            'Data Science', 'Artificial Intelligence', 'Cybersecurity', 'Network Engineering',
            'Information Systems', 'Web Development', 'Mobile Development', 'Cloud Computing',
            'Game Development', 'Robotics', 'Digital Marketing'
        ]);

        // Generate a unique code
        do {
            $code = strtoupper($this->faker->unique()->bothify('???'));
        } while (Major::where('code', $code)->exists());

        return [
            'name' => $name,
            'code' => $code,
            'description' => $this->faker->paragraph(),
            'is_active' => true,
        ];
    }
} 