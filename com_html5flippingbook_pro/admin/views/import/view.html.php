<?php defined('_JEXEC') or die('Restricted Access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/HtmlHelper.php');

class HTML5FlippingBookViewImport extends JViewLegacy
{
	//----------------------------------------------------------------------------------------------------
	function display($tpl = null) 
	{
		HtmlHelper::addCss();

		$this->flashmagazine = $this->flashmagazine();
		$this->categories = $this->get_categories();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		HtmlHelper::showTitle(JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_IMPORT'), '_import_data');
	}

	protected function flashmagazine()
	{
		$db = JFactory::getDbo();
		$allTables = $db->getTableList();

		if ( array_search( JFactory::getApplication()->getCfg('dbprefix').'flashmag_category', $allTables) )
		{
			// category items cache
			$categoriesCache = array();

			$db->setQuery("SELECT * FROM #__flashmag_magazine");
			$flashMagMagazine = $db->loadObjectList();

			if ( $flashMagMagazine )
			{
				$items = array();

				foreach ( $flashMagMagazine as $magazine )
				{
					if ( empty($categoriesCache[ $magazine->c_category_id ]) )
					{
						$db->setQuery("SELECT * FROM #__flashmag_category WHERE c_id = " . $magazine->c_category_id );
						$categoriesCache[ $magazine->c_category_id ] = $db->loadObject();
					}

					$magazine->category = $categoriesCache[ $magazine->c_category_id ];

					$db->setQuery("SELECT COUNT(*) FROM #__flashmag_pages WHERE magazine_id = " . $magazine->c_id);
					$magazine->pages_count = $db->loadResult();

					$db->setQuery("SELECT c_id FROM #__html5fb_publication WHERE `c_title` = ".$db->quote($magazine->c_title)." AND `c_created_time` = ".$db->quote($magazine->c_created_time));
					$magazine->exists = $db->loadObject();

					$items[] = $magazine;
				}

				return $items;
			}
			else
				return false;
		}
		else
			return false;
	}

	protected function get_categories()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('c.`c_id` AS value, c.`c_category` AS text');
		$query->from('`#__html5fb_category` AS c');
		$query->order('c.`c_category` ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JText::_('JOPTION_SELECT_CATEGORY'));

		return $options;
	}


}