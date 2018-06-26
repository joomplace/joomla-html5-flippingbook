<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
extract($displayData);

foreach($pages as &$page){
    if($page['page_image'])
        $page['page_image'] = COMPONENT_MEDIA_PATH. '/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : '') . 'original/'.str_replace(array('th_', 'thumb_'), '', $page['page_image']);
}

/* need to be in model, or view.html */

$wrap_up = array('before'=>array(),'after'=>array());

$item->template->hard_wrapp_blanked = $item->template->hard_wrapp = $item->template->hard_cover;
if($item->template->hard_wrapp){
    if($item->contents_page){
        $item->contents_page+=2;
    }
    $wrap_up['before'][]['page_image'] = JPATH_SITE.'/components/com_html5flippingbook/assets/images/front-side.jpg';
    if($item->template->hard_wrapp_blanked){
        $wrap_up['before'][]['page_image'] = JPATH_SITE.'/components/com_html5flippingbook/assets/images/front-in.jpg';
        $wrap_up['after'][]['page_image'] = JPATH_SITE.'/components/com_html5flippingbook/assets/images/back-in.jpg';
    }
    $wrap_up['after'][]['page_image'] = JPATH_SITE.'/components/com_html5flippingbook/assets/images/back-side.jpg';

    $pages_count = count($wrap_up['before']) + $item->pages_count + count($wrap_up['after']);
    $pages_count_adjust = count($wrap_up['before']) + count($wrap_up['after']);

    /*
    $document->addStyleDeclaration('
    .hard .paddifier{
        left:0px!important;
        right:0px!important;
        top:0px!important;
        height:100%!important;
        width:100%!important;
    }
    .page .paddifier{
        position: relative;
        height: 97.3%;
        width: 95.8%;
        top: 1.4%;
    }
    .page.even .paddifier{
        left: 4.2%;
    }
    .page.odd .paddifier{
        right: -0.0%;
    }
    ');
    */
}else{
    $document->addStyleDeclaration('	
	.paddifier{
		left:0px!important;
		right:0px!important;
		top:0px!important;
		height:100%!important;
		width:100%!important;
		background: #FFF;
		position: relative;
	}
	');

    $pages_count_adjust = 2;
    $pages_count = $item->pages_count;
}

$font_type["0"] = '"Times New Roman", Times, serif';
$font_type["1"] = 'Georgia, serif';
$font_type["2"] = '"Palatino Linotype", "Book Antiqua", Palatino, serif';
$font_type["3"] = 'Arial, Helvetica, sans-serif';
$font_type["4"] = '"Arial Black", Gadget, sans-serif';
$font_type["5"] = '"Comic Sans MS", cursive, sans-serif';
$font_type["6"] = 'Impact, Charcoal, sans-serif';
$font_type["7"] = '"Lucida Sans Unicode", "Lucida Grande", sans-serif';
$font_type["8"] = 'Tahoma, Geneva, sans-serif';
$font_type["9"] = '"Trebuchet MS", Helvetica, sans-serif';
$font_type["10"] = 'Verdana, Geneva, sans-serif';
$font_type["11"] = '"Courier New", Courier, monospace';
$font_type["12"] = '"Lucida Console", Monaco, monospace';
$template_css = array('.flipbook'=>array(),'.flipbook p'=> array(),'.flipbook .page'=>array());
if($item->template->fontsize)
    $template_css['html body .flipbook'][] = 'font-size: '.$item->template->fontsize.';';
if($item->template->fontfamily !== null)
    $template_css['html body .flipbook'][] = 'font-family: '.$font_type[$item->template->fontfamily].';';
if($item->template->text_color)
    $template_css['html body .flipbook'][] = 'color: '.$item->template->text_color.';';
if($item->template->background_color)
    $template_css['html body .flipbook'][] = 'background-color: '.$item->template->background_color.';';
if($item->template->p_margin)
    $template_css['html body .flipbook p'][] = 'margin-bottom: '.$item->template->p_margin.';';
if($item->template->p_lineheight)
    $template_css['html body .flipbook p'][] = 'line-height: normal;';
if($item->template->page_background_color)
    $template_css['html body .flipbook .page'][] = 'background-color: '.$item->template->page_background_color.';';

$double_page = $item->template->doublepages;

foreach($template_css as $rule => $style){
    $document->addStyleDeclaration($rule.'{'.implode("\r\n",$style).'}');
}

?>
<style>
    <?php if(JFactory::getApplication()->input->get('tmpl')=='component'){ ?>
    html, html body {
        margin: 0;
        height: 100%!important;
    }
    <?php } ?>
    html body .flipbook * {
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
    }
    html body {
        /* helpers */
        /* book */
        /*
      .flipbook .page.odd{
          padding: 1.2% 2.7% 1.2% 0%;
          background: url('/components/com_html5flippingbook/assets/images/back-in.jpg');
          background-size: 100% 100%;
      }
      .flipbook .page.even{
          padding: 1.2% 0% 1.2% 2.7%;
          background: url('/components/com_html5flippingbook/assets/images/front-in.jpg');
          background-size: 100% 100%;
      }
      .flipbook .page.cover-front,
      .flipbook .page.cover-back{
          padding: 0;
          background: none;
      }
      */
        /* hard */
    }
    html body .even .html-content {
        height: 100%;
        background: #fff;
        /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#ffffff+95,c9c9c9+100 */
        background: #ffffff;
        /* Old browsers */
        background: -moz-linear-gradient(left, #ffffff 95%, #c9c9c9 100%);
        /* FF3.6-15 */
        background: -webkit-linear-gradient(left, #ffffff 95%, #c9c9c9 100%);
        /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to right, #ffffff 95%, #c9c9c9 100%);
        /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#c9c9c9', GradientType=1);
        /* IE6-9 */
    }
    html body .html-content > div * {
        max-width: 100%!important;
    }
    html body .html-content > div {
        padding: 2% 5% 3%;
    }
    html body .odd .html-content {
        height: 100%;
        background: #fff;
        /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#c9c9c9+0,ffffff+5 */
        background: #c9c9c9;
        /* Old browsers */
        background: -moz-linear-gradient(left, #c9c9c9 0%, #ffffff 5%);
        /* FF3.6-15 */
        background: -webkit-linear-gradient(left, #c9c9c9 0%, #ffffff 5%);
        /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(to right, #c9c9c9 0%, #ffffff 5%);
        /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#c9c9c9', endColorstr='#ffffff', GradientType=1);
        /* IE6-9 */
    }
    html body body.flip-hide-overflow {
        overflow: hidden;
    }
    /*html body .flipbook-viewport {
      max-width: 1200px;
    }*/
    html body .flipbook-viewport {
        display: table;
        width: 100%;
        height: 100%;
    }
    html body .rel {
        position: relative;
    }
    html body .flipbook {
        margin: 20px auto !important;
        width: 90%;
        height: 90%;
        max-width: 90% !important;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    html body .flipbook .even .double{
        background-size: 200% 100%;
        background-position: 0px;
    }
    html body .flipbook .even .page {
        background-size: 200% 100%;
        background-position: 0%;
    }
    html body .flipbook .double,
    html body .flipbook .page {
        height: 100%;
        background-size: auto 100%;
        background-repeat: no-repeat;
        background-position: 100%;
    }
    html body .flipbook .page.odd .double{
        background-size: 100% 100%;
        margin-left: -100%;
    }
    html body .flipbook .double img{
        max-width: 200%;
        max-height: 100%;
    }
    html body .flipbook .page img {
        max-width: 100%;
        max-height: 100%;
    }
    html body .flipbook .paddifier img {
        height: 100%;
    }
    html body .tbicon {
        background-image: url("<?php echo JUri::root(true); ?>/components/com_html5flippingbook/assets/images/new-sprites.png");
        display: inline-block;
        height: 22px;
        width: 22px;
        cursor: pointer;
        margin: 0 20px;
    }
    html body .zoom-out,
    html body .zoom-out-hover {
        background-position: -220px 0;
        width: 44px;
        height: 44px;
        position: absolute;
        top: 30px;
        right: 48%;
        left: 48%;
        z-index: 1000;
    }
    html body .flipbook-viewport > .rel {
        padding: 20px 0px;
        z-index: 100;
    }
    html body .html5flippingbook #fb_bookname {
        display: inline-block;
        margin: 0px 20px 0px 0px;
    }
    html body .html5flippingbook .fa-lg {
        font-size: 20px;
        line-height: 28px;
        margin: 0px 2px;
    }
    html body :-webkit-full-screen {
        background-color: transparent;
    }
    html body #page-bar {
        display: inline-block;
        font-size: 14px;
        line-height: 20px;
        margin: 15px 0px;
    }
    html body #page-bar > * {
        display: inline-block;
        margin: 0px 5px 0px 0px;
    }
    html body #page-bar input {
        max-width: 120px;
    }
    html body .ui-slider-handle {
        z-index: 1000!important;
    }
    html body .fb_topBar {
        padding: 0px 30px;
    }
    html body .turnjs-slider {
        margin: 25px auto;
        width: 80%;
    }
    html body .turnjs-slider .ui-slider-horizontal {
        height: .8em;
        background-color: #eee;
        border-radius: 8px;
        box-shadow: inset 0px 0px 3px #D2D2D2;
    }
    html body .turnjs-slider .ui-slider .ui-slider-handle {
        border: 1px solid #E3E3E3;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        background-color: #EFEBEB;
        -webkit-box-shadow: 0px 0px 3px #737373, inset 0px 0px 6px #C5C5C5;
        -moz-box-shadow: 0px 0px 3px #737373, inset 0px 0px 6px #C5C5C5;
        box-shadow: 0px 0px 3px #737373, inset 0px 0px 6px #C5C5C5;
    }
    html body .turnjs-slider .thumbnail {
        width: 115px;
        height: 85px;
        position: absolute;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        top: -100px;
        left: -17px;
        display: none;
        -webkit-transform: translate3d(0px, 50px, 0px) scale(0.1, 0.1);
        -webkit-transition: -webkit-transform 0.2s;
        -webkit-border-radius: 5px;
        -moz-transform: translate3d(0px, 50px, 0px) scale(0.1, 0.1);
        -moz-transition: -moz-transform 0.2s;
        -moz-border-radius: 5px;
        -o-transform: translate3d(0px, 50px, 0px) scale(0.1, 0.1);
        -o-transition: -o-transform 0.2s;
        -o-border-radius: 5px;
        -ms-transform: translate3d(0px, 50px, 0px) scale(0.1, 0.1);
        -ms-transition: -ms-transform 0.2s;
        -ms-border-radius: 5px;
        transform: translate3d(0px, 50px, 0px) scale(0.1, 0.1);
        transition: transform 0.2s;
        border-radius: 5px;
    }
    html body .no-transition {
        -webkit-transition: none;
        -moz-transition: none;
        -o-transition: none;
        -ms-transition: none;
    }
    html body .turnjs-slider .thumbnail div {
        width: 100px;
        margin: 7px;
        height: 70px;
        background-color: white;
    }
    html body .turnjs-slider .ui-state-hover .thumbnail {
        display: block;
        opacity: 0;
    }
    html body .turnjs-slider .ui-state-active .thumbnail {
        display: block;
        opacity: 1;
        -webkit-transform: scale(1, 1);
        -moz-transform: scale(1, 1);
        -o-transform: scale(1, 1);
        -ms-transform: scale(1, 1);
        transform: scale(1, 1);
    }
    html body .fb_topBar:after {
        display: table;
        content: '';
        clear: both;
    }
    html body .flipbook .page-number {
        color: #999;
        width: 100%;
        bottom: 1.5em;
        position: absolute;
        display: block;
        text-align: center;
        line-height: 1em;
        font-size: 0.8em;
    }
    html body .next-button,
    html body .previous-button {
        display: block;
        height: 100%;
    }
    html body .next-button,
    html body .previous-button {
        width: 2em;
        position: absolute;
        top: 0;
        z-index: 10;
        background-image: url("<?php echo JUri::root(true); ?>/components/com_html5flippingbook/assets/images/arrows.png");
        background-repeat: no-repeat;
        background-color: rgba(0, 0, 0, 0.3);
        background-size: 200%;
    }
    html body .next-button {
        background-position: 100% 50%;
        right: -2em;
        -webkit-border-radius: 0 1em 1em 0;
        -moz-border-radius: 0 1em 1em 0;
        -ms-border-radius: 0 1em 1em 0;
        -o-border-radius: 0 1em 1em 0;
        border-radius: 0 1em 1em 0;
        box-shadow: 0 0 1em rgba(0, 0, 0, 0.2);
    }
    html body .previous-button {
        left: -2em;
        -webkit-border-radius: 1em 0 0 1em;
        -moz-border-radius: 1em 0 0 1em;
        -ms-border-radius: 1em 0 0 1em;
        -o-border-radius: 1em 0 0 1em;
        border-radius: 1em 0 0 1em;
        box-shadow: 0 0 1em rgba(0, 0, 0, 0.2);
        background-position: 0% 50%;
    }
    html body .previous-button:hover,
    html body .next-button:hover {
        background-color: rgba(0, 0, 0, 0.4);
    }
    .page-wrapper:nth-child(odd) .loaded-landscape-page {
        margin-left: -100%;
        height: 100%;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-position: 100%;
    }
    .page-wrapper:nth-child(even) .loaded-landscape-page {
        margin-left: 0px;
        background-size: 200% 100%;
        background-position: 0%;
        height: 100%;
        background-repeat: no-repeat;
    }
    <?php if($isMobile){ ?>
    #search-inp{
        display:none!important;
    }
    .flipbook-viewport {
        max-width: 80%;
        margin: 0px auto;
    }
    <?php } ?>
</style>
<?php
$user = JFactory::getUser();
$downloadOptionAccess = $user->authorise('core.download', COMPONENT_OPTION);
$downloadOptionAccessGranted = $user->authorise('core.download', COMPONENT_OPTION . '.publication.' . $item->c_id);
$downloadList = array();
if ($downloadOptionAccess && $downloadOptionAccessGranted) {
    $downloadList = HTML5FlippingBookFrontHelper::generateDownloadLinks($item->c_id);
}
?>
<div class="html5flippingbook">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <?php if(file_exists(JPATH_SITE.JUri::root(true).'/components/com_html5flippingbook/assets/css/'.$item->c_id.'-publication.css')){ ?>
        <link rel="stylesheet" href="<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/css/'.$item->c_id.'-publication.css'; ?>">
    <?php } ?>
    <div class="rel">
        <?php if ($config->social_email_use && $emaillayout && $mailLayoutData){
            echo $emaillayout->render($mailLayoutData);
        }
        ?>
        <div class="flip-hide-overflow">
            <div class="flipbook-viewport<?php echo ($item->template->hard_wrapp)?' hardcover':''; ?>"<?php echo ($item->template->hard_wrapp)?' style="opacity: 0;" ':' style="opacity: 0;" '; ?>>
                <div class="rel" id="flipbook-rel">
                    <div ignore="1" class="fb_topBar ">
                        <?php if($item->template->display_title){ ?>
                            <h2 id="fb_bookname"><?php echo $item->c_title; ?></h2>
                        <?php } ?>
                        <?php if(!$item->justImages){ ?>
                            <span id="search-inp" style="display: inline;">
							<input type="text" name="search" class="search rounded" placeholder="Search..." autocomplete="off">
						</span>
                        <?php } ?>
                        <?php if($item->template->display_topicons){ ?>
                            <div class="tb_social" style="float: right; margin-left: 0px;">
                                <i class="fa fa-step-backward fa-lg" aria-hidden="true" title="<?= JText::_( 'COM_HTML5FLIPPINGBOOK_FIRST_PAGE' );?>"></i>
                                <?php if ($config->social_email_use):?>
                                    <a href="#emailModal" data-toggle="modal"><i class="fa fa-envelope fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_EMAIL');?>"></i></a>
                                <?php endif;?>
                                <?php if($item->contents_page){ ?>
                                    <i class="fa fa-list fa-lg" title="Table of contents" rel="<?php echo $item->contents_page; ?>"></i>
                                <?php } ?>
                                <?php if(JFactory::getApplication()->input->get('tmpl','')!='component'){ ?>
                                    <i class="fa fa-expand fa-lg" id="fullscreen" onclick="fullscreenIt('flipbook');" title="<?= JText::_( 'COM_HTML5FLIPPINGBOOK_FULLSCREEN' );?>"></i>
                                <?php } ?>
                                <?php
                                array_map(function($link){
                                    if(strpos($link[0], 'pdf')) {
                                        echo "<a class='$link[1] fa-lg' title='$link[2]' href='$link[0]' target='_blank'></a>";
                                    }
                                },$downloadList);
                                ?>
                                <i class="fa fa-search-plus fa-lg" title="<?= JText::_( 'COM_HTML5FLIPPINGBOOK_ZOOM_IN' );?>"></i>
                                <?php if ($config->social_facebook_use == 1) { ?>
                                    <a style="color: #47639E;" target="_blank" href="https://www.facebook.com/sharer.php?src=sp&u=<?php echo urlencode(JUri::current());?>&utm_source=share2">
                                        <i class="fa fa-facebook fa-lg" title="Share on facebook"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($config->social_twitter_use == 1) { ?>
                                    <a style="color: #41ABE1;" target="_blank" href="https://twitter.com/intent/tweet?status=<?php echo urlencode($item->c_title);?>%20<?php echo urlencode(JUri::current());?>&utm_source=share2">
                                        <i class="fa fa-twitter fa-lg" title="Share on Twitter"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($config->social_google_plus_use == 1) { ?>
                                    <a style="color: #ED5448;"target="_blank" href="https://plus.google.com/share?url=<?php echo urlencode(JUri::current());?>&utm_source=share2">
                                        <i class="fa fa-google-plus fa-lg" title="Share on G+"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div style="width: 100%; height: 100%;">
                        <div id="flipbook" class="flipbook<?php echo ((int)$item->right_to_left ? ' rtl' : ' ltr'); ?>"
                                <?php if((int)$item->right_to_left) echo 'dir="rtl" direction="rtl"'; ?> >
                            <?php if($item->template->display_nextprev){ ?>
                                <div ignore="1" class="previous-button" <?php if(!(int)$item->right_to_left) echo 'style="display:none;"'; ?> ></div>
                                <div ignore="1" class="next-button" <?php if((int)$item->right_to_left) echo 'style="display:none;"'; ?> ></div>
                            <?php } ?>
                            <?php
                            $pages = array_merge($wrap_up['before'],$pages,$wrap_up['after']);
                            $bc = count($pages)-2-1;
                            unset($page);
                            foreach($pages as $i => $page){
                                /* only to wrap, so can be moved uptop to cover creation */
                                $page_class = ($item->template->hard_cover)?'hard':'';
                                switch($i){
                                    /* cover styles-classes */
                                    case 0 :
                                        $page_class .= ' cover-front';
                                        /*
                                         * we don't support page loading and double page in same time for now
                                         */
                                        if($double_page){
                                            $page_class .= ' p1';
                                        }else{
                                            $page_class .= ' p1';
                                        }
                                        $page_number  = '';
                                        break;
                                    case 1:
                                        $page_class .= ' front-side';
                                        if($double_page){
                                            $page_class .= ' double';
                                        }else{
                                            $page_class .= ' p2';
                                        }
                                        /* TODO: refactor and move $item->navi_settings out */
                                        $page_number  = (($item->navi_settings)?(2):1);
                                        break;
                                    case $bc+1 :
                                        $page_class .= ' back-side';
                                        if($double_page){
                                            $page_class .= ' double';
                                        }else{
                                            $page_class .= ' fixed';
                                        }
                                        if($double_page){

                                        }else{
                                            $page_class .= ' p'.($pages_count-1);
                                        }
                                        $page_number  = (int)$item->navi_settings ? ($pages_count-1) : ($pages_count-2);
                                        break;
                                    case $bc+2 :
                                        $page_class .= ' cover-back';
                                        if($double_page){
                                            $page_class .= ' p'.($pages_count*2-2);
                                        }else{
                                            $page_class .= ' p'.($pages_count);
                                        }
                                        $page_number  = '';
                                        break;

                                    /* pages styles-classes */
                                    case $bc :
                                        $page_class = 'page';
                                        if($double_page){
                                            $page_class .= ' double';
                                        }else{
                                            $page_class .= ' p'.($pages_count-2);
                                        }
                                        $page_number  = (int)$item->navi_settings ? ($pages_count-2) : ($pages_count-3);
                                        break;
                                    default:
                                        $page_class = 'page';
                                        if($double_page){
                                            $page_class .= ' double';
                                        }else{
                                            $page_class .= ' p'.($i+1);
                                        }
                                        $page_number  = (int)$item->navi_settings ? ($i+1) : $i;
                                }

                                $page_content = ($page['page_image'])?'<div class="paddifier"><img src="'.str_replace("\\", "/", JHtml::_('thumbler.generate', $page['page_image'], $page['id'].'_', json_encode(array('width' => $item->resolutions->width*(($double_page && ($i != 0 && $i != $bc+2))?2:1), 'height'=> $item->resolutions->height)), false)).'" /></div>':'<div class="paddifier"><div class="html-content"><div>'.$page['c_text'].((1)?'<span class="page-number">'.$page_number.'</span></div></div>':'').'</div>';
                                if($page['page_image'] && strpos($page_class,'double')!==false){
                                    ?>
                                    <div class="<?php echo $page_class; ?>" data-id="<?php echo $page['id']; ?>" style="background-image:url('<?php echo str_replace("\\", "/", JHtml::_('thumbler.generate', $page['page_image'], $page['id'].'_', json_encode(array('width' => $item->resolutions->width*($double_page?2:1), 'height'=> $item->resolutions->height)), false)); ?>')"></div>
                                    <?php
                                }else{
                                    ?>
                                    <div class="<?php echo $page_class; ?>" data-id="<?php echo $page['id']; ?>"><?php echo $page_content; ?></div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span4 text-center">
                            <?php if($item->template->display_pagebox){ ?>
                                <div ignore="1" id="page-bar">
                                    <label><?= JText::_( 'COM_HTML5FLIPPINGBOOK_GO_TO' );?></label>
                                    <input type="text" id="goto_page_input" value="" autocomplete="" placeholder="<?= JText::_( 'COM_HTML5FLIPPINGBOOK_PAGE' );?>">
                                    <span id="goto_page_input_button"><i class="fa fa-share"></i></span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="span8 text-center">
                            <?php if($item->template->display_slider){ ?>
                                <div ignore="1" id="slider-bar" class="turnjs-slider">
                                    <div id="slider"></div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="span12">
                            <?php echo JHtml::_('content.prepare', property_exists($item, 'fulltext') ? $item->fulltext : false); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <i class="tbicon zoom-out" style="display: none;"></i>
    </div>
</div>
<script type="text/javascript">
    function fullscreenIt(id){
        var elem = jQuery('#'+id).parent()[0];
        if (elem.requestFullscreen) {               // W3C
            elem.requestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.webkitRequestFullScreen) {  //old webkit
            elem.webkitRequestFullScreen();
        } else if (elem.webkitEnterFullscreen) {    //iOS
            elem.webkitEnterFullscreen();
        } else {
            jQuery(elem)
                .css('position','fixed')
                .css('left','0px')
                .css('top','0px')
                .css('background','#FFF')
                .width(jQuery('html').width())
                .height(jQuery('html').height())
                .css('z-index','1010');
            var size = flipbook.module.resize();
            flipbook.turn('size', size.width, size.height);
            jQuery(elem).append(jQuery('<div/>').html('Close').attr('onclick','alert("Please reload the page.")'));
        }
    }
    jQuery('body').on('keyup', function(e) {
        if (e.keyCode == 27) { // escape
            if (document.fullscreenElement || document.mozFullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement){
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }
    });

    function loadPage(page,adj) {
        <?php 
        
            $addPageRoute = JRoute::_('index.php?option=com_html5flippingbook&publication='.$item->c_id.'&task=publication.loadSpecPage', false);
            $url = $addPageRoute.(strpos($addPageRoute,'?')?'&':'?').'number="+(page-1) +"&doublepages='.($double_page?"1":"0");
            
        ?>
        jQuery.ajax({url: "<?php echo $url;?>"}).
        done(function(pageHtml) {
            jQuery('.flipbook .p' + page).html(pageHtml);
        });
    }


    <?php if(property_exists($item, 'c_audio') && $item->c_audio) { ?>
    jQuery('.previous-button, .next-button').click(function() {
        var audio = new Audio();
        audio.src = '<?php  echo COMPONENT_MEDIA_URL . "audio/" . $item->c_audio; ?>';
        audio.autoplay = true;
    });
    <?php } ?>
    var flipbook = jQuery('.flipbook');
    var publicationID = <?php echo $item->c_id;?>;
    var user = <?php echo $user->get('id', 'INT'); ?>;

    (function ($) {
        function zoomIn(book){
            $('.tbicon.zoom-out').show();
            $('.fa-search-plus').hide();
            book.turn('zoom',2);
            $(book).css({"font-size": 0.016*Math.pow($(book).turn('size').width,1.0145) + "px"});
            //book.turn('disable', true);

            /* add mouse move scroll */
            // http://stackoverflow.com/questions/6518600/scroll-window-when-mouse-moves
            // http://stackoverflow.com/questions/27924066/scroll-div-content-on-mouse-move

        }
        function zoomOut(book){
            $('.tbicon.zoom-out').hide();
            $('.fa-search-plus').show();
            book.turn('zoom',1);
            var size = module.resize();
            book.turn('size',size.width,size.height);
            $(book).css({"font-size": 0.016*Math.pow($(book).turn('size').width,1.0145) + "px"});
            //book.turn('disable', false);
        }

        function zoomHandle(book) {
            if(book.turn('zoom')!=1){
                //zoomOut(book);
                return false;
            }else{
                zoomIn(book);
            }
        }

        var onfullscreenchange =  function(e){
            var fullscreenElement =
                document.fullscreenElement ||
                document.mozFullscreenElement ||
                document.webkitFullscreenElement ||
                document.msFullscreenElement;
            return fullscreenElement;
        };

        'use strict';
        var module = {
            ratio: <?php echo $item->resolutions->width*2/$item->resolutions->height; ?>,
            init: function (id) {
                var me = this;
                // if older browser then don't run javascript
                if (document.addEventListener) {
                    this.el = document.getElementById(id);
                    this.resize();
                    this.plugins();
                    $(me.el).css({"font-size": 0.016*Math.pow($(me.el).turn('size').width,1.0145) + "px"});
                    // on window resize, update the plugin size
                    window.addEventListener('resize', function (e) {
                        var isMobile = <?php echo $isMobile ? 1 : 0; ?>,
                            isTablet = <?php echo $isTablet ? 1 : 0; ?>;
                        if((isMobile*1 == 1 || isTablet*1 == 1) && ($(me.el).turn('zoom') == 2)){
                            return false;
                        }
                        var size = me.resize();
                        zoomOut($(me.el));
                        $(me.el).turn('size',size.width,size.height);
                        $(me.el).css({"font-size": 0.016*Math.pow(size.width,1.0145) + "px"});
                    });
                }
                $(me.el).find('img').each(function(index){
                    var src = $(this).attr('src');
                    $('body').append("<div style=\"width: 10px; height: 10px; background: url("+src+") no-repeat -9999px -9999px; position: fixed; top: -9999px; left: -9999px;\"></div>");
                });
            },
            resize: function () {

                // reset the width and height to the css defaults
                this.el.style.width = '';
                this.el.style.height = '';
                var width = this.el.clientWidth,
                    height = Math.round(width / this.ratio),
                    padded = Math.round(document.documentElement.clientHeight * 0.9),
                    screenHeight = Math.round(document.documentElement.clientHeight),
                    fullscreen = onfullscreenchange(this.el);
                // if the height is too big for the window, constrain it
                if (height > padded) {
                    height = padded;
                    width = Math.round(height * this.ratio);
                }
                if (width > document.documentElement.clientWidth) {
                    width = document.documentElement.clientWidth;
                    height = Math.round(width / this.ratio);
                }

                if (fullscreen) {
                    if (height > screenHeight) {
                        height = screenHeight * 0.9;
                        width = Math.round(height * this.ratio);
                    }
                }
                // set the width and height matching the aspect ratio
                this.el.style.width = width + 'px';

                /*
                 if($('.html-content').length){
                 // hard calculation (html pages is in)
                 // recalculate height
                 height = parseInt($(this.el).css('min-height'),10);
                 $('.html-content').each(function(i){
                 var el_h = parseInt($(this).height(),10)+parseInt($(this).css('padding-top'),10)+parseInt($(this).css('padding-bottom').replace('px',''),10);
                 if(el_h > height) height = el_h;
                 });

                 if(width/height > this.ratio){
                 height = Math.round(width / this.ratio);
                 }
                 }
                 */

                this.el.style.height = height + 'px';

                return {
                    width: width,
                    height: height
                };
            },
            plugins: function () {
                var me = this;
                var slider = flipbook.parent().next().find('.turnjs-slider #slider');
                var thumb_file = '<?php echo ($item->template->slider_thumbs)?COMPONENT_MEDIA_URL.'/thumbs/preview_'.$item->c_id.'.gif':''; ?>';

                // URIs
                Hash.on('^page\/([0-9]*)$', {
                    yep: function(path, parts) {
                        var page = parts[1];
                        if (page!==undefined) {
                            if (flipbook.turn('is'))
                                flipbook.turn('page', page);
                        }
                    },
                    nop: function(path) {
                        if (flipbook.turn('is'))
                            flipbook.turn('page', 1);
                    }
                });
                // Arrows
                $(document).keydown(function(e){
                    var previous = 37, next = 39;
                    switch (e.keyCode) {
                        case previous:
                            flipbook.turn('previous');
                            break;
                        case next:
                            flipbook.turn('next');
                            break;
                    }
                });
                flipbook.find(".next-button,.previous-button").on('click',function(e){
                    var isRtl = flipbook.attr('direction') == 'rtl' ? 1 : 0;
                    switch ($(this).attr('class')) {
                        case 'previous-button':
                            if(isRtl){
                                flipbook.turn('next');
                            } else {
                                flipbook.turn('previous');
                            }
                            break;
                        case 'next-button':
                            if(isRtl){
                                flipbook.turn('previous');
                            } else {
                                flipbook.turn('next');
                            }
                            break;
                    }
                });

                // Slider

                slider.prop('slide', null).slider({
                    min: 1,
                    max: 100,
                    <?php
                    // http://keith-wood.name/rtlsliders.html
                    if((int)$item->right_to_left){
                        echo 'isRTL: true,';
                    }
                    ?>

                    start: function(event, ui) {
                        if (!window._thumbPreview) {
                            _thumbPreview = $('<div />', {'class': 'thumbnail'}).html('<div></div>');
                            setPreview(ui.value, thumb_file);
                            _thumbPreview.appendTo($(ui.handle));
                        } else {
                            setPreview(ui.value, thumb_file);
                        }
                        moveBar(false);
                    },

                    slide: function(event, ui) {
                        setPreview(ui.value, thumb_file);
                    },

                    stop: function() {
                        if (window._thumbPreview) {
                            _thumbPreview.removeClass('show');
                        }
                        flipbook.turn('page', Math.max(1, $(this).slider('value')*2 - 2));
                    }
                });

                // Flipbook
                /* choose other events!!! */
                //flipbook.bind(($.isTouch) ? 'doubletap' : 'dblclick', function(e){zoomHandle(flipbook);});

                $('.tbicon.zoom-out').on('click',function(e){
                    zoomOut(flipbook);
                    return false;
                });
                $('.fa-search-plus').on('click',function(e){
                    zoomHandle(flipbook);
                    return false;
                });
                $('.fa-list').on('click',function(e){
                    flipbook.turn('page',$(this).attr('rel'));
                    return false;
                });
                $('#goto_page_input_button').on('click',function(e){
                    var input = $(this).parent().find('#goto_page_input');
                    var val = parseInt(input.val(),10) + <?php echo ($item->navi_settings)?0:1; ?>;
                    flipbook.turn('page', val);
                    input.val('').prop('placeholder',(val-<?php echo ($item->navi_settings)?0:1; ?>)+' <?= JText::_( 'COM_HTML5FLIPPINGBOOK_PAGE_IS_OPENED' );?>');
                    return false;
                });

                $('.fa-step-backward').on('click', function (event) {
                    flipbook.turn('page', 1);
                    return false;
                });

                flipbook.find('.double').scissor();

                flipbook.turn({
                    elevation: 50,
                    acceleration: !isChrome(),
                    autoCenter: true,
                    gradients: true,
                    duration: 1000,
                    pages: <?php echo ($double_page)?($pages_count*2-2):($pages_count); ?>,
                    when: {
                        turning: function(e, page, view) {

                            var book = $(this),
                                currentPage = book.turn('page'),
                                pages = book.turn('pages');
                            <?php if($item->c_audio) { ?>
                            var audio = new Audio();
                            audio.src = '<?php  echo COMPONENT_MEDIA_URL . "audio/" . $item->c_audio; ?>';
                            audio.autoplay = true;
                            <?php } ?>
                            /*
                             if (currentPage>3 && currentPage<pages-3) {

                             if (page==1) {
                             book.turn('page', 2).turn('stop').turn('page', page);
                             e.preventDefault();
                             return;
                             } else if (page==pages) {
                             book.turn('page', pages-1).turn('stop').turn('page', page);
                             e.preventDefault();
                             return;
                             }

                             } else if (page>3 && page<pages-3) {
                             book.find('.front-side, .back-side').addClass('fixed');
                             if (currentPage==1) {
                             book.turn('page', 2).turn('stop').turn('page', page);
                             e.preventDefault();
                             return;
                             } else if (currentPage==pages) {
                             book.turn('page', pages-1).turn('stop').turn('page', page);
                             e.preventDefault();
                             return;
                             }
                             }
                             */


                            slider.slider('value', getViewNumber(book, page));

                            book.parent().next().find('#goto_page_input').prop('placeholder',(page-<?php echo ($item->navi_settings)?0:1; ?>)+' <?= JText::_( 'COM_HTML5FLIPPINGBOOK_PAGE_IS_OPENED' );?>');

                            updateDepth(book, page+1);
                            if (page>1){
                                book.find('.p2').addClass('fixed');
                                book.find('.next-button').show();
                            }else{
                                updateDepth(book, 0);
                                book.find('.p2').removeClass('fixed');
                            }

                            if (page!=pages){
                                book.find('.p'+(pages-1)).addClass('fixed');
                                book.find('.previous-button').show();
                            }else{
                                updateDepth(book, pages);
                                book.find('.p'+(pages-1)).removeClass('fixed');
                            }

                            Hash.go('page/'+page).update();


                        },

                        turned: function(e, page, view) {

                            var book = $(this);
                            /* for what? */
                            /*
                             if($('.html-content').length){
                             var size = me.resize();
                             zoomOut(flipbook);
                             flipbook.turn('size',size.width,size.height);
                             }
                             */

                            book.turn('center');

                            /*Update last open page*/
                            if (user) {
                                $.ajax({
                                    type: "POST",
                                    url: "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
                                    data: 'pubID=' + publicationID + '&action=updatePage&page=' + page,
                                    dataType: 'JSON',
                                });
                            }
                        },

                        zooming: function(e, newFactor, current) {

                            if(newFactor!=1){
                                $('.flip-hide-overflow').css('overflow','scroll');
                            }else{
                                $('.flip-hide-overflow').css('overflow','');
                            }

                        },

                        start: function(e, pageObj) {
                            moveBar(true);
                        },

                        end: function(e, pageObj) {

                            var book = $(this);

                            setTimeout(function() {

                                slider.slider('value', getViewNumber(book));

                            }, 1);

                            moveBar(false);

                        },

                        last: function(e) {
                            var book = $(this),
                                isRtl = book.attr('direction') == 'rtl' ? 1 : 0;
                            if(!isRtl) {
                                book.find('.next-button').hide();
                                book.find('.previous-button').show();
                            } else {
                                book.find('.next-button').show();
                                book.find('.previous-button').hide();
                                book.find('.depth-right').eq(0).css({'width':'0'});
                            }
                            zoomOut(book);
                        },

                        first: function(e) {
                            var book = $(this),
                                isRtl = book.attr('direction') == 'rtl' ? 1 : 0;
                            if(!isRtl) {
                                book.find('.previous-button').hide();
                                book.find('.next-button').show();
                            } else {
                                book.find('.previous-button').show();
                                book.find('.next-button').hide();
                                book.find('.depth-left').eq(0).css({'width':'0'});
                            }
                            zoomOut(book);
                        },

                        missing: function (e, pages) {
                            for (var i = 0; i < pages.length; i++) {
                                addPage(pages[i], $(this),<?php echo $pages_count_adjust; ?>);
                            }
                        }
                    }
                });

                flipbook.turn("peel", "tr");

                updateDepth(flipbook, flipbook.turn('page'));

                slider.slider('option', 'max', numberOfViews(flipbook));
                flipbook.addClass('animated')
                flipbook.closest('.flipbook-viewport').animate({"opacity": "1"}, 800);
            }
        };
        flipbook.module = module;

        function loadApp() {
            // Check if the CSS was already loaded
            module.init('flipbook');
            if (flipbook.width()==0 || flipbook.height()==0) {
                setTimeout(loadApp, 10);
                return;
            }
        }

        // Load the HTML4 version if there's not CSS transform
        yepnope({
            test : Modernizr.csstransforms,
            yep: ['<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>lib/turn.min.js'],
            nope: ['<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>lib/turn.html4.min.js', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>css/jquery.ui.html4.css'],
            both: ['<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>js/cust.js', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>lib/scissor.min.js', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>css/jquery.ui.css', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>css/double-page.css'],
            complete: loadApp
        });
    }(jQuery));

    <?php if(JFactory::getApplication()->input->get('tmpl') == 'component'){ ?>
    jQuery(function($){
        if($('#sbox-window', window.top.document).length){
            $('#sbox-window', window.top.document).find('iframe').css({'max-height':'100%','max-width':'100%'});
        }
    });
    <?php } ?>
</script>
<?php
if($item->template->hard_wrapp){
    ?>
    <style>
        .page-wrapper[page="1"],
        .page-wrapper[page="2"],
        .page-wrapper[page="<?php echo $pages_count-1; ?>"],
        .page-wrapper[page="<?php echo $pages_count; ?>"]{
            overflow: visible!important;
        }

        /* front-inside */
        .page-wrapper[page="2"] > div:first-child {
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="2"] > div:nth-child(2) {
            margin: -2% -3.7% -2% 0px;
            padding:  2% 3.7% 2% 0px;;
        }
        .page-wrapper[page="2"] .p2{
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="2"] .p1{
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }
        /* front-cover */
        .page-wrapper[page="1"] > div:first-child {
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }
        .page-wrapper[page="1"] > div:nth-child(2) {
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="1"] .p2{
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="1"] .p1{
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }
        /* back-inside (like front-cover) */
        .page-wrapper[page="<?php echo $pages_count-1; ?>"] > div:first-child {
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }
        .page-wrapper[page="<?php echo $pages_count-1; ?>"] > div:nth-child(2) {
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="<?php echo $pages_count-1; ?>"] .p<?php echo $pages_count; ?>{
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="<?php echo $pages_count-1; ?>"] .p<?php echo $pages_count-1; ?>{
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }
        /* back-cover (like front-inside) */
        .page-wrapper[page="<?php echo $pages_count; ?>"] > div:first-child {
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="<?php echo $pages_count; ?>"] > div:nth-child(2) {
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }
        .page-wrapper[page="<?php echo $pages_count; ?>"] .p<?php echo $pages_count; ?>{
            margin: -2% 0px -2% -3.7%;
            padding: 2% 0px 2% 3.7%;
        }
        .page-wrapper[page="<?php echo $pages_count; ?>"] .p<?php echo $pages_count-1; ?>{
            margin: -2% -3.7% -2% 0px;
            padding: 2% 3.7% 2% 0px;
        }

        .page-wrapper[page="1"] > div ,
        .page-wrapper[page="2"] > div,
        .page-wrapper[page="<?php echo $pages_count-1; ?>"] > div ,
        .page-wrapper[page="<?php echo $pages_count; ?>"] > div {
            height:100%!important;
            width: 100%!important;
        }

        .paddifier{
            left:0px!important;
            right:0px!important;
            top:0px!important;
            height:100%!important;
            width:100%!important;
            background: #FFF;
        }
    </style>
<?php } ?>
<script src="<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/extras/jquery.ui.touch-punch.min.js'; ?>" type="text/javascript"></script>