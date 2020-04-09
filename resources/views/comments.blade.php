@extends('welcome')
@section('content')
    <div class="col-md">
        <p class="alert-warning">{{ $msg ?? ''}}</p>
        <br/>
        <a href="/home" class="btn btn-secondary">
            Show My Post Comment
        </a>
    </div>
    @if (isset($posts))
        <br/>
        <form method="get" action="/jsonView">
            @csrf
            <input type="hidden" name="sortName" value="{{ $sortByName ?? '' }}"/>
            <a href="/sortByNameAsc" class="btn btn-outline-info">
                Sort By Name - Asc
            </a>
            <input type="hidden" name="orderLike" value="{{$sortByLike ?? '' }}"/>
            <a href="/sortByLikeHighest" class="btn btn-outline-info">
                Sort By Like - Highest
            </a>
            <button href="/jsonView" class="btn btn-outline-info" type="submit">
                jsonView
            </button>
            <a href="/downloadTxt" class="btn btn-outline-info">
                Download Txt
            </a>
        </form>

        <div class="col-md">
            <table class="table table-bordered">
                @foreach($posts as $post)
                    <tr>
                        <form method="post" action="{{ route('posts-like') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $post->user->id }}"/>
                            <input type="hidden" name="post_id" value="{{ $post->id }}"/>
                            <input type="hidden" name="likes" value="{{ $post->likes}}"/>
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
@endsection
