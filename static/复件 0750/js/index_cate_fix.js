$('body').addClass('xl');
var _fn = function() {
		$(window).width() <= 1280 ? ($("body").addClass("sl"), $(".sidebar").css("opacity", "0")) : ($("body").removeClass("sl"), $(".sidebar").css("opacity", "1"))
	};
$(document).ready(function() {
    _fn(), owr0750tuan.add(_fn).add(_fn).remove(_fn);
	$('.banner a.arrow').hide(), $('.banner').hover(function() {
		$(this).find('a.arrow').show();
	}, function() {
		$(this).find('a.arrow').hide();
	});
	$(".block").hover(function() {
		$(this).find(".blocktips").show()
	}, function() {
		$(this).find(".blocktips").hide()
	});
	"undefined" == typeof document.body.style.maxHeight || $("#history").scrollFix("top", "top");
	var a = $(".head").outerHeight(!0),
		b = $(".selector").outerHeight(!0),
		c = $(".content .panel:eq(0)").outerHeight(!0),
		d = $(".content .tpm:eq(0)").outerHeight(!0),
		e = d,
		f = 108,
		g = a + b + c + d + 50,
		h = $(".content .panel:eq(1)").outerHeight(!0) + g,
		i = $(".content .panel:eq(2)").outerHeight(!0) + h,
		j = $(".content .panel:eq(3)").outerHeight(!0) + i,
		k = $(".content .panel:eq(4)").outerHeight(!0) + j,
		l = $(".content .panel:eq(5)").outerHeight(!0) + k;
        $(window).scroll(function() {
        if (!$.support.leadingWhitespace || !$('.content .panel:eq(0)').hasClass('ep')) {} else {
            var a = $(this).scrollTop();
            a > g && h - f >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), $(".panel .piece .fix:eq(0)").addClass("fixed")) :a > h - f && h + e >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(0)").addClass("fixout")) :a > h + e && i + e - f >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(1)").addClass("fixed")) :a > i + e - f && i + 2 * e >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(1)").addClass("fixout")) :a > i + 2 * e && j + 2 * e - f >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(2)").addClass("fixed")) :a > j + 2 * e - f && j + 3 * e >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(2)").addClass("fixout")) :a > j + 3 * e && k + 3 * e - f >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(3)").addClass("fixed")) :a > k + 3 * e - f && k + 4 * e >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(3)").addClass("fixout")) :a > k + 4 * e && l + 4 * e - f >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(4)").addClass("fixed")) :a > l + 4 * e - f && l + 5 * e >= a ? ($(".panel .piece .fix").removeClass("fixed fixout"), 
            $(".panel .piece .fix:eq(4)").addClass("fixout")) :$(".panel .piece .fix").removeClass("fixed fixout");
        }
    });
});
