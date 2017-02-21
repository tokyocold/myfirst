<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Question extends Model
{
    //
    public function add()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'login required'];
        }
        if(!rq('title'))
        {
            return ['status'=>0,'msg'=>'title required'];
        }

        $this->title = rq('title');
        if(rq('desc'))
            $this->desc = rq("desc");
        $this->user_id = session('user_id');
        return $this->save()?['status'=>1,'id'=>$this->id]:['status'=>0,'msg'=>'insert failed'];
    }

    public function edit()
    {
        if(!rq("id"))
        {
            return err('need id');
        }
        $question = $this->find(rq("id"));
        if(!$question)
            return err('question not exists');
        if(!rq('title'))
        {
            return ['status'=>0,'msg'=>'title required'];
        }

        $question->title = rq("title");
        $question->desc = rq("desc");
        return $question->save()?suc():err('insert failed');
    }

    public function read()
    {
        if(rq('id'))
        {
            return ['status'=>1,'data'=>$this->with('answers','answers.user','answers.users','comments','answers.comments')->find(rq('id'))];
        }

        if(rq('user_id'))
        {
            $user = user_ins()->find(rq('user_id'));
            if(!$user)
            {
                return err('user not exists');
            }
            $r = $this->with('answers')->where('user_id',rq('user_id'))->get()->keyBy('id');
            return suc($r->toArray());
        }

        $limit = 15;
        $skip = ((rq('page')?:1)-1)*$limit;
        $r = $this->orderby('created_at')->limit($limit)->skip($skip)->get()->keyby('id');
        return $r;
    }

    public function remove()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'login required'];
        }
        if(!rq('id'))
        {
            return ['status'=>0,'msg'=>'id required'];
        }

        $question = $this->find(rq('id'));
        if(!$question)
        {
            return ['status'=>0,'msg'=>'question not exist'];
        }
        if(session("user_id")!=$question->user_id)
        {
            return ['status'=>0,'msg'=>'permission denied'];
        }
        return  $question->delete()?['status'=>1]:['status'=>0,'msg'=>'delete failed'];

    }

    public function answers()
    {
        return $this->hasMany('\App\Answer');
    }

    public function user()
    {
        return $this->belongsTo('\App\User')->select(array('id','username'));
    }

    public function comments()
    {
        return $this->hasMany('\App\Comment');
    }
}
