<?php
namespace Database\Factories;
use App\Models\QuestionCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class QuestionCategoryFactory extends Factory
{
    protected $model = QuestionCategory::class;

    public function definition(): array
    {
        $categories = [
            'Theory',
            'Practical',
            'Research',
            'Case Study',
            'Analysis',
            'Design',
            'Implementation',
            'Testing',
            'Documentation',
            'Review',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($categories),
            'description' => $this->faker->sentence(),
            'created_by' => User::where('role', 'doctor')->inRandomOrder()->first()->id,
        ];
    }
} 