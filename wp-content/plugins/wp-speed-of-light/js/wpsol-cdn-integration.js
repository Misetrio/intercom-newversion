jQuery(document).ready(function () {
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
});