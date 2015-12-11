<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerImport extends JControllerLegacy
{

	private $flashmag_categories = array();
	private $componentRootAssetId = 0;
	private $myResolutions = array();

	public function __construct( $config = array() )
	{
		$db = JFactory::getDbo();

		$query = "SELECT `id` FROM `#__assets` WHERE `name` = " . $db->quote(COMPONENT_OPTION);
		$db->setQuery($query);
		$this->componentRootAssetId = (int)$db->loadResult();

		$db->setQuery("SELECT id, resolution_name, height, width, CONCAT(width,'x',height) as resolution FROM `#__html5fb_resolutions`");
		$this->myResolutions = $db->loadObjectList('resolution');

		parent::__construct($config);
	}

	public function flashmagazine()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		define('FLASHPUBLICATION_MEDIA_PATH', JPATH_SITE.'/media/com_flashmagazinedeluxe');

		$item_id = JFactory::getApplication()->input->get('item_id', 0);

		if ( $item_id ) // one Item import
		{
			$this->flashmagazine_import($item_id);
		}
		else // import all
		{
			$db = JFactory::getDbo();

			$db->setQuery("SELECT * FROM #__flashmag_magazine");
			$items = $db->loadObjectList();

			foreach ( $items as $item )
				$this->flashmagazine_import( $item->c_id, $item );

		}

		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=import', JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_IMPORT_FINISH') , 'message');
	}

	protected function flashmagazine_import($item_id, $item = false)
	{
		$db = JFactory::getDbo();

		if ( !$item )
		{
			$db->setQuery("SELECT * FROM #__flashmag_magazine WHERE c_id = ". $item_id);
			$item = $db->loadObject();
		}

		$db->setQuery("SELECT * FROM #__flashmag_category WHERE c_id = " . $item->c_category_id);
		$item->category = $db->loadObject();

		$db->setQuery("SELECT * FROM #__flashmag_pages WHERE magazine_id = " . $item->c_id);
		$item->pages = $db->loadObjectList();

		$db->setQuery("SELECT * FROM #__flashmag_resolutions WHERE id = " . $item->c_resolution_id);
		$item->resolution = $db->loadObject();

		$item->resolution_id = $this->getResolutionId($item->resolution);
		$item->category_id = 	$this->flashmagazine_category_import($item);
		$item->id = 			$this->flashmagazine_magazine_import( $item );

		if ( $this->id != 'exists' )
			$this->flashmagazine_pages_import($item);
	}

	private function flashmagazine_category_import($item)
	{
		if ( !empty($this->flashmag_categories[ $item->category->c_id ]) )
			return $this->flashmag_categories[ $item->category->c_id ];

		$category_id = JFactory::getApplication()->input->get('flashmag_category_id', 0);
		$category_autocreate = JFactory::getApplication()->input->get('flashmag_category_autocreate', 0);

		// if need autocreate
		if ( $category_autocreate ) {
			$db = JFactory::getDbo();

			// checking exists category
			$db->setQuery("SELECT c_id FROM #__html5fb_category WHERE `c_category` = ".$db->quote($item->category->c_category)." AND `c_instruction` = ".$db->quote($item->category->c_instruction));
			$checkExists = $db->loadObject();

			if ( $checkExists )
			{
				$this->flashmag_categories[ $item->category->c_id ] = $checkExists->c_id;
				return $checkExists->c_id;
			}

				// add new category
				$db->setQuery("INSERT INTO #__html5fb_category (`c_category`, `c_instruction`, `user_id` )
									VALUES (" . $db->quote($item->category->c_category) . ", " . $db->quote($item->category->c_instruction) . ", '" . JFactory::getUser()->id . "')");
				$db->execute();

				$category_id = $db->insertid();

				$this->insertAsset( COMPONENT_OPTION.'.category.'.$category_id, $item->category->c_category);

			$this->flashmag_categories[ $item->category->c_id ] = $category_id;

		} else
		{
			$this->flashmag_categories[ $item->category->c_id ] = $category_id;
		}

		return $category_id;
	}

	private function flashmagazine_magazine_import($item)
	{
		$db = JFactory::getDbo();

		// check exists magazine
		$db->setQuery("SELECT c_id FROM #__html5fb_publication WHERE `c_title` = ".$db->quote($item->c_title)." AND `c_created_time` = ".$db->quote($item->c_created_time));
		$checkExists = $db->loadObject();

		if ( $checkExists )
			return 'exists';

		$item_allowedVars = array(
			'published',
			'c_title',
			'c_author',
			'c_imgsub',
			'c_imgsubfolder',
			'c_show_cdate',
			'c_created_time',
			'c_enable_pdf',
			'c_background_pdf',
			'c_thumb',
			'c_popup',
			'c_metadesc',
			'c_metakey',
			'hide_shadow',
			'c_enable_fullscreen',
			'fullscreen_mode',
			'right_to_left',
			'opengraph_title',
			'opengraph_author',
			'opengraph_image',
			'opengraph_description',
			'c_enable_frontpage',
			'c_author_image',
			'c_author_email',
			'c_author_description',
			'c_author_logo',
		);

		$insertItem = array();
		$insertItem['c_category_id'] = $item->category_id;
		$insertItem['c_user_id'] = JFactory::getUser()->id;
		$insertItem['c_resolution_id'] = $item->resolution_id;
		$insertItem['c_template_id'] = 1;
		$insertItem['c_pub_descr'] = $item->c_mag_descr;

		$db->setQuery("SELECT MAX(`ordering`) FROM #__html5fb_publication WHERE c_category_id = ".$item->category_id);
		$insertItem['ordering'] = (int)$db->loadResult();

		foreach ( $item_allowedVars as $var )
		{
			$insertItem[ $var ] = $item->$var;
		}

		$insertQuery = '(';
		$insertQuery_values = '(';

		foreach ( $insertItem as $key => $var )
		{
			$insertQuery.='`'.$key.'`, ';
			$insertQuery_values.= $db->quote($var).', ';
		}

		$query = "INSERT INTO `#__html5fb_publication` ". substr($insertQuery, 0, strlen($insertQuery)-2).")
		 				VALUES ".substr($insertQuery_values, 0, strlen($insertQuery_values)-2).")";

		$db->setQuery($query);
		$db->execute();

		$item->item_id = $item_id = $db->insertid();
		$this->insertAsset( COMPONENT_OPTION.'.publication.'.$item->item_id, $item->c_title);

		// check & copy Magazine Files
		$this->flashmagazine_magazine_import_file($insertItem['c_background_pdf'], 'pdf', $item_id);
		$this->flashmagazine_magazine_import_file($insertItem['c_author_image'], 'authors', $item_id);
		$this->flashmagazine_magazine_import_file($insertItem['c_thumb'], 'thumbs', $item_id);

		return $item_id;
	}

	private function flashmagazine_pages_import($item)
	{
		$db = JFactory::getDbo();

		$pagesPath = FLASHPUBLICATION_MEDIA_PATH.'/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder : '' ).'/';
		$pagesNewPath = COMPONENT_MEDIA_PATH.'/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder : '' ).'/';

		if ( !JFolder::exists($pagesNewPath) )
			JFolder::create($pagesNewPath);

		foreach ( $item->pages as $page )
		{
			if ( !$page->c_enable_video )
			{
				if ( $page->c_enable_image )
				{
					$c_new_filename = $page->page_image;

					if ( JFile::exists( $pagesNewPath . $page->page_image) )
						$c_new_filename = $item->id.'_'.$page->page_image;

					JFile::copy($pagesPath . $page->page_image, $pagesNewPath . $c_new_filename);
					$page->page_image = $c_new_filename;
				}

				$db->setQuery("INSERT INTO  #__html5fb_pages (`publication_id`,`page_title`,`ordering`,`c_enable_image`,`page_image`,`c_enable_text`,`c_text`)
								VALUES (".$db->quote($item->id).", "
									.$db->quote($page->page_title).", "
									.$db->quote($page->ordering).", "
									.$db->quote($page->c_enable_image).", "
									.$db->quote($page->page_image).", "
									.$db->quote($page->c_enable_text).", "
									.$db->quote($page->c_text)
								." ) ");

				$db->execute();
			}
		}
	}

	private function flashmagazine_magazine_import_file( $filename, $path, $item_id )
	{
		$db = JFactory::getDbo();

		if ( !empty($filename) )
		{
			$c_new_filename = $filename;

			if ( JFile::exists( COMPONENT_MEDIA_PATH.'/'.$path.'/'.$filename) )
				$c_new_filename = $item_id.'_'.$filename;

			JFile::copy( FLASHPUBLICATION_MEDIA_PATH.'/'.$path.'/'.$filename, COMPONENT_MEDIA_PATH.'/'.$path.'/'.$c_new_filename);

			if ( $c_new_filename != $filename )
			{
				$db->setQuery("UPDATE #__html5fb_publication SET c_thumb = ".$db->quote($c_new_filename)." WHERE c_id = ".$item_id);
				$db->execute();
			}
		}
	}

	private function getResolutionId( $itemResolution )
	{
		$resolutionKey = $itemResolution->width . 'x' . $itemResolution->height;

		if ( empty($this->myResolutions[ $resolutionKey ]) )
		{
			$db = JFactory::getDbo();

			if ( empty($itemResolution->resolution_name) )
				$itemResolution->resolution_name = $resolutionKey;

			//$db->setQuery("SELECT id, resolution_name, height, width, CONCAT(width,'x',height) as resolution FROM `#__html5fb_resolutio
				$db->setQuery("INSERT INTO #__html5fb_resolutions ( `resolution_name`, `width`, `height` )
								VALUES ( ". $db->quote($itemResolution->resolution_name) .", ". $db->quote($itemResolution->width). ", ". $db->quote($itemResolution->height) .")");
				$db->execute();

				$itemResolution->id = $db->insertid();

			$this->myResolutions[ $resolutionKey ] = $itemResolution;
		}

		return $this->myResolutions[ $resolutionKey ]->id;

	}

	private function insertAsset( $asset_name, $asset_title )
	{
		$asset = JTable::getInstance('Asset', 'JTable');
		$asset->name = $asset_name;
		$asset->title = $asset_title;
		$asset->rules = '{"core.view":[]}';
		$asset->setLocation($this->componentRootAssetId, 'last-child');
		$asset->store();

			$asset_data = explode('.', $asset_name);

			$db = JFactory::getDbo();
			$db->setQuery("UPDATE #__html5fb_".$asset_data[1]." SET asset_id = ".$asset->id." WHERE c_id = ".$asset_data[2]);
			$db->execute();

		return $asset->id;
	}
}