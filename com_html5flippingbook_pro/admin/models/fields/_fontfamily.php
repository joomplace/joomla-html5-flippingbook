<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

JFormHelper::loadFieldClass('list');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');

class JFormField_Fontfamily extends JFormFieldList
{
	protected $type = '_fontfamily';
	protected $fontsList = array();

	//----------------------------------------------------------------------------------------------------
	public function __construct($form = null)
	{
		$this->fontsList = PublicationTemplateFont::FontsList();
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
		
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= ' style="width:435px;"';
		$attr .= ($this->multiple ? ' multiple="multiple"' : '');
		
		$options = (array) $this->getOptions();
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
		$options = array();

		foreach ( $this->fontsList as $font_id => $font_value)
		{
			$options[] = array( 'text'=>$font_value, 'value' => $font_id);
		}

		return $options;
	}
	//----------------------------------------------------------------------------------------------------
	protected function getSelected()
	{
		$selected = array();
		
		if ( empty($this->value) )
		{
			$id = JFactory::getApplication()->input->get('id', 0);
			
			if ($id != 0)
			{
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);
				$query->select('t.`fontfamily`');
				$query->from('`#__html5fb_templates` AS t');
				$query->where('t.`id` = ' . (int) $id);
				$db->setQuery($query);

				$selected = array( (int)$db->loadResult() );
			}
		}
		else
			$selected = $this->value;
		
		return $selected;
	}
}