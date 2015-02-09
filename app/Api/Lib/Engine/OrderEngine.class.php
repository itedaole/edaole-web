<?php

define('TAB_ORDER', 'order');


class OrderEngine extends BaseEngine{
	
	static function getTeamOrders($teamId,$payState='pay'){
		$map['team_id']=array('EQ',$teamId);
		$map['state']=array('EQ',$payState);
		$resArr=self::ordersByIds(NULL,$map);
		return $resArr;
	}

	static function getTeamUnpayOrders($teamId){
		return self::getTeamOrders($teamId,'unpay');
	}

	static function getTeamPayedOrders($teamId){
		return self::getTeamOrders($teamId,'pay');
	}



	static function ordersByIds($ids,$where=NULL,$maping=false){
		$orders=self::objsByIds(TAB_ORDER,$ids,$where,$maping);
		return $orders;
	}

	static function orderById($id){
		$res=self::ordersByIds($id);
		return $res?$res[0]:$res;
	}
	
	function create($post){
		
	}

/**
废弃方法
*/
	// function changeOrderState2Pay($order){
	// 	$db=M(TAB_ORDER);
	// 	$data['service']='credit';
	// 	$data['money']='0';
	// 	$data['state']='pay';
	// 	$data['credit']=$order['origin'];
	// 	$data['pay_time']=time();
	// 	$db->where('id=%d',$order['id'])->save($data);
	// }

}