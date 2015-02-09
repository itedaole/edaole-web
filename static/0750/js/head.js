//邮件订阅
jQuery("article.drInfoSubscribe a.btnSub").click(function() {
    var mailstr = $.trim(jQuery("article.drInfoSubscribe input").val());
    if (mailstr == "请输入Email,订阅每日团购信息" || mailstr == "")
        alert("邮箱不能为空！");
    else
        location.href = "#";
        //location.href = ajaxUrl + "/EmailComQuerry.aspx?email=" + mailstr;
});
//添加收藏夹
// jQuery("#Favorite").click(function() { });
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



//----------------------------------
//});