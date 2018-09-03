jQuery(document).ready(function($){
    jQuery('.clean-cache-button').click(function(){
        wpsol_cache_callAjax();
    });

    jQuery('#wp-admin-bar-wpsol-clean-cache-topbar').click(function(){
        wpsol_cache_callAjax();
    });

    function wpsol_cache_callAjax(){
        jQuery('#wp-admin-bar-wpsol-clean-cache-topbar .ab-item .ab-icon').hide();
        jQuery('#wp-admin-bar-wpsol-clean-cache-topbar .ab-item .wpsol-ab-images').show();
        jQuery.ajax({
            url:ajaxurl,
            dataType:'json',
            method:'POST',
            data:{
                action:'wpsol_ajax_clean_cache'
            },
            success : function(res){
                var div = '';
                params = parseFloat(res.params);
                var messages = res.message;
                var status = res.status;

                if(!status){
                    div += '<div id="status-message" class="notice notice-error" style="margin-top:10px; margin-bottom:10px;padding: 10px;"><strong>Clear cache error, please check again!</strong></div>';
                }else{
                    if(!isNaN(params)){
                        if(params > 0){
                            div += '<div id="params-message" class="notice notice-success" style="margin-top:10px; margin-bottom:10px;padding: 10px;"><strong>OK cache is clean: '+params+'Kb cleaned</strong></div>';
                        }else{
                            div += '<div id="params-message" class="notice notice-success" style="margin-top:10px; margin-bottom:10px;padding: 10px;"><strong>Static cache has been cleaned.</strong></div>';
                        }
                    }
                }

                if( typeof messages !== 'undefined' &&    messages.length > 0 ){
                    messages.forEach(function($v){
                        div += '<div class="notice notice-error" style="margin-top:10px; margin-bottom:10px;padding: 10px;"><strong>'+$v+'</strong></div>';
                    });
                }
                jQuery('#wp-admin-bar-wpsol-clean-cache-topbar .ab-item .wpsol-ab-images').hide();
                jQuery('#wp-admin-bar-wpsol-clean-cache-topbar .ab-item .ab-icon').show();
                if(div !== ''){
                    var aftertitle = $('#wpbody .wrap h1');
                    if(aftertitle.length ){
                        aftertitle.after(div);
                    }else{
                        $("#wpbody #screen-meta").after(div);
                    }
                }

            }
        });
    }
});