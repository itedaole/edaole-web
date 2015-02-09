<?php
class ZOrder {
	static public function OnlineIt($order_id, $pay_id, $money, $currency='CNY', $service='alipay', $bank='支付宝',$trade_no=''){ 
		list($_, $_, $quantity, $_) = explode('-', $pay_id);
		if (!$order_id || !$pay_id || $money <= 0 ) return false;
		$order = Table::Fetch('order', $order_id);
		$team = Table::Fetch('team', $order['team_id']);
		$user_id = abs(intval($order['user_id']));
		team_state($team); 

		if ( $order['state'] == 'unpay' ) {

			$table = new Table('pay');
			$table->id = $pay_id;
			$table->vid = $trade_no;
			$table->order_id = $order_id;
			$table->money = $money;
			$table->currency = $currency;
			$table->bank = $bank;
			$table->service = $service;
			$table->create_time = time();
			$ia = array('id','vid', 'order_id', 'money', 'currency', 'service', 'create_time', 'bank');
			if (Table::Fetch('pay', $pay_id) || ! $table->insert($ia)) {
			   	return false;
			}
			//update user money; +money
			Table::UpdateCache('user', $user_id, array(
						'money' => array( "money + {$money}" ),
						));             
			$u = array(
					'user_id' => $user_id,
					'admin_id' => 0,
					'money' => $money,
					'direction' => 'income',
					'action' => 'paycharge',
					'detail_id' => $pay_id,
					'create_time' => time(),
				  );
			DB::Insert('flow', $u);
			$user = Table::FetchForce('user', $user_id);
            //print_r($user);exit;
			if ($user['money']<$order['origin']){
				return false;
			}

			if (in_array($team['state'], array('soldout')) ||  $team['end_time']<time()) {
				return false;
			}

			Table::UpdateCache('order', $order_id, array(
						'pay_id' => $pay_id,
						'money' => $money,
						'state' => 'pay',
						'trade_no' => $trade_no,
						'service' => $service,
						'quantity' => $quantity,
						'pay_time' => time(),
						));

			$order = Table::FetchForce('order', $order_id);
			if ( $order['state'] == 'pay' ) {
				//TeamBuy Operation
				ZTeam::BuyOne($order);
			}
		}
		return true;
	}

	static public function CashIt($order) {
		global $login_user_id;
		if (! $order['state']=='pay' ) return 0;

		//update order
		Table::UpdateCache('order', $order['id'], array(
					'state' => 'pay',
					'service' => 'cash',
					'admin_id' => $login_user_id,
					'money' => $order['origin'],
					'pay_time' => time(),
					));
		  /* cash flow */
		$order = Table::FetchForce('order', $order['id']);
                ZFlow::CreateFromStore($order['user_id'], $order['origin']);
		ZTeam::BuyOne($order);
	}

	static public function CreateFromCharge($money, $user_id, $time,$service) {
		if (!$money || !$user_id || !$time || !$service) return 0;
		$pay_id = "charge-{$user_id}-{$time}";
		$oarray = array(
				'user_id' => $user_id,
				'pay_id' => $pay_id,
				'service' => $service,
				'state' => 'pay',
				'money' => $money,
				'origin' => $money,
				'create_time' => $time,
			       );
		return DB::Insert('order', $oarray);
	}
}
?>
