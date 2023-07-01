<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class userController extends Controller
{
    public function showAvatarForm(){
        return view('avatar-form');
    }

public function storeAvatar(Request $request){
    $request->validate([
        'avatar' => 'required|image|max:3000'
    ]);

    $user = auth()->user();

    $filename = $user->id . '-' . uniqid() . '.jpg';

    $imgData =Image::make($request->file('avatar'))->fit(120)->encode('jpg');
    Storage::put('public/avatars/'. $filename, $imgData);

    $oldAvatar = $user->avatar;


    $user->avatar = $filename;
    $user->save();

    if ($oldAvatar != "/fallback-avatar.jpg"){

        Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
    }
    return back()->with('success', 'congrats on the new avatar!');
}

    public function register(Request $request){
        $incommingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        $user= User::create($incommingFields);
        auth()->login($user);

        return redirect('/')->with('success', 'Thank you for creating an account.');
    }


    public function showCorrectHomepage(Request $request){
        
        if(auth()->check()){
            return view('homepage-feed', ['posts'=> auth()->user()->feedPosts()->latest()->paginate(4)]);
        }else{
            return view('homepage');
        }
    }

    public function login(Request $request){
        $incommingFields = $request->validate([
            'loginusername' => ['required'],
            'loginpassword' => ['required'],
        ]);
        
        if(auth()->attempt(['username'=> $incommingFields['loginusername'], 'password' => $incommingFields['loginpassword']])){
           $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in');
        }else{
            return redirect('/')->with('error', 'invalid log in');
        }
    }

    public function logout(Request $request){
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out');
    }


    private function getShareData($user){
        $currentlyFollowing = 0;

        if(auth()->check()){
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id],['followeduser', '=', $user->id]])->count();
        }

        View::share('sharedData', ['currentlyFollowing' => $currentlyFollowing ,'username' => $user->username,'postCount' => $user->posts()->count(), 'followerCount' => $user->followers()->count(),'followingCount' => $user->following()->count(),'avatar' => $user->avatar]);
    }

    public function profile(User $user){
     
        $this->getShareData($user);
        return view('profile-posts', ['posts' => $user->posts()->latest()->get()]);
    }

    public function profileFollowers(User $user){
        $this->getShareData($user);
        return view('profile-followers', ['followers' => $user->followers()->latest()->get()]);
    }

    public function profileFollowing(User $user){
        $this->getShareData($user);
        return view('profile-following', ['followings' => $user->following()->latest()->get()]);
    }
}
