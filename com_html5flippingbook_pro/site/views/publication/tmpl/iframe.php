<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once (COMPONENT_LIBS_PATH . 'Mobile_Detect.php');
$detectMobile = new Mobile_Detect_HTML5FB();
$uri = JUri::getInstance();
?>
<iframe width="100%" frameborder="0" height="<?php echo ($this->resolutions->height+100); ?>" src="<?php echo JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id.'&tmpl=component', FALSE, $uri->isSSL()); ?>">
</iframe>