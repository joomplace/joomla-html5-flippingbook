<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewConfiguration extends JViewLegacy
{
	protected $googlePlusLanguageOptions;
	protected $twitterLanguageOptions;
	protected $facebookFontOptions;
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null) 
	{
		$document = JFactory::getDocument();
		$document->addScript(COMPONENT_JS_URL.'BootstrapFormHelper.js');
		$document->addScript(COMPONENT_JS_URL.'BootstrapFormValidator.js');
		
		HtmlHelper::addCss();
		
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		$this->googlePlusLanguageOptions = array(
			JHTML::_('select.option', 'en-US', 'English (United States)'),
			JHTML::_('select.option', 'en-GB', 'English (United Kingdom)'),
			JHTML::_('select.option', 'af', 'Afrikaans'),
			JHTML::_('select.option', 'am', 'Amharic'),
			JHTML::_('select.option', 'ar', 'Arabic'),
			JHTML::_('select.option', 'eu', 'Basque'),
			JHTML::_('select.option', 'bn', 'Bengali'),
			JHTML::_('select.option', 'bg', 'Bulgarian'),
			JHTML::_('select.option', 'ca', 'Catalan'),
			JHTML::_('select.option', 'zh-HK', 'Chinese (Hong Kong)'),
			JHTML::_('select.option', 'zh-CN', 'Chinese (Simplified)'),
			JHTML::_('select.option', 'zh-TW', 'Chinese (Traditional)'),
			JHTML::_('select.option', 'hr', 'Croatian'),
			JHTML::_('select.option', 'cs', 'Czech'),
			JHTML::_('select.option', 'da', 'Danish'),
			JHTML::_('select.option', 'nl', 'Dutch'),
			JHTML::_('select.option', 'et', 'Estonian'),
			JHTML::_('select.option', 'fil', 'Filipino'),
			JHTML::_('select.option', 'fi', 'Finnish'),
			JHTML::_('select.option', 'fr', 'French'),
			JHTML::_('select.option', 'fr-CA', 'French (Canadian)'),
			JHTML::_('select.option', 'gl', 'Galician'),
			JHTML::_('select.option', 'de', 'German'),
			JHTML::_('select.option', 'el', 'Greek'),
			JHTML::_('select.option', 'gu', 'Gujarati'),
			JHTML::_('select.option', 'iw', 'Hebrew'),
			JHTML::_('select.option', 'hi', 'Hindi'),
			JHTML::_('select.option', 'hu', 'Hungarian'),
			JHTML::_('select.option', 'is', 'Icelandic'),
			JHTML::_('select.option', 'id', 'Indonesian'),
			JHTML::_('select.option', 'it', 'Italian'),
			JHTML::_('select.option', 'ja', 'Japanese'),
			JHTML::_('select.option', 'kn', 'Kannada'),
			JHTML::_('select.option', 'ko', 'Korean'),
			JHTML::_('select.option', 'lv', 'Latvian'),
			JHTML::_('select.option', 'lt', 'Lithuanian'),
			JHTML::_('select.option', 'ms', 'Malay'),
			JHTML::_('select.option', 'ml', 'Malayalam'),
			JHTML::_('select.option', 'mr', 'Marathi'),
			JHTML::_('select.option', 'no', 'Norwegian'),
			JHTML::_('select.option', 'fa', 'Persian'),
			JHTML::_('select.option', 'pl', 'Polish'),
			JHTML::_('select.option', 'pt-BR', 'Portuguese (Brazil)'),
			JHTML::_('select.option', 'pt-PT', 'Portuguese (Portugal)'),
			JHTML::_('select.option', 'ro', 'Romanian'),
			JHTML::_('select.option', 'ru', 'Russian'),
			JHTML::_('select.option', 'sr', 'Serbian'),
			JHTML::_('select.option', 'sk', 'Slovak'),
			JHTML::_('select.option', 'sl', 'Slovenian'),
			JHTML::_('select.option', 'es', 'Spanish'),
			JHTML::_('select.option', 'es-419', 'Spanish (Latin America)'),
			JHTML::_('select.option', 'sw', 'Swahili'),
			JHTML::_('select.option', 'sv', 'Swedish'),
			JHTML::_('select.option', 'ta', 'Tamil'),
			JHTML::_('select.option', 'te', 'Telugu'),
			JHTML::_('select.option', 'th', 'Thai'),
			JHTML::_('select.option', 'tr', 'Turkish'),
			JHTML::_('select.option', 'uk', 'Ukrainian'),
			JHTML::_('select.option', 'ur', 'Urdu'),
			JHTML::_('select.option', 'vi', 'Vietnamese'),
			JHTML::_('select.option', 'zu', 'Zulu'),
			);
		
		$this->twitterLanguageOptions = array(
			JHTML::_('select.option', 'en', 'English'),
			JHTML::_('select.option', 'ar', 'Arabic'),
			JHTML::_('select.option', 'eu', 'Basque'),
			JHTML::_('select.option', 'ca', 'Catalan'),
			JHTML::_('select.option', 'cs', 'Czech'),
			JHTML::_('select.option', 'zh-cn', 'Simplified Chinese'),
			JHTML::_('select.option', 'zh-tw', 'Traditional Chinese'),
			JHTML::_('select.option', 'da', 'Danish'),
			JHTML::_('select.option', 'nl', 'Dutch'),
			JHTML::_('select.option', 'fa', 'Farsi'),
			JHTML::_('select.option', 'fil', 'Filipino'),
			JHTML::_('select.option', 'fi', 'Finnish'),
			JHTML::_('select.option', 'fr', 'French'),
			JHTML::_('select.option', 'de', 'German'),
			JHTML::_('select.option', 'el', 'Greek'),
			JHTML::_('select.option', 'he', 'Hebrew'),
			JHTML::_('select.option', 'hi', 'Hindi'),
			JHTML::_('select.option', 'hu', 'Hungarian'),
			JHTML::_('select.option', 'id', 'Indonesian'),
			JHTML::_('select.option', 'it', 'Italian'),
			JHTML::_('select.option', 'ja', 'Japanese'),
			JHTML::_('select.option', 'ko', 'Korean'),
			JHTML::_('select.option', 'msa', 'Malay'),
			JHTML::_('select.option', 'no', 'Norwegian'),
			JHTML::_('select.option', 'pl', 'Polish'),
			JHTML::_('select.option', 'pt', 'Portuguese'),
			JHTML::_('select.option', 'ru', 'Russian'),
			JHTML::_('select.option', 'es', 'Spanish'),
			JHTML::_('select.option', 'sv', 'Swedish'),
			JHTML::_('select.option', 'th', 'Thai'),
			JHTML::_('select.option', 'tr', 'Turkish'),
			JHTML::_('select.option', 'uk', 'Ukrainian'),
			JHTML::_('select.option', 'ur', 'Urdu'),
			);
		
		$this->facebookFontOptions = array(
			JHTML::_('select.option', 'arial', 'arial'),
			JHTML::_('select.option', 'lucida grande', 'lucida grande'),
			JHTML::_('select.option', 'segoe ui', 'segoe ui'),
			JHTML::_('select.option', 'tahoma', 'tahoma'),
			JHTML::_('select.option', 'trebuchet ms', 'trebuchet ms'),
			JHTML::_('select.option', 'verdana', 'verdana'),
			);

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_CONFIGURATION'), '_configuration');

		JToolBarHelper::apply('configuration.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::cancel('configuration.cancel', 'JTOOLBAR_CANCEL');
	}
}