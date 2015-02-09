/* 捕捉ie6 */
var ie6=!-[1,]&&!window.XMLHttpRequest;
if(ie6){
var l = ['https://www.google.com/intl/zh-CN/chrome/browser/','http://www.firefox.com.cn/','http://ie.microsoft.com/'];
var t = ' target="_blank"';
var html ='<div class="p ie6" style="width:706px;height:465px;position:absolute;top:50%;left:50%;margin:-228px 0 0 -353px;border:1px solid #000;background-color:#fff;z-index:99999;">'
+'<div class="noie6">'
+'<a class="g" href="'+l[0]+'"'+t+'></a>'
+'<a class="f" href="'+l[1]+'"'+t+'></a>'
+'<a class="i" href="'+l[2]+'"'+t+'"></a>'
+'<a class="o" href="javascript:;" onclick="close_ie6();"></a>'
+'</div></div><div class="mask ie6" style="background-color:#000;filter:alpha(opacity=30);width:100%;position:absolute;z-index:99998;left:0;top:0;"></div>'
var owr0750tuan = function(){
    var queue = [],
    indexOf = Array.prototype.indexOf || function(){
        var i = 0, length = this.length;
        for( ; i < length; i++ ){
            if(this[i] === arguments[0]){
                return i;
            }
        }
        return -1;
    };
    var isResizing = {},
    lazy = true,
    listener = function(e){
        var h = window.innerHeight || (document.documentElement && document.documentElement.clientHeight) || document.body.clientHeight,
            w = window.innerWidth || (document.documentElement && document.documentElement.clientWidth) || document.body.clientWidth;
        if( h === isResizing.h && w === isResizing.w){
            return;
        }else{
            e = e || window.event;
            var i = 0, len = queue.length;
            for( ; i < len; i++){
                queue[i].call(this, e);
            }
            isResizing.h = h,
            isResizing.w = w;
        }
    }
    return {
        add: function(fn){
            if(typeof fn === 'function'){
                if(lazy){
                    if(window.addEventListener){
                        window.addEventListener('resize', listener, false);
                    }else{
                        window.attachEvent('onresize', listener);
                    }
                    lazy = false;
                }
                queue.push(fn);
            }else{  }
            return this;
        },
        remove: function(fn){
            if(typeof fn === 'undefined'){
                queue = [];
            }else if(typeof fn === 'function'){
                var i = indexOf.call(queue, fn);
 
                if(i > -1){
                    queue.splice(i, 1);
                }
            }
 
            return this;
        }
    };
}.call(this);

$('html').addClass('hash');
var ie6h = function(){
	$('.mask').height($(window).height())
};
$('body').append(html);
ie6h();
owr0750tuan.add(ie6h);
$('html, body').animate({scrollTop:0}, 'slow');
}else{}

function close_ie6()
{
    $('html.hash').removeClass(),$('.ie6').remove();
    $.cookie("close_ie6",1,{
            expires: 30,
            path: '/'
        });
}