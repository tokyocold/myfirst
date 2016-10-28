<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Hash;
class User extends Model
{
    //
    public function signup()
    {
        $usernamePassword = $this->has_username_and_password();
        if(!$usernamePassword)
        {
            return ['status'=>0,'msg'=>'用户名密码不能为空'];
        }

        list($username,$password) = $usernamePassword;

        $username_exists = $this->where('username',$username)->exists();
        if($username_exists)
        {
            return ['status'=>0,'msg'=>'用户名以存在'];
        }

        $password = Hash::make($password);

        $this->username = $username;
        $this->password = $password;
        if($this->save())
        {
            return [
                'status' => 1 ,
                'id' => $this->id
            ];
        }else{
            return ['status'=>0 ,'msg'=>'db insert failed'];
        }
    }

    public function login()
    {
        $usernamePassword = $this->has_username_and_password();
        if(!$usernamePassword)
        {
            return ['status'=>0,'msg'=>'用户名密码不能为空'];
        }
        list($username,$password) = $usernamePassword;

        $user = $this->where('username',$username)->first();
        if(!$user)
            return ['status'=>0,'msg'=>'用户不存在'];

        if(!Hash::check($password,$user->password))
            return ['status'=>0,'msg'=>'密码错误'];

        session()->put('username',$username);
        session()->put('user_id',$user->id);
        return ['status'=>1,'id'=>$user->id];
    }

    public function has_username_and_password()
    {
        $username = Request::get('username');
        $password = Request::get('password');
        if(!$username||!$password)
        {
            return false;
        }
        return [$username,$password];
    }
}
