<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$maxSize = min((int) ini_get('post_max_size'), (int) ini_get('upload_max_filesize'));
$doc = JFactory::getApplication()->getDocument();
$doc->addStyleSheet("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css");
$doc->addStyleDeclaration("
	.progress {
		width: 100%;
		margin-bottom: 40px;
	}
	.result {
		display: none;
	}
	input {
		outline: none;
	}
	h2 {
		margin-top: 5px;
	}
	.progress-bar-container {
		display: none;
	}
	.progress-bar {
	    height: 100%;
	    width: 0px;
	}
");
?>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script type="text/javascript">

    jQuery(document).ready(function ()
    {
        jQuery(".progress-bar-container").show();
        var params = getUrlVars();
        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }
        delete params["view"];
        delete params["layout"];
        params["task"] = "pages.convert";
        params["islast"] = 0;
        var i = 1;
        var count = params["count"];

        sendRequest();
        function sendRequest() {
            params["pageNumb"] = i;
            if (i == count) {
                params["islast"] = 1;
            }
            jQuery.ajax({
                type: "POST",
                url: "<?php echo JURI::root(); ?>administrator/index.php",
                data: params,
                success: function (data) {
                    if (data == 1) {
                        window.location.href = "<?php echo JURI::root(); ?>administrator/index.php?option=com_html5flippingbook&view=pages";
                    }
                    jQuery(".progress-bar").css("width", 100*(i/count) + "%");
                    i++;
                    if (i<=count) {
                        sendRequest();
                    }
                },
                error: function () {
                    alert('ERROR REQUEST!');
                }
            });
        }
    })

</script>
<?php echo HtmlHelper::getMenuPanel(); ?>
<div class="progress-bar-container">
    <p>Please, wait...</p>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
</form>