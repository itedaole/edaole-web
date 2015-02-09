<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no"  />
	    <meta name="apple-mobile-web-app-capable" content="yes" />
	    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <title>帮助</title>
    </head>
    <body id='help'>
    <link rel="stylesheet" href="__TMPL__Res/css/normalize2.1.css"/>
<div class="body pay-help">
        <h2 style="color:red;font-size: 30px">本页模板放在/app/Api/Tpl/Pages/下，请团长们自行替换成自己的内容</h2>
        <h2>1. 支付安全问题如何保证? </h2>
        <ul>
        	<li>余额支付内部进行多重验证保证安全。</li>
            <li>银行卡支付数据加密传输。</li>
            <li>使用支付宝支付遇到资金损失，支付宝将给予全额赔付。</li>
        </ul>
        <h2>2. 购买后不满意，如何退款？</h2>
        <ul>
            <li>首先请确认已购买团购单是否支持退款。</li>
            <li>如果支持退款，请在“订单”->“未消费”中找到此订单，打开订单详情，点击页面底部的“申请退款”，即可进行退款操作。</li>
            <li>如果不支持退款，则无法进行退款。</li>
        </ul>
        <h2>2. 已退款的订单，如何取消退款？</h2>
        <ul>
            <li>请在“订单”->“退款”中找到已申请退款的订单。</li>
            <li>若订单状态为“退款中”，请打开订单详情，点击页面底部的“取消退款”，即可进行取消退款操作。</li>
            <li>若订单状态为“已退款”，则该订单的金额已退回至您的账户，无法进行取消退款操作。</li>
        </ul>
        <h2>客服电话：<a href="tel:<?php echo ($helpPhone); ?>"><?php echo ($helpPhone); ?></a></h2>

</div>
</body>
</html>