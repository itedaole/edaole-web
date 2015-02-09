<?php
require_once(dirname(__FILE__) . '/app.php');

//if (!$teams) { redirect( WEB_ROOT . '/team/index.php'); }

$now = time();
$detail = array();

foreach($teams AS $index => $team) {

	if($team['end_time']<$team['begin_time']){$team['end_time']=$team['begin_time'];}
	$diff_time = $left_time = $team['end_time']-$now;
	if ( $team['team_type'] == 'seconds' && $team['begin_time'] >= $now ) {
		$diff_time = $left_time = $team['begin_time']-$now;
	}

	/* progress bar size */
	$detail[$team['id']]['bar_size'] = ceil(190*($team['now_number']/$team['min_number']));
	$detail[$team['id']]['bar_offset'] = ceil(5*($team['now_number']/$team['min_number']));

	$left_day = floor($diff_time/86400);
	$left_time = $left_time % 86400;
	$left_hour = floor($left_time/3600);
	$left_time = $left_time % 3600;
	$left_minute = floor($left_time/60);
	$left_time = $left_time % 60;

	$detail[$team['id']]['diff_time'] = $diff_time;
	$detail[$team['id']]['left_day'] = $left_day;
	$detail[$team['id']]['left_hour'] = $left_hour;
	$detail[$team['id']]['left_minute'] = $left_minute;
	$detail[$team['id']]['left_time'] = $left_time;
	$detail[$team['id']]['is_today'] = $team['begin_time'] + 3600*24 > time() ? 1:0;

	/* state */
	$team['state'] = team_state($team);
	$teams[$index] = $team;
}
$team = null;

/*首页推荐项目*/
$condition_rec = array( 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
			"index_rec"=>'Y'
	);
$teams_rec = DB::LimitQuery('team', array(
			'condition' => $condition_rec,
			'order' => 'ORDER BY `sort_order` DESC, `id` DESC',
  )); 
/*首页推荐项目*/
/*首页今日团购项目*/
$condition_day = array( 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
			'city_id'=>$city_id,
	);
$teams_day = DB::LimitQuery('team', array(
			'condition' => $condition_day,
			'order' => 'ORDER BY begin_time ASC, `sort_order` DESC, `id` DESC',
			'size' => 4,
  )); 
/*首页今日团购项目*/
/*首页一级分类下的项目*/
$cates = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'group', 
		  'fid'=>'0', 
		  'display'=>'Y',
		  ), 
		  'order'=>'ORDER BY `sort_order` DESC, `id` DESC'
		  ));

$cat_team = array();		  
foreach($cates as $key=>$val){
        $cat_team[$key]['name'] = $val[name];
		$cat_team[$key]['ename'] = $val[ename];
		$cat_team[$key]['id'] = $val[id];
		$cat_team[$key]['number'] = Table::Count('team', array('city_id'=>$city_id,'team_type'=>'normal', 'group_id'=>$val[id], "begin_time < '{$now}'", "end_time > '{$now}'"));
		$cat_team[$key]['teams'] = DB::LimitQuery('team', array(
									'condition' => array('city_id'=>$city_id,'team_type'=>'normal', 'group_id'=>$val[id],"begin_time < '{$now}'", "end_time > '{$now}'"),
									'order' => 'ORDER BY `sort_order` DESC, `id` DESC',
									'size'=> 9,
									));
}
/*首页一级分类下的项目*/

/*热门团购*/
// $hot_cat = DB::LimitQuery('category', array('condition'=>array(
// 		  'zone'=>'group',
// 		  'is_hot'=>'Y', 
// 		  'display'=>'Y',
// 		  'fid <> 0',
// 		  ), 
// 		'order' => 'ORDER BY display ,sort_order DESC, id DESC',
// 		'size' => 9,
// ));



/*滚动文章*/
// $news = DB::LimitQuery('news', array('condition'=>array(
// 		  'is_index'=>'Y',
// 		  'display'=>'Y',
// 		  ), 
//     'order' => 'ORDER BY sort_order DESC, id DESC',
// ));

/*热门专题*/
// $topic = DB::LimitQuery('xh_topic', array('condition'=>array(
// 		  'is_index'=>'Y',
// 		  'display'=>'Y',
// 		  ), 
//     'order' => 'ORDER BY sort_order DESC, id DESC',
// 	'size'=>1,
// ));



if($INI['option']['indexmultimeituan'] == 'Y'){
	if (count($teams)%2 == 1) {
		$first_big = true;
		$first = array_shift($teams);
	}
	include template('team_meituan_new');
}else {
	include template('team_multi');
};

