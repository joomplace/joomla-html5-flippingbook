<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerResolutions extends JControllerAdmin
{
	//----------------------------------------------------------------------------------------------------
	public function getModel($name = 'Resolution', $prefix = COMPONENT_MODEL_PREFIX, $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
}