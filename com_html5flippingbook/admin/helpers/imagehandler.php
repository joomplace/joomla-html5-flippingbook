<?php
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

/**
 * Html5flippingbook component Image handler helper.
 *
 */
abstract class Html5flippingbookImagehandlerHelper
{
    public static $path_folder_svg = COMPONENT_MEDIA_PATH .'/svg';
    public static $url_folder_svg = '/media/'.COMPONENT_OPTION.'/svg';
    public static $path_folder_preview = COMPONENT_MEDIA_PATH .'/preview';
    public static $url_folder_preview = '/media/'.COMPONENT_OPTION.'/preview';

    public static function createFile($path_to_file=null, $content=null)
    {
        if (!$path_to_file || !$content){
            return false;
        }
        jimport('joomla.filesystem.file');
        if (!JFile::write($path_to_file, $content)) {
            $filename = end(explode('/', $path_to_file));
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_ERROR_CREATE_FILE', $filename), 'warning');
            return false;
        }
        return true;
    }

    public static function deleteFile($path_to_file=null)
    {
        if (!$path_to_file){
            return false;
        }
        jimport('joomla.filesystem.file');
        if (!JFile::exists($path_to_file)) {
            return true;
        }
        if (JFile::exists($path_to_file) && !JFile::delete($path_to_file)) {
            $filename = end(explode('/', $path_to_file));
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_ERROR_DELETE_FILE', $filename), 'warning');
            return false;
        }
        return true;
    }

    public static function createFolder($path_to_folder=null)
    {
        if (!$path_to_folder){
            return false;
        }
        jimport( 'joomla.filesystem.folder' );
        if (!JFolder::exists($path_to_folder)) {
            if (!JFolder::create($path_to_folder)) {
                $foldername = end(explode('/', $path_to_folder));
                JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_ERROR_CREATE_FOLDER', $foldername), 'warning');
                return false;
            }
        }
        return true;
    }

    public static function deleteFolder($path_to_folder=null)
    {
        if (!$path_to_folder){
            return false;
        }
        jimport( 'joomla.filesystem.folder' );
        if (JFolder::exists($path_to_folder) && !JFolder::delete($path_to_folder)) {
            $foldername = end(explode('/', $path_to_folder));
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_ERROR_DELETE_FOLDER', $foldername), 'warning');
            return false;
        }
        return true;
    }

    /*
     * save html-page as svg-file
     */
    public static function saveHtmlPageToSvgFile($data)
    {
        $publicationModel = JModelLegacy::getInstance('Publication','HTML5FlippingBookModel');
        $publication = $publicationModel->getItem((int)$data['publication_id']);

        $resolutionModel = JModelLegacy::getInstance('Resolution','HTML5FlippingBookModel');
        $resolution = $resolutionModel->getItem((int)$publication->c_resolution_id);

        $publicationFolder = self::$path_folder_svg . '/' . (int)$publication->c_id;
        $filePath = $publicationFolder .'/'. (int)$data['id'] .'.svg';

        $svg = '';
        //If there is a video or audio on the page, display the full page only as html.
        if( preg_match('/video|iframe|audio/i', $data['c_text']) != 1 ){
            $svg = self::getSvg($data['c_text'], (int)$data['id'], (int)$resolution->width, (int)$resolution->height);
        }

        if($svg && self::createFolder(self::$path_folder_svg) && self::createFolder($publicationFolder)) {
            self::createFile($filePath, $svg);
        } else {
            self::deleteFile($filePath);
        }

        return true;
    }


    public static function getSvg($html='', $page_id=0, $width=0, $height=0)
    {
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        //characters that lead to an error in svg
        $search = array(
            //'&rsquo;',
            //'&mdash;',
            //'&nbsp;',
            //'&ldquo;', '&rdquo;',
            '&',
            'src="media/com_html5flippingbook'
        );
        $replace = array(
            //"'",
            //'—',
            //' ',
            //'"', '"',
            '_',
            'src="/media/com_html5flippingbook'
        );
        $html = str_replace($search, $replace, $html);

        //image processing in html
        $html = preg_replace_callback('%<img([^>]+)/?>%iU', 'self::svgImageReplaceCallback', $html);

        $styles = 'style="font-size: 1.25rem !important;"';

        $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>' .
            '<svg xmlns="http://www.w3.org/2000/svg"  xmlns:xlink="http://www.w3.org/1999/xlink" ' .
                        'id="page_svg_'.$page_id.'" width="100%" height="100%" viewBox="0 0 '.$width.' '.$height.'">' .
                '<foreignObject width="100%" height="100%">' .
                    '<div xmlns="http://www.w3.org/1999/xhtml" '.$styles.'>'.$html.'</div>' .
                '</foreignObject>' .
            '</svg>';
        return $svg;
    }


