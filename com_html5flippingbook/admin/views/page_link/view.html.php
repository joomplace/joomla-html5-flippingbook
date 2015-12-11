<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookViewPage_link extends JViewLegacy
{
	protected $maxSize;

	//----------------------------------------------------------------------------------------------------
	public function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;

        $this->publication_id = $jinput->get('publication_id');
        $this->e_name = $jinput->get('e_name');
		$this->pages = array();

		$database = JFactory::getDbo();
		$database->setQuery("SELECT `page_title` FROM  #__html5fb_pages WHERE publication_id = ".$this->publication_id." ORDER BY `ordering`");
		$pages = $database->loadAssocList();
		foreach ( $pages as $k => $v)
			$this->pages[] = array('value'=>($k+1), 'text'=>$v['page_title']);

		parent::display($tpl);
	}
}