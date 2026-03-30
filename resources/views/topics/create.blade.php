@extends('layouts.app', [
    'title' => 'Create Topic',
    'heading' => 'Create a seminar topic',
    'subheading' => 'Add the core topic details to open student registration.',
])

@section('content')
    @include('topics.partials.form', [
        'action' => route('topics.store'),
        'method' => 'POST',
        'topic' => null,
        'button' => 'Create topic',
        'lecturers' => $lecturers,
    ])
@endsection
