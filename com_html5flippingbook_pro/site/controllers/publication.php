<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class HTML5FlippingBookControllerPublication extends JControllerLegacy
{
    public function getModel($name = 'Publication', $prefix = '', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function loadSpecPage(){
        jimport('joomla.filesystem.file');
        $number = JFactory::getApplication()->input->get('number',0,'INT');
        $doublepages = JFactory::getApplication()->input->get('doublepages',0,'INT');
        if ($doublepages) {
            $number = ceil($number/2);
        }
        $publication = JFactory::getApplication()->input->get('publication',0,'INT');

        if($publication && $number){

            $model = $this->getModel();
            list($pub, $page) = $model->getPageFromPub($publication, $number);
            $model->setState('publication.id', $publication);
            $resolutions = $model->getResolutions();

            if($page->page_image) {
                $page->page_image = COMPONENT_MEDIA_PATH . 'images/' . ($pub->c_imgsub ? $pub->c_imgsubfolder . '/' : '') . 'original/' . str_replace(array('th_', 'thumb_'), '', $page->page_image);
            }
            else if($page->c_enable_text == 1){
                $page->svg = '';
                if(JFile::exists(COMPONENT_MEDIA_PATH. '/svg/'. $publication .'/'. $page->id .'.svg')){
                    $page->svg = COMPONENT_MEDIA_URL. 'svg/'. $publication .'/'. $page->id .'.svg';
                }
            }

            if($pub->template->hard_cover){
                $number+=3;
            }
            else{
                $number+=1;
            }
            $page_number = $number;
            if (!(int)$pub->navi_settings) {
                $page_number = (int)$pub->template->hard_cover ? $number - 2 : $number - 1;
            }

            $page->page_image = str_replace(JUri::root(true),'',$page->page_image);
            if (!$pub->template->doublepages) {
                if($page->page_image) {
                    $page_content = '<div class="paddifier dd"><img src="'.str_replace("\\", "/", JHtml::_('thumbler.generate', $page->page_image, $page->id . '_', json_encode(array('width' => $resolutions->width, 'height' => $resolutions->height)), false)) .'" /></div>';
                } else {
                    if($page->svg && $page->enable_svg){
                        $page_content = '<div class="paddifier"><div class="html-content"><div class="svg-content" style="background-image: url(\''.$page->svg.'\');"><span class="page-number">'.$page_number.'</span></div></div></div>';
                    } else {
                        $page_content = '<div class="paddifier"><div class="html-content"><div>'.$page->c_text.((1)?'<span class="page-number">'.$page_number.'</span></div></div>':'').'</div>';
                    }
                }
            } else {
                $page_content = ($page->page_image)?'<div class="double" style="background-image:url('.str_replace("\\", "/", JHtml::_('thumbler.generate', $page->page_image, $page->id.'_', json_encode(array('width' => $resolutions->width*($pub->template->doublepages?2:1), 'height'=> $resolutions->height)), false)).')"></div>':'';
            }
            echo str_replace(array('="image','="media'),array('="/image','="/media'),$page_content);
        }
        die();
    }
}