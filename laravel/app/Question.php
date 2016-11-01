<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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



    public function read()
    {
        if(rq('id'))
        {
            return ['status'=>1,'data'=>$this->find(rq('id'))];
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
}
