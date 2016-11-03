<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CommonController extends Controller
{
    //
    public function timeline()
    {
        $limit = 15;
        $skip = ((rq('page')?:1) -1)*$limit;
        $questions = question_ins()->limit($limit)->skip($skip)->get()->toArray();

        $answers = answer_ins()->limit($limit)->skip($skip)->get()->toArray();

        $data = array_merge($questions,$answers);
        foreach ($data as $key=>$val)
        {
            $tem[$key] = $val['created_at'];
        }

        array_multisort($tem,SORT_DESC,$data);
        return $data;
    }
}
