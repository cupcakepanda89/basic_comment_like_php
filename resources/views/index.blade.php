@extends('welcome')

@section('content')
    <div class="col-md">

        <p class="alert-warning">{{ $msg ?? ''}}</p>
        <br />
        @auth('profile')
            {{ Auth::guard('profile')->user()->name }}
            <a href="{{ route('logout') }}">Logout</a>
            <br/>
            <div class="col">
                <a href="{{ route('posts') }}" class="btn btn-secondary">
                    Show My Post Comment
                </a>
                <a href="{{ route('showAllPosts') }}" class="btn btn-success">
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
                            @else
                                <textarea type="text" class="form-control" name="comment" id="comment" rows="3"
                                          disabled>
                        {{ $comment ?? '' }}
                    </textarea>
                            @endif
                        </div>
                    </div>
                    <br />
                    <div class="form-row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <br/>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>
    </div>
    @if (isset($posts))
        <div class="col-md">
            <table class="table table-bordered">
                @foreach($posts as $post)
                    <tr>
                        <form method="post" action="{{ route('posts-like') }}" >
                            @csrf
                        <input type="hidden" name="user_id" value="{{ $post->user->id }}" />
                        <input type="hidden" name="post_id" value="{{ $post->id }}" />
                        <td>{{ $post->user->name }}</td>
                        <td>{{ $post->comment }}</td>
                        <td>{{ $post->likes }}</td>
                        <td>
                            <button type="submit">
                                +1
                            </button>
                        </td>
                        </form>
                    </tr>
                @endforeach

            </table>
        </div>

    @endif

        @endauth
        @unless (Auth::guard('profile')->check())
            <a href="/login" class="btn btn-light btn-lg">
                <i class="fab fa-facebook"></i>
                Login with Facebook!
            </a>
        @endunless

        <div>
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </div>

        <br />

@endsection
