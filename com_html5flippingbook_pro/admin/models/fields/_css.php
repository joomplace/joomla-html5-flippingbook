<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JFormHelper::loadFieldClass('hidden');

//==================================================
// Allows adding custom CSS declarations to page. This is usefull for Joomla menu items, where you do not control form output with PHP (
// for example, enables to change control labels min-width). Field itself should be hidden via CSS also, otherwise there will be empty space.
//==================================================
class JFormField_CSS extends JFormFieldHidden
{
	protected $type = '_css';
	//----------------------------------------------------------------------------------------------------
	public function __construct($form = null)
	{
		parent::__construct($form);
	}
	//----------------------------------------------------------------------------------------------------
	public function getInput()
	{
		$value = ($this->element['value'] ? $this->element['value'] : '');
		
		$document = JFactory::getDocument();
		
		$document->addStyleDeclaration($value);
		
		return '';
	}
}