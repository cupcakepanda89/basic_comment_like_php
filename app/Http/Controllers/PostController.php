<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:post');
    }

    // return logged in user_id
    public function getUser()
    {
        $getUser = Auth::guard('profile')->user();
        $user_id = $getUser->id;
        return $user_id;
    }

    public function getUserName()
    {
        $getUser = Auth::guard('profile')->user();
        $name = $getUser->name;
        return $name;
    }

    // display all posts
    public function showAllPosts(Request $request)
    {
        $posts = Post::all();
        return view('index',
            [
                'posts' => $posts
            ]
        );
    }

    public function storeLike($post_id, $currentLikeCount, $user_id, $author_id)
    {
        try {
            // add number of like to post table
            Post::where('id', $post_id)->update([
                'likes' => $currentLikeCount + 1
            ]);

            // store like to Like table for tracking
            $like = new Like();
            $like->user_id = $user_id;
            // $like->author_id = $allParams['user_id'];
            $like->author_id = $author_id;
            $like->post_id = $post_id;
            $like->save();
            redirect('/showAllPosts');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return view('error', ['message' => 'Sorry, we were unable to process your action at this time.']);
        }
    }

    // add number of like to Post table and
    // add to Like table to track user_id and author_id
    public function addLike(Request $request)
    {

        $allParams = $request->all();
        $post_id = intval($allParams['post_id']);
        $authorId = intval($allParams['user_id']);

        // get the current count of likes for the post
        $currentLikeCount = 0;
        $posts = Post::where('id', $post_id)->get();
        foreach ($posts as $post) {
            $currentLikeCount = $post->likes;
        }

        // check logged in user
        $user_id = $this->getUser();
        $user_name = $this->getUserName();
        // check existing if the logged in user has liked any post
        $allLikes = Like::where('user_id', $user_id)->get();
        foreach ($allLikes as $eachLike) {
            $likeId = $eachLike->id;
            $authorId = $eachLike->author_id;
            $userId = $eachLike->user_id;
        }

        // user cannot vote on their own comment
        $msg = $user_name . ' cannot like your own post.';
        if ($user_id === intval($allParams['user_id'])) {
            return view('index',
                [
                    'msg' => $msg
                ]);
        }

        // user cannot like same post more than 1
        if (isset($userId) && isset($authorId) && $user_id === $userId) {
            $msg = $user_name . ' cannot like the same post more than 1.';
            return view('index',
                [
                    'msg' => $msg
                ]
            );
        }

        if ($user_id !== $authorId) {
            $this->storeLike($post_id, $currentLikeCount, $user_id, $authorId);
        }

    }

    // show logged in user post
    public function posts(Request $request)
    {
        $user_id = $this->getUser();
        $posts = Post::where('user_id', Auth::id())->get();
        $comment = '';
        $name = User::find($user_id)->name;
        foreach ($posts as $post) {
            $comment = $post->comment;
        }
        return view('index',
            [
                'comment' => $comment,
                'name' => $name
            ]
        );
    }

    // add new post
    public function postCreate(Request $request)
    {
        $name = $this->getUserName();
        // validate Name and Comment
        $request->validate([
            $name      => ['regex:/[a-z ]+$/i'],
            'comment'   => ['present', 'regex:/[a-z0-9 ,\.\?!]$/i','max:500']
        ]);

        $allParams = $request->all();

        // check if post comment already exist
        $msg = $name. '\'s comment already exist. Click Show Comment button to see your comment.';
        $exist_posts = Post::where('user_id', Auth::id())->get();
        foreach ($exist_posts as $post) {
            $comment = $post->comment;
        }
        if (isset($comment)) {
            return view('index',
                [
                    'msg' => $msg
                ]
            );
        }

        // add comment to post table if not exist
        try{
            $post = new Post();
            $post->user_id = Auth::id();
            $post->comment = $allParams['comment'];
            $post->likes = 0;
            $post->save();
            return response()->json(['saved']);
        }
        catch (\Exception $e) {
            error_log($e->getMessage());
            return view('error', ['message' => 'Sorry, we were unable to process your action at this time.']);
        }
    }

}
