<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:profile');
//        $this->middleware('cors');
    }

    public function user()
    {
        /** @var User $user */
        $user    = Auth::user();
        $id      = $user->id;
        $name    = $user->name;
        $picture = $user->picture;

        return response()->json([
            'user' => [
                'id'        => $id,
                'name'      => $name,
                'picture'   => $picture
            ]
        ]);
    }
}
