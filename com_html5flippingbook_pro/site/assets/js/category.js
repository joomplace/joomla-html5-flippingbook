function userPublAction(publID, action) {
    var availableActions = ['reading', 'reading_remove', 'favorite', 'favorite_remove', 'read', 'read_remove'];
    var actionsIcons = ['fa-list', 'fa-trash-o', 'fa-star', 'fa-trash-o', 'fa-eye-slash', 'fa-eye'];
    var actionsText = [
        Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_READING_TIP'),
        Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READING'),
        Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_FAVORITE_TIP'),
        Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_FAVORITE'),
        Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_READ_TIP'),
        Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READ')
    ];
    if (jQuery.inArray(action, availableActions) != -1) {
        if (!user) {
            alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_USER'));
            return;
        }
        jQuery('#loader-' + publID).show();
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component',
            data: 'pubID=' + publID + '&action=' + action + (action == 'favorite' ? '&favorite=1' : (action == 'read' ? '&read=1' : '')),
            dataType: 'JSON',
            success: function (data) {
                jQuery('#loader-' + publID).hide();

                if (!data.error) {
                    var curAction = action.split('_')[0];
                    var actIndx = jQuery.inArray(action, availableActions);

                    if (action == 'read' || action == 'favorite' || action == 'reading') {
                        jQuery('a#' + curAction + '_' + publID).attr('onclick', "userPublAction(" + publID + ", '" + curAction + "_remove'); return false;");
                        jQuery('a#' + curAction + '_' + publID + ' > i.' + actionsIcons[actIndx]).removeClass(actionsIcons[actIndx]).addClass(actionsIcons[actIndx + 1]);
                        jQuery('a#' + curAction + '_' + publID + ' > #' + curAction + '_text_' + publID).text(actionsText[actIndx + 1]);
                    }
                    else if (action == 'read_remove' || action == 'favorite_remove' || action == 'reading_remove') {
                        jQuery('a#' + curAction + '_' + publID).attr('onclick', "userPublAction(" + publID + ", '" + curAction + "'); return false;");
                        jQuery('a#' + curAction + '_' + publID + ' > i.' + actionsIcons[actIndx]).removeClass(actionsIcons[actIndx]).addClass(actionsIcons[actIndx - 1]);
                        jQuery('a#' + curAction + '_' + publID + ' > #' + curAction + '_text_' + publID).text(actionsText[actIndx - 1])
                    }
                }
                else {
                    alert(data.message);
                    return false;
                }
            }
        });
    }
    else {
        alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_ACTION'));
        return false;
    }
}