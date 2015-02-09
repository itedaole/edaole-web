//添加收藏夹
function AddFavorite() {
    try {
        window.external.addFavorite('http://www.0750.tuanzhang.cc', '0750团-江门人自己的团购网');
    } catch (e) {
        try {
            window.sidebar.addPanel('0750团-江门人自己的团购', 'http://www.0750.tuanzhang.cc', "");
        } catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}

    // 评论跳转页面
    function goToPage(page, is_supplier)
    {
        if(parseInt(page) > 0)
        {
            var c_list = $('#user-reviews-c');
            var goods_id = c_list.attr("data-goods_id");
            var is_content = $("#is_content").attr("checked");
            var order = $(".user-reviews-st a.focus").attr("data-order");
            var _supplier = 0;
            if(is_supplier > 0)
            {
                goods_id = c_list.attr("data-supplier_id");
                _supplier = 1;
            }
            if(goods_id > 0)
            {
                $.ajax({
                    url : "index.php?m=Ajax&a=comment_gotopage",
                    type : "POST",
                    data : {goods_id:goods_id,is_content:is_content,order:order,page:page,is_supplier:_supplier},
                    dataType : "JSON",
                    cache : false,
                    success : function(data)
                    {
                        if(data)
                        {
                            if(data.status == 1)
                            {
                                if(data.html)
                                {
                                    c_list.html(data.html);
                                    showStar();
                                    //window.location.hash = "user-reviews-st";
                                }
                                if(!data.html && page == 1)
                                    c_list.html('');
                            }
                            else
                                alert(data.msg);
                        }
                    }
                });
            }
        }
    }
    $('.user-reviews-st a.orderby').click(function(){
        if(!$(this).hasClass("focus"))
        {
            $(this).addClass("focus").siblings().removeClass("focus");
            var is_supplier = $("#user-reviews-c").attr("data-supplier_id") > 0 ? 1 : 0;
            goToPage(1,is_supplier);
        }
    });
    $('#is_content').click(function(){
        var is_supplier = $("#user-reviews-c").attr("data-supplier_id") > 0 ? 1 : 0;
        goToPage(1,is_supplier);
    });

    // 发送手机验证码
	function sms_mobile_verify_click(_obj, _this){
        var obj = $("#"+_obj);
        _this = $(_this);
        var this_id = _this.attr("id");
        if(obj.length > 0)
        {
            var mobile = $.trim(obj.val());
            if(!$.checkMobilePhone(mobile))
            {
                $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                obj.focus();
                return false;
            }
            
            var query = new Object();
            query.run = "sms_mobile_verify";
            query.mobile = mobile;
            $.ajax({
                url: "services/ajax.php",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function()
                {
                    _this.attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                        _this.removeAttr("disabled");
                    }
                    else if(data.type == 3)
                    {
                        $.showErr('请1分钟后再试!');
                        _this.removeAttr("disabled");
                    }
                    else
                    {
                        // alert('短信已经发送,请查收!');
                        timedCount(this_id,100);
                    }
                    return false;
                }
            });
        }
		return false;
	}
    
    // 发送注册手机验证码
	function reg_mobile_verify_click(_obj, _this){
        var obj = $("#"+_obj);
        _this = $(_this);
        var this_id = _this.attr("id");
        if(obj.length > 0)
        {
            var mobile = $.trim(obj.val());
            if(!$.checkMobilePhone(mobile))
            {
                $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                obj.focus();
                return false;
            }
            
            var query = new Object();
			query.code=$("#cn_code").val();
			if(!query.code||query.code=="")
			{
				$.showErr("请输入图片中的验证码！");
                obj.focus();
                return false;
			}
            query.run = "reg_mobile_verify";
            query.mobile = mobile;
			
            $.ajax({
                url: "services/ajax.php",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function()
                {
                    _this.attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                        _this.removeAttr("disabled");
                        refleshcode('img_code');
                    }
                    else if(data.type == 3)
                    {
                        $.showErr('请1分钟后再试!');
                        _this.removeAttr("disabled");
                    }
                    else
                    {
                        // alert('短信已经发送,请查收!');
                        timedCount(this_id,100);
                    }
                    return false;
                }
            });
        }
		return false;
	}
    
    // 手机未验证，发送绑定手机验证码
	function bind_mobile_verify_click(_obj, _this, user_id){
        var obj = $("#"+_obj);
        _this = $(_this);
        var this_id = _this.attr("id");
        if(obj.length > 0)
        {
            var mobile = $.trim(obj.val());
            // if(!$.checkMobilePhone(mobile))
            // {
                // $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                // obj.focus();
                // return false;
            // }
            if(!(user_id > 0))
            {
                alert("用户不存在，请重新登录。");
                window.location.href = "index.php?m=User&a=login";
                //obj.focus();
                return false;
            }
            
            var query = new Object();
            query.run = "bind_mobile_verify";
            query.mobile = mobile;
            query.user_id = user_id;
            $.ajax({
                url: "services/ajax.php",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function()
                {
                    _this.attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                        _this.removeAttr("disabled");
                    }
                    else if(data.type == 3)
                    {
                        $.showErr('请1分钟后再试!');
                        _this.removeAttr("disabled");
                    }
                    else
                    {
                        // alert('短信已经发送,请查收!');
                        timedCount(this_id,100);
                    }
                    return false;
                }
            });
        }
		return false;
	}
    
    // 发送重置密码手机验证码
	function reset_mobile_verify_click(_obj, _this){
        var obj = $("#"+_obj);
        _this = $(_this);
        var this_id = _this.attr("id");
        if(obj.length > 0)
        {
            var mobile = $.trim(obj.val());
            if(!$.checkMobilePhone(mobile))
            {
                $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                obj.focus();
                return false;
            }
            
            var query = new Object();
            query.run = "reset_mobile_verify";
            query.mobile = mobile;
            $.ajax({
                url: "services/ajax.php",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function()
                {
                    _this.attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                        _this.removeAttr("disabled");
                    }
                    else if(data.type == 3)
                    {
                        $.showErr('请1分钟后再试!');
                        _this.removeAttr("disabled");
                    }
                    else
                    {
                        // alert('短信已经发送,请查收!');
                        timedCount(this_id,100);
                    }
                    return false;
                }
            });
        }
		return false;
	}
    
    function bind_mobile()
    {
        var form = $("#mobile-bind-form");
        if(form.length > 0)
        {
            //var mobile = form.find("#settings-mobile");
            var user_id = form.find("#user_id");
            var mobile_verify = form.find("#mobile_verify");
            // if($.trim(mobile.val()) == "")
            // {
                // $.showErr("请输入11位手机号码。");
                // mobile.focus();
                // return false;
            // }
            // if(!$.checkMobilePhone($.trim(mobile.val())))
            // {
                // $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                // mobile.focus();
                // return false;
            // }
            if(!(user_id.val() > 0))
            {
                $.showErr("用户不存在。");
                //mobile.focus();
                return false;
            }
            if($.trim(mobile_verify.val()) == "")
            {
                $.showErr("请输手机验证码。");
                mobile_verify.focus();
                return false;
            }
            
            var query = new Object();
            //query.run = "sms_mobile_verify";
            query.user_id = $.trim(user_id.val());
            query.mobile_verify = $.trim(mobile_verify.val());
            $.ajax({
                url: "index.php?m=ajax&a=bind_mobile",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function(){
                    form.find("input[type='submit']").attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                    }
                    if(data.type == -1)
                    {
                        $.showErr(data.message);
                        //window.location.href = "index.php?m=User&a=login";
                    }
                    else if(data.type == 1)
                    {
                        alert('手机绑定成功，请重新登录。');
                        window.location.href = "index.php?m=User&a=login";
                    }
                    else if(data.type == 2)
                    {
                        alert(data.message);
                        window.location.href = window.location.href;
                    }
                    form.find("input[type='submit']").removeAttr("disabled");
                }
            });
        }
        return false;
    }
    
    function check_mobile()
    {
        var form = $("#mobile-check-form");
        if(form.length > 0)
        {
            var mobile = form.find("#settings-mobile");
            var mobile_verify = form.find("#mobile_verify");
            if($.trim(mobile.val()) == "")
            {
                $.showErr("请输入11位手机号码。");
                mobile.focus();
                return false;
            }
            if(!$.checkMobilePhone($.trim(mobile.val())))
            {
                $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                mobile.focus();
                return false;
            }
            if($.trim(mobile_verify.val()) == "")
            {
                $.showErr("请输手机验证码。");
                mobile_verify.focus();
                return false;
            }
            
            var query = new Object();
            query.run = "sms_mobile_verify";
            query.mobile = $.trim(mobile.val());
            query.mobile_verify = $.trim(mobile_verify.val());
            $.ajax({
                url: "index.php?m=ajax&a=check_mobile",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function(){
                    form.find("input[type='submit']").attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                    }
                    if(data.type == -1)
                    {
                        $.showErr(data.message);
                        window.location.href = "index.php?m=User&a=login";
                    }
                    else if(data.type == 1)
                    {
                        alert('手机绑定成功。');
                        window.location.href = window.location.href;
                    }
                    else if(data.type == 2)
                    {
                        alert(data.message);
                        window.location.href = window.location.href;
                    }
                    form.find("input[type='submit']").removeAttr("disabled");
                }
            });
        }
        return false;
    }
    
    function check_mobile_v2()
    {
        var form = $("#mobile-check-form");
        if(form.length > 0)
        {
            var mobile = form.find("#settings-mobile");
            if($.trim(mobile.val()) == "")
            {
                $.showErr("请输入11位手机号码。");
                mobile.focus();
                return false;
            }
            if(!$.checkMobilePhone($.trim(mobile.val())))
            {
                $.showErr(LANG.JS_ELEVEN_MOBILE_EMPTY);
                mobile.focus();
                return false;
            }
            
            var query = new Object();
            query.run = "sms_mobile_verify";
            query.mobile = $.trim(mobile.val());
            $.ajax({
                url: "index.php?m=ajax&a=check_mobile_v2",
                data:query,
                cache:false,
                dataType:"json",
                beforeSend:function(){
                    form.find("input[type='submit']").attr("disabled","disabled");
                },
                success:function(data)
                {
                    if(data.type == 0)
                    {
                        $.showErr(data.message);
                    }
                    if(data.type == -1)
                    {
                        $.showErr(data.message);
                        window.location.href = "index.php?m=User&a=login";
                    }
                    else if(data.type == 1)
                    {
                        alert('手机设置成功。');
                        window.location.href = window.location.href;
                    }
                    else if(data.type == 2)
                    {
                        alert(data.message);
                        window.location.href = window.location.href;
                    }
                    form.find("input[type='submit']").removeAttr("disabled");
                }
            });
        }
        return false;
    }
    
    var _tc;
    function timedCount(obj,time){
        var _obj = $("#"+obj);
        if(_obj.length > 0)
        {
            //var Time=3,    t;
            var c=time;
            _obj.attr("disabled","disabled").val((c-1)+"秒后重新发送");
            c--;
            _tc=setTimeout(function(){timedCount(obj,c);},1000);
            if(c<0){
                c=time;
                clearTimeout(_tc);
                _obj.val("获取验证码").removeAttr("disabled");
            }
        }
    }
