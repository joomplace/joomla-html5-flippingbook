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
            if($page->page_image)
                $page->page_image = COMPONENT_MEDIA_PATH. '/images/'. ( $pub->c_imgsub ? $pub->c_imgsubfolder.'/' : '') . 'original/'.str_replace(array('th_', 'thumb_'), '', $page->page_image);

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

            if (!$pub->template->doublepages) {
                $page_content = ($page->page_image)?'<div class="paddifier dd"><img src="'.JHtml::_('thumbler.generate', $page->page_image, (isset($page->id) ? $page->id.'_' : '_'), json_encode(array('width' => $resolutions->width, 'height'=> $resolutions->height)), false).'" /></div>':'<div class="paddifier"><div class="html-content"><div>'.$page->c_text.((1)?'<span class="page-number">'.$page_number.'</span></div></div>':'').'</div>';

            } else {
                $page_content = ($page->page_image)?'<div class="double" style="background-image:url('.str_replace("\\", "/", JHtml::_('thumbler.generate', $page->page_image, (isset($page->id) ? $page->id.'_' : '_'), json_encode(array('width' => $resolutions->width*($pub->template->doublepages?2:1), 'height'=> $resolutions->height)), false)).')"></div>':'';
            }
            echo str_replace(array('="image','="media'),array('="/image','="/media'),$page_content);
        }
        die();
    }
}