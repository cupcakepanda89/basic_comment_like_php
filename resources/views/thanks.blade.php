@extends('welcome')

@section('content')
        <div class="col-md">
            <img class="img-thumbnail" src="{{$picture}}"/>
            <h2>Thanks {{$name}}! 👏</h2>
        </div>
@endsection
