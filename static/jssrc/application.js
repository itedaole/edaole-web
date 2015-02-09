var X = {}; 
X.hook = function() {
    var pre_init_str = 'x_init_hook_';
    for ( var h in window ) {
        if ( 0 != h.indexOf(pre_init_str) )
            continue;
        var func = window[h];
        if ( typeof func == 'function' ) {
            try { func(); }catch(e){}
        }       
    }
};

X.get = function(u) { return X.ajax(u, 'GET'); };
X.post = function(u) { return X.ajax(u, 'POST'); };
X.ajax = function(u, method) {
    jQuery.ajax({
        url: u,
        dataType: "json",
        success: X.json
    });
    return false;
};

X.json = function(r) {
    var type = r['data']['type'];
    var data = r['data']['data'];
    if ( type == 'alert' ) {
        alert(data);
    } else if ( type == 'eval' ) { 
        eval(data);
	} else if ( type == 'refresh') {
		window.location.reload();
    } else if ( type == 'updater' ) {
        var id = data['id'];
        var inner = data['html'];
        jQuery('#' + id).html(inner);
    } else if ( type == 'dialog' ) {
        X.boxShow(data, true);
    } else if ( type == 'mix' ) {
        for (var x in data) {
            r['data'] = data[x];
            X.json(r);
        }
    }
};

X.getXY = function() {
	var x,y;
	if(document.body.scrollTop){
		x = document.body.scrollLeft;
		y = document.body.scrollTop;
	}
	else{
		x = document.documentElement.scrollLeft;
		y = document.documentElement.scrollTop;
	}
	return {x:x,y:y};
};

X.boxMask = function(display)
{
    var height = jQuery('body').height() + 'px';
    var width = jQuery(window).width() + 'px';
    jQuery('#pagemasker').css({'position':'absolute', 'z-index':'3000', 'width':width, 'height':height, 'filter':'alpha(opacity=0.5)', 'opacity':0.5, 'top':0, 'left':0, 'background':'#CCC', 'display':display});
	jQuery('#dialog').css('display', display);
};

X.boxShow = function(innerHTML, mask)
{
    var dialog = jQuery('#dialog');
    dialog.html(innerHTML);

    if (mask) { X.boxMask('block'); }
    var ew = dialog.get(0).scrollWidth;
    var ww = jQuery(window).width();
    var lt = (ww/2 - ew/2) + 'px';
	var wh = jQuery(window).height();
	var xy = X.getXY();

	var tp = (wh*0.15 + xy.y) + 'px';

    dialog.css('background-color', '#FFF');
    dialog.css('left',lt);
    dialog.css('top', tp);
    dialog.css('z-index', 9999);
    dialog.css('display', 'block');

    return false;
};

X.boxClose = function()
{
    jQuery('#dialog').html('').css('z-index', -9999);
    X.boxMask('none');
    return false;
};

X.location = function(url){
    jQuery('#ifra_show').attr({src:url});
};

jQuery(document).ready(X.hook);
