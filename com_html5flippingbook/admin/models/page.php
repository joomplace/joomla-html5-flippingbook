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

		return parent::save($data);
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

    public function saveorderinp($pks, $order, $publ_id)
    {

        $db = $this->getDbo();

        foreach ($pks as $key => $v) {

            $query = $db->getQuery(true);
            $query->select($db->qn('c.*'))
                ->from($db->qn('#__html5fb_pages', 'c'))
                ->where($db->qn('c.publication_id').'='.$publ_id)
                ->where($db->qn('c.id').'='.$v);
            $db->setQuery($query);
            $old_row = $db->loadObject();

            if ($old_row->ordering !== $order[$key] && $old_row->id === $v) {
                $new_row = new \stdClass();
                $new_row->id = $old_row->id;
                $new_row->publication_id = $old_row->publication_id;
                $new_row->page_title = $old_row->page_title;
                $new_row->ordering = $order[$key];
                $new_row->c_enable_image = $old_row->c_enable_image;
                $new_row->page_image = $old_row->page_image;
                $new_row->c_enable_text = $old_row->c_enable_text;
                $new_row->c_text = $old_row->c_text;
                $new_row->is_contents = $old_row->is_contents;

                $result = $db->updateObject('#__html5fb_pages', $new_row, 'id');

                if ($result !== false) {
                    unset($new_row);
                    unset($old_row);
                } else {
                    $error[] = 'Error upload' . ' ' . $new_row->id;
                }
            } else {
                continue;
            }

        }

        if (!empty($error)) {
            return false;
        } else {
            return true;
        }

    }
}