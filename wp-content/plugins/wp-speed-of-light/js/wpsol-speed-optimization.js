jQuery(document).ready(function ($) {
    jQuery('.speedoflight_tool').qtip({
        content: {
            attr: 'alt'
        },
        position: {
            my: 'bottom left',
            at: 'top top'
        },
        style: {
            tip: {
                corner: true
            },
            classes: 'speedoflight-qtip qtip-rounded speedoflight-qtip-dashboard'
        },
        show: 'hover',
        hide: {
            fixed: true,
            delay: 10
        }

    });

    $('input[name="all_control"]').click(function(){
        var checked = $(this).is(':checked');
        if(checked){
            $(".clean-data:enabled").prop("checked",true);
        }else{
            $(".clean-data:enabled").prop("checked",false);
        }
    });
    $(".clean-data").click(function(){
        var checked = $(this).is(':checked');
        if(!checked){
            $('input[name="all_control"]').prop('checked',false);
        }
    });

    // change value 
    $(".wpsol-optimization").change(function () {
        var clean_cache = $("#clean-cache");
        var active_cache = $("#active-cache");
        var add_expires = $("#add-expires");
        var query_strings = $("#query-strings");
        var cache_preload = $("#cache-preload");
        var val = clean_cache.val();
        (active_cache.is(':checked')) ? active_cache.attr("value", "1") : active_cache.attr("value", "0");
        (add_expires.is(':checked')) ? add_expires.attr("value", "1") : add_expires.attr("value", "0");
        (query_strings.is(':checked')) ? query_strings.attr("value", "1") : query_strings.attr("value", "0");
        (cache_preload.is(':checked')) ? cache_preload.attr("value", "1") : cache_preload.attr("value", "0");
        clean_cache.attr("value", val);
    });
    // change value
    $(".wpsol-minification").change(function () {
        var html = $("#html-minification");
        var css = $("#css-minification");
        var js = $("#js-minification");
        var cssGroup = $("#cssGroup-minification");
        var jsGroup = $("#jsGroup-minification");
        var fontGroup = $("#fontGroup-minification");
        var excludeFiles = $("#excludeFiles-minification");
        (html.is(':checked')) ? html.attr("value", "1") : html.attr("value", "0");
        (css.is(':checked')) ? css.attr("value", "1") : css.attr("value", "0");
        (js.is(':checked')) ? js.attr("value", "1") : js.attr("value", "0");
        (cssGroup.is(':checked')) ? cssGroup.attr("value", "1") : cssGroup.attr("value", "0");
        (jsGroup.is(':checked')) ? jsGroup.attr("value", "1") : jsGroup.attr("value", "0");
        (fontGroup.is(':checked')) ? fontGroup.attr("value", "1") : fontGroup.attr("value", "0");
        (excludeFiles.is(':checked')) ? excludeFiles.attr("value", "1") : excludeFiles.attr("value", "0");
    });


    $('#speed-optimization-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if(e.target.tagName != 'TEXTAREA') {
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        }
    });


    //   Display popup when active minify

    $("#js-minification,#css-minification").on("change",function(){
        if($(this).is(':checked')) {
            $("#wpsol_check_minify_modal").dialog("open");
            $(this).prop("checked", false);
            $(this).attr("value","0");
            var name = $(this).attr('name');
            $("#wpsol_check_minify_modal .check-minify-sucess #agree").attr('name', name);
        }
    });

    $("#wpsol_check_minify_modal .check-minify-sucess #agree").click(function(){
        var name = $(this).attr('name');
        // Set checked for type
            $("ul").find('#'+name).prop("checked",true).attr("value","1");
        // Close dialog
            $("#wpsol_check_minify_modal").dialog("close");

    });

    $("#wpsol_check_minify_modal .check-minify-sucess .cancel").click(function(){
        $("#wpsol_check_minify_modal").dialog("close");
    });

    $("#wpsol_check_minify_modal").dialog({
        width: 500,
        height: 400,
        autoOpen: false,
        closeOnEscape: true,
        draggable: false,
        resizable: false,
        modal : true,
        dialogClass: 'noTitle',
        show: {
            effect: "fade",
            duration: 500
        },
        hide: {
            effect: "fade",
            duration: 300
        }
     });
});