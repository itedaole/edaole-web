<?php
//商家引擎
define('P_IS_LOGIN','partner_api_islogin');
define('P_USER_ID','partner_id');
define('TAB_PARTNER','partner');


class PartnerEngine extends BaseEngine{

	function getPartnerId(){
		return $_SESSION[P_USER_ID];
	}

	function login($name,$pwd){
		// var_dump(func_get_args());
		if (!$name||!$pwd) {
			$this->error=C('ERR_400');
			return false;
		}
		$map['username']=array('EQ',$name);
		$map['password']=array('EQ',Utils::ParPassword($pwd));
		$res=self::objFromTable(TAB_PARTNER,$map);
		if ($res) {
			$_SESSION[P_IS_LOGIN]=true;
			$_SESSION[P_USER_ID]=intval($res['id']);	
			$res=Utils::smartSimplifyArr($res,self::fieldFilter());
			return $res;
		}else{
			$this->error=C('ERR_401');
			return false;
		}
	}

	static function ls($lng=NULL,$lat=NULL,$range=NULL,$count=NULL){

		$time=time();
		$map['begin_time']=array('ELT',$time);
		$map['end_time']=array('EGT',$time);
		if(C('TeamCondition')){//自定义筛选条件
			$map['_string'] = C('TeamCondition');
		}
		$db=M('team');
		$pars=$db->where($map)->field('id,partner_id')->select();
		$pids=Utils::arrVauleList($pars,'partner_id');
		$res=array();
		if ($pids) {
			$map=array();
			$map['longlat']=array('NEQ','');//查出带经纬度的数据
			$res=self::objsByIds(TAB_PARTNER,$pids,$map);
		}
		$flag=false;
		foreach ($res as $key => &$value) {
			unset($value['password']);
			@list($_lat, $_lng) = explode(',', $value['longlat'], 2);
			$value['_lng']=$_lng;
			$value['_lat']=$_lat;
			if ($lng!==NULL&&$lat!=NULL) {
				$value['_range']=CoorEngine::lnglat2m($_lng,$_lat,$lng,$lat);
				if ($value['_range']>$range) {
					unset($res[$key]);
					$flag=true;
				}
			}
		}
		if ($flag)$res=array_values($res);
		
		Utils::htmlspecialchars($res,'location,other');
		return $res?$res:array();
	}

	static function fieldFilter(){
		return 'password,create_time,'.C('PartnerFieldFilter');
	}


	function logout(){
		unset($_SESSION[P_IS_LOGIN]);
		unset($_SESSION[P_USER_ID]);
	}

	function isLogin(){
		return $_SESSION[P_IS_LOGIN]?true:false;
	}


	//往团购信息里面填充商家信息
	static function fillPartnerInfo(&$goodsArr,$lng=NULL,$lat=NULL,$coord=NULL){
		$partnerIds=Utils::arrVauleList($goodsArr,'partner_id');
		$partners=PartnerEngine::partnersByIds($partnerIds,NULL,true);
		//填充快递
		$coordType=C('CoordType');
		$autoTrans=C('CoordAutoTransform');
		foreach ($goodsArr as &$value) {
			if ($partners[$value['partner_id']]) {
				$value['partner']=$partners[$value['partner_id']];
				if ($value['partner']['longlat']){
					@list($_lat, $_lng) = explode(',', $value['partner']['longlat'], 2);
					if ($autoTrans) {//自动纠错
						if ($_lat>$_lng) {
							$tempz=$_lat;
							$_lat=$_lng;
							$_lng=$tempz;
						}
					}
					if ($coord)$_coordType=CoorEngine::conver($coordType, $coord, $_lng, $_lat);
					$value['partner']['_lng']=strval($_lng);
					$value['partner']['_lat']=strval($_lat);
					$value['partner']['_CoordinateType']=$_coordType;
					$value['_lng']=strval($_lng);
					$value['_lat']=strval($_lat);
					$value['_CoordinateType']=$_coordType;
					if ($lng!==NULL&&$lat!=NULL) {
						$value['partner']['_range']=CoorEngine::lnglat2m($_lng,$_lat,$lng,$lat);
						$value['_range']=$value['partner']['_range'];
					}
				}
				Utils::htmlspecialchars($value['partner'],'location,other');
			}
		}
	}

	static function partnersByIds($ids,$where=NULL,$maping=false){
		$table=TAB_PARTNER;
		$partners=self::objsByIds($table,$ids,$where,$maping);
		$partners=Utils::smartSimplifyArr($partners,self::fieldFilter());
		return $partners;
	}

	static function partnerById($id){
		$res=self::partnersByIds($id);
		return $res?$res[0]:$res;
	}

	function verifyCoupon($code,$pid=NULL){
		$db=M('coupon');
		$res=$db->where("`id`='$code'")->find();
		$flag=C('verifyOtherCoupon');
		if ($res) {
			if(!$flag&&$res['partner_id']!=$pid){
				$result['info']="非本商户优惠券";
				$result['code']=0;
			}else if ($res['consume']=='Y') {
				$consumetime=date('Y-m-d H:i:s',$res['consume_time']);
				$result['info']="无效,已经于: ".$consumetime." 消费";
				$result['code']=0;
			}else if($res['expire_time']<time()) {
				$expire_time=date('Y-m-d H:i:s',$res['expire_time']);
				$result['info']="无效,已经于: ".$expire_time." 过期";
				$result['code']=0;
			}else{
				$expire_time=date('Y-m-d H:i:s',$res['expire_time']);
				$result['info']="有效,到期时间: ".$expire_time;
				$result['team']=TuanEngine::goodById($res['team_id']);
				$result['code']=1;
			}
		}else{
			$result['info']='找不到相关的数据 -_-!';
			$result['code']=0;
		}
		return $result;
	}
}