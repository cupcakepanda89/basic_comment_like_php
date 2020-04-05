<?php

namespace App\Models;

class User extends \App\User
{
    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }
}
