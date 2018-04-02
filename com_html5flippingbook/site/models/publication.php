<?php defined('_JEXEC') or die('Restricted access');
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

JLoader::register('Html5flippingbookImagehandlerHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/imagehandler.php');

class HTML5FlippingBookModelPublication extends JModelItem
{
	//----------------------------------------------------------------------------------------------------
	public function populateState()
	{
		$params	= JFactory::getApplication()->getParams();

		$jinput = JFactory::getApplication()->input;

		$id	= $jinput->get('id', 0, 'INT');

		$this->setState('publication.id', $id);

		$this->setState('params', $params);
	}
	//----------------------------------------------------------------------------------------------------
	public function getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('publication.id');

				if (empty($id))
				{
					$params = $this->getState('params');
					$id = (int) $params->get('publication_id');
				}
			}

			if (empty($id)) return null;

			$table = JTable::getInstance('Publications', 'HTML5FlippingBookTable');

			if ($table->load($id))
			{
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');

				// Reading template type.

				$db = JFactory::getDBO();

				$query = "SELECT * FROM `#__html5fb_templates`" .
					" WHERE `id` = " . $this->_item->c_template_id;
				$db->setQuery($query);
				$row = $db->loadObject();

				$this->_item->template = $row;
				$this->_item->pages = $this->getPages();

				$this->_item->justImages = 1;

				$this->_item->contents_page = 0;

				$this->_item->pages_count = count($this->_item->pages);

				foreach ( $this->_item->pages as $kp => $page )
				{
					if ( $page['is_contents'] ) {
						$this->_item->contents_page = ($kp + 1);
					}
				}

// echo "<pre>"; print_r($this->_item); die;

				if ( $this->_item->template->slider_thumbs ){
                    $this->generatePreview($this->_item);
                }

				if ( !empty($this->_item->custom_metatags) )
					$this->_item->custom_metatags = unserialize( $this->_item->custom_metatags );

			}
			else if ($error = $table->getError())
			{
				$this->setError($error);

				return null;
			}
		}
		return $this->_item;
	}
	//----------------------------------------------------------------------------------------------------
	public function getResolutions()
	{
		$jinput = JFactory::getApplication()->input;

		$id	= $jinput->get('id', 0, 'INT');
		$resolution = null;

		if (empty($id))
		{
			$params = $this->getState('params');
			$id = (int) $params->get('publication_id');
		}

		if ($id)
		{
			$this->_db->setQuery('SELECT r.width, r.height FROM `#__html5fb_publication` AS m' .
				' LEFT JOIN `#__html5fb_resolutions` AS r  ON r.id=m.c_resolution_id ' .
				' WHERE m.c_id='.$id);
			$resolution = $this->_db->loadObject();
		}

		return $resolution;
	}

	public function getPages($force = false)
	{
		if (empty($id))
		{
			$id = $this->getState('publication.id');

			if (empty($id))
			{
				$params = $this->getState('params');
				$id = (int) $params->get('publication_id');
			}
		}

		if ( $id )
		{
			$this->_db->setQuery("SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering`");
			return $this->_db->loadAssocList();
		}
		else{
            return false;
        }
	}

    private function generatePreview( $item )
    {
        jimport('joomla.filesystem.file');

        function textImgCreate($page_num)
        {
            if ( $page_num%2==0 ){	// is right
                $file = COMPONENT_MEDIA_PATH . '/textpage_right.jpg';
                JFile::copy(JPATH_BASE . '/components/'.COMPONENT_OPTION.'/assets/images/textpage_right.jpg', $file);
            }
            else {
                $file = COMPONENT_MEDIA_PATH . '/textpage_left.jpg';
                JFile::copy(JPATH_BASE . '/components/'.COMPONENT_OPTION.'/assets/images/textpage_left.jpg', $file);
            }
            $handle = Html5flippingbookImagehandlerHelper::imgCreate($file);
            return $handle;
        }

        $path_folder_preview_publication = Html5flippingbookImagehandlerHelper::$path_folder_preview.'/'.(int)$item->c_id;
        if ( !file_exists($path_folder_preview_publication.'/preview_'.(int)$item->c_id.'.gif')
                || filesize($path_folder_preview_publication.'/preview_'.(int)$item->c_id.'.gif') < 200 )
        {
            $images = array();

            $k = 1;
            list($countPages, $pages) = $this->getPages(true);
            foreach ($pages as $page_num => $page)
            {
                if(file_exists($path_folder_preview_publication.'/'.(int)$page['id'].'.jpg')){
                    $imagedata = imagecreatefromjpeg($path_folder_preview_publication.'/'.(int)$page['id'].'.jpg');
                }
                else {
                    if ($page['c_enable_image']) {
                        if (is_file(COMPONENT_MEDIA_PATH . '/images/' . ($item->c_imgsub ? $item->c_imgsubfolder . '/' : '') . $page['page_image'])) {
                            $imagedata = Html5flippingbookImagehandlerHelper::imgCreate(COMPONENT_MEDIA_PATH . '/images/' . ($item->c_imgsub ? $item->c_imgsubfolder . '/' : '') . $page['page_image']);
                        } else {
                            $imagedata = Html5flippingbookImagehandlerHelper::imgCreate(COMPONENT_MEDIA_PATH . '/images/' . ($item->c_imgsub ? $item->c_imgsubfolder . '/' : '') . 'th_' . $page['page_image']);
                        }

                        if ($item->navi_settings == 0 && !in_array($page_num, array(0, 1, ($countPages - 1), $countPages))) {
                            $k++;
                        }
                    } else {
                        $page_num += 1;
                        if ($item->navi_settings == 0 && ($page_num <= 2 || $page_num == $countPages || $page_num == ($countPages - 1))) {
                            $page_num = 0;
                        } elseif ($item->navi_settings == 0) {
                            $page_num = $k;
                            $k++;
                        }
                        $imagedata = textImgCreate($page_num);

                        //ToDo:
                        //When will a "real" svg-page (not svg-wrapper), split into:
                        //- if there is a svg, Html5flippingbookImagehandlerHelper::imagecreatefromsvg ;
                        //- if there is no svg, textImgCreate($page_num)
                    }
                }

                if ( $imagedata ) {
                    $images[] = $imagedata;
                }
            }
            $firstImage = $images[0];
            unset($images[0]);

            $sizeofForHeight = ( sizeof($images)%2 == 0 ? sizeof($images) : sizeof($images)+1);

            $dest = imagecreatetruecolor( 114, ( 73 * $sizeofForHeight )/2 + 73);
            imagecopy($dest, $firstImage, 0, 0, 0, 0, imagesx($firstImage), imagesy($firstImage));

            foreach ( array_chunk($images,2) as $key => $image )
            {
                imagecopy($dest, $image[0], 0, (73*($key+1)), 0, 0, imagesx($image[0]), imagesy($image[0]));
                if ( @$image[1] ) {
                    imagecopy($dest, $image[1], 58, (73 * ($key + 1)), 0, 0, imagesx($image[1]), imagesy($image[1]));
                }
            }

            @chmod($path_folder_preview_publication, 0757);
            imagegif($dest, $path_folder_preview_publication.'/preview_'.(int)$item->c_id.'.gif');
        }
    }
}