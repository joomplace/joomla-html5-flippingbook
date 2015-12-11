<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookController extends JControllerLegacy
{
	//----------------------------------------------------------------------------------------------------
	function __construct($config = array())
	{
		$this->default_view = 'control_panel';
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
	}
}