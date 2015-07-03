jQuery(function () {
    jQuery('#optioinOrder').click(function () {
        jQuery('#productItem').css('display', 'none');
        jQuery('#categoryItem').css('display', 'none');
    });
    jQuery('#optionItem').click(function () {
        jQuery('#productItem').css('display', 'block');
        jQuery('#categoryItem').css('display', 'none');
    });
    jQuery('#optionCategory').click(function () {
        jQuery('#productItem').css('display', 'none');
        jQuery('#categoryItem').css('display', 'block');
    });
});