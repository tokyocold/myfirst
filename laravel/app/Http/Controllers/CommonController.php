<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    //
    public function timeline()
    {
        $limit = 15;

        $skip = ((rq('page')?:1)-1)*$limit;
        DB::connection()->enableQueryLog();
        $questions = question_ins()::with('user')->limit($limit)->skip($skip)->get();
        $questionIdArr = array();
        foreach ($questions as $question)
        {
            $questionIdArr[] = $question['id'];
        }

        $commentObj = new \App\Comment;
        $comments = $commentObj->whereIn("question_id",$questionIdArr)
            ->selectRaw("question_id,count(*) as c")
            ->groupby('question_id')
            ->get()->keyby("question_id");

        foreach ($questions as $question)
        {
            if(isset($comments[$question->id]))
            $question->commentCount = $comments[$question->id]->c;
            else
                $question->commentCount =0;

        }

        $questions = $questions->toArray();

        $answers = answer_ins()::with('user','question','users')->limit($limit)->skip($skip)->get();

        $comments = $commentObj->whereIn("answer_id",$answers->pluck("id")->toArray())
            ->selectRaw("answer_id,count(*) as c")
            ->groupby('answer_id')
            ->get()->keyby("answer_id");


        foreach ($answers as $answer)
        {
            if(isset($comments[$answer->id]))
            $answer->commentCount = $comments[$answer->id]->c;
            else
                $answer->commentCount = 0;
        }
        $answers = $answers->toArray();


        $data = array_merge($questions,$answers);
        $tmp = array();
        foreach ($data as $key => $val) {
            $tmp[$key] = $val['created_at'];
        }
        array_multisort($tmp,SORT_ASC,$data);

        return suc($data);
    }
}
