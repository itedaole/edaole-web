jQuery(function ($) {
    if ($(".slide-pic").length > 0) {
        var defaultOpts = { interval: 5000, fadeInTime: 300, fadeOutTime: 200 };
        var _titles = $("ul.slide-txt li");
        var _titles_bg = $("ul.op li");
        var _img_li = $("ul.slide-pic li");
        var nextBtn =  $(".btn-next");
        var prevBtn =  $(".btn-previous");
        var _count = _img_li.length;
        var _current = 0;
        var slide = function (current) {
        	
        	if(current >= _count){
        		_current = 0;
        	}
        	else if(current <=-1) {
        		_current = _count-1;
        	}
            _img_li.filter(":visible").fadeOut(defaultOpts.fadeOutTime, function () {
            	_img_li.eq(_current).fadeIn(defaultOpts.fadeInTime);
            	_img_li.removeClass("cur").eq(_current).addClass("cur");
            });
        };
        
		nextBtn.click(function() {
			_current = _current + 1;
			slide(_current);
		});
		
		prevBtn.click(function() {
			_current = _current - 1;
			slide(_current);
		});
        
    }
});
