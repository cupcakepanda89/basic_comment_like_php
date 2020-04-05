@extends('welcome')

@section('content')
    <div class="col-md">
        <a href="{{ route('logout') }}" class="btn btn-light btn-lg btn-block">
            Logout
        </a>
    </div>
    <br />

@endsection
