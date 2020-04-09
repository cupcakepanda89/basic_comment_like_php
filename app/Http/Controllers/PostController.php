<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

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

    // return logged in user_name
    public function getUserName()
    {
        $getUser = Auth::guard('profile')->user();
        $name = $getUser->name;
        return $name;
    }

    // display all post comments
    public function comments(Request $request){
        $posts = Post::all();
        return view('comments',
            [
                'posts' => $posts
            ]
        );
    }

    // store like
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
//            return Redirect::to('/comments');
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
        $post_id_field = intval($allParams['post_id']);
        $author_id_field = intval($allParams['user_id']);

        // get the current count of likes for the post
        $currentLikeCount = 0;
        $posts = Post::where('id', $post_id_field)->get();
        foreach ($posts as $post) {
            $currentLikeCount = $post->likes;
        }

        // check logged in user
        $user_id = $this->getUser();
        $user_name = $this->getUserName();
        // check existing if the logged in user has liked any post
        $allLikes = Like::all();

        if($allLikes){
            foreach ($allLikes as $eachLike) {
                $likeId = $eachLike->id;
                $authorId = $eachLike->author_id;
                $userId = $eachLike->user_id;
                // user cannot vote on their own comment
                $msg = $user_name . ' cannot like your own post.';
                if ($user_id === intval($allParams['user_id'])) {
                    $posts = Post::all();
                    return view('comments',
                        [
                            'msg' => $msg,
                            'posts' => $posts
                        ]);
                }

                // user cannot like same post more than 1
                if (isset($userId) && isset($authorId) &&
                    $user_id === $userId &&
                    $authorId === $author_id_field
                ) {
                    $posts = Post::all();
                    $msg = $user_name . ' cannot like the same post more than 1.';
                    return view('comments',
                        [
                            'msg' => $msg,
                            'posts' => $posts
                        ]
                    );
                }
                if ($user_id !== $userId &&
                    $user_id !== $author_id_field &&
                    $authorId !== $author_id_field
                ) {
                    $this->storeLike($post_id_field , $currentLikeCount, $user_id, $author_id_field);
                    return Redirect::to('/comments');
                }
            }
        }
        $msg = $user_name . ' cannot like your own post.';
        if ($user_id === intval($allParams['user_id'])) {
            $posts = Post::all();
            return view('comments',
                [
                    'msg' => $msg,
                    'posts' => $posts
                ]);
        }
        $this->storeLike($post_id_field , $currentLikeCount, $user_id, $author_id_field);
        return Redirect::to('/comments');
    }

    // get logged in user's post
    public function getUserPost(){
        $user_id = $this->getUser();
        $posts = Post::where('user_id', Auth::id())->get();
        $comment = '';
//        $name = User::find($user_id)->name;
        foreach ($posts as $post) {
            $comment = $post->comment;
        }
        return $comment;
    }

    // display logged in user post
    public function myPost(Request $request)
    {
        $name = $this->getUserName();
        $comment = $this->getUserPost();
        return view('home',
            [
                'comment' => $comment,
                'name' => $name
            ]
        );
    }

    // add new post for logged in user
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
        $msg = $name. '\'s comment already exist.';
        $exist_posts = Post::where('user_id', Auth::id())->get();
        foreach ($exist_posts as $post) {
            $comment = $post->comment;
        }
        if (isset($comment)) {
            $name = $this->getUserName();
            $comment = $this->getUserPost();
            return view('home',
                [
                    'msg' => $msg,
                    'comment' => $comment,
                    'name' => $name
                ]
            );
        }

        // add comment to post table if not exist
        try{
            $post = new Post();
            $post->user_id = Auth::id();
            $post->name = $this->getUserName();
            $post->comment = $allParams['comment'];
            $post->likes = 0;
            $post->save();
//            return response()->json(['saved']);
            return Redirect::to('/comments');
        }
        catch (\Exception $e) {
            error_log($e->getMessage());
            return view('error', ['message' => 'Sorry, we were unable to process your action at this time.']);
        }
    }

    public function sortByNameAsc(){
        $posts = Post::orderBy('name', 'asc')->get();
        $sortByName = true;
        return view('comments',
            [
                'posts' => $posts,
                'sortByName' => $sortByName
            ]
        );
    }

    public function sortByLikeHighest(){
        $posts = Post::orderBy('likes', 'desc')->get();
        $sortByLike = true;
        return view('comments',
            [
                'posts' => $posts,
                'sortByLike'=> $sortByLike
            ]
        );
    }

    public function jsonView(Request $request){
        $allParams = $request->all();
        if(isset($allParams['sortName'])){
            $posts = Post::orderBy('name', 'asc')->get();
        }
        if(isset($allParams['orderLike'])){
            $posts = Post::orderBy('likes', 'desc')->get();
        }
        if(!isset($allParams['sortName']) && !isset($allParams['orderLike'])){
            $posts = Post::all();
        }
        $data = [];
        foreach ($posts as $post){
            $eachPost = new \stdClass();
            $eachPost->name = $post->name;
            $eachPost->comment = $post->comment;
            $eachPost->votes = $post->likes;
            array_push($data,$eachPost);
        }
        $jsonData = new \stdClass();
        $jsonData->data = $data;
        return response(json_encode($jsonData),200, ['Content-Type' => 'application/json']);
    }

    public function downloadTxt (Request $request){
        $allParams = $request->all();
        if(isset($allParams['sortName'])){
            $posts = Post::orderBy('name', 'asc')->get();
        }
        if(isset($allParams['orderLike'])){
            $posts = Post::orderBy('likes', 'desc')->get();
        }
        if(!isset($allParams['sortName']) && !isset($allParams['orderLike'])){
            $posts = Post::all();
        }
        $fileText = '';
        foreach ($posts as $post){
            $eachPost = '';
            $eachPost .= $post->name .= ', ';
            $eachPost .= $post->comment .= ', ';
            $eachPost .= $post->likes;
            $fileText .= $eachPost .= PHP_EOL;
        }
        $myName = "post_comment_table.txt";
        $headers = ['Content-type'=>'text/plain', 'test'=>'YoYo', 'Content-Disposition'=>sprintf('attachment; filename="%s"', $myName),'X-BooYAH'=>'WorkyWorky','Content-Length'=>strlen($fileText)];
        return \Response::make($fileText, 200, $headers);

    }
}
