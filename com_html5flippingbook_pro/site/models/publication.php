<?php defined('_JEXEC') or die('Restricted access');
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
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
                list($this->_item->pages_count, $this->_item->pages) = $this->getPages();

//				if ( $this->_item->template->slider_thumbs )
//					$this->generatePreview($this->_item);

                if ( !empty($this->_item->custom_metatags) )
                    $this->_item->custom_metatags = unserialize( $this->_item->custom_metatags );




                /* seems not needed */

                $this->_item->justImages = 1;

                $this->_item->contents_page = 0;

                foreach ( $this->_item->pages as $kp => $page )
                {
                    if ( $page['is_contents'] ) {
                        $this->_item->contents_page = ($kp+1);
                    }
                }

                $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
                $tagPos = preg_match($pattern, $this->_item->c_pub_descr);
                if ($tagPos == 0){
                    $this->_item->introtext = $this->_item->c_pub_descr;
                    $this->_item->fulltext = '';
                }else{
                    list ($this->_item->introtext, $this->_item->fulltext) = preg_split($pattern, $this->_item->c_pub_descr, 2);
                }

                if ( $this->_item->template->slider_thumbs ){
                    $this->generatePreview($this->_item);
                }

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
        $id	= $this->getState('publication.id');
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
            $this->_db->setQuery("SELECT COUNT(*) FROM #__html5fb_pages WHERE publication_id = ".(int)$id."");
            $count = $this->_db->loadResult();
            if( $count > 16 && !$force){
                $query = array();
                $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering` ASC LIMIT 0, 7";
                //$query[1] = "(SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering` DESC LIMIT 0, 4)";
                //$query = implode(' UNION ',$query);
                //$query = $query." ORDER BY `ordering` ASC";
            }else{
                $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering` ASC";
            }

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssocList();

            if(!$this->_item->template->doublepages){
                if($count%2==1){
                    if (!$this->_item->template->hard_cover) {
                        $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = " . (int)$id . " ORDER BY `ordering` DESC LIMIT 0, 2";
                        $this->_db->setQuery($query);
                        $result = array_merge($result,array_reverse($this->_db->loadAssocList()));
                    }
                    $result[] = array('c_text'=>'<div class="page"></div>');
                    $count++;
                }elseif($count > 16 && !$force){
                    if ($this->_item->template->hard_cover) {
                        $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering` DESC LIMIT 0, 1";
                    }else {
                        $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering` DESC LIMIT 0, 3";
                    }
                    $this->_db->setQuery($query);
                    $result = array_merge($result,array_reverse($this->_db->loadAssocList()));
                }
            }elseif($count > 16 && !$force){
                $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$id." ORDER BY `ordering` DESC LIMIT 0, 1";
                $this->_db->setQuery($query);
                $result = array_merge($result,array_reverse($this->_db->loadAssocList()));
            }

            return array($count,$result);
        }
        else{
            return array(0,false);
        }
    }

    public function getPageFromPub($pub, $num)
    {
        $table = JTable::getInstance('Publications', 'HTML5FlippingBookTable');
        $table->load($pub);

        $query = "SELECT * FROM `#__html5fb_templates`" .
            " WHERE `id` = " . $table->c_template_id;
        $this->_db->setQuery($query);
        $table->template = $this->_db->loadObject();

        $query = "SELECT * FROM #__html5fb_pages WHERE publication_id = ".(int)$pub." ORDER BY `ordering` ASC";
        $this->_db->setQuery($query,$num,1);
        $page = $this->_db->loadObject();
        return array($table,$page);
    }

    private function generatePreview( $item )
    {
        jimport('joomla.image.image');
        jimport('joomla.filesystem.file');

        function imgCreate($file,$cropWidth = 57, $cropHeight = 73)
        {

            try
            {
                $image = new JImage();
                $image->loadFile($file);

                // in joomla 3.0 cropResize doesn't exists
                if ( method_exists($image, 'cropResize') )
                {
                    $image->cropResize($cropWidth, $cropHeight, false);
                }
                else
                {
                    $rx = ($cropWidth > 0) ? ($image->getWidth() / $cropWidth) : 0;
                    $ry = ($cropHeight > 0) ? ($image->getHeight() / $cropHeight) : 0;

                    $ratio = ($rx > $ry) ? $ry : $rx;

                    $nheight = (int)round($image->getWidth() / $ratio);
                    $nwidth = (int)round($image->getHeight() / $ratio);

                    if ( ($image->getWidth() / $cropWidth) > ($image->getHeight() / $cropHeight))
                    {
                        $image->resize($cropWidth, $nheight, false);
                    }
                    else
                    {
                        $image->resize($nwidth, $cropHeight, false);
                    }

                    $image->crop($cropWidth, $cropHeight, null, null);
                }

                $image->toFile($file.'tmp', IMAGETYPE_JPEG, array('quality' => 95));

                // in joomla 3.0 destroy doesn't exists
                if ( method_exists($image, 'destroy'))
                {
                    $image->destroy();
                }

                $handle = imagecreatefromjpeg($file.'tmp');
                if ( !is_resource($handle) )    return false;
                unlink($file.'tmp');

                return $handle;
            }
            catch (Exception $e ) {
                JFactory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

        function textImgCreate( $page_num )
        {
            if ( $page_num%2==0 )	// is right
            {
                $file = COMPONENT_MEDIA_PATH . '/textpage_right.jpg';
                JFile::copy(JPATH_BASE . '/components/'.COMPONENT_OPTION.'/assets/images/textpage_right.jpg', $file);
            }
            else
            {
                $file = COMPONENT_MEDIA_PATH . '/textpage_left.jpg';
                JFile::copy(JPATH_BASE . '/components/'.COMPONENT_OPTION.'/assets/images/textpage_left.jpg', $file);
            }

            $handle = imgCreate($file);

            switch ( strlen($page_num) )
            {
                case 1:
                    $fontsize = 20;
                    $x = 20;
                    $y = 45;
                    break;
                case 2:
                    $fontsize = 20;
                    $x = 13;
                    $y = 45;
                    break;
                case 3:
                    $fontsize = 18;
                    $x = 7;
                    $y = 45;
                    break;
                case 4:
                    $fontsize = 15;
                    $x = 4;
                    $y = 45;
                    break;
            }

            imagettftext($handle, $fontsize, 0, $x, $y, imagecolorallocate($handle, 0, 0, 0) , JPATH_BASE . '/components/'.COMPONENT_OPTION.'/assets/fonts/verdana.ttf', ($page_num == 0 ? '' : $page_num));

            return $handle;
        }

        if ( !file_exists(COMPONENT_MEDIA_PATH.'/thumbs/preview_'.$item->c_id.'.gif') || filesize(COMPONENT_MEDIA_PATH.'/thumbs/preview_'.$item->c_id.'.gif') < 200 )
        {
            $images = array();

            $k = 1;
            list($countPages, $pages) = $this->getPages(true);
            foreach ($pages as $page_num => $page)
            {
                if ( $page['c_enable_image'] )
                {
                    if(is_file(COMPONENT_MEDIA_PATH. '/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : ''). $page['page_image'])){
                        $imagedata = imgCreate(COMPONENT_MEDIA_PATH. '/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : ''). $page['page_image']);
                    }else{
                        $imagedata = imgCreate(COMPONENT_MEDIA_PATH. '/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : '').'th_'. $page['page_image']);
                    }

                    if ($item->navi_settings == 0 && !in_array($page_num, array(0, 1, ($countPages - 1), $countPages)))
                    {
                        $k++;
                    }
                }
                else
                {
                    $page_num += 1;
                    if ($item->navi_settings == 0 && ($page_num <= 2 || $page_num == $countPages || $page_num == ($countPages - 1)))
                    {
                        $page_num = 0;
                    }
                    elseif ($item->navi_settings == 0)
                    {
                        $page_num = $k;
                        $k++;
                    }

                    $imagedata = textImgCreate( $page_num );
                }

                if ( $imagedata )
                    $images[] = $imagedata;
            }
            $firstImage = $images[0];
            unset($images[0]);

            $sizeofForHeight = ( sizeof($images)%2 == 0 ? sizeof($images) : sizeof($images)+1);

            $dest = imagecreatetruecolor( 114, ( 73 * $sizeofForHeight )/2 + 73);
            imagecopy($dest, $firstImage, 0, 0, 0, 0, imagesx($firstImage), imagesy($firstImage));

            foreach ( array_chunk($images,2) as $key => $image )
            {
                imagecopy($dest, $image[0], 0, (73*($key+1)), 0, 0, imagesx($image[0]), imagesy($image[0]));
                if ( @$image[1] )
                    imagecopy($dest, $image[1], 58, (73*($key+1)), 0, 0, imagesx($image[1]), imagesy($image[1]));
            }

            @chmod(COMPONENT_MEDIA_PATH.'/thumbs', 0757);
            imagegif($dest, COMPONENT_MEDIA_PATH.'/thumbs/preview_'.$item->c_id.'.gif');
        }
        foreach($item->pages as $page){
            //c_enable_text is "table of content"
            if(!$page['page_image'] && $page['c_text']){
                $page_num += 1;
                if ($item->navi_settings == 0 && ($page_num <= 2 || $page_num == $countPages || $page_num == ($countPages - 1)))
                {
                    $page_num = 0;
                }
                elseif ($item->navi_settings == 0)
                {
                    $page_num = $k;
                    $k++;
                }

                $imagedata = textImgCreate( $page_num );

            }
        }
    }

    static function sanitizeWidth($width, $height, $imageObject)
    {
        // If no width was given we will assume it is a square and use the height.
        $width = ($width === null) ? $height : $width;

        // If we were given a percentage, calculate the integer value.
        if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width))
        {
            $width = (int) round($imageObject->getWidth() * (float) str_replace('%', '', $width) / 100);
        }
        // Else do some rounding so we come out with a sane integer value.
        else
        {
            $width = (int) round((float) $width);
        }

        return $width;
    }

    static function sanitizeHeight($height, $width, $imageObject)
    {
        // If no height was given we will assume it is a square and use the width.
        $height = ($height === null) ? $width : $height;

        // If we were given a percentage, calculate the integer value.
        if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height))
        {
            $height = (int) round($imageObject->getHeight() * (float) str_replace('%', '', $height) / 100);
        }
        // Else do some rounding so we come out with a sane integer value.
        else
        {
            $height = (int) round((float) $height);
        }

        return $height;
    }
}