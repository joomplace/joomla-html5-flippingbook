<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JFormHelper::loadFieldClass('list');

class JFormField_Template extends JFormFieldList
{
	protected $type = '_template';
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
		$attr = '';
		$this->multiple = false;
		
		ob_start();

		$addjs = ob_get_contents();
		ob_get_clean();
		
		$html[] = $addjs;
		
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= ($this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '');
		$attr .= ($this->multiple ? ' multiple="multiple"' : '');

		$options = (array) $this->getOptions();
		array_unshift($options, JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_SELECT_TEMPLATE'));
		$selected = (array)$this->getSelected();
		
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		else
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selected, $this->id);
		}
		
		return implode('', $html);
	}
	//----------------------------------------------------------------------------------------------------
	public function setProperty($name, $value)
	{
		$this->element[$name] = $value;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getSelected()
	{
		$jinput = JFactory::getApplication()->input;
		
		$id = $jinput->get('c_id');
		
		$selected = array();
		
		if ($id)
		{
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true);
			$query->select('m.c_template_id');
			$query->from('#__html5fb_publication AS m');
			$query->where('m.c_id='.(int) $id);
			$db->setQuery($query);
			$row = $db->loadObject();
			
			if (isset($row)) $selected = (array) $row;
		}
		
		return $selected;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('t.id AS value, t.template_name AS text');
		$query->from('#__html5fb_templates AS t');
		$query->order('t.template_name  ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}
}