@extends('welcome')

@section('content')
    <div class="col-md">
        @unless (Auth::guard('profile')->check())
            <a href="/login" class="btn btn-light btn-lg">
                <i class="fab fa-facebook"></i>
                Login with Facebook!
            </a>
        @endunless
    </div>
@endsection
