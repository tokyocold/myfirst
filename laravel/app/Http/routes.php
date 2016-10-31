<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
function rq($key ='',$default='')
{
    if (!$key)
    {
        return Request::all();
    }
    return Request::get($key,$default);
}
function user_ins()
{
    return new \App\User();
}
function question_ins()
{
    return new \App\Question();
}
Route::group(['middleware' => ['web']],function(){
    Route::get('/', function () {
        return view('welcome');
    });

    Route::any('/api',function(){
        return ['version'=>0.1];
    });

    Route::any('api/user',function(){

        $user = new App\User();
        return $user->signup();
    });

    Route::any('api/login',function(){
        $user = new \App\User();
        return $user->login();
    });

    Route::any('api/logout',function(){
        $user = new \App\User();
        return $user->logout();
    });


    Route::any('api/question/create',function(){
        return question_ins()->add();
    });
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});