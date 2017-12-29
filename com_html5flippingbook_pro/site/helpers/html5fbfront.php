<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

abstract class HTML5FlippingBookFrontHelper
{
	/**
	 * Method to make publication data
	 *
	 * @param bool   $mobile  Variable to determine if page opened on mobile phone
	 * @param bool   $tablet  Variable to determine if page opened on tablet
	 * @param object $item    Publication data
	 * @param bool   $link    TRUE if want to return only publication link, e.g. for email
	 *
	 * @return stdClass
	 */
	public static function htmlPublHelper($mobile = FALSE, $tablet = FALSE, $item, $link = FALSE, $fullPath = FALSE)
	{
		$uri  = JUri::getInstance();
		$user = JFactory::getUser();
		$data = new stdClass();

		if ($link)
		{
			if ($fullPath) {
				$data->rawPublicationLink= JUri::root().'index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$item;
				$data->publicationLink = JRoute::_($data->rawPublicationLink.'&tmpl=component', FALSE, $uri->isSSL());
			} else {
				$data->rawPublicationLink= 'index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$item;
				$data->publicationLink = JRoute::_($data->rawPublicationLink.'&tmpl=component', FALSE, $uri->isSSL());
			}
			return $data;
		}

		$linkTitle = JText::_('COM_HTML5FLIPPINGBOOK_FE_VIEW_PUBLICATION');
		$popupWidth = $item->width * 2 + 66;
		$popupHeight = $item->height + 100;

		$data->rawPublicationLink= 'index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$item->c_id;

		if ($mobile)
		{
			$data->publicationLink = JRoute::_($data->rawPublicationLink.'&layout=mobile&tmpl=component', FALSE, $uri->isSSL());
			$data->viewPublicationLink= '<a href="'.$data->publicationLink.'" target="_blank">';
			$data->viewPublicationLinkWithTitle = '<a class="thumbnail" href="'.$data->publicationLink.'" target="_blank" title="'.$linkTitle .'">';
		}
		elseif ($tablet)
		{
			$data->publicationLink = JRoute::_($data->rawPublicationLink.'&tmpl=component', FALSE, $uri->isSSL());
			$data->viewPublicationLink= '<a href="'.$data->publicationLink.'" target="_blank">';
			$data->viewPublicationLinkWithTitle = '<a class="thumbnail" href="'.$data->publicationLink.'" target="_blank" title="'.$linkTitle .'">';
		}
		elseif($item->c_popup == PublicationDisplayMode::DirectLink)
		{
			$data->publicationLink = JRoute::_($data->rawPublicationLink. ($item->uid == $user->get('id') && ($item->page) ? '#page/' . $item->page : ''), FALSE, $uri->isSSL());
			$data->viewPublicationLink= '<a href="'.$data->publicationLink.'" target="_blank" target="_self">';
			$data->viewPublicationLinkWithTitle = '<a class="thumbnail" href="'.$data->publicationLink.'" target="_blank" target="_self" title="'.$linkTitle .'">';
		}
		else if ($item->c_popup == PublicationDisplayMode::DirectLinkNoTmpl)
		{
			$data->publicationLink = JRoute::_($data->rawPublicationLink.'&tmpl=component' . ($item->uid == $user->get('id') && isset($item->page) ? '#page/' . $item->page : ''), FALSE, $uri->isSSL());
			$data->viewPublicationLink= '<a href="'.$data->publicationLink.'" target="_blank">';
			$data->viewPublicationLinkWithTitle = '<a class="thumbnail" href="'.$data->publicationLink.'" target="_blank" title="'.$linkTitle .'">';
		}
		else if ($item->c_popup == PublicationDisplayMode::PopupWindow)
		{
			$data->publicationLink = JRoute::_($data->rawPublicationLink.'&tmpl=component' . ($item->uid == $user->get('id') && isset($item->page) ? '#page/' . $item->page : ''), FALSE, $uri->isSSL());
			$data->viewPublicationLink= '<a href="javascript: ht5popupWindow(\''.$data->publicationLink.'\', \'fm_'.$item->c_id.'\', '.$popupWidth.', '.$popupHeight.', \'no\');">';
			$data->viewPublicationLinkWithTitle = '<a class="thumbnail" href="javascript: ht5popupWindow(\''.$data->publicationLink.'\', \''.$item->c_id.'\', '.$popupWidth.', '.$popupHeight.', \'no\');" title="'.$linkTitle .'">';
		}
		else if ($item->c_popup == PublicationDisplayMode::ModalWindow)
		{
			$data->publicationLink = JRoute::_($data->rawPublicationLink."&tmpl=component" . ($item->uid == $user->get('id') && isset($item->page) ? '#page/' . $item->page : ''), FALSE, $uri->isSSL());
			$data->viewPublicationLink= '<a class="html5-modal" rel="{handler: \'iframe\', size: {x:jQuery(window).width()*0.8, y:jQuery(window).height()*0.8}}" href="'.$data->publicationLink.'">';
			$data->viewPublicationLinkWithTitle = '<a class="thumbnail html5-modal" rel="{handler: \'iframe\', size: {x:jQuery(window).width()*0.8, y:jQuery(window).height()*0.8}}" href="'.$data->publicationLink.'" title="'.$linkTitle .'">';
		}

		// Preparing Publication's thumbnail.

		$thumbnailPath = COMPONENT_MEDIA_PATH.'/thumbs/'.$item->c_thumb;

		if ($item->c_thumb == "" || !is_file($thumbnailPath))
		{
			$data->thumbnailUrl = COMPONENT_IMAGES_URL."no_image.png";
		}
		else
		{
			$data->thumbnailUrl = COMPONENT_MEDIA_URL."thumbs/".$item->c_thumb;
		}

		return $data;
	}

