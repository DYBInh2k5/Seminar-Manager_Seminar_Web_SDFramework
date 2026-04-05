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
        $admin = User::factory()->create([
            'name' => 'Seminar Admin',
            'email' => 'admin@seminar.test',
            'role' => 'admin',
            'department' => 'Academic Affairs',
            'cohort' => 'Staff',
            'password' => Hash::make('password'),
        ]);

        $lecturerA = User::factory()->create([
            'name' => 'James Nguyen',
            'email' => 'lecturer@seminar.test',
            'role' => 'lecturer',
            'department' => 'Software Engineering',
            'cohort' => 'Faculty',
            'password' => Hash::make('password'),
        ]);

        $lecturerB = User::factory()->create([
            'name' => 'Thao Vo',
            'email' => 'lecturer2@seminar.test',
            'role' => 'lecturer',
            'department' => 'Information Systems',
            'cohort' => 'Faculty',
            'password' => Hash::make('password'),
        ]);

        $lecturerC = User::factory()->create([
            'name' => 'An Pham',
            'email' => 'lecturer3@seminar.test',
            'role' => 'lecturer',
            'department' => 'Artificial Intelligence',
            'cohort' => 'Faculty',
            'password' => Hash::make('password'),
        ]);

        $studentA = User::factory()->create([
            'name' => 'Minh Tran',
            'email' => 'student1@seminar.test',
            'role' => 'student',
            'department' => 'Software Engineering',
            'student_code' => 'SE230101',
            'cohort' => 'K2023',
            'password' => Hash::make('password'),
        ]);

        $studentB = User::factory()->create([
            'name' => 'Bao Chau Le',
            'email' => 'student2@seminar.test',
            'role' => 'student',
            'department' => 'Information Systems',
            'student_code' => 'IS230104',
            'cohort' => 'K2023',
            'password' => Hash::make('password'),
        ]);

        $studentC = User::factory()->create([
            'name' => 'Gia Huy Do',
            'email' => 'student3@seminar.test',
            'role' => 'student',
            'department' => 'Artificial Intelligence',
            'student_code' => 'AI220207',
            'cohort' => 'K2022',
            'password' => Hash::make('password'),
        ]);

        $studentD = User::factory()->create([
            'name' => 'Linh Nguyen',
            'email' => 'student4@seminar.test',
            'role' => 'student',
            'department' => 'Software Engineering',
            'student_code' => 'SE220118',
            'cohort' => 'K2022',
            'password' => Hash::make('password'),
        ]);

        $studentE = User::factory()->create([
            'name' => 'Khanh Phan',
            'email' => 'student5@seminar.test',
            'role' => 'student',
            'department' => 'Data Science',
            'student_code' => 'DS230052',
            'cohort' => 'K2023',
            'password' => Hash::make('password'),
        ]);

        $topicA = Topic::create([
            'title' => 'Using Laravel Boost in seminar management',
            'description' => 'Build a seminar management system and demonstrate how Laravel Boost helps AI with code generation, debugging, and testing.',
            'category' => 'AI-assisted Development',
            'capacity' => 3,
            'semester' => 'Fall 2026',
            'difficulty' => 'intermediate',
            'expected_outcomes' => 'Deliver a Laravel project, explain Boost usage, and present a working AI-assisted development workflow.',
            'lecturer_id' => $lecturerA->id,
            'status' => 'open',
        ]);

        $topicB = Topic::create([
            'title' => 'Student research topic registration website',
            'description' => 'Allow students to register for topics, lecturers to approve requests, and presentations to be scheduled in one system.',
            'category' => 'Academic Systems',
            'capacity' => 4,
            'semester' => 'Fall 2026',
            'difficulty' => 'beginner',
            'expected_outcomes' => 'Model role-based workflows, form validation, and student-facing reporting in Laravel.',
            'lecturer_id' => $lecturerA->id,
            'status' => 'open',
        ]);

        $topicC = Topic::create([
            'title' => 'AI chatbot for course support and FAQ guidance',
            'description' => 'Explore how a role-aware AI assistant can support students and lecturers with project guidance and system usage.',
            'category' => 'Conversational AI',
            'capacity' => 2,
            'semester' => 'Fall 2026',
            'difficulty' => 'advanced',
            'expected_outcomes' => 'Design prompt strategies, persistent chat history, and safety controls for an educational assistant.',
            'lecturer_id' => $lecturerC->id,
            'status' => 'open',
        ]);

        $topicD = Topic::create([
            'title' => 'Analytics dashboard for seminar performance',
            'description' => 'Create a hybrid Laravel and React dashboard that turns seminar data into actionable academic insights.',
            'category' => 'Learning Analytics',
            'capacity' => 3,
            'semester' => 'Fall 2026',
            'difficulty' => 'intermediate',
            'expected_outcomes' => 'Present department trends, topic categories, approval rates, and lecturer workload in a polished dashboard.',
            'lecturer_id' => $lecturerB->id,
            'status' => 'open',
        ]);

        $topicE = Topic::create([
            'title' => 'Legacy seminar archive migration',
            'description' => 'Study how older seminar records can be migrated into a modern Laravel portal with improved search and governance.',
            'category' => 'Data Migration',
            'capacity' => 2,
            'semester' => 'Spring 2026',
            'difficulty' => 'advanced',
            'expected_outcomes' => 'Evaluate migration constraints, audit trail requirements, and searchable archive design.',
            'lecturer_id' => $lecturerB->id,
            'status' => 'closed',
        ]);

        $approvedA = Registration::create([
            'topic_id' => $topicA->id,
            'student_id' => $studentA->id,
            'status' => 'approved',
        ]);

        $approvedB = Registration::create([
            'topic_id' => $topicD->id,
            'student_id' => $studentB->id,
            'status' => 'approved',
        ]);

        $pendingA = Registration::create([
            'topic_id' => $topicB->id,
            'student_id' => $studentC->id,
            'status' => 'pending',
        ]);

        $pendingB = Registration::create([
            'topic_id' => $topicC->id,
            'student_id' => $studentD->id,
            'status' => 'pending',
        ]);

        $rejected = Registration::create([
            'topic_id' => $topicE->id,
            'student_id' => $studentE->id,
            'status' => 'rejected',
        ]);

        $extraApproved = Registration::create([
            'topic_id' => $topicB->id,
            'student_id' => $studentB->id,
            'status' => 'approved',
        ]);

        Storage::disk('local')->put(
            'seeded/boost-seminar-report.pdf',
            "Laravel Boost Seminar Report\n\nThis is a demo report file created by the database seeder."
        );

        Storage::disk('local')->put(
            'seeded/analytics-dashboard-report.pdf',
            "Seminar Analytics Dashboard Report\n\nDemo data for analytics and reporting flow."
        );

        $approvedA->submission()->create([
            'original_name' => 'boost-seminar-report.pdf',
            'file_path' => 'seeded/boost-seminar-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now()->subDays(3),
            'note' => 'Initial project report and seminar outline.',
            'review_status' => 'accepted',
            'review_note' => 'Strong structure and solid implementation plan.',
            'reviewed_by' => $lecturerA->id,
            'reviewed_at' => now()->subDays(2),
            'revision_number' => 1,
        ]);

        $approvedB->submission()->create([
            'original_name' => 'analytics-dashboard-report.pdf',
            'file_path' => 'seeded/analytics-dashboard-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now()->subDays(1),
            'note' => 'First analytics storyboard for lecturer review.',
            'review_status' => 'changes_requested',
            'review_note' => 'Add department-level comparison and show open seminar capacity.',
            'reviewed_by' => $lecturerB->id,
            'reviewed_at' => now()->subHours(12),
            'revision_number' => 1,
        ]);

        $extraApproved->submission()->create([
            'original_name' => 'registration-portal-draft.pdf',
            'file_path' => 'seeded/boost-seminar-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now()->subHours(8),
            'note' => 'Updated portal wireframes and approval flow notes.',
            'review_status' => 'submitted',
            'revision_number' => 2,
        ]);

        $approvedA->presentation()->create([
            'scheduled_at' => now()->addWeek(),
            'room' => 'Room A203',
        ]);

        $approvedB->presentation()->create([
            'scheduled_at' => now()->addDays(10),
            'room' => 'Innovation Lab B102',
        ]);

        Score::create([
            'registration_id' => $approvedA->id,
            'score' => 8.50,
            'comment' => 'Clear content and good demo. Security evaluation still needs improvement.',
        ]);

        Score::create([
            'registration_id' => $approvedB->id,
            'score' => 9.10,
            'comment' => 'Very strong dashboard storytelling and clean analytics presentation.',
        ]);
    }
}
