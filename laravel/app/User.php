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

    public function is_logged_in()
    {
        if(session()->get('user_id'))
        {
            return session()->get('user_id');
        }
        return false;
    }

    public function logout()
    {
        session()->flush();
        return ['status'=>1];
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


    public function change_password()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }

        if(!rq('old_password') || !rq('new_password'))
        {
            return ['status'=>0,'msg'=>' password is required'];
        }


    }
    public function answers()
    {
        return $this->belongsToMany('\App\Answer')
            ->withPivot('vote')
            ->withTimestamps();
    }

    public function changePassword()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }

        if(!rq('old_password')||!rq('new_password'))
        {
            return err('need password');
        }

        $user = $this->find(session('user_id'));

        if(!Hash::check(rq('old_password'),$user->password))
        {
            return err('password err');
        }
        $user->password = Hash::make(rq('new_password'));
        return $user->save()?suc():err('insert failed');
    }


    public function read()
    {
        if(!rq('id'))
        {
            return err('need id');
        }
        $user = $this->find(rq('id'));
        if(!$user)
            return err('user not exists');

        $data = array(
            'id' => $user->id,
            'username' => $user->username,
            'intro' => $user->intro,
            'avatar_url' => $user->avatar_url
        );
        return suc(['data'=>$data]);
    }

    public function exists()
    {
        return suc(array('count'=>$this->where(rq())->count()));
    }
}
