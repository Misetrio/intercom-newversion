jQuery(document).ready(function ($) {
    var success_icon = '<i class="material-icons top-field-icon-right success">check_circle</i>';
    var warning_icon = '<i class="material-icons top-field-icon-right info">info</i>';
    $.ajax({
        url : ajaxurl,
        method : "POST",
        dataType : "json",
        data:{
            action : 'wpsol_check_response_dashboard'
        },success : function(res){
            if (res.gzip) {
                $(".list-element-left ul li").find('.gzip-panel').after(success_icon);
            } else {
                $(".list-element-left ul li").find('.gzip-panel').after(warning_icon);
            }

            if (res.expires) {
                $(".list-element-right ul li").find('.expires-panel').after(success_icon);
            } else {
                $(".list-element-right ul li").find('.expires-panel').after(warning_icon);
            }
        }
    });

    $("ul li.dashboad-link").hover(function(){
        var ele = $(this).data('id');
        $(this).find("#" + ele).addClass('link-area-hover');
    }, function () {
        $(this).find('.link-area').removeClass('link-area-hover');
    });
});