<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@university.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gender' => 'male',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create default doctor users
        $doctors = [
            [
                'name' => 'Reda El Haddad',
                'email' => 'doctor@university.edu',
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'gender' => 'male',
                'status' => 'active',
                'bio' => 'Professor of Computer Science with 15 years of experience.',
                'phone' => '+1234567890',
            ],
            [
                'name' => 'Ahmed',
                'email' => 'student@university.edu',
                'password' => Hash::make('password'),
                'role' => 'student',
                'gender' => 'male',
                'status' => 'active',
                'bio' => 'Associate Professor specializing in Software Engineering.',
                'phone' => '+1234567891',
            ],
        ];

        foreach ($doctors as $doctor) {
            User::create($doctor);
        }

        // Create 10 students for each major
        $majors = \App\Models\Major::all();
        foreach ($majors as $major) {
            User::factory()
                ->count(10)
                ->state(['major_id' => $major->id])
                ->create();
        }

        // Create additional random users
        User::factory()->count(5)->state(['role' => 'doctor'])->create();
        User::factory()->count(20)->state(['role' => 'student'])->create();
        User::factory()->count(2)->state(['status' => 'inactive'])->create();
        User::factory()->count(1)->state(['status' => 'suspended'])->create();
    }
} 