/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var progressTimer;
function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1) + min);
}
var WPSoL_Scan = {
    // List of pages to scan
    pages: [],
    // Current page
    current_page: 0,
    // Create a random string
    random: function (length) {
        var ret = "";
        var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < length; i++) {
            ret += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
        }
        return ret;
    },
    // Update the display
    update_display: function () {
        progress();
        function progress() {
            var wpsol_progress = jQuery("#wpsol-progress");
            var val = wpsol_progress.progressbar("value") || 0;

            wpsol_progress.progressbar("value", val + Math.floor(Math.random() * 3));

            if (val <= 99) {
                progressTimer = setTimeout(progress, 15);
            }
        }
    },
    //scan tab 2
    start_scan_query: function () {

        data = {
            'wpsol_scan_name_query': 'scan_' + WPSoL_Scan.random(6),
            'action': 'wpsol_start_scan_query',
            'ajaxnonce' : wpsolAnalysisJS.ajaxnonce
        };

        jQuery.post(ajaxurl, data, function () {
            WPSoL_Scan.update_display();
            setTimeout(function () {
                jQuery("#wpsol-scan-frame").attr("src", WPSoL_Scan.pages[0]);
                WPSoL_Scan.current_page = 0;
                if (WPSoL_Scan.current_page >= WPSoL_Scan.pages.length - 1) {
                    WPSoL_Scan.stop_scan_query();
                    return true;
                }
            }, 1500);
        });
    },
    stop_scan_query: function () {

        // Turn off the profiler
        data = {
            'action': 'wpsol_stop_scan_query'
        };
        jQuery.post(ajaxurl, data, function (response) {
            // Hide the cancel button
            jQuery("#wpsol-view-results-buttonset").show();

            // Show the view results button
            jQuery("#wpsol-view-results-submit").attr("data-scan-name", response);

            // Update the caption
            jQuery("#wpsol-scanning-caption").html("Scanning is complete").css("color", "black");
        });
    }
};



