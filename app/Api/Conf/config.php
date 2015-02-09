<?php
$config=array(


//-----------------以下商户定制内容，可以根据自己需求定制-----------------

// 注意:
        //在关闭了DEBUG模式后修改一下内容需要清空缓存才能生效，
        //快捷的办法是把DEBUG打开，访问下服务器，然后再关闭DEBUG

//缓存配置（注意，缓存只有在关闭了DEBUG模式下才有明显作用，DEBUG模式在Api.php里改）

    'DATA_CACHE_TYPE' => 'File', //缓存类型，可以为File（默认），Memcache，APC、Db、Shmop、Sqlite、Redis、Eaccelerator和Xcache
    // 'MEMCACHE_HOST'=>'127.0.0.1',//缓存地址，默认为127.0.0.1
    // 'MEMCACHE_PORT'=>'11211',//缓存端口，默认为11211
    // 'DATA_CACHE_TIMEOUT'=>3600,//过期时间，单位为秒，设置false为永远不过期


    'T_RESULT_DEFAULT_COUNT'  =>10, //团购列表每次返回条数
    'D_PARTNER_LS_R'    =>'1000',//获取周边商家默认范围，单位米

    //搜索开关，注意，匹配越多消耗服务器越大，请酌情打开
    'SEARCH_SWITCH_TITLE' =>true,//搜索的时候匹配标题
    'SEARCH_SWITCH_PRODUCT' =>false,//商品名称
    'SEARCH_SWITCH_SEOKEYWORD' =>true,//SEO关键词搜索

    'SEARCH_OTHER_KEYS'=>'',//自定义搜索字段（数据库字段），如果你有特殊需要匹配的字段，在这写,多个可用逗号分割例如"summary,notice"

    //空格分词，打开后例如输入“烤鸡 烤鸭” 会分别匹配“烤鸡”和“烤鸭”，如果关闭，会直接搜索“烤鸡 烤鸭”
    //为了达到更理想的搜索效果，建议打开
    'SEARCH_WORD_LEAVE'=>true,

    //验证码短信MSMCode为验证码存放位置，其他文字可以定制
    'MobileVerifyMsg'=>"您的手机验证码为:MSMCode  请勿泄露他人使用",

    //团购表输出字段过滤，如果你有些隐私字段不希望暴露或为了节省流量，可以在这配置输出时过滤掉
    'TeamFieldFilter'=>'',//配置多字段用逗号分割，例如要过滤title和summary就：'TeamFieldFilter'=>'title,summary',
    
    //团购过滤条件，若如果你有些特殊团购不想在手机上显示，可以通过这个来尝试屏蔽
    //如果配置不当将会影响输出内容，请慎用
    'TeamCondition'=>"",//例如: id!=13 AND price>10
    
    //商家数据过滤，
    'PartnerFieldFilter'=>'other,open',//用法同上

    //客服电话
    'SystemHelpPhone'=>'10086',
    
    //团购合作电话
    'SystemTuanJoinPhone'=>'10001',


//-------------图片 CDN ------------开始

//以下三项只对新版客户端有效
    //图片前缀，可用于图片CDN 不填直接读取系统配置;
    'TuanImagePrefix' =>'',

//大图    
    //图片地址后缀，可用于传参或者清缓存，
    //例如更改了图片想让客户度生效，可以在后面加上:?r=xxx xxx,随便填一个，目的只为了改变图片地址，使客户端图片缓存失效，只对新版客户端有效
    //例如使用了第三方图片服务，可以利用此项加上分辨率等参数
    'TuanLargeImageSuffix' =>"",

//小图
    //图片地址后缀，可用于传参或者清缓存，
    //例如更改了图片想让客户度生效，可以在后面加上:?r=xxx xxx,随便填一个，目的只为了改变图片地址，使客户端图片缓存失效，只对新版客户端有效
    //例如使用了第三方图片服务，可以利用此项加上分辨率等参数
    'TuanSmallImageSuffix' =>'',
//案例
    // 'TuanSmallImageSuffix' =>'!340x',//拍云里传递缩略图参数，生成地址为XXX.JPG!340x

//-------------图片 CDN ------------结束

    'TuanSearchHotKeys' => '火锅,蛋糕,滑雪',//客户端搜索界面推荐搜索关键字，英文逗号分割




//财付通支付key 和sec  如果不填则读取系统默认的

    'openTenpay'=>false,//如果你没有财付通支付，那就设为false（表示关掉财付通支付功能）
    'tenpay_mid'=>'', //设置好后如果支付报 未授权错误，可以直接找财付通客服，叫他们开通就好了，这是他们返回来的错误
    'tenpay_sec'=>'',


//支付宝快捷支付配置
    //注意，填完以下内容还是不够的，想支付宝成功工作还得在支付宝后台上传配好，并在项目里上传
    //支付宝公钥和商家私钥到Api/Conf/AliConfig.php修改

    'alipay_partner'=>'2088502009793717',//合作伙伴ID ，例如：2088502009793717
    'alipay_seller'=>'512267226@qq.com',//签约支付宝账号或卖家支付宝帐户，例如：512267226@qq.com

//银联手机支付配置
    'umspay_merchantId'=>'',//商户号 ，例如：898000093990001
    'umspay_termId'=>'',//终端号，例如：99999999

//微信支付
    'wechatAppID' => '',
    'wechatPayPartner' => '',//商户号
    'wechatPayPartnerKey' => '',//初始密钥
    'wechatPayAppSecret' => '',//AppSecret
    'wechatPaySignKey' => '',//密钥
    

    'SMS_VERIFY_EXP'=>60*10,//提示短信验证码获取次数过多的时间间隔


    'verifyOtherCoupon'=>false,//是否可以验证别家商户券

    'CoordAutoTransform'=>true,//经纬度自动转换，由于中国区经度>纬度，(有的团长自己改地图把经纬度搞反了)，所以可以开启自动逆转
    'CoordType'=>'baidu', //网站地图坐标系，目前支持Mars,baidu(如果是google地图就填Mars，百度就填baidu)

//客户端更新配置
    'AppUpdateIos'=>array(  'ver'=>'2.1.2',
                            'description'=>'1.性能优化\n 2.界面美化',
                            'url'=>'https://itunes.apple.com/cn/app/id638608681?mt=8'
                           ),

    'AppUpdateAndroid'=>array(  'ver'=>'2.1.2',
                                'description'=>'1.性能优化\n 2.界面美化',
                                'url'=>'http://baidu.com/tuan.apk'
                            ),
//-----------------以下内容为框架配置，如果你不熟千万别动-----------------

    //用户密码加密串，与/include/classes/ZUser.class.php保持一致，否则用户无法登录
    'USER_SECRET_KEY'=>'@4!@#$%@',

    //商户密码加密串，与/include/classes/ZPartner.class.php保持一致，否则商户无法登录
    'PARTNER_SECRET_KEY'=>'@4!@#$%@',

	//'配置项'=>'配置值'
	'SHOW_PAGE_TRACE'=>false,
    'URL_HTML_SUFFIX'=>'.html',
    'APP_AUTOLOAD_PATH' =>'@.Engine,@.Other.Tenpay,@.Other.AliPay,@.Other.UnionPay',
    'OUTPUT_ENCODE'=>true,//输出压缩
    
	'LOG_RECORD' => false, // 开启日志记录,debug模式下依然记录部分
    'DB_SQL_LOG' => false,  //sql log
    'LOG_LEVEL'  =>'ERR', // 只记录EMERG ALERT CRIT ERR 错误

//数据库配置
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀 
    

//团购引擎配置

    'T_RESULT_COUNT_MAX'    =>60,

    
    'MobileVerifyCodeRangeMin'=>10000,//验证码范围
    'MobileVerifyCodeRangeMax'=>99999,//验证码范围
    

    //ERROR
    'ERR_400'=>array('code'=>400,'info'=>'Bad Request'),//请求出现语法错误
    'ERR_401'=>array('code'=>401,'info'=>'Unauthorized'),//客户试图未经授权访问受密码保护的页面
    'ERR_402'=>array('code'=>402,'info'=>'Forbidden'),//资源不可用。服务器理解客户的请求，但拒绝处理它。通常由于服务器上文件或目录的权限设置导致。
    'ERR_403'=>array('code'=>403,'info'=>'Not Found'),// 无法找到指定位置的资源。
    'ERR_500'=>array('code'=>500,'info'=>'Internal Server Error'),//服务器遇到了意料不到的情况，不能完成客户的请求。

    'ERR_4021'=>array('code'=>4021,'info'=>'用户名已存在'),
    'ERR_4022'=>array('code'=>4022,'info'=>'邮箱已存在'),
    'ERR_4023'=>array('code'=>4022,'info'=>'验证码错误'),
);

    if($config['OUTPUT_ENCODE']&&!extension_loaded('zlib')){//检查服务器是否开启了zlib拓展
        $config['OUTPUT_ENCODE']=false;
    }

 
//加载数据库配置
// if (!IS_SAE) {
     $root = dirname(dirname(dirname(dirname(__FILE__))));
     @require($root.'/include/configure/db.php');
     $config['DB_HOST']=$value['host'];
     $config['DB_USER']=$value['user'];
     $config['DB_NAME']=$value['name'];
     $config['DB_PWD']=$value['pass'];
// }
     
     $aliConfig=@require(dirname(__FILE__).'/AliConfig.php');
     $umsConfig=@require(dirname(__FILE__).'/UmsConfig.php');

     $otherConfig=array_merge($aliConfig,  $umsConfig);

return array_merge($config,$otherConfig);
?>
