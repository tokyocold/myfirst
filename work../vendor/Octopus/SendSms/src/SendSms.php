<?php

/**
 * Copyright (c) 2016,上海二三四五网络科技股份有限公司
 * 文件名称：SendSms.php
 * 摘    要：发送短信
 * 作    者：杜海明
 * 修改日期：2016.02.25
 * */
namespace Octopus;
class SendSms
{
    private static $sendUrl = 'http://smsp.2345.net/Api/Sms/Send';
    private static $curlError = '';
    private static $curlInfo = '';
    /**
     * curl获取内容
     * @param type $url
     * @param type $options
     * @return type
     */
    protected static function curl_get_contents($url, $options = array())
    {
        $default = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT =>
                "Mozilla/5.0 (Windows NT 6.1; rv:17.0) Gecko/17.0 Firefox/17.0",
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            );
        foreach ($options as $key => $value)
        {
            $default[$key] = $value;
        }
        $ch = curl_init();
        curl_setopt_array($ch, $default);
        $result = curl_exec($ch);
        if($result === false)
        {
            self::$curlError = curl_error($ch);
        }
        self::$curlInfo = var_export(curl_getinfo($ch), true);
        curl_close($ch);
        return $result;
    }
    /**
     * http post请求
     * @param type $url
     * @param type $params
     * @param type $options
     * @return type
     */
    protected static function http_post($url, $params = array(), $options = array())
    {
        $paramsFMT = array();
        foreach ($params as $key => $val)
        {
            $paramsFMT[] = $key . "=" . urlencode($val);
        }
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = join("&", $paramsFMT);
        return self::curl_get_contents($url, $options);
    }
    
    /**
     *  $data下的值                 必填项
     *  phone	        int	         是	手机号码
     *  msg	            varchar	     是	发送的短信内容
     *  smsType	        int	         是	发送的短信类型
     *  pid	            int	         是	项目ID
     *  clientIp	    varchar	     是	用户客户端IP
     *  positionId	    int	         否	统计发送位置ID
     *  passid	        int	         否	用户的passid
     *  返回短信返回状态码参数 参见 API文档
     *  本接口返回错误状态码定义
     *  500    curl 失败
     *  501    返回结果集非json
     *  503    缺少参数
     *  返回格式 Array (status,msg)
     * */
    public static function send ( $data )
    {
        $checkEmpty = array('phone','msg','smsType','pid','clientIp');
        foreach ( $checkEmpty as $checkEmptyInfo)
        {
            if (empty($data[$checkEmptyInfo]))
            {
                return self::returnArr('503' , '缺少参数' . $checkEmptyInfo );
            }
        }
        $reslut = self::http_post(self::$sendUrl,$data);
        if ( !empty($reslut))
        {
            $reslutArr = json_decode($reslut);
            if ( !empty($reslutArr) && is_object($reslutArr) )
            {
                return self::returnArr($reslutArr->status , $reslutArr->msg );
            }
            else
            {
                return self::returnArr( 501 , '返回结果集非json' . var_export($reslutArr,true)  );
            }
            
        }
        else
        {
            return self::returnArr( 500 , self::$curlError.self::$curlInfo );
        }
    }
    
    /**
     * 返回统一格式
     * */
    private static function returnArr ($status , $msg)
    {
        return array('status' =>$status,'msg' =>  $msg);
    }

}
