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

    public function edit()
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
            return ['status'=>0,'msg'=>'question not exists'];

        if($question->user_id != session('user_id'))
            return ['status'=>0,'msg'=>'permission denied'];
        if(rq('title'))
            $question->title=rq('title');
        if(rq('desc'))
            $question->desc=rq('desc');
        return $question->save()?['status'=>1,'id'=>$question->id]:
            ['status'=>0,'msg'=>'insert failed'];
    }
}
