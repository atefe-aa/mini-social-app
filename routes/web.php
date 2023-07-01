<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\postController;
use App\Http\Controllers\userController;
use App\Http\Controllers\followController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/admins-only', function (){
    return 'only admins should be able to see this page';
})->middleware('can:visitAdminPages');


//user related routs
Route::get('/', [userController::class, "showCorrectHomepage"])->name('login');
Route::post('/register', [userController::class, "register"])->middleware('guest');
Route::post('/login', [userController::class, "login"])->middleware('guest');
Route::post('/logout', [userController::class, "logout"])->middleware('auth');
Route::get('/manage-avatar', [userController::class, "showAvatarForm"])->middleware('auth');
Route::post('/manage-avatar', [userController::class, "storeAvatar"])->middleware('auth');


//follow related routs
Route::post('/create-follow/{user:username}', [followController::class, 'createFollow'])->middleware('auth');
Route::post('/remove-follow/{user:username}', [followController::class, 'removeFollow'])->middleware('auth');

//blog post related routs
Route::get('/create-post', [postController::class, "showCreateForm"])->middleware('auth');
Route::post('/create-post', [postController::class, "storeNewPost"])->middleware('auth');
Route::get('/post/{post}', [postController::class, "viewSinglePost"]);
Route::delete('/post/{post}', [postController::class, "delete"])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [postController::class, "showEditForm"])->middleware('can:update,post');
Route::put('/post/{post}', [postController::class, "actuallyUpdate"])->middleware('can:update,post');
Route::get('/search/{term}', [postController::class, "search"]);


//profile related routs
Route::get('/profile/{user:username}', [userController::class, 'profile']);
Route::get('/profile/{user:username}/followers', [userController::class, 'profileFollowers']);
Route::get('/profile/{user:username}/following', [userController::class, 'profileFollowing']);