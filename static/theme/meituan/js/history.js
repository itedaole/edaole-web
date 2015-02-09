function SetHistory(goods_id) {
    var historyp;
	if (goods_id > 0) {
		//设置cookie保存的浏览记录的条数
		var N = 5;
		var count = 0;
		//判断是否存在cookie
		if ($.cookie('0750t_h') == null) //cookie 不存在
		{
			//创建新的cookie,保存浏览记录
			$.cookie('0750t_h', goods_id, {
				expires: 7,
				path: '/'
			});
		} else //cookies已经存在
		{
			//获取浏览过的商品编号ID
			historyp = $.cookie('0750t_h');
			//分解字符串为数组 
			var pArray = historyp.split(',');
			//最新访问的商品编号放置载最前面
			historyp = goods_id;
			//判断是该商品编号是否存在于最近访问的记录里面
			for (var i = 0; i < pArray.length; i++) {
				if (pArray[i] != goods_id) {
					historyp = historyp + "," + pArray[i];
					count++;
					if (count == N - 1) {
						break;
					}
				}
			}
			//修改cookie的值
			$.cookie('0750t_h', historyp);
		}
	}
}

function GetHistoryHtml(data)
{
    //var data = GetHistory();
    if(data != null && data != "")
    {
        var render = "";
        $.each(data,function(index, val){
            render += "<ul>";
        $.each(val,function(index2, ct){
            render += '<li><div class="picture"><a href="'+ct.url+'" target="_blank"><img src="'+ct.small_img+'" width="65" height="41" /></a></div><div class="title"><a href="'+ct.url+'" target="_blank" title="'+ct.name_1+'"><span>['+ct.quan_name+']</span>'+ct.short+'</a></div><div class="price"><b class="fee">'+ct.shop_price_format+'</b><b class="exfee">'+ct.market_price_format+'</b></div></li>';
        });
        });
        render = render.replace(new RegExp("/upyun/","g"),"http://pic.0750.tuanzhang.cc/");
        if(render != "")
        {
            render += "<li class=\"tips\"><a href=\"javascript:ClearHistory();\">清空浏览历史</a></li>";
            render += "</ul>";
        }
        $(".history .showmenu").html(render);
        $(".history").attr("data-is_loaded",true);
    }
    else
        $(".history .showmenu").html("<ul><li class=\"tips\">尚未有浏览记录，<a href=\"/http://www.0750.tuanzhang.cc/index.php?m=Goods&amp;a=new\">去逛逛</a></li></ul>");
}

function ShowHistoryHtml(data)
{
    //var data = GetHistory();
    if(data != null && data != "")
    {
        var render = "";
        $.each(data,function(index, val){
            // render += "<ul>";
        $.each(val,function(index2, ct){
            render += '<div class="box"><ul class="right-history"><li><div class="pi"><a href="'+ct.url+'" target="_blank"><img src="'+ct.small_img+'" width="65" height="41" /></a></div><div class="ti"><a href="'+ct.url+'" target="_blank" title="'+ct.name_1+'"><span>['+ct.quan_name+']</span>'+ct.short+'</a></div><div class="pr"><b class="fee red">'+ct.shop_price_format+'</b><b class="exfee">'+ct.market_price_format+'</b></div></li></ul></div>';
        });
        });
        render = render.replace(new RegExp("/upyun/","g"),"http://pic.0750.tuanzhang.cc/");
        if(render != "")
        {
            // render += "<li class=\"tips\"><a href=\"javascript:ClearHistory();\">清空浏览历史</a></li>";
            // render += "</ul>";
        }
        $("#history .list").html(render);
        $("#history").attr("data-is_loaded",true);
    }
    else
        $(".history .showmenu").html("<ul><li class=\"tips\">尚未有浏览记录，<a href=\"/http://www.0750.tuanzhang.cc/index.php?m=Goods&amp;a=new\">去逛逛</a></li></ul>");
}

function ClearHistory()
{
    if(confirm("您确定要清除浏览记录吗？"))
    {
        $.cookie('RecentlyGoods','');
        $(".history .showmenu,#history .list").html("<div class='box'><ul><li class='tips'>尚未有浏览记录，<a href='/index.php'>去逛逛</a></li></ul></div>");
    }
}

function i_clearHistory()
{
    // $.cookie('0750t_h','');
    $("#history .list").html("<div class='box'><ul><li class='tips'>尚未有浏览记录，<a href='/index.php'>去逛逛</a></li></ul></div>");
    ClearHistory();
}

function GetHistory() {
	var historyp = $.cookie('0750t_h');
	if (historyp != null) {
		//ajax 根据goods_id获取信息列表
		$.ajax({   
			type: "POST",
			url: "index.php?m=Ajax&a=gethistory",
			dataType: 'json',
			data: "goods_id=" + historyp,
			success: function(data) {
                if(data)
                {
                    GetHistoryHtml(data);
                    //$(".history .showhistory .list").html(render).siblings(".remove").show();
                }
			}
		});
	}
}

function ShowHistory() {
	var historyp = $.cookie('0750t_h');
	if (historyp != null) {
		//ajax 根据goods_id获取信息列表
		$.ajax({   
			type: "POST",
			url: "index.php?m=Ajax&a=gethistory",
			dataType: 'json',
			data: "goods_id=" + historyp,
			success: function(data) {
                if(data)
                {
                    ShowHistoryHtml(data);
                    //$(".history .showhistory .list").html(render).siblings(".remove").show();
                }
			}
		});
	}
}

// $(function(){
    // var htout = "";
    // $(".history").hover(function(){
        // clearTimeout(htout);
        // if($(this).attr("data-is_loaded"))
        // {
            // $(this).find(".showhistory").show();
        // }
        // else
        // {
            // GetHistory();
            // $(this).find(".showhistory").show();
        // }
    // },function(){
        // var _this = $(this);
        // htout = setTimeout(function(){_this.find(".showhistory").hide();},200);
    // });
    // $(".history .showhistory .cross").click(function(){$(this).parent().hide();});
    // $(".history .showhistory .remove a").click(function(){ClearHistory();return false;});
// });