	/**
	 * Method to create button block
	 *
	 * @param object $item      Publication data
	 * @param string $list      Tab list
	 * @param string $position  Extra class for button block
	 * @param object $config    Component parameters
	 *
	 * @return string
	 */
	public static function publActionButtonBlock($item, $list, $position, $config)
	{
		$content = '
		<div class="btn-group ' . $position . '">
			<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
				<span class="fa fa-gear"></span>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">';

		if ($list == 'reading')
		{
			$content .= '
				<li>
					<a href="javascript: void(0);" onclick="userPublAction(' . $item->c_id . ', \'favorite\', \'' . $list . '\'); return false;">
						<i class="fa fa-star"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_FAVORITE_TIP') . '
					</a>
				</li>
				<li>
					<a href="javascript: void(0);" onclick="userPublAction(' . $item->c_id . ', \'reading_remove\', \'' . $list . '\'); return false;">
						<i class="fa fa-trash-o"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READING') . '
					</a>
				</li>';
		}
		elseif ($list == 'favorite')
		{
			$content .= '
				<li>
					<a href="javascript: void(0);" onclick="userPublAction(' . $item->c_id . ', \'reading\', \'' . $list . '\'); return false;">
						<i class="fa fa-list"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_READING_TIP') . '
					</a>
				</li>
				<li>
					<a href="javascript: void(0);" onclick="userPublAction(' . $item->c_id . ', \'favorite_remove\', \'' . $list . '\'); return false;">
						<i class="fa fa-trash-o"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_FAVORITE') . '
					</a>
				</li>';
		}

		$content .= '
				<li>';
		if ($item->read)
		{
			$content .= '
						<a href="javascript: void(0);" id="read_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'read_remove\', \'' . $list . '\'); return false;">
							<i class="fa fa-eye"></i> <span id="text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READ') . '</span>
						</a>';
		}
		else
		{
			$content .= '
						<a href="javascript: void(0);" id="read_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'read\', \'' . $list . '\'); return false;">
							<i class="fa fa-eye-slash"></i> <span id="text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_READ_TIP') . '</span>
						</a>';
		}

		$content .= '
				</li>';

		if ($config->social_email_use)
		{
			$content .= '
					<li>
						<a href="javascript: void(0);" onclick="userPublAction(' . $item->c_id . ', \'sendtofriend\', \'' . $list . '\'); return false;">
							<i class="fa fa-envelope"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_SEND_TO_FRIEND') . '
						</a>
					</li>';
		}

		if ($config->social_jomsocial_use)
		{
			$content .= '
					<li>
						<a href="javascript: void(0);" onclick="userPublAction(' . $item->c_id . ', \'share\', \'' . $list . '\'); return false;">
							<i class="fa fa-share-alt"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_JOMSOCIAL_SHARE') . '
						</a>
					</li>';
		}

		$content .= '</ul>
		</div>';

		return $content;
	}

