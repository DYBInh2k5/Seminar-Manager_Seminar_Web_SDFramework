<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/activity', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai-chat.index');
    Route::post('/ai-chat', [AiChatController::class, 'store'])->name('ai-chat.store');
    Route::post('/ai-chat/conversations', [AiChatController::class, 'createConversation'])->name('ai-chat.conversations.store');
    Route::get('/ai-chat/conversations/{conversation}', [AiChatController::class, 'showConversation'])->name('ai-chat.conversations.show');

    Route::resource('topics', TopicController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('topics', TopicController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('role:lecturer,admin');
    Route::get('/topics/{topic}/summary', [ExportController::class, 'topicSummary'])
        ->middleware('role:lecturer,admin')
        ->name('topics.summary');

    Route::resource('users', UserManagementController::class)
        ->except(['show'])
        ->middleware('role:admin');

    Route::post('/topics/{topic}/register', [RegistrationController::class, 'store'])
        ->middleware('role:student')
        ->name('registrations.store');

    Route::patch('/registrations/{registration}/status', [RegistrationController::class, 'updateStatus'])
        ->middleware('role:lecturer,admin')
        ->name('registrations.update-status');

    Route::post('/registrations/{registration}/submission', [SubmissionController::class, 'store'])
        ->middleware('role:student')
        ->name('submissions.store');
    Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy'])
        ->name('submissions.destroy');
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])
        ->name('submissions.download');
    Route::patch('/submissions/{submission}/review', [SubmissionController::class, 'review'])
        ->middleware('role:lecturer,admin')
        ->name('submissions.review');

    Route::get('/registrations/{registration}/presentation/create', [PresentationController::class, 'create'])
        ->middleware('role:lecturer,admin')
        ->name('presentations.create');
    Route::post('/registrations/{registration}/presentation', [PresentationController::class, 'store'])
        ->middleware('role:lecturer,admin')
        ->name('presentations.store');
    Route::get('/presentations/{presentation}/edit', [PresentationController::class, 'edit'])
        ->middleware('role:lecturer,admin')
        ->name('presentations.edit');
    Route::put('/presentations/{presentation}', [PresentationController::class, 'update'])
        ->middleware('role:lecturer,admin')
        ->name('presentations.update');

    Route::post('/registrations/{registration}/score', [ScoreController::class, 'store'])
        ->middleware('role:lecturer,admin')
        ->name('scores.store');
    Route::put('/scores/{score}', [ScoreController::class, 'update'])
        ->middleware('role:lecturer,admin')
        ->name('scores.update');
});
