<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JFormHelper::loadFieldClass('list');

class JFormField_Publication extends JFormFieldList
{
	protected $type = '_publication';
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
		$jinput = JFactory::getApplication()->input;
		
		$option = $jinput->get('option');
		
		$html = array();
		$attr = '';
		$this->multiple = false;
		
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= ($this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '');
		$attr .= ($this->multiple ? ' multiple="multiple"' : '');
		
		$options = (array) $this->getOptions();
		array_unshift($options, JText::_('COM_HTML5FLIPPINGBOOK_BE_SELECT_PUBLICATION'));
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
		
		return implode($html);
	}
	//----------------------------------------------------------------------------------------------------
	public function setProperty($name, $value)
	{
		$this->element[$name] = $value;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('m.`c_id` AS value, m.`c_title` AS text');
		$query->from('`#__html5fb_publication` AS m');
		$query->order('m.`c_title` ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getSelected()
	{
		$selected = array();
		
		if (sizeof($this->value) > 0)
		{
			$selected[] = $this->value;
		}
		else
		{
			$jinput = JFactory::getApplication()->input;
			
			$id = $jinput->get('id', 0);
			
			if ($id != 0)
			{
				$db = JFactory::getDbo();
				
				$query = $db->getQuery(true);
				$query->select('p.`publication_id`');
				$query->from('`#__html5fb_pages` AS p');
				$query->where('p.`id` = ' . (int) $id);
				$db->setQuery($query);
				$selected = $db->loadColumn(0);
			}
		}
		
		return $selected;
	}
	//----------------------------------------------------------------------------------------------------
	public function setValue($value)
	{
		$this->value = $value;
	}
}