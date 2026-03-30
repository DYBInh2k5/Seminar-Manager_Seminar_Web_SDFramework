@extends('layouts.app', [
    'title' => 'Create User',
    'heading' => 'Create user account',
    'subheading' => 'Add a new admin, lecturer, or student account.',
])

@section('content')
    @include('users.form', [
        'action' => route('users.store'),
        'method' => 'POST',
        'user' => null,
        'button' => 'Create user',
    ])
@endsection
