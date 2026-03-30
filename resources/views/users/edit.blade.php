@extends('layouts.app', [
    'title' => 'Edit User',
    'heading' => 'Edit user account',
    'subheading' => 'Update account details and role permissions.',
])

@section('content')
    @include('users.form', [
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'user' => $user,
        'button' => 'Save user',
    ])
@endsection
