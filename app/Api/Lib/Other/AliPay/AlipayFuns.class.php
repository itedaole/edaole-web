<?php
class AlipayFuns{

    /**生成客户端支付用的签名串
    *
    *@out_trade_no  订单号
    *@subject       商品名称
    *@body          商品描述
    *@subject       商品名称
    *@totalFee      金额
    *
    */
	static function initPayStr($out_trade_no,$subject,$body,$totalFee){
        
        $host=Utils::host();
        $notify_url=$host.'/app/api.php';//支付回调
        // $notify_url=$host.'/app/api.php?m=PayReturn&a=notifyAlipay';//支付回调

        $partner=C('alipay_partner');//合作伙伴ID
        $seller=C('alipay_seller');//签约支付宝账号或卖家支付宝帐户
        if (!$partner||!$seller) {
            return false;
        }
        $content=self::getData($partner,$seller,$out_trade_no,$subject,$body,$totalFee,$notify_url);
        $mySign =self::sign($content);
        $content.='&sign="'.$mySign.'"&sign_type="RSA"';
        return $content;
	}

	//获取客户端创建交易请求的参数
    static function getData($partner,$seller,$out_trade_no,$subject,$body,$totalFee,$notify_url){
        //组装待签名数据
        $signData = "partner=" . "\"" . $partner ."\"";
        $signData .= "&";
        $signData .= "seller=" . "\"" .$seller . "\"";
        $signData .= "&";
        $signData .= "out_trade_no=" . "\"" . $out_trade_no ."\"";
        $signData .= "&";
        $signData .= "subject=" . "\"" . $subject ."\"";
        $signData .= "&";
        $signData .= "body=" . "\"" . $body ."\"";
        $signData .= "&";
        $signData .= "total_fee=" . "\"" . $totalFee ."\"";
        $signData .= "&";
        $signData .= "notify_url=" . "\"" . urlencode($notify_url) ."\"";
        return $signData;
    }

    /**RSA签名
     * $data待签名数据
     * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
     * 最后的签名，需要用base64编码
     * return Sign签名
     */
    static function sign($data) {
        //读取私钥文件
        // $path=C('PATH_ALI_RSA_PRI_KEY');
        // $priKey = file_get_contents($path);
        $priKey=trim(C('PATH_ALI_RSA_PRI_PEM'));
        
        // var_dump($path);
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
        if (!$res) {
            error_log("PATH_ALI_RSA_PRI_PEM not set");
        }
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);

        //释放资源
        openssl_free_key($res);
        
        //base64编码
        $sign = base64_encode($sign);
        return urlencode($sign);
    }

    /**RSA验签
     * $data待签名数据
     * $sign需要验签的签名
     * 验签用支付宝公钥
     * return 验签是否通过 bool值
     */
    static function verify($data, $sign)  {
        //读取支付宝公钥文件
        $data= 'notify_data='.$data;
        // $pubKey = file_get_contents('key/alipay_public_key.pem');
        // $path=C('PATH_ALI_PUB_KEY');
        // $pubKey = file_get_contents($path);
        $pubKey=trim(C('PATH_ALI_PUB_PEM'));
        // var_dump($pubKey);
        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);
        if (!$res) {
            error_log("PATH_ALI_PUB_PEM not set");
        }
        // var_dump($res);
        
        //调用openssl内置方法验签，返回bool值

        $result = openssl_verify($data, base64_decode($sign), $res);
        
        //释放资源
        openssl_free_key($res);

        //返回资源是否成功
        return $result===1?true:false;
    }

}