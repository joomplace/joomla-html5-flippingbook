<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5 Flipping Book plugin
* @package HTML5 Flipping Book
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.plugin.plugin');

class plgButtonHtml5flippingbook_button extends JPlugin
{
	protected $autoloadLanguage = true;

	//----------------------------------------------------------------------------------------------------
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}
	//----------------------------------------------------------------------------------------------------
	function onDisplay($name, $asset, $author)
	{
		$js = "
			function html5fbInsertPublicationTag(pubId, linkText)
			{
				var tagContent = '';
				
				if (linkText == '')
				{
					tagContent = '{html5fb id=' + pubId + '}';
				}
				else
				{
					tagContent = '{html5fb id=' + pubId + ' link=' + linkText + '}';
				}
				
				jInsertEditorText(tagContent, '".$name."');
				SqueezeBox.close();
			}";
		
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		
		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->modal = true;
		$button->link = 'index.php?option=com_html5flippingbook&amp;view=insert_publication_tag&amp;tmpl=component';
		$button->class = 'btn';
		$button->text = JText::_('PLG_HTML5FLIPPINGBOOK_BUTTON_BUTTON_TEXT');
		$button->name = 'tablet';
		$button->options =  "{handler: 'iframe', size: {x: 600, y: 220}}";

		return $button;
	}
}