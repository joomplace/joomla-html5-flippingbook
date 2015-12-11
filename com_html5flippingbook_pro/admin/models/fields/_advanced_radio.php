<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JFormHelper::loadFieldClass('radio');

//==================================================
// Using one 'onclick' parameter (instead of separate for each option in default field).
//==================================================
class JFormField_Advanced_Radio extends JFormFieldRadio
{
	protected $type = '_advanced_radio';
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
		$html = array();
		
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';
		
		$onclick = (!empty($this->element['onclick']) ? ' onclick="' . $this->element['onclick'] . '"' : '');
		
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
		
		$options = $this->getOptions();
		foreach ($options as $i => $option)
		{
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
			$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
			
			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';
				
			$html[] = '<label for="' . $this->id . $i . '"' . $class . '>'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
		}
		
		$html[] = '</fieldset>';
		
		return implode($html);
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