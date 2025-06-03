<?php
namespace Database\Seeders;
use App\Models\Course;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;
class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get a doctor user to be the creator
        $doctor = User::where('role', 'doctor')->first();

        $courses = [
            [
                'name' => 'Introduction to Programming',
                'code' => 'CS101',
                'description' => 'Basic programming concepts and problem-solving techniques.',
                'credits' => 3,
                'semester' => 1,
                'major_id' => Major::where('code', 'CS')->first()->id,
                'created_by' => $doctor->id,
                'is_active' => true,
                'academic_year' => '2024-2025',
            ],
            [
                'name' => 'Data Structures and Algorithms',
                'code' => 'CS201',
                'description' => 'Study of fundamental data structures and algorithm design.',
                'credits' => 4,
                'semester' => 2,
                'major_id' => Major::where('code', 'CS')->first()->id,
                'created_by' => $doctor->id,
                'is_active' => true,
                'academic_year' => '2024-2025',
            ],
            [
                'name' => 'Database Systems',
                'code' => 'IT301',
                'description' => 'Design and implementation of database systems.',
                'credits' => 3,
                'semester' => 3,
                'major_id' => Major::where('code', 'IT')->first()->id,
                'created_by' => $doctor->id,
                'is_active' => true,
                'academic_year' => '2024-2025',
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'SE401',
                'description' => 'Principles and practices of software development.',
                'credits' => 4,
                'semester' => 4,
                'major_id' => Major::where('code', 'SE')->first()->id,
                'created_by' => $doctor->id,
                'is_active' => true,
                'academic_year' => '2024-2025',
            ],
            [
                'name' => 'Machine Learning',
                'code' => 'AI501',
                'description' => 'Introduction to machine learning algorithms and applications.',
                'credits' => 4,
                'semester' => 5,
                'major_id' => Major::where('code', 'AI')->first()->id,
                'created_by' => $doctor->id,
                'is_active' => true,
                'academic_year' => '2024-2025',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        // Create additional random courses
        Course::factory()->count(15)->create([
            'created_by' => $doctor->id,
            'academic_year' => '2024-2025',
        ]);
    }
} 