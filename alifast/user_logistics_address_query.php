<?php
/* *
 * 功能：快捷登录用户物流地址查询接口接入页
 * 版本：3.2
 * 修改日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

require_once("alipayadd.config.php");
require_once("lib/alipay_service.class.php");

/**************************请求参数**************************/

//选填参数//

//授权令牌，该参数的值由快捷登录接口(alipay.auth.authorize)的页面跳转同步通知参数中获取
$token  = $_SESSION['ali_token'];
//注意：
//token的有效时间为30分钟，过期后需重新执行快捷登录接口(alipay.auth.authorize)获得新的token

/************************************************************/

//构造要请求的参数数组
$parameter = array(
		"service"			=> "user.logistics.address.query",
		"partner"			=> trim($aliapy_config['partner']),
		"_input_charset"	=> trim(strtolower($aliapy_config['input_charset'])),
        "return_url"		=> trim($aliapy_config['return_url']),

        "token"				=> $token
);

//构造快捷登录用户物流地址查询接口
$alipayService = new AlipayService($aliapy_config);
$html_text = $alipayService->user_logistics_address_query($parameter);
echo $html_text;

?>
