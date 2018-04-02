<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookModelPage extends JModelAdmin
{
	protected $text_prefix = COMPONENT_OPTION;
	//----------------------------------------------------------------------------------------------------
	public function getTable($type = 'pages', $prefix = COMPONENT_TABLE_PREFIX, $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	//----------------------------------------------------------------------------------------------------
	public function getItem($pk = null)
	{
		$database = JFactory::getDbo();
		$item = parent::getItem($pk);

		if ( empty($item->id) )
		{
			$item->page_type = 'image';
			$item->publication_id = JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.pages.filter.publication_id', 0);
		}
		else
		{
			if ( $item->c_enable_image )
				$item->page_type = 'image';
			else
				$item->page_type = 'text';

			$database->setQuery("SELECT id FROM #__html5fb_pages WHERE `ordering` < ".$item->ordering." AND publication_id = ".$item->publication_id." ORDER BY `ordering` DESC LIMIT 1");
			$item->prev_page = (int)$database->loadResult();

			$database->setQuery("SELECT id FROM #__html5fb_pages WHERE `ordering` > ".$item->ordering." AND publication_id = ".$item->publication_id." ORDER BY `ordering` ASC LIMIT 1");
			$item->next_page = (int)$database->loadResult();
		}

		$database->setQuery("SELECT * FROM #__html5fb_publication WHERE c_id = ".$item->publication_id);
		$item->publication = $database->loadObject();

		$database->setQuery("SELECT * FROM #__html5fb_resolutions WHERE id = ".$item->publication->c_resolution_id);
		$item->page_resolution = $database->loadObject();

		return $item;
	}
	//----------------------------------------------------------------------------------------------------
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.edit.page.data', array());
		
		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	//----------------------------------------------------------------------------------------------------
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(COMPONENT_OPTION.'.pages', 'page', array('control' => 'jform', 'load_data' => $loadData));
		return (empty($form) ? false : $form);
	}
	//----------------------------------------------------------------------------------------------------
	public function save($data)
	{
		$data['c_enable_image'] = 0;
		$data['c_enable_text'] = 0;
		
		switch ($data['page_type'])
		{
			case 'image': {
				$data['c_enable_image'] = 1;
				$data['c_text'] = '';
			} break;
			case 'text': {
				$data['c_enable_text'] = 1;
				$data['page_image'] = '';
			} break;
		}

	    $data['c_text'] = $_POST['jform']['c_text'];    // TODO: just for work
        $data['canvas'] = isset($_POST['jform']['canvas']) ? $_POST['jform']['canvas'] : '';

        if(parent::save($data)){
            //save html-page as svg-file
            if($data['page_type'] == 'text' && (int)$data['enable_svg']) {
                Html5flippingbookImagehandlerHelper::saveHtmlPageToSvgFile($data);
            }
            //delete svg-file if it exists
            else {
                Html5flippingbookImagehandlerHelper::deleteFile(COMPONENT_MEDIA_PATH.'/svg/'.(int)$data['publication_id'].'/'.(int)$data['id'].'.svg');
            }
            Html5flippingbookImagehandlerHelper::savePagePreview($data);
        }

		return true;
	}
	//----------------------------------------------------------------------------------------------------
	public function delete(&$pks)
	{
		return parent::delete($pks);
	}
	//----------------------------------------------------------------------------------------------------
	public function move($ids, $targetPublicationId)
	{
		$this->copy($ids, $targetPublicationId);
		
		$this->delete($ids);
	}
	//----------------------------------------------------------------------------------------------------
	public function copy($ids, $targetPublicationId)
	{
		$db = $this->_db;
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$query = "SELECT `c_imgsubfolder`" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_id` = " . $targetPublicationId;
		$db->setQuery($query);
		$targetImagesSubdir = $db->loadResult();
		
		$query = "SELECT p.*, m.`c_title` as publication_name, m.`c_imgsubfolder`" .
			" FROM `#__html5fb_pages` as p" .
			" LEFT JOIN `#__html5fb_publication` as m ON m.`c_id` = p.`publication_id`" .
			" WHERE p.`id` IN (" . implode(',', $ids) . ")";
		$db->setQuery($query);
		$selectedPages = $db->loadObjectList();
		
		$query = "SELECT p.`id`, p.`page_title`, p.`publication_id`, m.`c_title` as publication_name, m.`c_imgsubfolder`, p.`page_image`" .
			" FROM `#__html5fb_pages` as p" .
			" LEFT JOIN `#__html5fb_publication` as m ON m.`c_id` = p.`publication_id`" .
			" WHERE `publication_id` = " . $targetPublicationId;
		$db->setQuery($query);
		$targetPubPages = $db->loadObjectList();
		
		//==================================================
		// Checking resources conflicts.
		//==================================================
		
		foreach ($selectedPages as $selectedPage)
		{
			if ($selectedPage->publication_id == $targetPublicationId)
			{
				throw new Exception(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_PAGE_ALREADY_BELONGS_TO_PUBLICATION', $selectedPage->page_title, $selectedPage->id));
			}
			
			foreach ($targetPubPages as $targetPubPage)
			{
				if (($selectedPage->page_image != '' && $targetPubPage->page_image == $selectedPage->page_image))
				{
					throw new Exception(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONFLICT_ON_COPYING', $selectedPage->page_title, $selectedPage->id, $selectedPage->publication_name,
						$targetPubPage->page_title, $targetPubPage->id, $targetPubPage->publication_name) . '. ' . JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONFLICT_TIP'));
				}
			}
		}
		
		//==================================================
		// Copying resources.
		//==================================================
		
		foreach ($selectedPages as $page)
		{
			if ($page->page_image != '')
			{
				$sourceImageFileFullName = COMPONENT_MEDIA_PATH.'/images/'.($page->c_imgsubfolder == '' ? '' : $page->c_imgsubfolder.'/').$page->page_image;
				$targetImageFileFullName = COMPONENT_MEDIA_PATH.'/images/'.($targetImagesSubdir == '' ? '' : $targetImagesSubdir.'/').$page->page_image;

                if (JFile::exists($sourceImageFileFullName) && $sourceImageFileFullName != $targetImageFileFullName )
				{
					JFile::copy($sourceImageFileFullName, $targetImageFileFullName);
				}
			}
		}
		
		//==================================================
		// Copying database rows.
		//==================================================
		
		// Getting pages columns names.
		
		$query = "SHOW COLUMNS FROM `#__html5fb_pages`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$pageColNames = array();
		
		foreach ($rows as $row)
			if ($row->Field != 'id')
                $pageColNames[] = $row->Field;

		// Getting max ordering.
		
		$query = "SELECT MAX(`ordering`)" .
			" FROM `#__html5fb_pages`" .
			" WHERE `publication_id` = " . $targetPublicationId;
		$db->setQuery($query);
		$maxOrdering = $db->loadResult();
		
		// Copying.
		
		foreach ($selectedPages as $selectedPage)
		{
			$maxOrdering += 1;
			
			$query = "INSERT INTO `#__html5fb_pages` (";
			
			for ($i = 0; $i < count($pageColNames); $i++)
			{
				$columnName = $pageColNames[$i];
				
				$query .= ($i == 0 ? "" : ", ") . "`" . $columnName . "`";
			}
			
			$query .= ") VALUES (";
			
			for ($i = 0; $i < count($pageColNames); $i++)
			{
				$columnName = $pageColNames[$i];
				
				switch ($columnName)
				{
					case 'publication_id':
					{
						$value = $targetPublicationId;
						break;
					}
					case 'ordering':
					{
						$value = $maxOrdering;
						break;
					}
					default:
					{
						$value = $selectedPage->{$columnName};
					}
				}
				
				$query .= ($i == 0 ? "" : ", ") . $db->quote($value);
			}
			
			$query .= ")";
			
			$db->setQuery($query);
			$db->execute();
		}
	}
}