<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JFormHelper::loadFieldClass('text');

//==================================================
// Allows using JText constant as value (instead of raw text only in default field).
// Also allows changing value from PHP.
//==================================================
class JFormField_Advanced_Text extends JFormFieldText
{
	protected $type = '_advanced_text';
	//----------------------------------------------------------------------------------------------------
	public function __construct($form = null)
	{
		parent::__construct($form);
	}
	//----------------------------------------------------------------------------------------------------
	public function getLabel()
	{
		return parent::getLabel();
	}
	//----------------------------------------------------------------------------------------------------
	public function getInput()
	{
		$size = ($this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '');
		$maxLength = ($this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '');
		$class = ($this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '');
		$readonly = ((string) $this->element['readonly'] == 'true' ? ' readonly="readonly"' : '');
		$disabled = ((string) $this->element['disabled'] == 'true' ? ' disabled="disabled"' : '');
		
		$onchange = ($this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '');
		
		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' .
			htmlspecialchars(JText::_($this->value), ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
	}
	//----------------------------------------------------------------------------------------------------
	public function setProperty($name, $value)
	{
		$this->element[$name] = $value;
	}
	//----------------------------------------------------------------------------------------------------
	public function setValue($value)
	{
		$this->value = $value;
	}
}