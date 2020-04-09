@extends('welcome')

@section('content')
    <a href="/home" class="btn btn-secondary">
        Go back to Home page
    </a>
    {{ $message }}
@endsection
