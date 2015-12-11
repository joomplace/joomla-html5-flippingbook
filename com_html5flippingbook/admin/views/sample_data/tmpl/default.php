<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JHtml::_('behavior.tooltip');
?>

<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" autocomplete="off">
	<input type="hidden" name="option" value="<?php echo COMPONENT_OPTION; ?>" />
	<input type="hidden" name="view" value="<?php echo $this->getName(); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
		<div class="hero-unit" style="padding: 20px ! important;">
			<h1><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_SAMPLEDATA'); ?></h1>
			<br />
			<div class="well span6" style="width: 48%;">
				<img src="<?php echo COMPONENT_ASSETS_URL;?>images/sample/sample_steve_jobs.jpg" />
				<br />
				<br />
				<p>Steve Jobs is the authorized biography of Steve Jobs. The biography was written at the request of Jobs by acclaimed biographer Walter Isaacson, a former executive at
					CNN and Time who has written best-selling biographies about Benjamin Franklin and Albert Einstein.</p>
			</div>
			<div class="well span6" style="width: 48%;">
				<img src="<?php echo COMPONENT_ASSETS_URL;?>images/sample/sample_data_online_shopping.jpg" style="height: 296px;"/>
				<br />
				<br />
				<p>Here I present to you some collection of Bedroom Design concept from IKEA Catalog 2013. We hope it can be a bedroom design inspiration for you or it can be a reference for you to redesign your old bedroom.<br /></p>
			</div>
			<p style="clear: both">
				<h2><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_SAMPLEDATA_TEXT'); ?></h2>
				<br />
				<button class="btn btn-primary btn-large" onclick="Joomla.submitbutton('sample_data.install');">
					<i class="icon-download"></i>
					<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_SAMPLEDATA_INSTALL'); ?>
				</button>
			</p>
		</div>

</form>