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
function comment_ins()
{
    return new \App\Comment();
}
function question_ins()
{
    return new \App\Question();
}
function answer_ins()
{
    return new \App\Answer();
}

function err($msg = null)
{
    return ['status'=>0,'msg'=>$msg];
}

function suc($data = array())
{
    if($data)
    {
        return ['status'=>1,'data'=>$data];
    }
    return ['status'=>1];
}

Route::group(['middleware' => ['web']],function(){
    Route::get('/', function () {
        return view('index');
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

    Route::any("api/user/changePassword",function(){
        return user_ins()->changePassword();
    });

    Route::any("api/user/read",function(){
        return user_ins()->read();
    });


    Route::any('api/question/add',function(){
        return question_ins()->add();
    });

    Route::any('api/question/edit',function(){
        return question_ins()->edit();
    });
    Route::any('api/question/read',function(){
        return question_ins()->read();
    });


    Route::any('api/question/remove',function(){
        return question_ins()->remove();
    });



    Route::any('api/answer/add',function(){
        return answer_ins()->add();
    });

    Route::any('api/answer/change',function(){
        return answer_ins()->change();
    });
    Route::any('api/answer/read',function(){
        return answer_ins()->read();
    });
    Route::any('api/answer/vote',function(){
        return answer_ins()->vote();
    });





    Route::any('api/comment/add',function(){
        return comment_ins()->add();
    });

    Route::any('api/comment/read',function(){
        return comment_ins()->read();
    });
    Route::any('api/comment/remove',function(){
        return comment_ins()->remove();
    });




    Route::any('api/timeline','CommonController@timeline');

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
