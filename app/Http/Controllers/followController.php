<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class followController extends Controller
{
    public function createFollow(User $user){
        //you can not follow yourself

        if($user->id == auth()->user()->id){
            return back()->with('error', 'You can not follow yourself!');
        }
        //you can not follow a user you already followed
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id],['followeduser', '=', $user->id]])->count();

        if($existCheck){
            return back()->with('error', 'You can not follow the user you already followed');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success', 'User successfully followed!');

    }

    public function removeFollow(User $user){
        Follow::where([['user_id', '=', auth()->user()->id],['followeduser', '=', $user->id]])->delete();
        return back()->with('success', 'user successfully unfollowed');
    }
}