//刷新验证在码 20140504
function refleshcode(id)
{
	var d=new Date();
	$('#'+id).val('');
	$('#'+id).attr('src',HTTP_URL +'/index.php?m=Ajax&a=verify&rand='+d.getTime());
}

(function($){

	$.getStringLength=function(str)
	{
		str = $.trim(str);

		if(str=="")
			return 0;

		var length=0;
		for(var i=0;i <str.length;i++)
		{
			if(str.charCodeAt(i)>255)
				length+=2;
			else
				length++;
		}

		return length;
	}

	$.getLengthString=function(str,length,isSpace)
	{
		if(arguments.length < 3)
			var isSpace = true;

		if($.trim(str)=="")
			return "";

		var tempStr="";
		var strLength = 0;

		for(var i=0;i <str.length;i++)
		{
			if(str.charCodeAt(i)>255)
				strLength+=2;
			else
			{
				if(str.charAt(i) == " ")
				{
					if(	isSpace)
						strLength++;
				}
				else
					strLength++;
			}

			if(length >= strLength)
				tempStr += str.charAt(i);
		}

		return tempStr;
	}

	$.getBodyScrollTop=function(){
        var scrollPos;
        if (typeof window.pageYOffset != 'undefined') {
            scrollPos = window.pageYOffset;
        }
        else if (typeof document.compatMode != 'undefined' &&
            document.compatMode != 'BackCompat') {
            scrollPos = document.documentElement.scrollTop;
        }
        else if (typeof document.body != 'undefined') {
            scrollPos = document.body.scrollTop;
        }
        return scrollPos;
    }

	$.copyText = function(id)
	{
		var txt = $(id).val();
		if(window.clipboardData)
		{
			window.clipboardData.clearData();
			var judge = window.clipboardData.setData("Text", txt);
			if(judge === true)
				alert(LANG.JS_COPY_SUCCESS);
			else
				alert(LANG.JS_COPY_NOT_SUCCESS);
		}
		else if(navigator.userAgent.indexOf("Opera") != -1)
		{
			window.location = txt;
		}
		else if (window.netscape)
		{
			try
			{
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			}
			catch(e)
			{
				alert(LANG.JS_NO_ALLOW);
			}
			var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
			if (!clip)
				return;
			var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
			if (!trans)
				return;
			trans.addDataFlavor('text/unicode');
			var str = new Object();
			var len = new Object();
			var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
			var copytext = txt;
			str.data = copytext;
			trans.setTransferData("text/unicode",str,copytext.length*2);
			var clipid = Components.interfaces.nsIClipboard;
			if (!clip)
				return false;
			clip.setData(trans,null,clipid.kGlobalClipboard);
			alert(LANG.JS_COPY_SUCCESS);
		}
	};
	
	/// 修改标记 开始
	/// 2013-04-23
	$.copyTextV2 = function(txt)
	{
		//var txt = obj.val();
		if(window.clipboardData)
		{
			window.clipboardData.clearData();
			var judge = window.clipboardData.setData("Text", txt);
			if(judge === true)
				alert(LANG.JS_COPY_SUCCESS);
			else
				alert(LANG.JS_COPY_NOT_SUCCESS);
		}
		else if(navigator.userAgent.indexOf("Opera") != -1)
		{
			window.location = txt;
		}
		else if (window.netscape)
		{
			try
			{
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			}
			catch(e)
			{
				alert(LANG.JS_NO_ALLOW);
			}
			var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
			if (!clip)
				return;
			var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
			if (!trans)
				return;
			trans.addDataFlavor('text/unicode');
			var str = new Object();
			var len = new Object();
			var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
			var copytext = txt;
			str.data = copytext;
			trans.setTransferData("text/unicode",str,copytext.length*2);
			var clipid = Components.interfaces.nsIClipboard;
			if (!clip)
				return false;
			clip.setData(trans,null,clipid.kGlobalClipboard);
			alert(LANG.JS_COPY_SUCCESS);
		}
	};
	/// 修改标记 结束
    
    // if(t_offset == undefined){var t_offset = 0;}
	// $(window).scroll(function(){
		// if($("#sysmsg-error") != "none" || $("#sysmsg-success") != "none")
		// {
			// var top = $.getBodyScrollTop();
            // var d_top = 157 + t_offset;
			// if(top < d_top)
				// top = d_top;
			// $("#sysmsg-error-box").stop();
			// $("#sysmsg-error-box").animate({"top":top},{duration:300});
		// }
	// });

	$.showErr = function(str)
	{
        alert(str);
		// var top = $.getBodyScrollTop();
        // var d_top = 157 + t_offset;
		// if(top < d_top)
			// top = d_top;
		// $("#sysmsg-error-box").css({"top":top});
		// $("#sysmsg-error span:first").html(str);
		// $("#sysmsg-error").show();
		// $("#sysmsg-success").hide();
		// $("#sysmsg-error-box").show();

		// clearTimeout(errHideTimeOut);

		// var hideErr = function(){
			// $("#sysmsg-error-box").slideUp(300);
		// };

		// errHideTimeOut = setTimeout(hideErr,5000);

		// $("#sysmsg-error-box .close").one("click", function(){
			// $("#sysmsg-error-box").hide();
		// });
	}

	$.ShowDialog=function(option,toppix)
	{
		if(toppix==null) toppix = 120;
		option = $.extend({
			dialog:null,
			html:null,
			closeFun:null,
			closeHandler:null
		}, option || {});

		var bgDiv= $("<div class='mask'></div>");
        var pos_bg = $("<div class='pos_bg'></div>");
        var selfObj=$("."+option.dialog);
        if(selfObj.length==0)
        {
            $("body").append(option.html);
            selfObj=$("."+option.dialog);
        }
        else if(option.html != "" && option.html != undefined)
        {
            selfObj.find(".init").html(option.html);
        }
        if(selfObj.find(".mask").length > 0)
            $bgDiv = selfObj.find(".mask");
        else
            selfObj.append(bgDiv);
        
        if(selfObj.find(".pos_bg").length > 0)
            $pos_bg = selfObj.find(".pos_bg");
        else
            selfObj.append(pos_bg);
        $('html').addClass('hash');
        // if ($.browser.msie && ($.browser.version == "6.0"))
        // $(bgDiv).css({position:"absolute",width:$(document).width(), height:$(document).height(),top:"0",left:"0",opacity:0.3,background:"#000",display:"none","z-index":100});
        // else
        // $(bgDiv).css({position:"fixed",width:"100%", height:"100%",top:"0",left:"0",opacity:0.3,background:"#000",display:"none","z-index":100});
        // $(bgDiv).attr('id','__back_ground_div');
        // if ($.browser.msie && ($.browser.version == "6.0"))
        // {
		// //selfObj.bgiframe();
		// //$(bgDiv).bgiframe();
        // }

        selfObj.show();
        // $(bgDiv).show();
        _selfObj = selfObj.find(".pos");
		$.windowCenter(_selfObj,toppix,pos_bg);

		var __closeHandler=function(){
            // $(bgDiv).remove();
            $('html').removeClass('hash');
            selfObj.css({display:"none"});
			if(option.closeFun)
               option.closeFun.call(this);
        }
		$(".close",selfObj).click(__closeHandler);
		option.closeHandler=__closeHandler;
        
        // $(window).scroll(function(){
            // if ($.browser.msie && ($.browser.version == "6.0"))
            // {
                // if(selfObj.css("display") != "none")
                // {
                    // $.windowCenter(_selfObj,toppix);
                    // // if ($.browser.msie && ($.browser.version == "6.0"))
                        // // $(bgDiv).css({width:$(document).width(), height:$(document).height()});
                // }
            // }
        // });
		$(window).resize(function(){
			if(selfObj.css("display") != "none")
			{
				$.windowCenter(_selfObj,toppix,pos_bg);
                // if ($.browser.msie && ($.browser.version == "6.0"))
                    // $(bgDiv).css({width:$(document).width(), height:$(document).height()});
			}
		});
	}

	$.windowCenter=function(obj,toppix,obj_bg)
	{
		if(toppix==null) toppix = 120;
		var windowWidth=$.support.opacity ? window.innerWidth : document.documentElement.clientWidth;
		var windowHeight=$.support.opacity ? window.innerHeight : document.documentElement.clientHeight;
		// var windowWidth=$(document).width();
		// var windowHeight=$(document).height();
		var objWidth=obj.width();
		var objHeight=obj.height();
        // var objTop=(windowHeight/2)-(objHeight/2);
        var objTop=(objHeight/2);
		var objLeft=(windowWidth - objWidth ) / 2;
        var d_height = obj.find(".pos").height();
        var s_height = 0;
        if(obj.find(".pos").length > 0)
            s_height = obj.find(".pos")[0].clientHeight;
        else
            s_height = obj.clientHeight;
        // alert(s_height);
        if(s_height > d_height){d_height = s_height;}
        var c_height = objHeight - s_height;
        
        if(d_height > (windowHeight-c_height-50) && (windowHeight-c_height-50) <= s_height)
        {
            obj.find(".pos").css({overflow:"auto",height:windowHeight-c_height-50});
        }
        else
        {
            obj.find(".pos").css({height:"auto"});
        }
        // if(d_height > toppix || windowHeight < (d_height+toppix))
            // objTop=(windowHeight/2)-(obj.height()/2);
        // else
            // objTop=toppix;
        
        // if ($.browser.msie && ($.browser.version == "6.0"))
        // {
            // objTop=objTop + $.getBodyScrollTop();
            // obj.css({position:"absolute",display:"block","z-index":1000,top:objTop,left:objLeft});
        // }
        // else
            // obj.css({position:"fixed",display:"block","z-index":1000,top:objTop,left:objLeft});
        obj.css({
        "margin-top":-objTop
        // ,"margin-left":-objLeft
        });
        obj_bg.css({"height":objHeight,"margin-top":-(objTop+10)});
	}

	$.minLength = function(value, length , isByte) {
		var strLength = $.trim(value).length;
		if(isByte)
			strLength = $.getStringLength(value);

		return strLength >= length;
	};

	$.maxLength = function(value, length , isByte) {
		var strLength = $.trim(value).length;
		if(isByte)
			strLength = $.getStringLength(value);

		return strLength <= length;
	};

	$.rangeLength = function(value, minLength,maxLength, isByte) {
		var strLength = $.trim(value).length;
		if(isByte)
			strLength = $.getStringLength(value);

		return length >= minLength && length <= maxLength;
	}

	$.checkMobilePhone = function(value){
		return /^(13\d{9}|18\d{9}|14\d{9}|15\d{9})$/i.test($.trim(value));
	}
    
	$.checkUserName = function(value){
		return /^[a-zA-Z_\u4e00-\u9fa5]/.test($.trim(value));
	}
    
	$.checkPassword = function(value){
		// return /^[a-zA-Z0-9]/.test($.trim(value));
		// return /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[~!@#$%^&*()_+`\-={}\[\]:";'<>?,.\/]).{4,32}$/.test($.trim(value));
        if(/(^\s+)|(\s+$)/g.test(value))
            return false;
        else
            return /^(?=.*[a-zA-Z0-9~!@#$%^&*()_+`\-={}\[\]:";'<>?,.\/]).{4,32}$/.test(value);
	}

	$.checkPhone = function(val){
  		var flag = 0;
		val = $.trim(val);
  		var num = ".0123456789/-()";
  		for(var i = 0; i < (val.length); i++)
		{
    		tmp = val.substring(i, i + 1);
    		if(num.indexOf(tmp) < 0)
      			flag++;
 		}
  		if(flag > 0)
			return true;
		else
			return false;
	}

	$.checkEmail = function(val){
		var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
		return reg.test(val);
	};

})(jQuery);

jQuery(function($){
    // 账户充值
	$("#incharge-form").submit(function(){

		var money = $(this).find("#money").val();
		var moneyreg=/^[0-9]+([.]{1}[0-9]{1,2})?$/;
		if(money==''||isNaN(money)||parseFloat(money)<=0||!moneyreg.test(money))
		{
			$.showErr(LANG.JS_MONEY_EMPTY);
			$("#money").focus();
			return false;
		}
		if($(this).find("input:checked").length==0)
		{
			$.showErr(LANG.JS_SELECT_PAYMENT);
			return false;
		}
	});
    
    $("select#ecv_sn").change(function(){
        if($(this).val() != "")
        {
            $("#cardcode-sn").val($(this).val());
            $("#cardcode-pwd").val('');
            $("#cardcode-verify").click();
        }
        else
        {
            reset_ecv();
            countCartTotal();
        }
    });
    
    function reset_ecv()
    {
        // $(".ecvinfo p span").eq(0).html('');
        // $(".ecvinfo p span").eq(1).html('');
        // $(".ecvinfo p span").eq(2).html('');
        // $(".ecvinfo p span").eq(3).html('');
        $(".ecvinfo td b").html('');
        $("#cardcode-pwd").val('');
        $("#cardcode-sn").val('');						
    }
    
	$("#is-credit-all").bind("click",function(){
		if(!$(this).attr("checked"))
		{
			$("#credit-text").val("0");
		}
		countCartTotal();
	});
    // add by 关池顺 2013-10-10 佣金支付
	$("#is-rebate-all").bind("click",function(){
		if(!$(this).attr("checked"))
		{
			$("#credit-text").val("0");
			$("#rebate-text").val("0");
		}
		countCartTotal();
	});
    
	$("#score-btn").bind("click",function(){
        var score = parseInt($("#score-text").val());
        var available_score = parseInt($("#available_score").html());
		if(score > available_score)
		{
			$("#score-text").val(available_score);
		}
		countCartTotal();
	});
    
	$("#cardcode-verify").click(function(){
		var sn = $.trim($("#cardcode-sn").val());
		var password = $.trim($("#cardcode-pwd").val());
        var _this = $(this);
		if(sn.length == 0)
		{
			$.showErr(LANG.JS_BONUS_SN_EMPTY);
			$("#cardcode-sn").focus();
			return false;
		}
        
        var $payment_id = 0;
        if($("input[name='payment']:checked").length > 0)
            $payment_id = $("input[name='payment']:checked").val();
        
		$.ajax({
			  url: ROOT_PATH+"/index.php?m=Ajax&a=ecvVerify&sn="+sn+"&pwd="+password+"&payment_id="+$payment_id,
			  cache: false,
			  type: "POST",
			  dataType: "json",
			  success:function(data)
			  {
			 		if(data.type == 0)
					{
						$.showErr(data.msg);
						// $(".ecvinfo").removeClass('ok');
						$(".ecvinfo td b").eq(0).html('');
						$(".ecvinfo td b").eq(1).html('');
						$(".ecvinfo td b").eq(2).html('');
						$(".ecvinfo td b").eq(3).html('');
						$("#cardcode-pwd").val('');
						$("#cardcode-sn").val('');
                        $("#ecv_sn option[value='']").attr("selected","selected");
					}
					else
					{
						// $(".ecvinfo td b").eq(0).html(data.ecv.ecvType.name);
						$(".ecvinfo td b").eq(0).html(data.ecv.ecvType.name);
						$(".ecvinfo td b").eq(1).html(data.ecv.use_end_date);
						$(".ecvinfo td b").eq(2).html(data.ecv.money);
						// $(".ecvinfo td b").eq(2).html(data.ecv.use_end_date);
						$(".ecvinput").addClass('act');
						$(".ecvinfo").addClass('ok').removeClass('act');
                        setTimeout(function(){if(_this.siblings(".close").length> 0){_this.siblings(".close").click();}},500);
					}

					countCartTotal();
			  }
		});
		return false;
	});
    
	$("#credit-text").keydown(function(event){
		var event=event?event:window.event;
		var k=event.keyCode;
		if(!(k==8 || k==9 || k==13 || k==16 || k>=33 && k<=40 || k==45 || k==46 || k>=48 && k<=57 || k>=96 && k<=105 || k==190))
		{
			return false;
		}
	}).blur(function(){
		var money = getRoundFloat(this.value);
		maxMoney = getRoundFloat(maxMoney.toString());
		totalPrice = getRoundFloat(totalPrice.toString());

		if(money > maxMoney)
		{
			$.showErr(LANG.JS_MONEY_NO_LT+LANG.JS_PP+maxMoney+LANG.JS_LIMIT_5);
			$(this).val(maxMoney);
		}
		else
		{
			$(this).val(money);
		}
		countCartTotal();
	});
    
    // add by 关池顺 2013-10-10 佣金支付
	$("#rebate-text").keydown(function(event){
		var event=event?event:window.event;
		var k=event.keyCode;
		if(!(k==8 || k==9 || k==13 || k==16 || k>=33 && k<=40 || k==45 || k==46 || k>=48 && k<=57 || k>=96 && k<=105 || k==190))
		{
			return false;
		}
	}).blur(function(){
		var money = getRoundFloat(this.value);
		maxRebateMoney = getRoundFloat(maxRebateMoney.toString());
		totalPrice = getRoundFloat(totalPrice.toString());

		if(money > maxRebateMoney)
		{
			$.showErr("输入的金额不能大于你的佣金金额￥"+maxRebateMoney+"。");
			$(this).val(maxRebateMoney);
		}
		else
		{
            var credit_money = $("#credit-text").val();
            if(money > parseFloat(_totalPrice - credit_money).toFixed(2))
            {
                var r = parseFloat(_totalPrice - money).toFixed(2);
                if (r > 0)
                    $("#credit-text").val(r);
                else
                    $("#credit-text").val(0);
            }
            $(this).val(money);
		}
		countCartTotal();
	});

	$("#order_done").click(function(){
		var ret=true;
		if(is_smzq == 0 && goodsType == 1 && totalPrice >= 0)
		{
			if($.trim($("#delivery-consignee").val()).length == 0&&$("input[name='delivery_refer_order_id']:checked").length == 0)
			{
				$.showErr(LANG.JS_CONSIGNEE_NAME_ENPTY);
				return false;
			}

			if($("#region_lv1_0").val() == 0&&$("input[name='delivery_refer_order_id']:checked").length==0)
			{
				$.showErr(LANG.JS_SELECT_COUNTRT);
				return false;
			}
			else
			{
				if($("#region_lv2_0 option").length > 0&&$("input[name='delivery_refer_order_id']:checked").length==0)
				{
					if($("#region_lv2_0").val() == 0&&$("input[name='delivery_refer_order_id']:checked").length == 0)
					{
						$.showErr(LANG.JS_PROVINCE);
						return false;
					}
					else
					{
						if($("#region_lv3_0 option").length > 0&&$("input[name='delivery_refer_order_id']:checked").length==0)
						{
							if($("#region_lv3_0").val() == 0&&$("input[name='delivery_refer_order_id']:checked").length == 0)
							{
								$.showErr(LANG.JS_CITY);
								return false;
							}
							else
							{
								if($("#region_lv4_0 option").length > 0&&$("input[name='delivery_refer_order_id']:checked").length==0)
								{
									if($("#region_lv4_0").val() == 0&&$("input[name='delivery_refer_order_id']:checked").length == 0)
									{
										$.showErr(LANG.JS_AREA);
										return false;
									}
								}
							}
						}
					}
				}
			}

			if($.trim($("#delivery-address").val()).length < 5&&$("input[name='delivery_refer_order_id']:checked").length == 0)
			{
				$.showErr(LANG.JS_ADDRESS_NOT_NULL);
				return false;
			}

			if($.trim($("#delivery-zip").val()).length == 0&&$("input[name='delivery_refer_order_id']:checked").length == 0)
			{
				$.showErr(LANG.JS_POST);
				return false;
			}

			if($.trim($("#delivery-fix-phone").val()).length == 0 && $.trim($("#delivery-mobile-phone").val()).length == 0 &&$("input[name='delivery_refer_order_id']:checked").length==0)
			{
				$.showErr(LANG.JS_PHONE_OR_MOBILE);
				return false;
			}
			else
			{
				if($.checkPhone($("#delivery-fix-phone").val())&&$("#delivery-fix-phone").val().length > 0)
				{
					$.showErr(LANG.JS_PHONT_ERRER);
					return false;
				}

				if(!$.checkMobilePhone($("#delivery-mobile-phone").val())&&$("input[name='delivery_refer_order_id']:checked").length == 0)
				{
					$.showErr(LANG.JS_MOBILE_ERROR);
					return false;
				}
			}

			if(isInquiry == 0)
			{
				if($("input[name='delivery']:checked").length == 0&&$("input[name='delivery_refer_order_id']:checked").length == 0)
				{
					$.showErr(LANG.JS_SELECT_SHIPPING_METHOD);
					return false;
				}
			}
		}

		if($.trim($("#user-mobile-phone").val()).length > 0 && $.checkPhone($("#user-mobile-phone").val())&&$("input[name='delivery_refer_order_id']:checked").length == 0)
		{
			$.showErr(LANG.JS_BOTH_MOBILE);
			return false;
		}

		if(totalPrice > 0)
		{
			if($("input[name='payment']:checked").length == 0)
			{
				$.showErr(LANG.JS_SELECT_PAYMENT);
				return false;
			}
		}
		//add by chenfq 2010-09-29
		cart_done();
	});
});

//add by chenfq 2010-09-29
var cart_done_ing = false; 
function cart_done(){
	$("#order_done").attr("disabled",true).addClass("disabled");
	if (cart_done_ing){//add by chenfq 2011-03-17 数据正在处理中，请务重复提交.
		alert(LANG.CART_DONE_ING);
		return false;
	}
	cart_done_ing = true;
	var query = new Object();
	if (isOrder==false){
		//var pamrm = "?m=Cart&a=done";
		query.m = "Cart";
		query.a = "done";
	}else{
		//var pamrm = "?m=Order&a=done&order_id=" + orderID;
		query.m = "Order";
		query.a = "done";
		query.order_id = orderID;
	}

		//开始获取提交的数据

	var delivery_id = 0;  //配送方式
	var payment_id =  0;   //支付方式
	var is_protect =  0;    //是否保价
	var	delivery_refer_order_id = 0; //快递拼单

	//提交的地区
	var region_lv1 = $("#region_lv1_0").val();   //一级地区
	var region_lv2 = $("#region_lv2_0").val();   //二级地区
	var region_lv3 = $("#region_lv3_0").val();   //三级地区
	var region_lv4 = $("#region_lv4_0").val();   //四级地区

	//pamrm = pamrm + "&region_lv1=" + region_lv1 + "&region_lv2=" + region_lv2 + "&region_lv3=" + region_lv3 + "&region_lv4=" + region_lv4;
	query.region_lv1 = region_lv1;
	query.region_lv2 = region_lv2;
	query.region_lv3 = region_lv3;
	query.region_lv4 = region_lv4;

	if($("input[name='payment']:checked").length > 0)
		payment_id = $("input[name='payment']:checked").val();

	if($("input[name='delivery']:checked").length > 0)
	{
		delivery_id = $("input[name='delivery']:checked").val();
		var parent = $("input[name='delivery']:checked").parent().parent();
		if($(".protect:checked",parent).length > 0)
			is_protect = 1;
	}

	var credit = $("#credit-text").val();
	var iscreditall = $("#credit-all input").attr("checked") ? 1 : 0;
    
    var score = $("#score-text").val();// 积分抵现
    
    // add by 关池顺 2013-10-10 佣金金额
	var rebate = $("#rebate-text").val();
	var isrebateall = $("#rebate-all input").attr("checked") ? 1 : 0;

	//pamrm = pamrm + "&payment_id=" + payment_id + "&delivery_id=" + delivery_id + "&credit=" + credit + "&iscreditall=" + iscreditall;
	query.payment_id = payment_id;
	query.delivery_id = delivery_id;
	query.credit = credit;
	query.iscreditall = iscreditall;
    // add by 关池顺 2013-10-10 佣金金额
	query.rebate = rebate;
	query.isrebateall = isrebateall;
    
	query.score = score;

	//是否开票
	var tax = $("#tax").attr("checked")?1:0;
	var ecvSn = $.trim($("#cardcode-sn").val());
	var ecvPassword = $.trim($("#cardcode-pwd").val());

	//pamrm = pamrm + "&is_protect=" + is_protect + "&tax=" + tax + "&ecv_sn=" + ecvSn + "&ecv_password=" + ecvPassword;
	query.is_protect = is_protect;
	query.tax = tax;
	query.ecv_sn = ecvSn;
	query.ecv_password = ecvPassword;


	var memo = $.trim($("#memo").val());
	var tax_content = $.trim($("#tax_content").val());

	//pamrm = pamrm + "&memo=" + memo + "&tax_content=" + tax_content;
	query.memo = memo;
	query.tax_title = $.trim($("#tax_title").val()); //add by chenfq 2011-03-17
	query.tax_content = tax_content;
	//收信地址
	var consignee = $.trim($("#delivery-consignee").val());
	var address = $.trim($("#delivery-address").val());
	var zip = $.trim($("#delivery-zip").val());
	var fix_phone = $.trim($("#delivery-fix-phone").val());
	var mobile_phone = $.trim($("#delivery-mobile-phone").val());

	//pamrm = pamrm + "&consignee=" + consignee + "&address=" + address + "&zip=" + zip + "&fix_phone=" + fix_phone + "&mobile_phone=" + mobile_phone;
	query.consignee = consignee;
	query.address = address;
	query.zip = zip;
	query.fix_phone = fix_phone;
	query.mobile_phone = mobile_phone;

	//快递拼单
	if($("input[name='delivery_refer_order_id']:checked").length > 0)
		delivery_refer_order_id = $("input[name='delivery_refer_order_id']:checked").val();

	var user_mobile_phone = $.trim($("#user-mobile-phone").val());

	//pamrm = pamrm + "&delivery_refer_order_id=" + delivery_refer_order_id + "&user_mobile_phone=" + user_mobile_phone;
	query.delivery_refer_order_id = delivery_refer_order_id;
	query.user_mobile_phone = user_mobile_phone;


	//var url = "services/cart.php" + pamrm;
	var url = "services/cart.php";

	$.ajax({
		url: url,
		cache: false,
		type: "POST",
		data: query,
		dataType:"json",
		success:function(data)
		{
			var rs = data;
			if (rs.status == false){
				alert(rs.error);
				$("#order_done").attr("disabled",false).removeClass("disabled");
			}else{
				var url = "index.php?m=Order&a=pay&pay=1&id=" + rs.order_id+"&accountpay_str=" + rs.accountpay_str + "&ecvpay_str=" + rs.ecvpay_str;
				if (rs.money_status == 2){
					url = "index.php?m=Order&a=pay_success&id=" + rs.order_id;
				}
				location.href = url;
			}
		},
		error:function(a,b,c)
		{
			if(a.responseText)
				alert(a.responseText);
		}
	});
	cart_done_ing = false;
	$("#order_done").attr("disabled",false).removeClass("disabled");
}

function getRoundFloat(x)
{
	if(isNaN(x))
		return 0;

	var float=0;
	if(isNaN(x) || $.trim(x) == "")
		return 0;
	else
		float = parseFloat(x);

	if(float < 0)
		return 0;

	return Math.round(float * 100) / 100;
}

//地区切换
function selectRegion(obj,region_id,lvl)
{
	var id=obj.value;
	$.ajax({
		  url: APP+"?"+VAR_MODULE+"=Ajax&"+VAR_ACTION+"=getChildRegion&is_ajax=1&pid="+id,
          dataType : "JSON",
		  success:function(data)
		  {
			// data = $.evalJSON(data);
			var origin_html = "<option value='0'>"+NO_SELECT+"</option>";
			switch(lvl)
			{
				case 1:
					html = origin_html;
					if(data)
					for(var i=0;i<data.length;i++)
					{
						html+="<option value='"+data[i].id+"'>"+data[i].name+"</option>";
					}
					if(id==0) html = origin_html;  //当未作选择时清空
					$("#region_lv2_"+region_id).html(html);
					$("#region_lv3_"+region_id).html(origin_html);
					$("#region_lv4_"+region_id).html(origin_html);
					break;
				case 2:
					html = origin_html;
					if(data)
					for(var i=0;i<data.length;i++)
					{
						html+="<option value='"+data[i].id+"'>"+data[i].name+"</option>";
					}
					if(id==0) html = origin_html;  //当未作选择时清空
					$("#region_lv3_"+region_id).html(html);
					$("#region_lv4_"+region_id).html(origin_html);
					break;
				case 3:
					html = origin_html;
					if(data)
					for(var i=0;i<data.length;i++)
					{
						html+="<option value='"+data[i].id+"'>"+data[i].name+"</option>";
					}
					if(id==0) html = origin_html;  //当未作选择时清空
					$("#region_lv4_"+region_id).html(html);
					break;
				}
		  }
	});
}

function selectRegionDelivery(obj,region_id,lvl)
{
	var id=obj.value;
	var origin_html = "<option value='0'>"+NO_SELECT+"</option>";
	html = origin_html;
	switch(lvl)
	{
		case 1:
			if(id > 0)
			{
				var evalStr="regionConf.r"+id+".c";
				var regionConfs=eval(evalStr);
				evalStr+=".";
				for(var key in regionConfs)
				{
					html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
				}
			}

			$("#region_lv2_"+region_id).html(html);
			$("#region_lv3_"+region_id).html(origin_html);
			$("#region_lv4_"+region_id).html(origin_html);
			break;
		case 2:
			if(id > 0)
			{
				var evalStr="regionConf.r"+$("#region_lv1_"+region_id).val()+".c.r"+id+".c";
				var regionConfs=eval(evalStr);
				evalStr+=".";
				for(var key in regionConfs)
				{
					html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
				}
			}

			$("#region_lv3_"+region_id).html(html);
			$("#region_lv4_"+region_id).html(origin_html);
			break;
		case 3:
			if(id > 0)
			{
				var evalStr="regionConf.r"+$("#region_lv1_"+region_id).val()+".c.r"+$("#region_lv2_"+region_id).val()+".c.r"+id+".c";
				var regionConfs=eval(evalStr);
				evalStr+=".";
				for(var key in regionConfs)
				{
					html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
				}
			}

			$("#region_lv4_"+region_id).html(html);
			break;
	}

	loadDelivery();
}

//读取配送方式
function loadDelivery()
{
	var id = 0;
	if(parseInt($("#region_lv4_0").val())>0)
	{
		id = parseInt($("#region_lv4_0").val());
	}
	else if(parseInt($("#region_lv3_0").val())>0)
	{
		id = parseInt($("#region_lv3_0").val());
	}
	else if(parseInt($("#region_lv2_0").val())>0)
	{
		id = parseInt($("#region_lv2_0").val());
	}
	else if(parseInt($("#region_lv1_0").val())>0)
	{
		id = parseInt($("#region_lv1_0").val());
	}

	var url = "services/cart.php?m=Cart&a=loadDelivery&id="+id;

	$.ajax({
		  //url: APP+"?"+VAR_MODULE+"=Cart&"+VAR_ACTION+"=loadDelivery&id="+id,
		  url : url,
		  cache: false,
		  success:function(data)
		  {
		  	$("#cart_delivery").html(data);
			countCartTotal();
		  }
	});

}

//切换配送方式
function deliveryChange(obj)
{
	$("input[name='delivery_refer_order_id']").attr("checked",false);
	$(".consignee-box").show();
	$("input.protect").attr({"disabled":true,"checked":false});
	obj.checked = true;
   	$("input",$(obj).parent().parent()).attr("disabled",false);
	
	//开始获取货到付款是否允许
	var id = 0;  //地区ID
	if(parseInt($("#region_lv4_0").val())>0)
	{
		id = parseInt($("#region_lv4_0").val());
	}
	else if(parseInt($("#region_lv3_0").val())>0)
	{
		id = parseInt($("#region_lv3_0").val());
	}
	else if(parseInt($("#region_lv2_0").val())>0)
	{
		id = parseInt($("#region_lv2_0").val());
	}
	else if(parseInt($("#region_lv1_0").val())>0)
	{
		id = parseInt($("#region_lv1_0").val());
	}

	var url = "services/cart.php?m=Cart&a=checkCod2&region_id="+id+"&delivery_id="+obj.value;
	$.ajax({
		  url: url,
		  cache: false,
		  dataType:'json',
		  success:function(data)
		  {
			 if(data.allow_cod==1)
			 {
			 	$("#payment_Cod").show();			 	
			 }
			 else
			 {
			 	$("#payment_Cod").hide();
			 	$("#payment_Cod").find("input").attr("checked",false);
			 }
			 if(data.is_smzq==1){
				 $("#consignee_region_id").hide(); 
			 }else{
				 $("#consignee_region_id").show(); 
			 }
			 is_smzq = data.is_smzq;
			 countCartTotal();	
		  }
	});
}

//计算订单中所有费用
function countCartTotal()
{
	$("#order_done").attr("disabled",true).addClass("disabled");
	var delivery_id = 0;  //配送方式
	var payment_id =  0;   //支付方式
	var is_protect =  0;    //是否保价
	var region_lv1 = $("#region_lv1_0").val();   //一级地区
	var region_lv2 = $("#region_lv2_0").val();   //二级地区
	var region_lv3 = $("#region_lv3_0").val();   //三级地区
	var region_lv4 = $("#region_lv4_0").val();   //四级地区

	var tax = $("#tax").attr("checked")?1:0;
	var credit = $("#credit-text").val();
	var isCreditAll = $("#credit-all input").attr("checked") ? 1 : 0;
    
    // add by 关池顺 2013-10-10 佣金金额
	var rebate = $("#rebate-text").val();
	var isRebateAll = $("#rebate-all input").attr("checked") ? 1 : 0;
    
    var score = $("#score-text").val();

	var ecvSn = $.trim($("#cardcode-sn").val());
	var ecvPassword = $.trim($("#cardcode-pwd").val());

	if($("input[name='delivery']:checked").length > 0)
	{
		delivery_id = $("input[name='delivery']:checked").val();
		var parent = $("input[name='delivery']:checked").parent().parent();
		if($(".protect:checked",parent).length > 0)
			is_protect = 1;
	}

	if($("input[name='payment']:checked").length > 0)
		payment_id = $("input[name='payment']:checked").val();

	var query=new Object();
	query.m = "Cart";
	query.a = "getCartTotal";
	query.delivery_id = delivery_id;
	query.payment_id = payment_id;
	query.is_protect = is_protect;
	query.region_lv1 = region_lv1;
	query.region_lv2 = region_lv2;
	query.region_lv3 = region_lv3;
	query.region_lv4 = region_lv4;
	query.tax = tax;
	query.isCreditAll = isCreditAll;
	query.credit = credit;
    
    // add by 关池顺 2013-10-10 佣金金额
	query.isRebateAll = isRebateAll;
	query.rebate = rebate;
    
	query.score = score;
    
	query.ecvSn = ecvSn;
//	if(ecvSn!="")	alert(ecvSn);
//	if(ecvPassword!="") alert(ecvPassword);
	query.ecvPassword = ecvPassword;
	if(isOrder)
	{
		query.id = orderID;
		query.m = "Order";
		query.a = "getOrderTotal";
	}
	$.ajax({
		  type: "POST",
		  url: "services/cart.php",
		  data:query,
		  cache: false,
		  dataType:'json',
		  success:function (data)
		  {
            // modify by 关池顺 2013-10-10 佣金金额
			if(data.total_price == 0 && (data.credit > 0 || data.rebate > 0 || data.ecvFee > 0))
			{
				if(payType == 1)
                {
					$("#payment-list").hide().undelegate("input[type=radio]","click");
					// $("#payment-list").hide();
                }
				else
                {
					$("#payment-list").show().undelegate("input[type=radio]","click").delegate("input[type=radio]","click",function(){countCartTotal();});
					// $("#payment-list").show();
                }

				$("input[name='payment']").attr("checked",false);
			}
			else
			{
				$("#payment-list").show().undelegate("input[type=radio]","click").delegate("input[type=radio]","click",function(){countCartTotal();});
				// $("#payment-list").show();
			}
			totalPrice = data.total_price;

			if(totalPrice > 0)
				$("#accountpay-desc").html(LANG.JS_NO_ENOUGH_1+totalPrice+LANG.JS_NO_ENOUGH_2);
			else
				$("#accountpay-desc").html(LANG.JS_USE_BALANCE_PAY);

			$("#credit-text").val(data.credit);
            
            // add by 关池顺 2013-10-10 佣金金额
			$("#rebate-text").val(data.rebate);
            
			$("#score-text").val(data.score);
            
            $("#available_score").html(data.available_score);
            $("#use_score").html(data.score);
            $("#score_money").html(data.score_money);
            
			$("#cart_total_box").html(data.html);
			$("#order_done").attr("disabled",false).removeClass("disabled");
		  },
			error:function(a,b,c)
			{
				alert(a.responseText);
			}
	});
}

//是否开票
function checkTax(obj)
{
	if(obj.checked)
	{
		$("#tax-table").removeClass("hidd");
		$("#tax_content").attr("disabled",false);
	}
	else
	{
		$("#tax-table").addClass("hidd");
		$("#tax_content").attr("disabled",true);
		$("#tax_content").val("");
	}
	countCartTotal();
}


function fav_add(fav_id)
{
    if(fav_id > 0)
    {
        $.ajax({
            url : "index.php?m=Ajax&a=fav_add",
            type : "POST",
            data : {id:fav_id},
            dataType : "JSON",
            cache : false,
            success : function(data)
            {
                if(data)
                {
                    if(data.status == 1)
                    {
                        alert("添加收藏成功");
                        window.location.href = window.location.href;
                        return true;
                    }
                    else if(data.status == -1)
                    {
                        alert("请先登陆");
                        window.location.href = ROOT_PATH+"/index.php?m=User&a=login";
                    }
                    else
                    {
                        alert(data.msg);
                        return false;
                    }
                }
            }
        });
    }
    else
        return false;
}

function fav_delete(fav_id)
{
    if(fav_id > 0 && confirm("您确定要取消收藏吗？"))
    {
        $.ajax({
            url : "index.php?m=Ajax&a=fav_delete",
            type : "POST",
            data : {id:fav_id},
            dataType : "JSON",
            cache : false,
            success : function(data)
            {
                if(data)
                {
                    if(data.status == 1)
                    {
                        alert("取消收藏成功");
                        window.location.href = window.location.href;
                        return true;
                    }
                    else if(data.status == -1)
                    {
                        alert("请先登陆");
                        window.location.href = ROOT_PATH+"/index.php?m=User&a=login";
                        return false;
                    }
                    else
                    {
                        alert(data.msg);
                        return false;
                    }
                }
                else
                    return false;
            }
        });
    }
    else
        return false;
}

	$("#seller_msg").submit(function(){
		if($.trim($(this).find("#user_name").val())=='')
		{
			$.showErr(LANG.JS_USERNAME_EMPTY);
			$("#user_name").focus();
			return false;
		}
		// if($.trim($(this).find("#phone").val())=='')
		// {
			// $.showErr(LANG.JS_PHONE_EMPTY);
			// $("#phone").focus();
			// return false;
		// }
		// if(!$.checkMobilePhone($(this).find("#phone").val()))
		// {
			// $.showErr(LANG.JS_MOBILE_ERROR);
			// $("#phone").focus();
			// return false;
		// }
		if($.trim($(this).find("#groupon_seller_name").val())=='')
		{
			$.showErr(LANG.JS_GB_USER_EMPTY);
			$("#groupon_seller_name").focus();
			return false;
		}
		// if($.trim($(this).find("#address").val())=='')
		// {
			// $.showErr(LANG.JS_GB_ADDRESS_EMPTY);
			// $("#address").focus();
			// return false;
		// }
		if($.trim($(this).find("#title").val())=='')
		{
			$.showErr(LANG.JS_CONTACT_EMPTY);
			$("#title").focus();
			return false;
		}
		if($.trim($(this).find("#content").val())=='')
		{
			$.showErr(LANG.JS_GB_DESC_EMPTY);
			$(this).find("#content").focus();
			return false;
		}
		if($.trim($(this).find("#groupon_goods").val())=='')
		{
			$.showErr(LANG.JS_GB_GOOD_EMPTY);
			$("#groupon_goods").focus();
			return false;
		}
	});
    
	// $("#incharge-form").submit(function(){

		// var money = $(this).find("#money").val();
		// var moneyreg=/^[0-9]+([.]{1}[0-9]{1,2})?$/;
		// if(money==''||isNaN(money)||parseFloat(money)<=0||!moneyreg.test(money))
		// {
			// $.showErr(LANG.JS_MONEY_EMPTY);
			// $("#money").focus();
			// return false;
		// }
		// if($(this).find("input:checked").length==0)
		// {
			// $.showErr(LANG.JS_SELECT_PAYMENT);
			// return false;
		// }
	// });
    
	$('.cancel_tocash').click(function(){
		if(confirm("确定取消本次提现申请吗？")) // modify by 关池顺 2013-05-11 取消提现语言项
		{
			var tocash_id = $(this).attr('tocash_id');
			var cancelform = $("<form></form>");
			cancelform.attr('action',ROOT_PATH+"/index.php?"+VAR_MODULE+"=uctocash&"+VAR_ACTION+"=cancel");
			cancelform.attr('method','post');
			input1 = $("<input type='id' name='id' />");
			input1.attr('value',tocash_id);
			cancelform.append(input1);
			cancelform.appendTo("body");
			cancelform.css('display','none');
			cancelform.submit();
//			cancelform.attr("action", ROOT_PATH+"/index.php");
//			cancelform.VAR_MODULE = "uctocash";
//			cancelform.VAR_ACTION = "cancel";
//			cancelform.id = tocash_id;
//			cancelform.submit();
			//$.post(ROOT_PATH+"/index.php",{VAR_MODULE:"uctocash",VAR_ACTION:"cancel",id:tocash_id});
			//alert(tocash_id);
		}
	});

	$("#tocash-form").submit(function(){
        // add by 关池顺 2013-10-10 佣金金额
        if($(this).find("#rebate_money").length > 0)
        {
            var money = $(this).find("#money").val();
            var maxMoney = $(this).find("#money").attr('maxmoney');
            var moneyreg=/^[0-9]+([.]{1}[0-9]{1,2})?$/;
            money = parseFloat(money);
            maxMoney = parseFloat(maxMoney.toString());
            if(money > maxMoney)
            {
                $.showErr(LANG.JS_MONEY_NO_LT+maxMoney+LANG.JS_LIMIT_5);
                $("#money").focus().select();
                //$("#money").val(maxMoney);
                return false;
            }
            var rebate_money = $(this).find("#rebate_money").val();
            var maxRebateMoney = $(this).find("#rebate_money").attr('maxmoney');
            // if(rebate_money==''||isNaN(rebate_money)||parseFloat(rebate_money)<=0||!moneyreg.test(rebate_money))
            // {
                // $.showErr("请填写正确的佣金金额");
                // $("#rebate_money").focus();
                // return false;
            // }
            rebate_money = parseFloat(rebate_money);
            maxRebateMoney = parseFloat(maxRebateMoney.toString());
            if(rebate_money > maxRebateMoney)
            {
                $.showErr("输入的金额不能大于你的佣金余额¥"+maxRebateMoney+"。");
                $("#rebate_money").focus().select();
                //$("#money").val(maxMoney);
                return false;
            }
            if((money==''||isNaN(money)||parseFloat(money)<=0||!moneyreg.test(money))&&(rebate_money==''||isNaN(rebate_money)||parseFloat(rebate_money)<=0||!moneyreg.test(rebate_money)))
            {
                $.showErr("请填写正确的金额或佣金金额");
                //$("#money").focus();
                return false;
            }
        }
        else
        {
            var money = $(this).find("#money").val();
            var maxMoney = $(this).find("#money").attr('maxmoney');
            var moneyreg=/^[0-9]+([.]{1}[0-9]{1,2})?$/;
            if(money==''||isNaN(money)||parseFloat(money)<=0||!moneyreg.test(money))
            {
                $.showErr(LANG.JS_MONEY_EMPTY);
                $("#money").focus();
                return false;
            }
            money = parseFloat(money);
            maxMoney = parseFloat(maxMoney.toString());
            if(money > maxMoney)
            {
                $.showErr(LANG.JS_MONEY_NO_LT+maxMoney+LANG.JS_LIMIT_5);
                $("#money").focus().select();
                //$("#money").val(maxMoney);
                return false;
            }
        }
		if(parseInt($(this).find("input[name='account_type']:checked").val())==1)// modify by 关池顺 2013-05-10 选中值浏览器兼容问题
		{
			if($(this).find("#bank_account").val()=='')
			{
				$.showErr("请填写银行卡账号");
				return false;
			}
			if($(this).find('#bank_name').val()=='')
			{
				$.showErr('请填写银行名称');
				return false;
			}
			if($(this).find('#bank_point').val()=='')
			{
				$.showErr('请填写开户支行名称');
				return false;
			}
//			if($(this).find('#payee_name').val()=='')
//			{
//				$.showErr('请填写收款人姓名');
//				return false;
//			}
		}
		else
		{
			// if($('#auto_alipay_account').length>0 && parseInt($('#auto_alipay_account').val()) == 0)
			// {
				// if($(this).find("input[name='account']:checked").val()=='' || $(this).find("input[name='account']:checked").val() == undefined)// modify by 关池顺 2013-05-10 选中值浏览器兼容问题
				// {
					// $.showErr("请选择支付宝账号");
					// return false;
				// }
			// }
			// else
			// {
				// if($(this).find("#account").val()=='')
				// {
					// $.showErr("请填写支付宝账号");
					// return false;
				// }
			// }
            var account = $(this).find("#account").val();
            // if((account == "" || account == undefined) && $('#auto_alipay_account').length>0)
            // {
                // account = $(this).find("input[name='account_l']:checked").val();
            // }
            if($(this).find("input[name='account_l']").length > 0)
            {
                var account_l = $(this).find("input[name='account_l']:checked").val();
                if(account_l == "" || account_l == undefined)
                {
                    $.showErr("请选择支付宝账号");
                    return false;
                }
                else if(account_l == "other_alipay_account" && (account == "" || account == undefined))
                {
                    $.showErr("请填写支付宝账号");
                    return false;
                }
            }
            else
            {
                if(account == "" || account == undefined)
                {
                    $.showErr("请填写支付宝账号");
                    return false;
                }
            }
		}
		if($(this).find('#payee_name').val()=='')
		{
			$.showErr('请填写收款人姓名');
			return false;
		}
	});
	$("#enter-address-form").submit(function(){
		var email = $.trim($(this).find("#enter-address-mail").val());
		if(email.length == 0)
		{
			$.showErr(LANG.JS_EMAIL_ADDRESS_EMPTY);
			$("#enter-address-mail").focus();
			return false;
		}

		if(!$.checkEmail(email))
		{
			$.showErr(LANG.JS_EMAIL_ADDRESS_ERROR_EMPTY);
			$("#enter-address-mail").focus();
			return false;
		}

	});
	$("#ecv_incharge").submit(function(){
		var sn = $.trim($("#sn").val());
		if(sn.length == 0)
		{
			$.showErr(LANG.JS_BONUS_SN_EMPTY);
			$("#sn").focus();
			return false;
		}


	});

	$("#ecv-form").submit(function(){
		var ecvSn = $.trim($(this).find("#ecvSn").val());
		var ecvPassword = $.trim($(this).find("#ecvPassword").val());
		if(ecvSn.length == 0)
		{
			$.showErr(LANG.JS_ECVSN_EMPTY);
			$("#ecvSn").focus();
			return false;
		}
	});
