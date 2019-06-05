<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Overtrue\EasySms\EasySms;


class VerificationCodesController extends Controller
{

    public function store(Request $request)
    {
        $phone = $request->phone;
        $code = rand(1000,9999);
        $sms = new SMS();
        $re = $sms->send_verify($phone,$code);
        if($re['Code'] == 'OK'){
            $key = 'verificationCode_'.str_random(15);
            \Cache::put($key,['phone' => $phone, 'code' => $code],60);
            return ['code'=>'200','message'=>'发送成功','data'=>['key'=>$key,'expired_at'=>60]];
        }else{
            return ['code'=>'400','message'=>'发送失败请重试'];
        }

    }





}
class SMS{
    // 保存错误信息

    public function __construct($cofig = array())
    {
        $cofig = array(
            'accessKeyId' => 'LTAIB3W9wXEGHzzX',
            'accessKeySecret' => 'Nu1BQJ00MwW5w7pN5AlnBMxxTfvJCn',
            'signName' => '卢磊blog',
            'templateCode' => 'SMS_10660199'
        );
        // 配置参数
        $this->accessKeyId = $cofig ['accessKeyId'];
        $this->accessKeySecret = $cofig ['accessKeySecret'];
        $this->signName = $cofig ['signName'];
        $this->templateCode = $cofig ['templateCode'];
    }

    private function percentEncode($string)
    {
        $string = urlencode($string);
        $string = preg_replace('/\+/', '%20', $string);
        $string = preg_replace('/\*/', '%2A', $string);
        $string = preg_replace('/%7E/', '~', $string);
        return $string;
    }

    /**
     * 签名
     *
     * @param unknown $parameters
     * @param unknown $accessKeySecret
     * @return string
     */
    private function computeSignature($parameters, $accessKeySecret)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
        return $signature;
    }

    /**
     * @param unknown $mobile
     * @param unknown $verify_code
     *
     */
    public function send_verify($mobile, $verify_code)
    {
        //dump(123);
        $params = array(   //此处作了修改
            'SignName' => $this->signName,
            'Format' => 'JSON',
            'Version' => '2017-05-25',
            'AccessKeyId' => $this->accessKeyId,
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(),
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Action' => 'SendSms',
            'TemplateCode' => $this->templateCode,
            'PhoneNumbers' => $mobile,
            'TemplateParam' => '{"code":"' . $verify_code . '"}' //替换成自己的模板
        );
        //var_dump($params);die;
        // 计算签名并把签名结果加入请求参数
        $params ['Signature'] = $this->computeSignature($params, $this->accessKeySecret);
        // 发送请求（此处作了修改）
        //$url = 'https://sms.aliyuncs.com/?' . http_build_query ( $params );
        $url = 'http://dysmsapi.aliyuncs.com/?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if (isset ($result ['Code'])) {
//            $this->error = $this->getErrorMessage($result ['Code']);
            return $result;
            return false;
        }
        return true;
    }
}
