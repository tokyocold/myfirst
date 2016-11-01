<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    public function add()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }
        if( (!rq('question_id')&&!rq('answer_id'))||(rq('question_id')&&rq('answer_id')) )
        {
            return ['status'=>0,'msg'=>'need question_id or answer_id'];
        }
        if(!rq('content'))
        {
            return ['status'=>0,'msg'=>'need comment'];
        }
        if(rq('question_id'))
        {
            $question = question_ins()->find(rq('question_id'));
            if(!$question)
            {
                return ['status'=>0,'msg'=>'question not exists'];
            }
            $this->question_id = rq('question_id');
        }else{
            $answer = answer_ins()->find(rq('answer_id'));
            if(!$answer)
            {
                return ['status'=>0,'msg'=>'answer not exists'];
            }
            $this->answer_id = rq('answer_id');
        }
        if(rq('reply_to'))
        {
            $comment = $this->find(rq('reply_to'));
            if(!$comment)
            {
                return ['status'=>0,'msg'=>'comment not exists'];
            }
            if ($comment->user_id==session('user_id'))
            {
                return ['status'=>0,'msg'=>'can not reply yourself'];
            }
            $this->reply_to = rq('reply_to');
        }
        $this->content = rq('content');
        $this->user_id = session('user_id');
        return $this->save()?['status'=>1,'id'=>$this->id]:['status'=>0,'msg'=>'insert failed'];
    }


    public function read()
    {
        if( (!rq('question_id')&&!rq('answer_id'))||(rq('question_id')&&rq('answer_id')) )
        {
            return ['status'=>0,'msg'=>'need question_id or answer_id'];
        }

        if(rq('question_id'))
        {
            $question = question_ins()->find(rq('question_id'));
            if(!$question)
            {
                return ['status'=>0,'msg'=>'question not exists'];
            }
            $data = $this->where('question_id',rq('question_id'))->get()->keyby('id');
        }else{
            $answer = answer_ins()->find(rq('answer_id'));
            if(!$answer)
            {
                return ['status'=>0,'msg'=>'answer not exists'];
            }
            $data = $this->where('answer_id',rq('answer_id'))->get()->keyby('id');
        }
        return ['status'=>1,'data'=>$data];
    }


    public function remove()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }
        if(!rq('id'))
        {
            return ['status'=>0,'msg'=>'need id'];
        }
        $comment = $this->find(rq('id'));
        if(!$comment)
        {
            return ['status'=>0,'msg'=>'comment not exists'];
        }

        if($comment['user_id']!=session('user_id'))
        {
            return ['status'=>0,'msg'=>'promission denied'];
        }
        return $comment->delete()?['status'=>1]:['status'=>0,'msg'=>'delete failed'];
    }
}
