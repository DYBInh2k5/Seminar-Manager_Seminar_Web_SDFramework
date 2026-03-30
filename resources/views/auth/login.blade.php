@extends('layouts.app', [
    'title' => 'Login',
    'heading' => 'Sign in to the system',
    'subheading' => 'Use one of the demo accounts below for a quick classroom demo.',
])

@section('content')
    <div class="grid single">
        <section class="card hero">
            <div>
                <span class="eyebrow">Laravel Project</span>
                <h2>Student Seminar Management</h2>
                <p>
                    This demo project includes login, topic management, topic registration,
                    approval workflow, presentation scheduling, and grading.
                </p>
            </div>

            <form action="{{ route('login.store') }}" method="POST" class="form">
                @csrf
                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', 'admin@seminar.test') }}" required>
                </label>

                <label>
                    <span>Password</span>
                    <input type="password" name="password" value="password" required>
                </label>

                <label class="inline">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="button">Log in</button>
            </form>
        </section>

        <section class="card">
            <h3>Demo accounts</h3>
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
@endsection
