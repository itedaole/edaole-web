<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
require_once(dirname(__FILE__) . '/current.php');

need_manager();
need_auth('team');

$id = abs(intval($_GET['id']));
$team = $eteam = Table::Fetch('team', $id);

if ( is_get() && empty($team) ) {
	$team = array();
	$team['id'] = 0;
	$team['user_id'] = $login_user_id;
	$team['begin_time'] = strtotime('+1 days');
	$team['end_time'] = strtotime('+2 days'); 
	$team['expire_time'] = strtotime('+3 months +1 days');
	$team['min_number'] = 10;
	$team['per_number'] = 1;
    $team['permin_number'] = 1;
	$team['market_price'] = 1;
	$team['team_price'] = 1;
	$team['delivery'] = 'coupon';
	$team['address'] = $profile['address'];
	$team['mobile'] = $profile['mobile'];
	$team['fare'] = 5;
	$team['farefree'] = 0;
	$team['bonus'] = abs(intval($INI['system']['invitecredit']));
	$team['conduser'] = $INI['system']['conduser'] ? 'Y' : 'N';
	$team['buyonce'] = 'Y';
}
else if ( is_post() ) {
	$team = $_POST;
	$insert = array(
		'title','title_local', 'market_price', 'team_price','js_price', 'end_time', 
		'begin_time', 'expire_time', 'min_number', 'max_number', 
		'summary', 'notice', 'per_number','permin_number','allowrefund', 'product','image', 'image1', 'image2', 'flv', 'now_number',
		'detail','wap_detail', 'userreview', 'card', 'systemreview', 
		'conduser', 'buyonce', 'bonus', 'sort_order',
		'delivery', 'mobile', 'address', 'fare', 
		'express', 'credit', 'farefree', 'pre_number',
		'user_id', 'city_id','area_id', 'group_id','sub_id', 'partner_id',
		'team_type', 'sort_order', 'farefree', 'state',
		'condbuy','express_relate','city_ids','index_rec','free_yuyue','weekend','holiday','score','wifi','park',
		'fj_team_id','share_summary',
		);
	$team['user_id'] = $login_user_id;
	$team['state'] = 'none';
	$team['begin_time'] = strtotime($team['begin_time']);
	$team['city_id'] = abs(intval($team['city_id']));
	$team['partner_id'] = abs(intval($team['partner_id']));
	$team['sort_order'] = abs(intval($team['sort_order']));
	$team['fare'] = abs(intval($team['fare']));
	$team['farefree'] = intval($team['farefree']);
	$team['pre_number'] = abs(intval($team['pre_number']));
	$team['end_time'] = strtotime($team['end_time']);
	$team['expire_time'] = strtotime($team['expire_time']);
	$team['image'] = upload_image('upload_image',$eteam['image'],'team',true);
	$team['image1'] = upload_image('upload_image1',$eteam['image1'],'team');
	$team['image2'] = upload_image('upload_image2',$eteam['image2'],'team');
	/* 序列化选取的城市 */
	$team['city_ids'] = '@'.$team[city_id].'@'; 
	/*if (!empty($team['city_ids'])) {
		if(in_array(0, $team['city_ids'])) { 
			$team['city_id'] = 0; $team['city_ids'] = '@0@'; 
		}
		else {
			$team['city_id'] = abs(intval($team['city_ids'][0]));
			$team['city_ids'] = '@'.implode('@', $team['city_ids']).'@';
		}
	}else {
		Session::Set('notice', '请选择项目发布的城市');
		include template('manage_team_edit');
		return ;
	}*/
	
	if(empty($team['allowrefund']))  $team['allowrefund'] = 'N';
	if(empty($team['index_rec']))  $team['index_rec'] = 'N';//是否首页推荐
	if(empty($team['free_yuyue']))  $team['free_yuyue'] = 'N';
	if(empty($team['weekend']))  $team['weekend'] = 'N';
	if(empty($team['holiday']))  $team['holiday'] = 'N';
	if(empty($team['wifi']))  $team['wifi'] = 'N';
	if(empty($team['park']))  $team['park'] = 'N';

	/* 自定义快递价格 */
	$express_relate = $team['express_relate'];
	foreach ($express_relate as $k=>$v) {
		$e[$k]['id'] = $v;
		$e[$k]['price'] = $team["express_price_{$v}"];
	}
	$team['express_relate'] = serialize($e);

	//team_type == goods
	if($team['team_type'] == 'goods'){ 
		$team['min_number'] = 1; 
		$team['conduser'] = 'N';
	}

	if ( !$id ) {
		$team['now_number'] = $team['pre_number'];
	} else {
		$field = strtoupper($table->conduser)=='Y' ? null : 'quantity';
		$now_number = Table::Count('order', array(
					'team_id' => $id,
					'state' => 'pay',
					), $field);
		$team['now_number'] = ($now_number + $team['pre_number']);

		/* 增加了总数，未卖完状态 */
		if ( $team['max_number'] > $team['now_number'] ) {
			$team['close_time'] = 0;
			$insert[] = 'close_time';
		}

		/* update coupon */
		DB::Update('coupon', array('team_id' => $id), array(
			'expire_time' => $team['expire_time'],
            'partner_id'  => $team['partner_id'],
		));
		/* update order */
		DB::Update('order', array('team_id' => $id), array(
			'allowrefund' => $team['allowrefund'],
		));
	}

	//dbx($team);
	$insert = array_unique($insert);
	$table = new Table('team', $team);
	$table->SetStrip('detail', 'systemreview', 'notice');

	if ( $team['id'] && $team['id'] == $id ) {
		$table->SetPk('id', $id);
		$table->update($insert);
		log_admin('team', '编辑team项目',$insert);
		Session::Set('notice', '编辑项目信息成功');
		redirect( WEB_ROOT . "/manage/team/index.php");
	} 
	else if ( $team['id'] ) {
		log_admin('team', '非法编辑team项目',$insert);
		Session::Set('error', '非法编辑');
		redirect( WEB_ROOT . "/manage/team/index.php");
	}

	if ( $table->insert($insert) ) {
		log_admin('team', '新建team项目',$insert);
		Session::Set('notice', '新建项目成功');
		redirect( WEB_ROOT . "/manage/team/index.php");
	}
	else {
		log_admin('team', '编辑team项目失败',$insert);
		Session::Set('error', '编辑项目失败');
		redirect(null);
	}
}

$groups = DB::LimitQuery('category', array(
			'condition' => array( 'zone' => 'group','fid' => '0', ),
			));
$groups = Utility::OptionArray($groups, 'id', 'name');
$level_groups = DB::LimitQuery('category', array(
			'condition' => array( "zone" => "group", " fid <> 0" ),
			));

$level_groups = Utility::OptionArray($level_groups, 'id','name');

$partners = DB::LimitQuery('partner', array(
			'order' => 'ORDER BY id DESC',
			));
$partners = Utility::OptionArray($partners, 'id', 'title');
$selector = $team['id'] ? 'edit' : 'create';

/* 快递公司信息 */
$express = db::LimitQuery('category',array(
			'condition' => array( 'zone' => 'express', 'display'=>'Y'),
			));
$relate = unserialize($team['express_relate']);
/* 合并订单快递和快递表快递数据 */
foreach ($relate as $k=>$v) {
	$ids[] = $v['id'];
	$data[$v['id']] = $v['price'];
}
foreach ($express as $k=>$v) {
	if (in_array($v['id'] , $ids)) {
		$express[$k]['relate_data'] = $data[$v['id']];
		$express[$k]['checked'] = 'checked';
	}
}

/* 反序列化城市信息 */
//$city_ids = array_filter(explode('@', $team['city_ids']));

if($team['city_id']){
	$areas=DB::LimitQuery('category',array(
		'condition'=>array(
			'zone'=>'area',
			'fid'=>$team['city_id'],
		),
		'order'=>'order by sort_order'
	));
	$areas=Utility::OptionArray($areas,'id','name');
}
//初始化share_summary
if(empty($team['share_summary'])) $team['share_summary'] = $INI['system']['team_share_summary'];

/* 反序列化城市信息 */
$city_ids = array_filter(explode('@', $team['city_ids']));
include template('manage_team_edit');
