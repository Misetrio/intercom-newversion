jQuery(document).ready(function ($) {
    var slugs = [];
    $('.plugins tr.active').each(function (i, obj) {
        slugs.push($(obj).attr('id'));
    });
    $('.plugins tr.inactive').each(function (i, obj) {
        slugs.push($(obj).attr('id'));
    });
    var data = {
        'slugs': slugs
    };
    $.ajax({
        url: 'http://api.wpspeedster.com/v1/plugins/info',
        type: "POST",
        crossDomain: true,
        data: data,
        dataType: "json",
        success: function (result) {
            $.each(result, function (key, value) {
                var values = value.split("|");
                $('#hp_' + key).html(values[0]);
                $('#pp_' + key).html(values[1]);
                $('#rs_' + key).html(values[2]);
                $('#db_' + key).html(values[3]);
            });
        },
        error: function (xhr, status, error) {
            $('#hp_' + key).html('N/A');
            $('#pp_' + key).html('N/A');
            $('#rs_' + key).html('N/A');
            $('#db_' + key).html('N/A');
        }
    });
});