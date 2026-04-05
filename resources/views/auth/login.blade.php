@extends('layouts.app', [
    'title' => 'Login',
    'heading' => 'Sign in to the system',
    'subheading' => 'Use one of the demo accounts below for a quick classroom demo.',
])

@section('content')
    <section class="login-shell">
        <div class="login-visual">
            <div class="login-overlay"></div>
            <div class="login-copy">
                <div class="login-brand">
                    <span class="material-symbols-outlined">school</span>
                    <span>Seminar Manager</span>
                </div>
                <blockquote>
                    "The beautiful thing about learning is that no one can take it away from you."
                </blockquote>
                <p class="muted">Academic portal for seminar workflows, project guidance, and classroom demonstrations.</p>
            </div>
        </div>

        <div class="login-panel">
            <div class="login-intro">
                <span class="eyebrow">Welcome Back</span>
                <h2>Sign in to the academic portal</h2>
                <p class="muted">Use one of the seeded accounts below to explore the full Seminar Manager workflow.</p>
            </div>

            <form action="{{ route('login.store') }}" method="POST" class="form login-form">
                @csrf
                <label>
                    <span>Email address</span>
                    <input type="email" name="email" value="{{ old('email', 'admin@seminar.test') }}" placeholder="name@university.edu" required>
                </label>

                <label>
                    <span>Password</span>
                    <input type="password" name="password" value="password" placeholder="Enter your password" required>
                </label>

                <label class="inline">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="button button-hero">
                    <span>Sign in</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>

            <section class="login-accounts">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Demo Accounts</span>
                        <h3>Quick access</h3>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Admin</td><td>admin@seminar.test</td><td>password</td></tr>
                            <tr><td>Lecturer</td><td>lecturer@seminar.test</td><td>password</td></tr>
                            <tr><td>Student 1</td><td>student1@seminar.test</td><td>password</td></tr>
                            <tr><td>Student 2</td><td>student2@seminar.test</td><td>password</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection
