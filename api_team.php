<?php
require_once(dirname(__FILE__) . '/app.php');
$city_id = abs(intval($city['id']));
$id = abs(intval($_GET['id']));
if (!$id || !$team = Table::FetchForce('team', $id) ) {
	redirect( WEB_ROOT . '/team/index.php');
}

if($team['city_id'] != $city_id){
    redirect( WEB_ROOT . '/team/index.php');
}


if($login_user_id){
   $check_collect = Table::Fetch('collect', $id,'team_id');
		
	if($check_collect['user_id'] == $login_user_id){
		$is_collect = 1;
	}
}
$act = $_GET['act'];
if($act == 'collect'){
	if(!$login_user_id){
        redirect( WEB_ROOT . '/account/login.php');
	}else{
	    $check_collect = Table::Fetch('collect', $id,'team_id');
		
		if($is_collect != 1){
			$collect['team_id'] = $team['id'];
			$collect['user_id'] = $login_user_id;
			$collect['title'] = $team['title'];
			$collect['time'] = time();
			$collect['team_price'] = $team['team_price'];
			$collect['image'] = $team['image'];
			
			$insert = array('team_id','user_id','title','time','team_price','image');
			
			$table = new Table('collect', $collect);
			if ( $table->insert($insert) ){
			   redirect( WEB_ROOT . "/team.php?id={$id}");
			}
		}
	}
}
if($act == 'delcollect'){
   if(Table::Delete('collect', $check_collect['id'])){
      redirect( WEB_ROOT . "/team.php?id={$id}");
   }
}




/* refer */
if ($_rid = strval($_GET['r'])) { 
	$_rid = udecode($_rid);
	$_user = Table::Fetch('user',$_rid,'email');
	if($_user) cookieset('_rid', abs(intval($_user['id'])));
	redirect( WEB_ROOT . "/team.php?id={$id}");
}
$teamcity = Table::Fetch('category', $team['city_id']);
$city = $teamcity ? $teamcity : $city;
$city = $city ? $city : array('id'=>0, 'name'=>'全部');

$pagetitle = $team['title'];

$discount_price = $team['market_price'] - $team['team_price'];
$discount_rate = team_discount($team);

$left = array();
$now = time();

if($team['end_time']<$team['begin_time']){$team['end_time']=$team['begin_time'];}

$diff_time = $left_time = $team['end_time']-$now;
if ( $team['team_type'] == 'seconds' && $team['begin_time'] >= $now ) {
	$diff_time = $left_time = $team['begin_time']-$now;
}

$left_day = floor($diff_time/86400);
$left_time = $left_time % 86400;
$left_hour = floor($left_time/3600);
$left_time = $left_time % 3600;
$left_minute = floor($left_time/60);
$left_time = $left_time % 60;

/* progress bar size */
$bar_size = ceil(190*($team['now_number']/$team['min_number']));
$bar_offset = ceil(5*($team['now_number']/$team['min_number']));
$partner = Table::Fetch('partner', $team['partner_id']);
$team['state'] = team_state($team);

/* your order */
if ($login_user_id && 0==$team['close_time']) {
	$order = DB::LimitQuery('order', array(
				'condition' => array(
					'team_id' => $id,
					'user_id' => $login_user_id,
					'state' => 'unpay',
					),
				'one' => true,
				));
}
/* end order */

/*附近的团购*/
$condition_fj = array( 
		'team_type' => 'normal',
		"begin_time < '{$now}'",
		"end_time > '{$now}'",
		"id in ({$team['fj_team_id']})",     
		);
	
$fjteams = DB::LimitQuery('team', array(
			'condition' => $condition_fj,
			'order' => 'ORDER BY  sort_order  DESC, id DESC',
));


/*买家点评列表*/
/*
$com_list = array();
$comm_list = array();
$comment_list = DB::LimitQuery('order', array(
	'condition' => array(
		'team_id' => $id,
),
	'order' => 'ORDER BY id DESC',
	'size'=>30,
));
*/
function get_com_user($id){
   $res = DB::LimitQuery('user',array(
     'condition'=>array(
	     'id'=>$id,
		 ),'one'=>true));
		 return $res;
}


/*新功能卖家店铺2014-10-01*/
$act = $_GET['act'];
$o = $_GET['o'];
$t = $_GET['t'];


if($t == 't'){
   $condition_c[] = " (image1 != '') ";
}

$condition_c['team_id'] = $id;
if($act == 3){
   $condition_c['comment_grade_three'] = 3;
}elseif($act == 2){
   $condition_c['comment_grade_three'] = 2;
}elseif($act == 1){
   $condition_c['comment_grade_three'] = 1;
}

if($o == 't'){
   $order_c = " ORDER BY add_time DESC";
}else{
   $order_c = " ORDER BY id DESC";
}

//print_r($condition_c);

$comment_list = array(); /*获取所有评论*/
$comment_list = DB::LimitQuery('comment', array(
	'condition' => array(
		'team_id' => $id,
   ),
));

$fen_1 = 0;
$fen_2 = 0;
$fen_3 = 0;
$fen_4 = 0;
$fen_5 = 0;
foreach($comment_list as $val){
   $comment_num += $val['comment_grade'];
   $xing_arr[] = (array)json_decode($val['comment_grade_all']);
   if($val['comment_grade'] == 5){
      $fen_5 += 1;
   }
    if($val['comment_grade'] == 4){
      $fen_4 += 1;
   }
    if($val['comment_grade'] == 3){
      $fen_3 += 1;
   }
    if($val['comment_grade'] == 2){
      $fen_2 += 1;
   }
    if($val['comment_grade'] == 1){
      $fen_1 += 1;
   }
}
$all_comment = count($comment_list);

foreach($xing_arr as $key=>$val){
  foreach($val as $keyw=>$value){
      $array_x[$keyw]['id'] = $keyw;
      $array_x[$keyw]['cnum'] += $value;
  } 
}


function get_comment_partner_name($id){
  $comp = Table::Fetch('comment_partner', $id);
  return $comp['name'];
}

/*计算分数*/
function get_xing_fen($cnum,$allnum){
   return sprintf("%.1f", $cnum/$allnum); 
}


//print_r($array_x);
$count = Table::Count('team', $condition_c);
$comment_all_list = DB::LimitQuery('comment', array(
	'condition' => $condition_c,
	'order' => $order_c,
));


foreach($comment_all_list as $key=>$val){
      
	   $comment_all_list[$key]['comment_time'] = $val['add_time'];
	   $res = get_com_user($val['user_id']);
	   $comment_all_list[$key]['username'] =  '***'.substr($res['username'],1);
	   $partner = Table::Fetch('partner', $val['partner_id']);
	   $comment_all_list[$key]['partner'] = $partner['username'];
	
}

$api_coment = array();
$api_coment['count'] = count($comment_all_list);
$api_coment['list'] = $comment_all_list;
echo json_encode($api_coment);

/*新功能卖家店铺借结束2014-10-01*/


/*kxx team_type */
if ($team['team_type'] == 'seconds') {
	die(include template('team_view_seconds'));
}
if ($team['team_type'] == 'goods') {
	die(include template('team_view'));
}
/*xxk*/
$ll = $partner['longlat'];
if ($ll) list($longi,$lati) = preg_split('/[,\s]+/',$ll,-1,PREG_SPLIT_NO_EMPTY);

/*seo*/
$seo_title = $team['seo_title'];
$seo_keyword = $team['seo_keyword'];
$seo_description = $team['seo_description'];
if($seo_title) $pagetitle = $seo_title;
/*end*/

