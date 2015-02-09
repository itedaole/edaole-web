<?php

/*
 *最近商品浏览历史记录
*/
function get_history(){

//TempNum 显示临时记录数
$TempNum=5;  
//RecentlyGoods 最近商品RecentlyGoods临时变量
if (isset($_COOKIE['RecentlyGoods']))
{
	$RecentlyGoods=$_COOKIE['RecentlyGoods'];
	$RecentlyGoodsArray=explode(",", $RecentlyGoods);
	$RecentlyGoodsNum=count($RecentlyGoodsArray); //RecentlyGoodsNum 当前存储的变量个数
}
if($_GET['id']!="")
{
   $Id=$_GET['id']; //ID 为得到请求的字符
	//如果存在了，则将之前的删除，用最新的在尾部追加
	if (strstr($RecentlyGoods, $Id))  
	{
	   //echo "已经存在,则不写入COOKIES<hr />";
	}
	else
	{
	   if($RecentlyGoodsNum<$TempNum) //如果COOKIES中的元素小于指定的大小，则直接进行输入COOKIES
		{
		  if($RecentlyGoods=="")
			{
			  setcookie("RecentlyGoods",$Id,time()+3600);
			}
			else
			{
			  $RecentlyGoodsNew=$RecentlyGoods.",".$Id;
			  setcookie("RecentlyGoods", $RecentlyGoodsNew,time()+3600);
			}
		}
		else //如果大于了指定的大小后，将第一个给删去，在尾部再加入最新的记录。
		{
			$pos=strpos($RecentlyGoods,",")+1; //第一个参数的起始位置
			$FirstString=substr($RecentlyGoods,0,$pos); //取出第一个参数
			$RecentlyGoods=str_replace($FirstString,"",$RecentlyGoods); //将第一个参数删除
			$RecentlyGoodsNew=$RecentlyGoods.",".$Id; //在尾部加入最新的记录
			setcookie("RecentlyGoods", $RecentlyGoodsNew,time()+3600);
		}
    }
}
 return $_COOKIE["RecentlyGoods"];
}

/*
*获取历史纪录产品
*/

function get_history_list($id){
   $teams = DB::LimitQuery('team', array(
	'condition'=>array(
				"id in ($id)",
		),  
  ));
  return $teams;
}
?>