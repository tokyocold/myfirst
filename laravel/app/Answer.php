<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Answer extends Model
{
    //
    public function add()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }
        if(!rq('question_id')||!rq('content'))
        {
            return ['status'=>0,'msg'=>'need content and question_id'];
        }
        $question = question_ins()->find(rq('question_id'));
        if(!$question)
            return ['status'=>0,'msg'=>'question not exists'];

        //�ظ��ش�
        $answerCount = $this->where(['question_id'=>rq('question_id'),'user_id'=>session('user_id')])->count();
        if($answerCount)
            return ['status'=>0,'msg'=>'duplicated answer'];

        $this->question_id = rq('question_id');
        $this->content = rq('content');
        $this->user_id = session('user_id');

        return $this->save()?['status'=>1,'id'=>$this->id]:['status'=>0,'msg'=>'insert failed'];

    }

    public function change()
    {
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }
        if(!rq('id'))
        {
            return ['status'=>0,'msg'=>'need answer id'];
        }
        $answer = $this->find(rq('id'));
        if(!$answer)
            return ['status'=>0,'msg'=>'answer not exists'];
        if($answer['user_id'] != session('user_id'))
            return ['status'=>0,'msg'=>'permission denied'];
        if(rq('content'))
            $answer->content = rq('content');

        return $answer->save()?['status'=>1,'id'=>$answer->id]:['status'=>0,'msg'=>'insert failed'];
    }

    public function read()
    {
        if(!rq('id')&&!rq('question_id'))
        {
            return ['status'=>0,'msg'=>'need id or question_id'];
        }
        if(rq('id'))
        {
            $answer = $this->find(rq('id'));
            if(!$answer)
                return ['status'=>0,'msg'=>' answer not exists'];
            return ['status'=>1,'data'=>$answer];
        }

        $question = question_ins()->find(rq('question_id'));
        if(!$question)
            return ['status'=>0,'msg'=>'question not exists'];
        $answers = $this->where(['question_id'=>rq('question_id')])->orderby('created_at')->get()->keyby('id');
        return ['status'=>1,'data'=>$answers];
    }

    public function vote()
    {
        if(!rq('id')||!rq('vote'))
        {
            return ['status'=>0,'msg'=>'need id or vote'];
        }
        if(!user_ins()->is_logged_in())
        {
            return ['status'=>0,'msg'=>'need login'];
        }
        $vote = rq('vote')<=1?1:2;
        DB::connection()->enableQueryLog();
        $answer = $this->find(rq("id"));
        if(!$answer)
            return ['status'=>0,'msg'=>'answer not exists'];
        $answerUser = $answer->users()->where(['user_id'=>session('user_id')])->first();

       if($answerUser)
       {
           $answerUser->pivot->delete();
       }

        $answer->users()->attach(session('user_id'),['vote' => $vote]);

        return ['status'=>0 ] ;

        //dd(DB::getQueryLog());

       // $user =  $answer->users();
      //  var_dump( $user);


    }

    public function users()
    {
        return $this->belongsToMany('\App\User')
            ->withPivot('vote')
            ->withTimestamps()
            ;
    }
}
