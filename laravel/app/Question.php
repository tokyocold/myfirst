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
}
