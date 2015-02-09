<?php
class ZCoupon
{
	static public function Consume($coupon) {
		if ( !$coupon['consume']=='N' ) return false;
		$u = array(
			'ip' => Utility::GetRemoteIp(),
			'consume_time' => time(),
			'consume' => 'Y',
		);
		Table::UpdateCache('coupon', $coupon['id'], $u);
		ZFlow::CreateFromCoupon($coupon);
		return true;
	}

	static public function CheckOrder($order, $isCode=false) {
	
		$coupon_array = array('coupon', 'pickup');
		$team = Table::FetchForce('team', $order['team_id']);
		//if (!in_array($team['delivery'], $coupon_array)) return;
		//获取code 无需判断其他条件
		if ($isCode) {
			self::Create($order, $isCode);
		}
		else {
			if ( $team['now_number'] >= $team['min_number'] ) {
				//init coupon create;
				$last = ($team['conduser']=='Y') ? 1 : $order['quantity'];
				$offset = max(5, $last);
				if ( $team['now_number'] - $team['min_number'] < $last) {
					$orders = DB::LimitQuery('order', array(
								'condition' => array(
									'team_id' => $order['team_id'],
									'state' => 'pay',
									),
								));
					foreach($orders AS $order) {
						self::Create($order);
					}
				}
				else{
					self::Create($order);
				}
			}
		}
			
	}

	static public function Create($order_one, $isCode=false) {
		//如果购物车
		$order_id = $order_one['id'];
		$condition = array( 
				 "packageid = $order_id or id = $order_id",  
			);
		$order_list = DB::LimitQuery('order', array(
			'condition' => $condition,
		));
        
	    /*for 循环开始*/
		for($i = 0;$i < count($order_list);$i++){
			$order = $order_list[$i];
			$team = Table::Fetch('team', $order['team_id']);
			$partner = Table::Fetch('partner', $order['partner_id']);
			$ccon = array('order_id' => $order['id']);
			$count = Table::Count('coupon', $ccon);
			if($team['delivery'] != 'express'){
				if ($isCode) {
					$order ['quantity'] = $order ['quantity'] > 0 ? $order ['quantity'] : 1;
				}
				while($count<$order['quantity']) {
					/* 配合400验证，ID统一修改为12位伪随机数字,密码为6位数字 */
					$id = (ceil(time()/100)+rand(10000000,40000000)).rand(1000,9999);
					$id = Utility::VerifyCode($id);
					$cv = Table::Fetch('coupon', $id);
					if ($cv) continue;
					$coupon = array(
							'id' => $id,
							'user_id' => $order['user_id'],
							'buy_id' => $order['buy_id'],
							'partner_id' => $team['partner_id'],
							'order_id' => $order['id'],
							'credit' => $team['credit'],
							'team_id' => $order['team_id'],
							'secret' => Utility::VerifyCode(Utility::GenSecret(6, Utility::CHAR_NUM)),
							'expire_time' => $team['expire_time'],
							'create_time' => time(),
							);
					if(DB::Insert('coupon', $coupon))
						sms_coupon($coupon);
					$count = Table::Count('coupon', $ccon);
				}
            }			
		}  /*for 循环结束*/	
	}
}
