<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class JFormField_ColorPicker extends JFormField
{
	protected $type = '_colorpicker';
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
		if (!defined('CUSTOM_COLOR_PICKER_ADDED'))
		{
			define('CUSTOM_COLOR_PICKER_ADDED', true);
			
			$document = JFactory::getDocument();
			
			$document->addStyleSheet(COMPONENT_CSS_URL.'colorpicker.css');
			$document->addScript(COMPONENT_JS_URL.'ColorPicker.js');
		}
		
		$classes = (string) $this->element['class'];
		
		$class = $classes ? ' class="' . trim($classes) . '"' : '';
		
		$value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		
		$previewElementId = $this->id . '-preview';
		
		$previewBgColor = ($value == "" ? '#ffffff' : $value);
		
		return '<div' . $class . '>' .
				'<input type="text"' .
					' name="' . $this->name . '"' .
					' id="' . $this->id . '"' .
					' size="7"' .
					' maxlength="7"' .
					' value="' . $value . '"' .
					' />' .
				'<div id="' . $previewElementId . '" style="background-color:' . $previewBgColor . ';"></div>' .
				'<a href="javascript:void(0)" onclick="showColorPicker(this, \'' . $this->id . '\', \'' . $previewElementId . '\');">' .
					'<img src="' . COMPONENT_IMAGES_URL.'colorpicker/color_picker.gif' . '" border="0" width="20" height="20" />' .
				'</a>' .
			'</div>';
	}
	//----------------------------------------------------------------------------------------------------
	public function setProperty($name, $value)
	{
		$this->element[$name] = $value;
	}
}