if (typeof ajaxurl === "undefined") {
    ajaxurl = myAjax.ajaxurl;
}

jQuery(document).ready(function ($) {
    jQuery('.speedoflight_tool').qtip({
        content: {
            attr: 'alt'
        },
        position: {
            my: 'top left',
            at: 'bottom bottom'
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

    jQuery('#export-button').on('click', function($){
        jQuery("#wpsol_export_config_modal").dialog("open");
    });

    jQuery('#import-button').on('click', function($){
        jQuery("#wpsol_import_config_modal").dialog("open");
    });

    jQuery('.import-export-dialog .cancel').on('click', function($){
        jQuery("#wpsol_export_config_modal").dialog("close");
        jQuery("#wpsol_import_config_modal").dialog("close");
    });

    jQuery('.import-export-dialog #export-agree').on('click', function($){
        var val = [];
        jQuery('.data-format :checkbox:checked').each(function(i){
            val[i] = jQuery(this).val();
        });

        if (val.length > 0) {
            jQuery.ajax({
                url : ajaxurl,
                dataType : 'json',
                method : 'POST',
                data : {
                    action : 'wpsol_export_configuration',
                    ajaxnonce : ajaxNonce.ajaxnonce
                },
                success : function(res){
                    if (typeof res.json === "undefined" || typeof res.xml === "undefined") {
                        jQuery('.import-export-dialog .import-select-error').html('Export error!');
                        jQuery('.import-export-dialog .import-select-error').show();
                        setTimeout(function(){
                            jQuery('.import-export-dialog .import-select-error').hide("fade");
                        },800);
                    } else {
                        val.forEach(function(item) {
                            if (item == 'json') {
                                data = res.json;
                            }
                            if (item == 'xml') {
                                data = res.xml;
                            }
                            var blob=new Blob([data]);
                            var link=document.createElement('a');
                            link.href=window.URL.createObjectURL(blob);
                            link.download="speedoflight."+item;
                            link.click();
                        });
                    }
                }
            });
        } else {
            jQuery('.import-export-dialog .import-select-error').show();
            setTimeout(function(){
                jQuery('.import-export-dialog .import-select-error').hide("fade");
            },800);
        }
    });

    $(".import-export-dialog").dialog({
        width: 600,
        height: 265,
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

