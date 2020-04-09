@extends('welcome')

@section('content')
    <div class="col-md">
        <p class="alert-warning">{{ $msg ?? ''}}</p>
        @auth('profile')
            {{ Auth::guard('profile')->user()->name }}
            <br />
            <a href="{{ route('logout') }}" class="btn btn-light">
                Logout
            </a>
            <a href="/comments" class="btn btn-success">
                Show All Posts
            </a>
    </div>
    <div class="col-md">
        <form method="post" action="{{ route('posts-create') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="col">
                    <label for="comment">Comment</label>
                    @if (empty($comment))
                        <textarea type="text" class="form-control" name="comment" id="comment" rows="3">
                            {{ $comment ?? '' }}
                        </textarea>
                        <br/>
                        <div class="form-row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    @else
                        <textarea type="text" class="form-control" name="comment" id="comment" rows="3"
                                  disabled>
                            {{ $comment ?? '' }}
                        </textarea>
                        <br/>
                        <div class="form-row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary" disabled>Submit</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
    @endauth

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
