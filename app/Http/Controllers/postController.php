<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class postController extends Controller
{

    public function showEditForm(Post $post){
        return view('edit-post', ['post' => $post]);
    }

    public function actuallyUpdate(Post $post, Request $request){
        $incommingFields = $request->validate([
            'title'=> 'required',
            'body' => 'required'
        ]);

        $incommingFields['title'] = strip_tags($incommingFields['title']);
        $incommingFields['body'] = strip_tags($incommingFields['body']);
       
        $post->update($incommingFields);
        return back()->with('success', 'post updated successfully');
    }

    public function storeNewPost(Request $request){
        $incommingFields = $request->validate([
            'title'=> 'required',
            'body' => 'required'
        ]);
        $incommingFields['title'] = strip_tags($incommingFields['title']);
        $incommingFields['body'] = strip_tags($incommingFields['body']);
        $incommingFields['user_id'] = auth()->id();

        $newPost = Post::create($incommingFields);

        return redirect("/post/{$newPost->id}")->with('success', 'new post successfulle created.');
    }

    public function showCreateForm(){
        return view('create-post');
    }

    public function viewSinglePost(Post $post ){
     
       $post['body'] = strip_tags(Str::markdown($post->body), '<p><li><ul><ol><em><strong>');
       return view('single_post', ['post' => $post]);
    }

    public function delete(Post $post ){
     
       if(auth()->user()->cannot('delete', $post)){
            return 'you cannot do that';
       }
       $post->delete();
       return redirect('/profile/'.auth()->user()->username)->with('success', 'post successfully deleted!');
    }
}
