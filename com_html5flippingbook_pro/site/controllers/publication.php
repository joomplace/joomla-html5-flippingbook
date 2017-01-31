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
		$publication = JFactory::getApplication()->input->get('publication',0,'INT');
		if($publication && $number){
			$model = $this->getModel();
			list($pub, $page) = $model->getPageFromPub($publication, $number);
			
			if($page->page_image)
				$page->page_image = COMPONENT_MEDIA_URL. 'images/'. ( $pub->c_imgsub ? $pub->c_imgsubfolder.'/' : '') . 'original/'.str_replace(array('th_', 'thumb_'), '', $page->page_image);
			
			if($pub->template->hard_cover){
				$number+=2;
			}
			$page_number  = (($pub->navi_settings)?$number:$number-1);
			$page_content = ($page->page_image)?'<div class="paddifier"><img src="'.$page->page_image.'" /></div>':'<div class="paddifier"><div class="html-content"><div>'.$page->c_text.((1)?'<span class="page-number">'.$page_number.'</span></div></div>':'').'</div>';
			echo str_replace(array('="image','="media'),array('="/image','="/media'),$page_content);
		}
		die();
	}
}