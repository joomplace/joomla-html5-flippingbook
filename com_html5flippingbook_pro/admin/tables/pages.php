<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookTablePages extends JTable
{
	//----------------------------------------------------------------------------------------------------
	function __construct(&$db) 
	{
		parent::__construct('#__html5fb_pages', 'id', $db);
	}
	//----------------------------------------------------------------------------------------------------
	public function store($updateNulls = false)
	{
		if (!$this->id)
		{
			$db = $this->_db;
			$query = "SELECT MAX(`ordering`)" .
				" FROM `#__html5fb_pages`" .
				" WHERE `publication_id` = " . $this->publication_id;
			$db->setQuery($query);
			$this->ordering = $db->loadResult() + 1;
		}
        else
        {
            @unlink(COMPONENT_MEDIA_PATH.'/thumbs/preview_'.$this->publication_id.'.gif');
        }

//		parent::reorder('`publication_id` = ' . $this->publication_id);

		return parent::store($updateNulls);
	}
	//----------------------------------------------------------------------------------------------------
	public function delete($pk = null)
	{
        @unlink(COMPONENT_MEDIA_PATH.'/thumbs/preview_'.$this->publication_id.'.gif');

		$db = $this->_db;
		
		$query = "SELECT p.`page_image`,m.`c_imgsubfolder`, p.`c_text` " .
			" FROM `#__html5fb_pages` as p" .
			" LEFT JOIN `#__html5fb_publication` as m ON m.`c_id` = p.`publication_id`" .
			" WHERE p.`id` = " . $pk;
		$db->setQuery($query);
		$page = $db->loadObject();
		
		$deleted = parent::delete($pk);
		
		if (!$deleted)
		{
			$error = $this->getError();
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_CANT_DELETE', $pk) . ($error == '' ? '' : '. ' . $error), 'error');
			return false;
		}

		// Deleting resources, if they are not used by other pages.
		jimport('joomla.filesystem.file');
		
		if ($page->page_image != '')
		{
			$query = "SELECT COUNT(*)" .
				" FROM `#__html5fb_pages` as p" .
				" LEFT JOIN `#__html5fb_publication` as m ON m.`c_id` = p.`publication_id`" .
				" WHERE p.`page_image` = " . $db->quote($page->page_image) .
				" AND m.`c_imgsubfolder` = " . $db->quote($page->c_imgsubfolder);
			$db->setQuery($query);
			$count = $db->loadResult();
			
			if ($count == 0)
			{
				$imageFileFullName = COMPONENT_MEDIA_PATH.'/images/'.($page->c_imgsubfolder == '' ? '' : $page->c_imgsubfolder.'/').$page->page_image;
				
				if (JFile::exists($imageFileFullName)) JFile::delete($imageFileFullName);
			}
		}

        // Deleting media files, if they are not used by other pages
        if ( strpos($page->c_text, '<audio') !== false
            ||  strpos($page->c_text, '<video') !== false )
        {
            preg_match_all('/<[audio|video][^src]+src="([^"]+)[^>]+/is', $page->c_text, $mediaObjects);
            foreach ( $mediaObjects[1] as $objectPath )
            {
                // check used by other pages
                $query = "SELECT COUNT(*) FROM `#__html5fb_pages` WHERE `c_text` LIKE '%" . $db->escape($objectPath) . "%'";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ( $count == 0 )
                {
                    if (JFile::exists(JPATH_SITE.'/'.$objectPath)) JFile::delete(JPATH_SITE.'/'.$objectPath);
                }
            }
        }

		
		return true;
	}
}