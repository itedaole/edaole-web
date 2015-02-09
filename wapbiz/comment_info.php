<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$nowtime = time();
$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);

$id = $_GET['id'];
$act = $_GET['act'];

$condition = array('team_id' => $id);
$condition_c = array('team_id' => $id);
if($act == 3){
   $condition_c['comment_grade_three'] = 3;
}elseif($act == 2){
   $condition_c['comment_grade_three'] = 2;
}elseif($act == 1){
   $condition_c['comment_grade_three'] = 1;
}elseif($act == 'no'){
   $condition_c['reply_cotent'] = null;
}

$comment_list = array(); /*获取所有评论*/
$comment_list = DB::LimitQuery('comment', array(
	'condition' => $condition,
));
$comment_list_c = DB::LimitQuery('comment', array(
	'condition' => $condition_c,
));
$fen_1 = 0;
$fen_2 = 0;
$fen_3 = 0;
$fen_4 = 0;
$fen_5 = 0;
foreach($comment_list as $key=>$val){
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


foreach($comment_list_c as $key=>$val){
   $comment_list_c[$key]['comment_time'] = $val['add_time'];
   $res = get_com_user($val['user_id']);
   $comment_list_c[$key]['username'] =  '***'.substr($res['username'],1);
}

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

function get_com_user($id){
   $res = DB::LimitQuery('user',array(
     'condition'=>array(
	     'id'=>$id,
		 ),'one'=>true));
		 return $res;
}

/*
echo get_xing_fen($comment_num,$all_comment).'分';

echo $fen_1;
echo $fen_2;
echo $fen_3;
echo $fen_4;
echo $fen_5;




echo '<hr><pre>';

print_r($comment_list);
*/
include template('mb_comment_info');