    /*
     * Get the attributes of a tag as an array:
     *     $key - name of attribute,
     *     $val - value of attribute
     * If $key == 'style', $val is array
     *
     * array $matches is filled with the results of search
     */
    public static function getTagAttributes($matches)
    {
        $atts = array();

        //if the tag has a style attribute, we'll process it separately
        preg_match('/style="([^"]*)"/i', $matches[1],$styles);
        if(!empty($styles[0])){
            $matches = preg_replace('/style="[^"]*"/i', '', $matches);
            $styles_arr = explode(';', $styles[1]);
            if(!empty($styles_arr)){
                $all_styles = array();
                foreach ($styles_arr as $style) {
                    $style_arr = explode(':', $style);
                    if(trim($style_arr[0])){
                        $all_styles[trim($style_arr[0])] = trim($style_arr[1]);
                    }
                }
            }
            $atts['style'] = $all_styles;
        }

        $a = preg_split('/\s+/', trim($matches[1]));
        foreach ($a as $value) {
            $value = explode('=', $value);
            $key = strtolower(trim($value[0]));
            $val = isset($value[1]) ? trim($value[1]) : '';
            if ($val && ($val[0] == '"' || $val[0] == "'")) {
                $val = substr($val, 1, strlen($val) - 2);
            }
            $atts[$key] = $val;
        }

        return $atts;
    }

    public static function svgImageReplaceCallback($matches)
    {
        $atts = self::getTagAttributes($matches);
        $src = isset($atts['src']) ? $atts['src'] : '';
        if($src) {
            $type = pathinfo($src, PATHINFO_EXTENSION);
            $data = file_get_contents(JPATH_SITE . $src);
            $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $matches[1] = preg_replace('/src=(["\'])([^"\']*)(["\'])/i', 'src=$1'.$dataUri.'$3', $matches[1]);
            return '<img '.$matches[1].' />';
        }
        return $matches;
    }

	public static function savePagePreview($data=array())
	{
        jimport('joomla.filesystem.file');
        jimport( 'joomla.filesystem.folder' );

        $publicationModel = JModelLegacy::getInstance('Publication','HTML5FlippingBookModel');
        $publication = $publicationModel->getItem((int)$data['publication_id']);

        self::createFolder(self::$path_folder_preview);
        @chmod(self::$path_folder_preview, 0757);
        self::createFolder(self::$path_folder_preview.'/'.$publication->c_id);
        @chmod(self::$path_folder_preview.'/'.$publication->c_id, 0757);

        $imagedata = '';

        if($data['page_type'] == 'text') {
            if(isset($data['canvas']) && $data['canvas']) {
                $image_content = base64_decode(str_replace('data:image/jpeg;base64,','', $data['canvas']));
                $tempfile = tmpfile();
                fwrite($tempfile, $image_content);
                $metaDatas = stream_get_meta_data($tempfile);
                $tmpFilename = $metaDatas['uri'];
                $imagedata = self::imgCreate($tmpFilename);
                fclose($tempfile);
            }
            else {
                $fake_images = array(
                    COMPONENT_MEDIA_PATH . '/textpage_right.jpg',
                    COMPONENT_MEDIA_PATH . '/textpage_left.jpg'
                );
                $fi_key = array_rand($fake_images, 1);
                if(JFile::exists($fake_images[$fi_key])){
                    $imagedata = self::imgCreate($fake_images[$fi_key]);
                }
            }
        }
        else if($data['page_type'] == 'image')
        {
            if(JFile::exists(COMPONENT_MEDIA_PATH. '/images/'. ( $publication->c_imgsub ? $publication->c_imgsubfolder.'/' : ''). $data['page_image'])){
                $imagedata = self::imgCreate(COMPONENT_MEDIA_PATH. '/images/'. ( $publication->c_imgsub ? $publication->c_imgsubfolder.'/' : ''). $data['page_image']);
            }else{
                $imagedata = self::imgCreate(COMPONENT_MEDIA_PATH. '/images/'. ( $publication->c_imgsub ? $publication->c_imgsubfolder.'/' : '').'th_'. $data['page_image']);
            }
        }

        if($imagedata){
            imagejpeg($imagedata, self::$path_folder_preview.'/'.$publication->c_id.'/'.$data['id'].'.jpg');
            imagedestroy($imagedata);
        }

        //delete preview.gif of publication
        self::deleteFile(self::$path_folder_preview.'/'.$publication->c_id.'/preview_'.(int)$data['publication_id'].'.gif');

        return true;
	}


    public static function imgCreate($file, $cropWidth = 57, $cropHeight = 73)
    {
        try
        {
            $image = new JImage();
            $image->loadFile($file);
            $image->cropResize($cropWidth, $cropHeight, false);
            $image->toFile($file.'tmp', IMAGETYPE_JPEG, array('quality' => 95));
            $image->destroy();
            $handle = imagecreatefromjpeg($file.'tmp');
            if ( !is_resource($handle) )    return false;
            unlink($file.'tmp');
            return $handle;
        }
        catch (Exception $e ) {
            JFactory::getApplication()->enqueueMessage($e->getMessage());
            return false;
        }
    }

}