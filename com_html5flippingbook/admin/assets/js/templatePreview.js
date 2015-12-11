function updateTemplatePreview()
{
    if ( jQuery('#jform_fontfamily').val() )
        jQuery('.template_preview').css('font-family', jQuery('#jform_fontfamily option:selected').text());

    if ( jQuery('#jformfontsize').val() )
        jQuery('.template_preview').css('font-size', jQuery('#jformfontsize option:selected').text());

    if ( jQuery('#jformp_margin').val() )
        jQuery('.template_preview p').css('margin', jQuery('#jformp_margin option:selected').text()+' 0');

    if ( jQuery('#jformp_lineheight').val() )
        jQuery('.template_preview p').css('line-height', jQuery('#jformp_lineheight option:selected').text());

    if ( jQuery('#jform_background_color').val() )
        jQuery('.template_preview').css('background-color', jQuery('#jform_background_color').val());

    if ( jQuery('#jform_page_background_color').val() )
        jQuery('.template_preview > div').css('background-color', jQuery('#jform_page_background_color').val());

    if ( jQuery('#jform_text_color').val() )
        jQuery('.template_preview > div').css('color', jQuery('#jform_text_color').val());

    jQuery('a[href="#preview"]').animate({opacity:0},200,"linear",function(){ jQuery(this).animate({opacity:1},500); });
}

    jQuery(document).ready( function(){

        jQuery('#adminForm').find('input, select').bind('change', updateTemplatePreview);

    });