jQuery(document).ready(function ($) {
    window.onclick = function(event) {
             if (event.target == document.getElementById('wpsol-more-details-dialog')) {
                $("#wpsol-more-details-dialog").css('display','none');
            }
        };
   
    $(".wpsol-close").click(function(){
        $("#wpsol-more-details-dialog").css('display','none');
    });    
    // Iframe scanner
    $("#wpsol-scanner-dialog").dialog({
        'autoOpen': false,
        'closeOnEscape': true,
        'draggable': false,
        'resizable': false,
        'modal': true,
        'width': 800,
        'height': 600,
        'title': "URL to Analyse",
        'dialogClass': 'noPadding'
    });

    // Progress dialog
    $("#wpsol-progress-dialog").dialog({
        'autoOpen': false,
        'closeOnEscape': false,
        'draggable': false,
        'resizable': false,
        'modal': true,
        'width': 450,
        'height': 120,
        'dialogClass': 'noTitle'
    });

    //click button scan
    $("#speed-button").click(function () {
        var loadtime_button = $('input[name="loadtime-button"]');
        $("#message-scan").show();
        loadtime_button.prop('disabled', true);
        $(".error-message").hide();
        var url = $("#insert-url").val();
        var main_url = $("#main-url").data("url");
        var regexp = new RegExp("^(http|https)://");
        if (regexp.test(url)) {
            loadtime_button.prop('disabled', false);
            alert("Incorrect path !");
            window.location.href = "admin.php?page=wpsol_speed_analysis";
        }else {
        if(url === ''){
            url = main_url + '/';
        }else{
            url = main_url + '/' + url;
        }
        var old_url = $("#old-url").data("url");
        if(old_url === url){
            var ran = getRandomInt(1,100);
            if(url.indexOf('?') > 0){
                //seen ?
                url = url + '&wpsol=' + ran.toString();
            }else{
                //not ?
                url = url + '?wpsol=' + ran.toString();
            }
        }

            $(".analysis-result-content").hide();
            $("#message-not-key").hide();
            $(".scan-test-progress").show();
            var method = function () {
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: "json",
                    data: {
                        action: "wpsol_load_page_time",
                        urlPage: url,
                        ajaxnonce : wpsolAnalysisJS.ajaxnonce
                    },
                    success: function (res) {
                        if (!res || res === 'null') {
                            setTimeout(function(){
                                $(".analysis-result-content").hide();
                                $("#message-scan").hide();
                                $(".scan-test-progress").hide();
                                $("#message-error-scan").show();
                                setTimeout(function(){
                                    window.location.href = "admin.php?page=wpsol_speed_analysis";
                                },3000);
                            },3000);
                        }
                        if (res === 100 || res === 101 ) {
                             setTimeout(function(){method() },10000);   
                        }
                        if (res === 200) {
                            window.location.href = "admin.php?page=wpsol_speed_analysis";
                        }
                        if (res > 400) {
                            setTimeout(function(){
                               $("#message-scan").hide();
                               $("#message-error-scan").show();
                               setTimeout(function(){
                                    window.location.href = "admin.php?page=wpsol_speed_analysis";
                                },3000);
                            },3000);   
                        }
                        if (res === 'wrong-key') {
                            setTimeout(function(){
                                $(".analysis-result-content").hide();
                                $("#message-scan").hide();
                                $(".scan-test-progress").hide();
                                $("#message-error-apikey-scan").show();
                                setTimeout(function(){
                                    window.location.href = "admin.php?page=wpsol_speed_analysis";
                                },3000);
                            },3000);
                        }
                        if (res === 'not-key') {
                            setTimeout(function(){
                                $(".scan-test-progress").hide();
                                $("#message-scan").hide();
                                $(".analysis-result-content").show();
                                $("#message-not-key").show();
                                loadtime_button.prop('disabled', false);
                            },3000);
                            
                        }
                        if( res === 'scanned'){
                            setTimeout(function(){
                                window.location.href = "admin.php?page=wpsol_speed_analysis";
                            },3000);
                        }
                    }
                });
            };
            method();
        }

    });
    //click more details test
    $(".wpsol-more-details").click(function(){
        var urlDetail = $(this).data('url');
        $.ajax({
            url : ajaxurl,
            dataType : 'json',
            method : 'POST',
            data : {
                action : 'wpsol_more_details',
                url : urlDetail,
                ajaxnonce : wpsolAnalysisJS.ajaxnonce
            },success : function(res){
                $(".wpsol-modal-content").html(res);
                $(".wpsol-modal").css("display","block");
                $('.tooltipped').tooltip({delay: 50});
            }
        });
    });
    
  
    $("#wpsol-view-results-submit").click(function () {
        // Close the dialogs
        jQuery("#wpsol-scanner-dialog").dialog("close");
        jQuery("#wpsol-progress-dialog").dialog("close");

        // View the scan
        location.href = window.location.href;
    });

    //clear page test
     $(".clear-test").click(function(){
        $(this).off("click");
        var urlDetail = $(this).data('url');
        $.ajax({
            url : ajaxurl,
            datatype : 'json',
            method : 'POST',
            data : {
                action : "wpsol_delete_details",
                url : urlDetail,
                ajaxnonce : wpsolAnalysisJS.ajaxnonce,
            },success : function(res){
                var id = '#'+res;
                $(id).fadeOut(1000, function(){ $(this).remove();});
            }
        });
    });
    //scan tab2
    $("#query-button").click(function () {
        var url_input = $("#insert-url-queries").val();
        var main_url = $("#main-url-queries").val();
        var url = main_url+'/'+url_input;
        var exp = "^(http|https)://";
        var regex = new RegExp(exp);
        if (regex.test(url_input)) {
            alert("Incorrect url !");
            window.location.href = "admin.php?page=wpsol_speed_analysis";
        } else {
            page = [url];

            $("#wpsol-scanner-dialog").dialog("open");
            $("#wpsol-progress-dialog").dialog("open");

            $("#wpsol-progress").progressbar({
                'value': 0
            });
            WPSoL_Scan.pages = page;
            WPSoL_Scan.start_scan_query();
        }
    });
   
});

