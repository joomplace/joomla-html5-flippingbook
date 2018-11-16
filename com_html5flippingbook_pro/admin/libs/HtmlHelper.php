<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HtmlHelper
{
	//----------------------------------------------------------------------------------------------------
	public static function addCss()  
	{
		$document = JFactory::getDocument();
		
		$document->addStyleSheet(COMPONENT_CSS_URL.'html5flippingbook.css');
	}
	//----------------------------------------------------------------------------------------------------
	public static function showTitle($viewTitle = '', $toolbarIconClass = '')  
	{
		$title = JText::_('COM_HTML5FLIPPINGBOOK_SHORT');
		$toolbarIconClass = '';
		
		if ($viewTitle != '') $title .= ' : ' . $viewTitle;

		$document = JFactory::getDocument();

		$document->setTitle($title);

		JToolBarHelper::title($title, $toolbarIconClass);
	}
	//----------------------------------------------------------------------------------------------------
	public static function getSidebarMenu($view)  
	{
		$currentViewName = strtolower($view->getName());
		
		switch ($currentViewName)
		{
			case 'configuration':
			{
				JHtmlSidebar::addEntry('<i class="icon-list-view"></i> '.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_CONFIGURATION'), 'index.php?option='.COMPONENT_OPTION.'&view=configuration', ($currentViewName == 'configuration'));
				break;
			}
			case 'categories': case 'category':
			case 'publications': case 'publication':
			case 'pages': case 'page':
			{
				JHtmlSidebar::addEntry('<i class="icon-list-view"></i> '.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_CATEGORIES'), 'index.php?option='.COMPONENT_OPTION.'&view=categories', ($currentViewName == 'categories' || $currentViewName == 'category'));
				JHtmlSidebar::addEntry('<i class="icon-folder"></i> '.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PUBLICATIONS'), 'index.php?option='.COMPONENT_OPTION.'&view=publications', ($currentViewName == 'publications' || $currentViewName == 'publication'));
				JHtmlSidebar::addEntry('<i class="icon-tablet"></i> '.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PAGES'), 'index.php?option='.COMPONENT_OPTION.'&view=pages', ($currentViewName == 'pages' || $currentViewName == 'page'));
				break;
			}
			case 'templates': case 'template':
			case 'resolutions': case 'resolution':
			{
				JHtmlSidebar::addEntry('<i class="icon-pictures"></i> '.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_TEMPLATES'), 'index.php?option='.COMPONENT_OPTION.'&view=templates', ($currentViewName == 'templates' || $currentViewName == 'template'));
				JHtmlSidebar::addEntry('<i class="icon-screen"></i> '.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_RESOLUTIONS'), 'index.php?option='.COMPONENT_OPTION.'&view=resolutions', ($currentViewName == 'resolutions' || $currentViewName == 'resolution'));
				break;
			}
		}
	}
	//----------------------------------------------------------------------------------------------------
	public static function getMenuPanel()  
	{
		$html = array();
		
		$html[] = '<div id="tm-navbar" class="navbar navbar-static navbar-inverse html5fb_menu_panel">';
		$html[] = 	'<div class="navbar-inner">';
		$html[] = 		'<div class="container" style="width:auto;">';
		$html[] = 			'<a class="brand" href="http://www.joomplace.com" target="_blank">';
		$html[] = 				'<img src="'.COMPONENT_ASSETS_URL.'images/joomplace_logo_64.png" class="tm-panel-logo">  JOOMPLACE';
		$html[] = 			'</a>';
		$html[] = 			'<ul class="nav" role="navigation">';
		$html[] = 				'<li class="dropdown">';
		$html[] = 					'<a id="control-panel" href="index.php?option='.COMPONENT_OPTION.'" role="button" class="dropdown-toggle">';
		$html[] = 						JText::_('COM_HTML5FLIPPINGBOOK_BE_MENU_CONTROL_PANEL');
		$html[] = 					'</a>';
		$html[] = 				'</li>';
		$html[] = 			'</ul>';
		$html[] = 			'<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse-html5fb">';
		$html[] = 				'<span class="icon-bar"></span>';
		$html[] = 				'<span class="icon-bar"></span>';
		$html[] = 				'<span class="icon-bar"></span>';
		$html[] = 			'</a>';
		$html[] = 			'<div class="nav-collapse-html5fb nav-collapse collapse">';
		$html[] = 				'<ul class="nav" role="navigation">';

		$html[] = 					'<li class="dropdown">';
		$html[] = 						'<a href="#" id="drop-customization" role="button" class="dropdown-toggle" data-toggle="dropdown">';
		$html[] = 							JText::_('COM_HTML5FLIPPINGBOOK_BE_MENU_MANAGEMENT') . '<b class="caret"></b>';
		$html[] = 						'</a>';
		$html[] = 						'<ul class="dropdown-menu" role="menu" aria-labelledby="drop-customization">';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&view=categories"><i class="icon-list-view"></i> ';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_CATEGORIES');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&view=publications"><i class="icon-folder"></i> ';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PUBLICATIONS');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&view=pages"><i class="icon-tablet"></i> ';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_PAGES');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation" class="divider"></li>';
		$html[] =							'<li role="presentation">';
		$html[] = 								'<a onclick="return confirm(\''.JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_SUBMIT_SETUP_SIMPLE_DATA').'\')" role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&task=sample_data.install"><i class="icon-download"></i> ';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_IMPORT');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 						'</ul>';
		$html[] = 					'</li>';

		$html[] = 					'<li class="dropdown">';
		$html[] = 						'<a href="#" id="drop-customization" role="button" class="dropdown-toggle" data-toggle="dropdown">';
		$html[] = 							JText::_('COM_HTML5FLIPPINGBOOK_BE_MENU_CUSTOMIZATION') . '<b class="caret"></b>';
		$html[] = 						'</a>';
		$html[] = 						'<ul class="dropdown-menu" role="menu" aria-labelledby="drop-customization">';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&view=templates"><i class="icon-pictures"></i> ';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_TEMPLATES');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&view=resolutions"><i class="icon-screen"></i> ';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_RESOLUTIONS');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 						'</ul>';
		$html[] = 					'</li>';

		$html[] = 					'<li>';
		$html[] = 						'<a href="index.php?option='.COMPONENT_OPTION.'&view=configuration" id="drop-customization" role="button" class="dropdown-toggle">';
		$html[] = 							JText::_('COM_HTML5FLIPPINGBOOK_BE_MENU_CONFIGURATION');
		$html[] = 						'</a>';
		$html[] = 					'</li>';


		$html[] = 				'</ul>';
		$html[] = 				'<ul class="nav pull-right" role="navigation">';
		$html[] = 					'<li class="dropdown">';
		$html[] = 						'<a href="#" id="drop-customization" role="button" class="dropdown-toggle" data-toggle="dropdown">';
		$html[] = 							JText::_('COM_HTML5FLIPPINGBOOK_BE_MENU_HELP') . '<b class="caret"></b>';
		$html[] = 						'</a>';
		$html[] = 						'<ul class="dropdown-menu" role="menu" aria-labelledby="drop-customization">';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" target="_blank" href="http://www.joomplace.com/video-tutorials-and-documentation/html5-flipping-book/index.html?description.htm">';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_HELP');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation" class="divider"></li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="http://www.joomplace.com/forum/joomla-components/html5-flipping-book.html" target="_blank">';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_SUPPORT_FORUM');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk.html" target="_blank">';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_HELPDESK');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create.html" target="_blank">';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_SUBMIT_REQUEST');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 							'<li role="presentation" class="divider"></li>';
		$html[] = 							'<li role="presentation">';
		$html[] = 								'<a role="menuitem" tabindex="-1" href="index.php?option='.COMPONENT_OPTION.'&view=sample_data">';
		$html[] = 									JText::_('COM_HTML5FLIPPINGBOOK_BE_SUBMENU_SAMPLEDATA');
		$html[] = 								'</a>';
		$html[] = 							'</li>';
		$html[] = 						'</ul>';
		$html[] = 					'</li>';
		$html[] = 				'</ul>';
		$html[] = 			'</div>';
		$html[] = 		'</div>';
		$html[] = 	'</div>';
		$html[] = '</div>';
		
		return implode('', $html);
	}
	//----------------------------------------------------------------------------------------------------
	public static function getPublishedOptions()
	{
		$options = array();
		
		$options[] = JHtml::_('select.option', '1', 'JPUBLISHED');
		$options[] = JHtml::_('select.option', '0', 'JUNPUBLISHED');
		
		return $options;
	}
	//----------------------------------------------------------------------------------------------------
	public static function createIndexHtmlFile($directoryFullName)
	{
		$handle = @fopen($directoryFullName.'/index.html', 'w+');
		@fwrite($handle, '<html><body bgcolor="#FFFFFF"></body></html>');
		@fclose($handle);
	}

	public static function tinyMCE_js( $width, $height, $content_css, $elements )
	{
		$COMPONENT_ASSETS_URL = COMPONENT_ASSETS_URL;
		$jUriroot = JUri::root();

		return <<<HTML

		function isBrowserIE()
		{
			return navigator.appName=="Microsoft Internet Explorer";
		}

		function jInsertEditorText( text, editor )
		{
			if (isBrowserIE())
			{
				if (window.parent.tinyMCE)
				{
					window.parent.tinyMCE.selectedInstance.selection.moveToBookmark(window.parent.global_ie_bookmark);
				}
			}
			tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,text);
		}

		function jReplaceSelectedContents( text, editor )
		{
			tinyMCE.execInstanceCommand(editor, 'mceReplaceContent',false,text);
		}

		var global_ie_bookmark = false;

		function IeCursorFix()
		{
			if (isBrowserIE())
			{
				tinyMCE.execCommand('mceInsertContent', false, '');
				global_ie_bookmark = tinyMCE.activeEditor.selection.getBookmark(false);
			}
			return true;
		}

		function getFormControls()
		{
			return { pageImageSelect : document.getElementById('jform_page_image') };
		}

		GeneralHelper.addLibrary('{$COMPONENT_ASSETS_URL}tinymce/jscripts/tiny_mce/tiny_mce.js', 'tinyMCE', onTinyMceDefined);

		function onTinyMceDefined()
		{
			tinyMCE.init({
				theme : "advanced",
				mode : "exact",
				skin : "default",
				elements : "{$elements}",
				document_base_url : "{$jUriroot}",
				plugins : "advlink, advimage",
				relative_urls : false,
				convert_fonts_to_spans : false,
				remove_script_host : false,
				invalid_elements : "script,applet",
				directionality: "ltr",
				force_br_newlines : "false",
				force_p_newlines : "true",
				debug : false,
				safari_warning : false,
				paste_use_dialog : false,
				paste_auto_cleanup_on_paste : true,
				paste_convert_headers_to_strong : false,
				paste_strip_class_attributes : "all",
				paste_remove_spans : true,
				paste_remove_styles : true,

				plugin_insertdate_dateFormat : "%Y-%m-%d",
				plugin_insertdate_timeFormat : "%H:%M:%S",
				plugin_preview_width : "750",
				plugin_preview_height : "550",

				width: "{$width}",
				height: "{$height}",

				content_css: '{$content_css}',

				verify_html: false,
				cleanup_on_startup: false,
				trim_span_elements: false,
				cleanup: false,

				extended_valid_elements : "a[name|href|target|title|onclick],img[style|class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],video[src|controls|width|height],audio[src|controls|width|height],iframe[width|height|src|frameborder|allowfullscreen]",
				setup : function(ed) {
					ed.onPostProcess.add(function(ed, o) { onTinyMcePostProcess(ed, o); });
				}
			});
		}

		function onTinyMcePostProcess(editor, obj)
		{
			var baseUrl = editor.settings['document_base_url'];

			obj.content = obj.content.replace(new RegExp('href *= *"' + baseUrl, 'gi'), 'href="');
			obj.content = obj.content.replace(new RegExp('src *= *"' + baseUrl, 'gi'), 'src="');
			obj.content = obj.content.replace(new RegExp('mce_real_src *= *"\s*"', 'gi'), '');
			obj.content = obj.content.replace(new RegExp('mce_real_href *= *"\s*"', 'gi'), '');
		}
HTML;

	}
}