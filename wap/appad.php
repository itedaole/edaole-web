<?php
$adtitle1="美味碳烤鸡排/超级大鸡排套餐，原价28元，团购价仅为18.8元，口感独特，美味难挡！";
$adtitle2="仅售168元，价值318元芝华士375ml洋酒套餐！超值特惠！畅快节奏即刻唤醒自我！";
$adtitle3="仅65元可享道天下跆拳道馆（超值月卡260元+道服150元）套餐";
$adurl1="http://m.tuan0598.com/team_json_more.php?id=56";
$adurl2="http://m.tuan0598.com/team_json_more.php?id=60";
$adurl3="http://m.tuan0598.com/team_json_more.php?id=63";
$adimg1="http://www.tuan0598.com//static/team/2013/0511/13682762804980.jpg";
$adimg2="http://www.tuan0598.com/static/team/2013/0511/13682761717994.jpg";
$adimg3="http://www.tuan0598.com/static/team/2013/0511/13682758413989.jpg";

$adtitle1=utf8_unicode($adtitle1);
$adtitle2=utf8_unicode($adtitle2);
$adtitle3=utf8_unicode($adtitle3);

//最多三个轮播，没有三个请将二三两个配置为同第一
//图片大小为480x200 或同等比例



function utf8_unicode($name){
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len  = strlen($name);
        $str  = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2){
            $c  = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0){   //两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
                //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
                //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
       // $str = strtoupper($str);//转换为大写
        return $str;
    }
?>


{"ad":[{"adimg":"<?php echo $adimg1?>","adtitle":"<?php echo $adtitle1?>","adurl":"<?php echo $adurl1?>"},{"adimg":"<?php echo $adimg2?>","adtitle":"<?php echo $adtitle2?>","adurl":"<?php echo $adurl2?>"},{"adimg":"<?php echo $adimg3?>","adtitle":"<?php echo $adtitle3?>","adurl":"<?php echo $adurl3?>"}]}