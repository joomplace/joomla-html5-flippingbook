<?php
defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
$document = JFactory::getDocument();
JHtml::_('jquery.framework');
JHtml::_('jquery.ui');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/extras/jquery-ui-1.8.20.custom.min.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/extras/modernizr.2.5.3.min.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/extras/jquery.mousewheel.min.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/lib/hash.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/lib/turn.min.js');

$item = $this->item;
$pages = $item->pages;

$data = array(
    'item'=>$item,
    'pages'=>$pages,
    'config'=>$this->config,
    'emaillayout'=>$this->emaillayout,
    'emaillayoutData'=>$this->emaillayoutData
);

if($this->item->template->hard_cover){
    $hard_html = JLayoutHelper::render('book.hardcover', $data);
}

// TODO: refactor layouts

if($hard_html){
    echo $hard_html;
}else{
    echo JLayoutHelper::render('book.magazine', $data);
}