	/**
	 * Method to create bookshelf
	 *
	 * @param string $list      Tab list
	 * @param array  $shelf     Publication data
	 * @param bool   $isMobile  Variable to determine if page opened on mobile phone
	 * @param bool   $isTablet  Variable to determine if page opened on tablet
	 * @param int    $shelfN    Shelf number
	 * @param object $config    Component parameters
	 *
	 * @return string
	 */
	public static function createBookShelf($list = 'reading', $shelf = array(), $isMobile = FALSE, $isTablet = FALSE, $shelfN = 1, $config)
	{
		$str = '';
        if(!is_array($shelf) || empty($shelf)){
            return $str;
        }
		$rowCount = count($shelf);
		foreach ($shelf as $i => $item)
		{
			$data = self::htmlPublHelper($isMobile, $isTablet, $item);
			$tooltip = '';
			$descr = strip_tags($item->c_pub_descr);
			if (strlen($descr) != "")
			{
				if (strlen($descr) <= 990)
				{
					$tooltip = '<strong>' . $item->c_title . '</strong><br/>' . $descr;
				}
				else
				{
					$tooltip = '<strong>' . $item->c_title . '</strong><br/>' . substr($descr, 0, strpos($descr, ' ', 990)).' ...';
				}
			}

			if ($i == 0)
			{
				$str .= '<div class="row-' . $shelfN . '">';
			}

			if (($i + 4) % 4 == 0)
			{
				$str .= '   <div class="loc">';
			}

			$str .= '<div class="' . $list . '-pub-' . $item->c_id .' ' . ($item->read ? 'hide-publ' : '') . '" ' . ($item->read ? 'style="display: none;"' : '') . '>';
			$str .= self::publActionButtonBlock($item, $list, '', $config);
			$str .= '	<div class="book hasTooltip" title="' . $tooltip . '">';
			$str .= str_replace("thumbnail", "", $data->viewPublicationLinkWithTitle);
			$str .= '		    <img class="pub-' . $item->c_id .'" src="' . $data->thumbnailUrl . '" alt="' . htmlspecialchars($item->c_title) . '" />';
			$str .= '       </a>';
			$str .= '   </div>';
			$str .= '</div>';

			if (($i + 1) % 4 == 0 || (($i + 1) == $rowCount))
			{
				$str .= '   </div>';
			}

			if ($i + 1 == $rowCount)
			{
				$str .= '</div>';
			}
		}

		return $str;
	}

