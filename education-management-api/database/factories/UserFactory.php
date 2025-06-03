<?php
namespace Database\Factories;
use App\Models\User;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserFactory extends Factory
{
    protected $model = User::class;
    public function definition(): array
    {
        static $index = 0;
        $gender = $this->faker->randomElement(['male', 'female', 'other']);
        $firstName = $this->faker->firstName($gender);
        $lastName = $this->faker->lastName();
        return [
            'name' => $firstName . ' ' . $lastName,
            'email' => Str::lower($firstName . '.' . $lastName . $index++ . '@university.edu'),
            'password' => Hash::make('password'),
            'role' => 'student',
            'gender' => $gender,
            'major_id' => Major::inRandomOrder()->first()->id ?? null,
            'status' => 'active',
            'last_login' => $this->faker->dateTimeThisMonth(),
            'bio' => $this->faker->paragraph(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'date_of_birth' => $this->faker->dateTimeBetween('-30 years', '-18 years'),
            'avatar_url' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
} 