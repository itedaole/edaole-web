/*
 *	----------------------
 *	innerScript
 *	----------------------
 *	http://www.0750.tuanzhang.cc 
 *	by Gavin Lin (林嘉文)
 *	version 1.0
 *	2014/02/28
 *
 */

$(".topbar ul li").hover(function() {
    $(".showmenu").hide(), $(this).find("a.dot").addClass("dotactive"), $(this).find(".showmenu").show();
}, function() {
    $("a.dot").removeClass("dotactive"), $(".showmenu").hide();
});

$(".search .select a.tg ,.search .select a.sj").click(function() {
    // $(this).siblings("a").removeClass("focus"), $(this).addClass("focus"), $(".search .sp input.tg").toggle(), 
    // $(".search .sp input.sj").toggle();
    var is_changed = $(this).hasClass("focus") ? false : true;
    $(this).addClass("focus").siblings("a").removeClass("focus");
    var i = $(this).parent("div.select").find("a").index(this);
    var input = $(".search .sp input[name=keywords]");
    var i_m = $(".search .sb input[name=m]");
    var i_a = $(".search .sb input[name=a]");
    if(i == 0)
    {
        input.attr("placeholder","请输入要搜索的团购名称");
        i_m.val("Goods");
        i_a.val("showall");
    }
    else if(i == 1)
    {
        input.attr("placeholder","请输入要搜索的商家名称");
        i_m.val("Supplier");
        i_a.val("list");
    }
    if(input.val() != "" && is_changed)
        $(".search #search_form").submit();
});

$(".tl ul li").hover(function() {
    $(this).addClass("tl" + ($(this).index() + 1) + "h"), $(this).addClass("bh"), $(this).find("a.n").addClass("nhover"), 
    $(".tl ul li .showmenu").hide(), $(this).find(".showmenu").show();
}, function() {
    $(this).removeClass("tl" + ($(this).index() + 1) + "h"), $(this).removeClass("bh"), 
    $(this).find("a.n").removeClass("nhover");
}), $(".ptl .tl").hover(function() {}, function() {
    $(".tl ul li .showmenu").hide();
});

$(".history").hover(function() {
    // if(!$(this).attr("data-is_loaded"))
    // {
        // GetHistory();
    // }
    $(this).find("a:eq(0)").addClass("hover"), $(this).find(".showmenu").show();
}, function() {
    var _this = $(this);
    _this.find("a:eq(0)").removeClass("hover"), _this.find(".showmenu").hide();
});

panelHover();
function panelHover()
{
    $(".panel").undelegate(".block","mouseenter").undelegate(".block","mouseleave").delegate(".block","mouseenter",function(){
        $(this).addClass("blockhover").find(".title").find("a").addClass("hover");
        var c_count_obj = $(this).find(".click_count");
        var goods_id = c_count_obj.attr("data-goods_id");
        //getGoodsClickCount(goods_id,c_count_obj);
        $(this).find('.blocktips').show();
    }).delegate(".block","mouseleave",function(){
        $(this).removeClass("blockhover").find(".title").find("a").removeClass("hover");
        $(this).find('.blocktips').hide();
    });
    // $(".panel .block").hover(function() {
        // $(this).addClass("blockhover"), $(this).children(".title").find("a").addClass("hover");
    // }, function() {
        // $(this).removeClass("blockhover"), $(this).children(".title").find("a").removeClass("hover");
    // });
    $(".content .panel").each(function() {
        $(this).find(".piece").height(parseInt($(this).height() - 19));
    });
}

function getGoodsClickCount(goods_id, obj)
{
    var is_loaded = obj.attr("data-is_loaded") ? true : false;
    var up_time = obj.attr("data-up_time") > 0 ? parseInt(obj.attr("data-up_time")) : 0;
    var now = (Date.parse(new Date()))/1000;
    if(goods_id > 0 && (!is_loaded || now - up_time >= 60))
    {
        $.ajax({
            url : ROOT_PATH+"index.php?m=Ajax&a=getGoodsClickCount",
            type : "POST",
            data : {id:goods_id},
            dataType : "JSON",
            cache : false,
            success : function(data)
            {
                if(data)
                {
                    if(data.status == 1)
                    {
                        obj.html(data.data);
                        obj.attr("data-is_loaded",true);
                        obj.attr("data-up_time",data.time);
                    }
                }
            }
        });
    }
}

$(".rp .fix ul li").hover(function() {
    $(".rp .fix ul li i.tips").stop(!0, !0), $(this).find("i.tips").fadeIn(300);
}, function() {
    $(".rp .fix ul li i.tips").stop(!0, !0), $(this).find("i.tips").fadeOut(200);
});

$(function() {
    $(window).scroll(function() {
        if ("undefined" == typeof document.body.style.maxHeight) ; else {
            var a = $(this).scrollTop();
            a>260&&500>=a?($(".app_download_tip .fix").addClass("fixed"),$(".app_download_tip .fix").removeClass("move")):a>500?$(".app_download_tip .fix").addClass("fixed move"):$(".app_download_tip .fix").removeClass("fixed move");
            a > 200 ? ($(".btt").stop(!0, !0), $(".btt").fadeIn(500)) :($(".btt").stop(!0, !0), 
            $(".btt").fadeOut(500)), a > 500 ? ($(".sidebar .fix").addClass("fixed"), $(".sidebar").stop(!0, !0), 
            $(".sidebar").fadeIn(500)) :($(".sidebar .fix").removeClass("fixed"), $(".sidebar").stop(!0, !0), 
            $(".sidebar").fadeOut(50));
        }
    });
});
