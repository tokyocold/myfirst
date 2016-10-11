<?php

/**
 * Copyright (c) 2016,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�SendSms.php
 * ժ    Ҫ�����Ͷ���
 * ��    �ߣ��ź���
 * �޸����ڣ�2016.02.25
 * */
namespace Octopus;
class SendSms
{
    private static $sendUrl = 'http://smsp.2345.net/Api/Sms/Send';
    private static $curlError = '';
    private static $curlInfo = '';
    /**
     * curl��ȡ����
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
     * http post����
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
     *  $data�µ�ֵ                 ������
     *  phone	        int	         ��	�ֻ�����
     *  msg	            varchar	     ��	���͵Ķ�������
     *  smsType	        int	         ��	���͵Ķ�������
     *  pid	            int	         ��	��ĿID
     *  clientIp	    varchar	     ��	�û��ͻ���IP
     *  positionId	    int	         ��	ͳ�Ʒ���λ��ID
     *  passid	        int	         ��	�û���passid
     *  ���ض��ŷ���״̬����� �μ� API�ĵ�
     *  ���ӿڷ��ش���״̬�붨��
     *  500    curl ʧ��
     *  501    ���ؽ������json
     *  503    ȱ�ٲ���
     *  ���ظ�ʽ Array (status,msg)
     * */
    public static function send ( $data )
    {
        $checkEmpty = array('phone','msg','smsType','pid','clientIp');
        foreach ( $checkEmpty as $checkEmptyInfo)
        {
            if (empty($data[$checkEmptyInfo]))
            {
                return self::returnArr('503' , 'ȱ�ٲ���' . $checkEmptyInfo );
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
                return self::returnArr( 501 , '���ؽ������json' . var_export($reslutArr,true)  );
            }
            
        }
        else
        {
            return self::returnArr( 500 , self::$curlError.self::$curlInfo );
        }
    }
    
    /**
     * ����ͳһ��ʽ
     * */
    private static function returnArr ($status , $msg)
    {
        return array('status' =>$status,'msg' =>  $msg);
    }

}
