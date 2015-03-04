<?php
define('WWW_ROOT', str_replace('\\','/',dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

class TuanAction extends CommonAction {
	public function index(){
		if (!APP_DEBUG)return;
		header('Content-Type:text/html; charset=utf-8');
		echo '<html>';
		echo '<body>';
		echo '<pre/>';
		echo 'Tuan模块接口:<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/typeList&help">/typeList?help</a> 得到团购类型（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/typeList">/typeList</a> ：得到团购类型<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/cityList&help">/cityList?help</a> 得到城市列表（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/cityList">/cityList</a> 得到城市列表<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/goodsList&help">/goodsList?help</a> ：获取团购列表（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/goodsList">/goodsList</a> ：获取团购列表（可以不传任何参数）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/goodsList&key=测试">/goodsList?key=测试</a> ：根据关键词获取团购信息（搜索）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/goodsList&type=1">/goodsList?type=1</a> ：根据类型词获取团购信息<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/goodsList&key=测试&type=1">/goodsList?key=测试&type=1</a> ：根据关键词和类型词获取团购信息<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/goodsList&partnerid=1&ignore=1">/goodsList&partnerid=1&ignore=1</a> ：获取一个商家的指定团购的其他团购<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/goodById&help">/goodById?help</a> ：获取某条团购的详情（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/goodById&id=1">/goodById?id=1</a> ：通过获取某条团购的详情<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/buy&help">/buy&help</a> :购买接口（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/buy&id=1&quantity=1&condbuy=绿色@大号">/buy?id=1&quantity=2&condbuy=绿色@大号</a> :购买接口<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/detail_mobile&help">/detail_mobile?help</a> :获取团购详情页（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/detail_mobile&id=1">/detail_mobile?id=1</a> :获取团购详情页<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/detail_mobile&id=1&format=json">/detail_mobile?id=1&format=json</a> :获取团购详情页(json格式)<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/detail_new&id=1&user_id=1">/detail_new?id=1&user_id=1</a> :获取用户预定的数据(json格式)<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Tuan/hotKeys&help">/hotKeys?help</a> :搜索推荐关键字（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Tuan/hotKeys">/hotKeys</a> :搜索推荐关键字<br/>';
		echo '|<br/>';
		echo '</body>';
		echo '</html>';
	}
 /**
团购
 */



function buy(){
	$this->hinfo='
	/**
	*购买接口
	*如果余额不足，则会给出换去token的url（token_url），
	*自己去取token回来然后支付就行，如果余额足会直接给出支付url（pay_url）
	*直接调就能支付了·请保证已登录
	*
	*	|id 	 	 :商品id
	*	|quantity 	 :商品数量
	*	|plat        :平台号（新字段，签名用的）ios为201，安卓为211 
	*	|condbuy 	 :商品型号信息，格式如：绿色@大号@女款
	*	|
	*	|tell 	 	 :快递电话(废弃，转用手机验证途径)
	*	|name 	 	 :快递收件人姓名
	*	|address 	 :快递邮寄地址
	*	|zipcode 	 :快递邮编
	*	|expressid 	 :快递id
	*
	*请求方式:GET
	*@return maxied 操作结果
		*/';
	$this->_help();

	$id=intval($this->_get('id'));
	$quantity=intval($this->_get('quantity'));

	$name=$this->_get('name');
	// $tell=$this->_get('tell');
	$address=$this->_get('address');
	$zipcode=$this->_get('zipcode');
	$remark=$this->_get('remark');
	$condbuy=$this->_get('condbuy');
	$expressid=$this->_get('expressid');

	$from=$this->_get('plat');//平台号
	
	$user=new UserEngine();
	if ($user->isLogin()) {
		if ($id>0&&$quantity>0) {
				$tuan=new TuanEngine();//$sid,$uid,$num,$name,$tell,$address,$zipcode,$remark,$condbuy
				$res=$tuan->buy($id,$user->userId(),$quantity,$name,$address,$zipcode,$remark,$condbuy,$expressid,$from);
				if ($res) {
					$data['status'] = 1;
					$data['result']	= $res?$res:array();
				}else{
					$data['status'] = 0;
					$data['error']	= $tuan->getError();
				}
			}else{
				$data['status'] = 0;
				$data['error']	= C('ERR_400');
			}
		}else{
			$data['status'] = -1;
			$data['error']	= C('ERR_400');
		}
		$this->out($data); 
	}

//检测团购卷是否有效

	function typeList(){
		$this->hinfo='
		/**
		*获取团购所有类型
		*
		*请求方式:GET
		*@return maxied 操作结果
		*/';
		$this->_help();
		$tuan=new TuanEngine();
		$res=$tuan->tuanTypeList();
		if ($res!==false) {
			$data['status'] = 1;
			$data['result']	= $res?$res:array();
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_500');
		}
		$this->out($data); 
	}


	function cityList(){
		$this->hinfo='
		/**
		*获取团购所有类型
		*
		*参数无
		*
		*请求方式:GET
		*@return maxied 操作结果
		*/';
		$this->_help();
		$tuan=new TuanEngine();
		$res=$tuan->tuanCityList();
		if ($res!==false) {
			$data['status'] = 1;
			$data['result']	= $res?$res:array();
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_500');
		}
		$this->out($data);
	}

	function goodById(){
		$this->hinfo='
		/**
		*获取单条团购详细信息
		*	
		*@param:
		*	|id 	 :商品id
		*
		*请求方式:GET
		*@return maxied 操作结果
		*/';
		$this->_help();
		$id=intval($this->_get('id'));
		if ($id) {
			$res=TuanEngine::goodById($id);
			Utils::FillTuanSystemInfo($res);
			Utils::htmlspecialchars($res,'detail,notice');
			unset($res['systemreview']);
			$data['status'] = 1;
			$data['result']	= $res?$res:array();
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_400');
		}
		$this->out($data); 
	}
	
	function goodsList(){
		$this->hinfo='
		/**
		*获取团购列表（可以用于搜索）
		*	
		*@param:
		*	|key 	 :商品搜索关键字，可以不传
		*	|type 	 :传入团购类型code，可以单独使用，也可以配合key使用
					  如果有两级，比如A类下面的B小类，可以传:A@B 
		*	|city_id	 :城市id,传0或不传为全部
		*	|count 	 :最多返回数据条数,不填返回系统默认条数60条，最大只能返回200
		*	|page 	 :如果数据又多页，可以加上此参数指定需要返回的页数，默认为1
		*
		*
		* ----2.0新参数-------
		*
		*	|orderby:排序方式，支持：sort_order（默认，团长控制排序）、team_price（价格）、now_number（购买数）、begin_time（开始时间）、range（范围，范围排序需要一并传入经纬度）
		*			排序方式默认为降序(DESC)，如果需要反向排序可以在前面加上负号，例如orderby=-sort_order
		*	|lng:经度（新参数）配合纬度一起传，并且排序方式选择的是距离排序时方起效
		*	|lat:纬度（新参数）
		*	|coor:期望返回坐标系（新参数），支持字段baidu/Mars(百度坐标和火星坐标)
		*
		*	|partnerid:返回指定商户id的团购
		*
		* ----3.0新参数-------
		*	
		*	|ignore:需要排除的id，可以用来配合partnerid返回指定商户的其他团购，格式可以为:ignore=2 or ignore=2,3,4
		*
		*	
		*请求方式:GET
		*@return maxied 操作结果
		*/';
		$this->_help();
		$key=$this->_get('key');
		$type=$this->_get('type');

		$orderby=$this->_get('orderby');
		$lng=$this->_get('lng');
		$lat=$this->_get('lat');
		$coord=$this->_get('coordType');

		$partnerid=$this->_get('partnerid');

		$city_id=intval($this->_get('city_id'));

		$count=intval($this->_get('count'));
		if ($count<=0) {
			$count=C('T_RESULT_DEFAULT_COUNT');
		}else if ($count>C('T_RESULT_COUNT_MAX')) {
			$count=C('T_RESULT_COUNT_MAX');
		}
		$page=intval($this->_get('page'));
		if ($page<=0) {
			$page=1;
		}

		$ignore=$this->_get('ignore');
		if ($ignore) {
			$ignore=Utils::uniqueArr($ignore);
		}

		$tuan=new TuanEngine();
		$page_current=$page;
		$res=$tuan->goodsList($key,$type,$city_id,$count,$page,$orderby,$lng,$lat,$partnerid,$coord,$ignore);
		$res=$res?$res:array();
		$data['result'] = array('exitf'=>array('count'=>$count,'page_current'=>$page_current,'page_count'=>$page),'data'=>$res);
		$this->out($data); 
	}

	function detail_mobile()
	{
		$this->hinfo='
		/**
        *获取团购详情页
        *	
        *@param:
        *	|id 	 :团购id
        *	
        *请求方式:GET
        *@return html 操作结果
                        */';
		$this->_help();
		$id=intval($this->_get('id'));
		$res=TuanEngine::teamById($id);
		
		if (strtolower($_GET['format'])=='xml') {
			$this->ajaxReturn($res,'XML');
		}else if(strtolower($_GET['format'])=='json'){
			$this->ajaxReturn($res,'JSON');
		}
		else {
			foreach ($res as $key => $value) {
				$this->assign($key,	$value);
			}
			$this->assign('title', $res['product']);
			$this->display();
		}
	}

	function pay(){
		$this->hinfo='
		/**
		*客户端余额
		*	
		*@param:
		*	|source 	 	:来源，客户端统一写:_client
		*	|id		:订单id
		*
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果
		*/';

		$this->_help();
		$order_id=intval($this->_get('id'));
		if ($_GET['source']=='_client') {
			if (UserEngine::isLogin()) {
				$tuan=new TuanEngine();
				$res=$tuan->creditPay($order_id);
				if ($res) {
					$data['status'] = 1;
					$data['result']	= 'success';
				}else{
					$data['status'] = 0;
					$data['error']	= $tuan->getError();
				}
			}else{
				$data['status'] = -1;
				$data['error']	= C('ERR_400');
			}
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_403');
		}
		$this->out($data); 
	}

	function pay3(){
		$this->hinfo='
		/**
		*第三方支付
		*/';
		$this->_help();
	}

	function hotKeys(){
		$this->hinfo='
		/**
		*返回后台配置的推荐搜索关键字
		*
		*
		*状态码(status)：1:正常,0:失败
		*
		*是否需要登录:NO
		*请求方式:GET
		*@return maxied 操作结果
		*/';

		$this->_help();
		$keys=explode(',',C('TuanSearchHotKeys'));
		$keys=array_diff($keys,array(''));

		$data['status'] = 1;
		$data['result']	= $keys;
		$this->out($data); 
	}
	
	/**
	 * 
	 */
	function detail_new()
	{
		$this->hinfo='
		/**
        *获取用户预订的商品情况
        *
        *@param:
        *	|id 	 :团购id
        *
        *请求方式:GET
        *@return html 操作结果
                        */';
		$this->_help();
		$id=intval($this->_get('id'));
		$team=TuanEngine::teamById($id);
		$partner = PartnerEngine::partnerById($team['partner_id']);
		$user_id = intval($this->_get('user_id'));
		$login_user = UserEngine::objById(TAB_USER, $user_id);
		
		$newCoupon = $this->createCoupon($id, $user_id);
		if (!$newCoupon) {
			$res['status'] = 0;
			$res['result'] = array();
			$this->ajaxReturn($res,'JSON');
		}
		$teamFields = array('id','title','title_local','user_id','product','image','image1','image2');
		$partnerFields = array('username','address','yy_time','phone');
		$returnData = array();
		$returnData['id']=$team['id'];
		$returnData['title']=$team['title'];
		$returnData['title_local']=$team['title_local'];
		$team['image'] = '/static/'.$team['image'];
		$large_image =  preg_replace('#(\d+)\.(\w+)$#', "\\1_large.\\2", $team['image']);
		if (isset($_SERVER['DOCUMENT_ROOT'])) {
			$wwwroot = $_SERVER['DOCUMENT_ROOT'];
		}
		else {
			$wwwroot = WWW_ROOT;
		}
		if (!file_exists($wwwroot.$large_image)) {
			$large_image = $team['image'];
		}
		$returnData['large_image']=$large_image;
		$returnData['image']=$team['image'];
		$returnData['shop_name']=$partner['username'];
		$returnData['address']=$partner['address'];
		$returnData['yy_time']=$partner['yy_time'];
		$returnData['phone']=$partner['phone'];
		$returnData['couponcode']= isset($newCoupon['couponcode'])?$newCoupon['couponcode']:$login_user['couponcode'];
		if (empty($returnData['couponcode'])) {
			$tuan=new TuanEngine();
			$returnData['couponcode'] = $tuan->createCouponcode($user_id);
		}
		$returnData['share_summary']=$team['share_summary'];
		$returnData['share_url'] = 'http://www.edaole.com/wap/team.php?id='.$team['id'];
		$res['status'] = 1;
		$res['result'] = $returnData;
		$this->ajaxReturn($res,'JSON');
	}
	
	function createCoupon($teamId, $userId){
		
		if ($teamId>0) {
			$tuan=new TuanEngine();
			$res=$tuan->buynew($teamId,$userId);
			if ($res) {
				return $res;
			}
			else {
				return false;
			}
		}
		
		return true;
	}

}