	/**
	 * Generate dropdown list with download options for publication
	 *
	 * @param int $id   Publication id
	 *
	 * @return bool|string
	 */
	public static function generateDownloadOptions($id)
	{
		$uri = JUri::getInstance();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('`c_enable_pdf`, `c_background_pdf`, `convert`, `convert_formats`, `cloudconvert`, `cloudconvert_formats`')
			->from('`#__html5fb_publication`')
			->where('`c_id` = ' . $id);
		$db->setQuery($query);
		$download = $db->loadObject();
		if ($download->convert || $download->cloudconvert || $download->c_enable_pdf)
		{
			$formats = ($download->convert ? explode(',', $download->convert_formats) : ($download->cloudconvert ? explode(',', $download->cloudconvert_formats) : ''));
			$content = '
			<div class="btn-group">
				<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#" style="display: inline-block">
					<span class="fa fa-download"></span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">';
			if($download->c_enable_pdf || $formats){
				if($download->c_enable_pdf){
					$content .= '
								<li>
									<a href="' . JRoute::_('index.php?option='.COMPONENT_OPTION .'&task=convert.getpdf' . '&id='.$id .'&filename='.preg_replace('/[<>:"\/\\\|\?\*]/is', '', $download->c_background_pdf) .'&id=' . $id, FALSE, $uri->isSSL()) . '">
										<i class="fa fa-file-text-o"></i> ' . JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_PDF') . '
									</a>
								</li>';
				}
				if ($formats)
				{
					foreach($formats as $i=> $format)
					{
                        if( $format == 'pdf' && $download->c_enable_pdf ){
                            continue;
                        }
					    $content .= '
								<li>
									<a href="' . JRoute::_('index.php?option=com_html5flippingbook&task=convert.get'.strtolower($formats[$i]).''.($download->cloudconvert ? '&target=cloud' : '').'&id=' . $id, FALSE, $uri->isSSL()) . '">
										<i class="fa fa-file-text-o"></i> ' . JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_DOWNLOAD_OPTION_FORMATS', strtoupper($formats[$i])) . '
									</a>
								</li>';
					}
				}
			}
			else
			{
				return false;
			}

			$content .= '
				</ul>
			</div>';

			return $content;
		}

		return false;
	}

    public static function generateDownloadLinks($id)
    {
        $uri = JUri::getInstance();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`c_enable_pdf`, `c_background_pdf`, `convert`, `convert_formats`, `cloudconvert`, `cloudconvert_formats`')
            ->from('`#__html5fb_publication`')
            ->where('`c_id` = ' . $id);
        $db->setQuery($query);
        $download = $db->loadObject();
        $return = array();
        if ($download->convert || $download->cloudconvert || $download->c_enable_pdf)
        {
            $formats = ($download->convert ? explode(',', $download->convert_formats) : ($download->cloudconvert ? explode(',', $download->cloudconvert_formats) : ''));
            if($download->c_enable_pdf || $formats){
                if($download->c_enable_pdf){
                    $return[] = array(
                        JRoute::_('index.php?option='.COMPONENT_OPTION .'&task=convert.getpdf' . '&id='.$id .'&filename='.preg_replace('/[<>:"\/\\\|\?\*]/is', '', $download->c_background_pdf) .'&id=' . $id, FALSE, $uri->isSSL()),
                        'fa fa-file-text-o',
                        JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_PDF')
                    );
                }
                if ($formats)
                {
                    foreach($formats as $i=> $format)
                    {
                        if( $format == 'pdf' && $download->c_enable_pdf ){
                            continue;
                        }
                        $return[] = array(
                            JRoute::_('index.php?option=com_html5flippingbook&task=convert.get'.strtolower($formats[$i]).''.($download->cloudconvert ? '&target=cloud' : '').'&id=' . $id, FALSE, $uri->isSSL()),
                            'fa fa-file-text-o',
                            JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_DOWNLOAD_OPTION_FORMATS', strtoupper($formats[$i]))
                        );
                    }
                }
            }
        }

        return $return;
    }

	/**
	 * Method to create publication list
	 *
	 * @param string $list           Tab list
	 * @param array  $listData       Publication data
	 * @param bool   $isMobile       Variable to determine if page opened on mobile phone
	 * @param bool   $isTablet       Variable to determine if page opened on tablet
	 * @param string $publbuttontext Link text which can display instead of default text (View publication)
	 * @param object $config         Component parameters
	 *
	 * @return string
	 */
	public static function createPublicationList($list, $listData, $isMobile = FALSE, $isTablet = FALSE, $publbuttontext, $config)
	{
		$user = JFactory::getUser();
		$downloadOptionAccess = $user->authorise('core.download', COMPONENT_OPTION);

		$str = '';
		$k = 0;
		foreach ($listData as $item)
		{
			$downloadOptionAccessGranted = $user->authorise('core.download', COMPONENT_OPTION.'.publication.'.$item->c_id);

			if ($item->read != 1) $k++;

			$data = self::htmlPublHelper($isMobile, $isTablet, $item);

			$str .= '<li class="html5fb-list-item ' . $list . '-pub-' . $item->c_id . ' ' . ($item->read ? 'hide-publ' : '') . '" ' . ($item->read ? 'style="display: none;"' : '') . '>';
			$str .= '   <div class="html5fb-top" style="display: none" onclick="backToTop();"><span class="fa fa-arrow-up"></span></div>';
			$str .= '	<div class="list-overlay" style="display: none;"></div>';
			$str .= '	<div class="html5fb-pict">';
			$str .= $data->viewPublicationLinkWithTitle;
			$str .= '           <img class="html5fb-img" src="' . $data->thumbnailUrl . '" alt="' . htmlspecialchars($item->c_title) . '" />';
			$str .= '       </a>';
			$str .= '   </div>';

			$str .= '	<div class="html5fb-descr">';
			$str .= '		<div class="pull-left">';
			$str .= '           <h3 class="html5fb-name pub-' . $item->c_id . '">';
			$str .= str_replace("thumbnail", "", $data->viewPublicationLinkWithTitle) . htmlspecialchars($item->c_title);
			$str .= '               </a><br/>';
			$str .= '               <small>' . $item->c_author . '</small>';
			$str .= '           </h3>';
			$str .= '       </div>';

			$str .= self::publActionButtonBlock($item, $list, 'pull-right', $config);

			$str .= '       <br clear="all">';

			if (strlen($item->c_pub_descr) != "")
			{
				$str .= '   <p>';
				if (strlen($item->c_pub_descr) <= 990)
				{
					$str .= $item->c_pub_descr;
				}
				else
				{
					$str .= substr($item->c_pub_descr, 0, strpos($item->c_pub_descr, ' ', 990)).' ...';
				}
				$str .= '   </p>';
			}

			$str .= '       <div class="html5fb-links">';
			$str .= $data->viewPublicationLink . (isset($item->page) && $item->page != 0 ? JText::_('COM_HTML5FLIPPINGBOOK_FE_CONT_READ') : htmlspecialchars((empty($publbuttontext) ? JText::_('COM_HTML5FLIPPINGBOOK_FE_VIEW_PUBLICATION') : $publbuttontext))) . '</a>';

			if ($downloadOptionAccess && $downloadOptionAccessGranted)
			{
				$downloadList = HTML5FlippingBookFrontHelper::generateDownloadOptions($item->c_id);
				if ($downloadList)
				{
					$str .=   '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
					$str .=   JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_OPTIONS') . $downloadList;
				}
			}

			$str .= '       </div>';

			if ($item->c_show_cdate)
			{
				$date = new JDate($item->c_created_time);
				$date = $date->toUnix();
				$dateString = gmdate("Y-m-d", $date);

				$str .= '   <div class="html5fb-date">' . $dateString . '</div>';
			}

			$str .= '   </div>';
			$str .= '</li>';
		}

		return array($k, $str);
	}

	/**
	 * Method to convert seconds to human readable view
	 *
	 * @param $secs
	 *
	 * @return string
	 */
	public static function secsToString($secs)
	{
		$units = array(
			"week" => 7 * 24 * 3600,
			"day" => 24 * 3600,
			"hour" => 3600,
			"minute" => 60,
			"second" => 1,
		);

		// specifically handle zero
		if ($secs == 0) return "0 seconds";

		$s = "";

		foreach ($units as $name => $divisor)
		{
			if ($quot = intval($secs / $divisor))
			{
				$s .= "$quot $name";
				$s .= (abs($quot) > 1 ? "s" : "") . ", ";
				$secs -= $quot * $divisor;
			}
		}

		return substr($s, 0, -2);
	}
}