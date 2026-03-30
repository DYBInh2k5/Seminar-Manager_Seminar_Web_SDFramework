<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\Score;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Seminar Admin',
            'email' => 'admin@seminar.test',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $lecturer = User::factory()->create([
            'name' => 'James Nguyen',
            'email' => 'lecturer@seminar.test',
            'role' => 'lecturer',
            'password' => Hash::make('password'),
        ]);

        $studentA = User::factory()->create([
            'name' => 'Minh Tran',
            'email' => 'student1@seminar.test',
            'role' => 'student',
            'password' => Hash::make('password'),
        ]);

        $studentB = User::factory()->create([
            'name' => 'Bao Chau Le',
            'email' => 'student2@seminar.test',
            'role' => 'student',
            'password' => Hash::make('password'),
        ]);

        $topicA = Topic::create([
            'title' => 'Using Laravel Boost in seminar management',
            'description' => 'Build a seminar management system and demonstrate how Laravel Boost helps AI with code generation, debugging, and testing.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $topicB = Topic::create([
            'title' => 'Student research topic registration website',
            'description' => 'Allow students to register for topics, lecturers to approve requests, and presentations to be scheduled in one system.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $approved = Registration::create([
            'topic_id' => $topicA->id,
            'student_id' => $studentA->id,
            'status' => 'approved',
        ]);

        Registration::create([
            'topic_id' => $topicB->id,
            'student_id' => $studentB->id,
            'status' => 'pending',
        ]);

        Storage::disk('local')->put(
            'seeded/boost-seminar-report.pdf',
            "Laravel Boost Seminar Report\n\nThis is a demo report file created by the database seeder."
        );

        $approved->submission()->create([
            'original_name' => 'boost-seminar-report.pdf',
            'file_path' => 'seeded/boost-seminar-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now()->subDays(2),
            'note' => 'Initial project report and seminar outline.',
        ]);

        $approved->presentation()->create([
            'scheduled_at' => now()->addWeek(),
            'room' => 'Room A203',
        ]);

        Score::create([
            'registration_id' => $approved->id,
            'score' => 8.50,
            'comment' => 'Clear content and good demo. Security evaluation still needs improvement.',
        ]);
    }
}
