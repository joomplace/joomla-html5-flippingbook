<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookController extends JControllerLegacy
{
	protected $_config;

	//----------------------------------------------------------------------------------------------------
	function __construct($config = array())
	{
		$this->default_view = 'html5flippingbook';

		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/configuration.php');
		$configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
		$this->_config = $configurationModel->GetConfig();
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	public function display($cachable = false, $urlparams = false)
	{
		$uri  = JUri::getInstance();
		$user = JFactory::getUser();
		$view = $this->input->get('view', 'html5flippingbook', 'CMD');

		if ($view == 'profile' && !$user->get('id'))
		{
			$this->setMessage(JText::_('COM_HTML5FLIPPINGBOOK_FE_ERROR_NO_USER'), 'NOTICE');
			$this->setRedirect(JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()));
			return false;
		}

		parent::display($cachable, $urlparams);
	}

	public function search()
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$publicationId = $app->input->get('id', 0, 'INT');
		$text = $app->input->get('text', '', 'STRING');
		$is_hard = $app->input->get('hard', 0, 'INT');
		$search = $db->quote('%' . $db->escape($text, true) . '%');

		$query = $db->getQuery(true)
			->select('`id`')
			->from('`#__html5fb_pages`')
			->where('`publication_id` = ' . $publicationId);
		$db->setQuery($query);
		$pages = $db->loadColumn();

		$query = $db->getQuery(true)
			->select('`id`, `page_title`')
			->from('`#__html5fb_pages`')
			->where('`c_enable_text` = 1 AND `c_text` LIKE ' . $search . ' AND `publication_id` = ' . $publicationId);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$str = "";
		if (count($result))
		{
			foreach ($result as $page)
			{
				$indx = array_search($page->id, $pages);
				$pageN = $indx + ($is_hard ? 4 : 0);
				$str .= '<div class="search-result" id="page-' . $pageN . '">';
				$str .= '<a href="javascript: void(0);" onclick="turnPage(' . $pageN . ');">' . $page->page_title . '</a>';
				$str .= '</div>';
			}
		}
		else
		{
			$str .= '<div class="search-result">';
			$str .= JText::_('COM_HTML5FLIPPINGBOOK_FE_SEARCH_NO_RESULTS');
			$str .= '</div>';
		}


		print_r($str);

		$app->close();
	}

	public function getPageContent()
	{
		$db    = JFactory::$database;
		$app   = JFactory::$application;
		$cache = JCache::getInstance();

		if (!$cache->getCaching())
		{
			$cache->setCaching(true);
		}

		$pubID = $app->input->getInt('pubID', 0);
		$page  = $app->input->getInt('page', 1);

		$publCache = $cache->get('publicationStore:' . $pubID);

		if (!$publCache || !count($publCache['pages'])|| !($publCache['subfolder']))
		{
			$query = $db->getQuery(true)
				->select('`id`')
				->from('`#__html5fb_pages`')
				->where('`publication_id` = ' . $pubID)
				->order('`ordering` ASC');
			$db->setQuery($query);
			$pages = $db->loadColumn();

			$query->clear()
				->select('`c_title`, `c_author`, `c_imgsub`, `c_imgsubfolder`')
				->from('`#__html5fb_publication`')
				->where('`c_id` = ' . $db->quote($pubID));
			$db->setQuery($query);
			$pubSettings = $db->loadObject();

			$publCache = array(
				'pages'     => $pages,
				'lastPage'  => count($pages),
				'title'     => $pubSettings->c_title,
				'author'    => $pubSettings->c_author,
				'subfolder' => ($pubSettings->c_imgsub ? $pubSettings->c_imgsubfolder : '')
			);

			$cache->store($publCache, 'publicationStore:' . $pubID);
		}

		$query = $db->getQuery(true)
			->select('`c_enable_image`, `page_image`, `c_enable_text`, `c_text`')
			->from('`#__html5fb_pages`')
			->where('`publication_id` = ' . $pubID . ' AND `id` = ' . ($publCache['pages'][$page - 1]));
		$db->setQuery($query);
		$content = $db->loadObject();

		$response = '';
		if ($content->c_enable_image)
		{
			$response = array(
				"image"     => 1,
				"lastPage"  => $publCache['lastPage'],
				"title"     => $publCache['title'],
				"author"    => $publCache['author'],
				"content"   => '<img src="' . (COMPONENT_MEDIA_URL . 'images/' . ($publCache['subfolder'] != '' ? $publCache['subfolder'] . '/' : '') . $content->page_image) .'" alt="' . $content->page_image . '">'
			);
		}
		elseif ($content->c_enable_text)
		{
			$content->c_text = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $content->c_text);
			$content->c_text = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $content->c_text);
			$response = array(
				"image"     => 0,
				"lastPage"  => $publCache['lastPage'],
				"title"     => $publCache['title'],
				"author"    => $publCache['author'],
				"content"   => $content->c_text
			);
		}

		//Delete cache file, after last page has been sent to user
		if ($page == $publCache['lastPage'])
		{
			$cache->remove('publicationStore:' . $pubID);
		}

		echo json_encode($response);

		$app->close();
	}

	public function userPublAction()
	{
		$db    = JFactory::$database;
		$app   = JFactory::$application;
		$user  = JFactory::getUser();

		$sec        = $app->input->getInt('sec', 0);
		$page       = $app->input->getInt('page', 0);
		$publID     = $app->input->getInt('pubID', 0);
		$action     = $app->input->getWord('action', '');
		$response   = array();
		$update     = FALSE;

		if (!$publID)
		{
			$response = array("error" => 1, "message" => JText::_("COM_HTML5FLIPPINGBOOK_FE_ERROR_PUBL_ID"));
			echo json_encode($response);

			$app->close();
		}

		$query = $db->getQuery(true)
			->select('1')
			->from('`#__html5fb_users_publ`')
			->where('`uid` = ' . $user->get('id') . ' AND `publ_id` = ' . $publID);
		$db->setQuery($query);

		if ($db->loadResult())
		{
			$update = TRUE;
			$query = $db->getQuery(true)
				->update('`#__html5fb_users_publ`');

			switch ($action)
			{
				case 'lastopen':
					$query->set('`lastopen` = ' . time());
					break;
				case 'updateSpendTime':
					$query->set('`spend_time` = `spend_time` + ' . $sec);
					break;
				case 'updatePage':
					$query->set('`page` = ' . $page);
					break;
				case 'read':
					$query->set('`read` = 1');
					break;
				case 'read_remove':
					$query->set('`read` = 0');
					break;
				case 'favorite':
					$query->set('`fav_list` = 1');
					break;
				case 'favorite_remove':
					$query->set('`fav_list` = 0');
					break;
				case 'reading':
					$query->set('`read_list` = 1');
					break;
				case 'reading_remove':
					$query->set('`read_list` = 0');
					break;
			}

			$query->where('`uid` = ' . $user->get('id') . ' AND `publ_id` = ' . $publID);
		}
		else
		{
			$query = $db->getQuery(true)
				->insert('`#__html5fb_users_publ`')
				->columns(
					array(
						$db->quoteName('uid'), $db->quoteName('publ_id'),
						$db->quoteName('read_list'), $db->quoteName('fav_list'),
						$db->quoteName('read')
					)
				)
				->values($user->get('id') . ', ' . $publID . ', ' . ($action == 'reading' ? 1 : 0) . ', ' . ($action == 'favorite' ? 1 : 0) . ', ' . ($action == 'read' ? 1 : 0));
		}

		$db->setQuery($query);
		try
		{
			$db->execute();

			//Delete publication from user lists
			/*if ($update)
			{
				$query = $db->getQuery(true)
					->select('1')
					->from('`#__html5fb_users_publ`')
					->where('`uid` = ' . $user->get('id') . ' AND `publ_id` = ' . $publID . ' AND `read_list` = 0 AND `fav_list` = 0');
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$query = $db->getQuery(true)
						->delete('`#__html5fb_users_publ`')
						->where('`uid` = ' . $user->get('id') . ' AND `publ_id` = ' . $publID);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$response = array("error" => 1, "message" => $e->getMessage());

						echo json_encode($response);
						$app->close();
					}
				}
			}*/

			$response = array("error" => 0, "message" => "SUCCESS");
		}
		catch (RuntimeException $e)
		{
			$response = array("error" => 1, "message" => $e->getMessage());
		}

		echo json_encode($response);
		$app->close();
	}

	public function getAjaxContent()
	{
		require_once (JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');
		require_once (COMPONENT_LIBS_PATH . 'Mobile_Detect.php');
		$detectMobile = new Mobile_Detect_HTML5FB();

		$isMobile = FALSE;
		$isTablet = FALSE;
		// Exclude tablets.
		if ($detectMobile->isMobile() && !$detectMobile->isTablet())
		{
			$isMobile = TRUE;
		}
		elseif ($detectMobile->isTablet())
		{
			$isTablet = TRUE;
		}

		$app = JFactory::$application;

		$start = $app->input->getInt('start', 0);
		$limit = $app->input->getInt('limit', 3);
		$list  = $app->input->getString('list', '');
		$user  = JFactory::getUser();

		$downloadOptionAccess = $user->authorise('core.download', COMPONENT_OPTION);

		$model = $this->getModel('Profile', 'HTML5FlippingBookModel');
		if ($list == 'reading')
		{
			$model->_startR = $start;
			$model->_limitR = $limit;
			$content = $model->getReadList();
		}
		else
		{
			$model->_startF = $start;
			$model->_limitF = $limit;
			$content = $model->getFavoriteList();
		}

		$str = '';
		foreach ($content as $item)
		{
			$downloadOptionAccessGranted = $user->authorise('core.download', COMPONENT_OPTION.'.publication.'.$item->c_id);

			$data = HTML5FlippingBookFrontHelper::htmlPublHelper($isMobile, $isTablet, $item);
			$str .= '<li class="html5fb-list-item ' . $list . '-pub-' . $item->c_id .' '. ($item->read ? 'hide-publ' : '').'" '. ($item->read ? 'style="display: none;"' : '') .'>
						<div class="html5fb-top" style="display: none" onclick="backToTop();"><span class="fa fa-arrow-up"></span></div>
					    <div class="list-overlay" style="display: none;"></div>
						<div class="html5fb-pict">';
			$str .= $data->viewPublicationLinkWithTitle;
			$str .= '<img class="html5fb-img" src="'. $data->thumbnailUrl .'" alt="'. htmlspecialchars($item->c_title) .'" />
						</a>
					</div>';

			$str .= '<div class="html5fb-descr">
				<div class="pull-left">
					<h3 class="html5fb-name">
						'. str_replace("thumbnail", "", $data->viewPublicationLinkWithTitle) . htmlspecialchars($item->c_title) . '</a>' . '<br/>
						<small>'. $item->c_author .'</small>
					</h3>
				</div>';

			$str .= HTML5FlippingBookFrontHelper::publActionButtonBlock($item, $list, 'pull-right', $this->_config);

			$str .= '<br clear="all">';

			if (strlen($item->c_pub_descr) != "")
			{
				$str .= '<p>';
				if (strlen($item->c_pub_descr) <= 990)
				{
					$str .= $item->c_pub_descr;
				}
				else
				{
					$str .= substr($item->c_pub_descr, 0, strpos($item->c_pub_descr, ' ', 990)).' ...';
				}
				$str .= '</p>';
			}

			$str .= '<div class="html5fb-links">';
			$str .= $data->viewPublicationLink . (isset($item->page) && $item->page != 0 ? JText::_('COM_HTML5FLIPPINGBOOK_FE_CONT_READ') : htmlspecialchars((empty($this->viewPublicationButtonText) ? JText::_('COM_HTML5FLIPPINGBOOK_FE_VIEW_PUBLICATION') : $this->viewPublicationButtonText))) . '</a>';

			if ($downloadOptionAccess && $downloadOptionAccessGranted)
			{
				$downloadList = HTML5FlippingBookFrontHelper::generateDownloadOptions($item->c_id);
				if ($downloadList)
				{
					$str .=   '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
					$str .=   JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_OPTIONS') . $downloadList;
				}
			}

			$str .= '</div>';

			if ($item->c_show_cdate)
			{
				$date = new JDate($item->c_created_time);
				$date = $date->toUnix();
				$dateString = gmdate("Y-m-d", $date);

				$str .= '<div class="html5fb-date">' . $dateString . '</div>';
			}
			$str.= '</div>
			</li>';
		}
		print_r($str);
		$app->close();
	}

	public function userSettings()
	{
		$db    = JFactory::$database;
		$app   = JFactory::$application;
		$user  = JFactory::getUser();

		$settings    = $app->input->getString('settings', '');
		$publID      = $app->input->getInt('pubID', 0);
		$response    = array();
		$defSettings = array("showNavi" => 0, "nightMode" => 0, "backColor" => "#f9f9f9", "fontColor" => "#000", "fontSize" => 12);

		if (!$publID)
		{
			$response = array("error" => 1, "message" => JText::_("COM_HTML5FLIPPINGBOOK_FE_ERROR_PUBL_ID"));
			echo json_encode($response);

			$app->close();
		}

		$query = $db->getQuery(true)
			->select('`id`, `page`, `settings`')
			->from('`#__html5fb_users_publ`')
			->where('`uid` = ' . $user->get('id') . ' AND `publ_id` = ' . $publID);
		$db->setQuery($query);
		$publication = $db->loadObject();

		if (isset($publication->id))
		{
			$query = $db->getQuery(true)
				->select('`id`')
				->from('`#__html5fb_pages`')
				->where('`publication_id` = ' . $publID)
				->order('`ordering` ASC');
			$db->setQuery($query);
			$pages = $db->loadColumn();

			if ($settings != '')
			{
				$query = $db->getQuery(true)
					->update('`#__html5fb_users_publ`')
					->set('`settings` = "' . addslashes($settings) . '"')
					->where('`uid` = ' . $user->get('id') . ' AND `publ_id` = ' . $publID);
				$db->setQuery($query);
				try
				{
					$db->execute();

					$response = array(
						"error"      => 0,
						"message"    => 'SUCCESS',
						"settings"   => $settings,
						"storedPage" => array(
							"openPage"    => $publication->page ? $publication->page : 1,
							"publication" => $publID,
							"lastPage"    => count($pages)
						)
					);
				}
				catch (RuntimeException $e)
				{
					$response = array("error" => 1, "message" => $e->getMessage());
				}
			}
			else
			{
				$response = array(
					"error"      => 0,
					"message"    => 'SUCCESS',
					"settings"   => !empty($publication->settings) ? $publication->settings : json_encode($defSettings),
					"storedPage" => array(
						"openPage"    => $publication->page ? $publication->page : 1,
						"publication" => $publID,
						"lastPage"    => count($pages)
					)
				);
			}
		}

		echo json_encode($response);
		$app->close();
	}

	public function sendEmail()
	{
		$mail = JFactory::getMailer();
		$user = JFactory::getUser();
		$uri  = JUri::getInstance();
		$app  = JFactory::$application;
		$publID = $this->input->post->getInt('publID', 0);
		
		$link = HTML5FlippingBookFrontHelper::htmlPublHelper(FALSE, FALSE, $publID, TRUE)->publicationLink;
				
		if ($user->get('id'))
		{
			$link = JRoute::_('index.php?option=com_html5flippingbook&view=profile', FALSE, $uri->isSSL());
		}

		// An array of email headers we do not want to allow as input
		$headers = array (	'Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:');

		// An array of the input fields to scan for injected headers
		$fields = array(
			'recipient',
			'sender',
			'from',
			'subject',
			'publID'
		);

		/*
		 * Here is the meat and potatoes of the header injection test.  We
		 * iterate over the array of form input and check for header strings.
		 * If we find one, send an unauthorized header and die.
		 */
		foreach ($fields as $field)
		{
			foreach ($headers as $header)
			{
				if (strpos($_POST[$field], $header) !== false)
				{
					throw new Exception('', 403);
				}
			}
		}

		/*
		 * Free up memory
		 */
		unset ($headers, $fields);

		$email           = $app->input->post->getString('recipient', '');
		$sender          = $app->input->post->getString('sender', '');
		$from            = $app->input->post->getString('from', '');
		$subject_default = JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_SENT_BY', $sender);
		$subject         = $app->input->post->getString('subject', $subject_default);
		

		$SiteName       = $app->get('sitename');

		// Check for a valid to address
		$error	= false;
		if (!$email || !JMailHelper::isEmailAddress($email))
		{
			$error	= JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_MAILTO_EMAIL_INVALID', $email);
			throw new Exception($error, 0);
		}

		// Check for a valid from address
		if (!$from || !JMailHelper::isEmailAddress($from))
		{
			$error	= JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_MAILTO_EMAIL_INVALID', $from);
			throw new Exception($error, 0);
		}

		if (!$publID)
		{
			$this->setMessage(JText::_('COM_HTML5FLIPPINGBOOK_FE_ERROR_PUBL_ID'));
			$this->setRedirect($link);
		}

		// Build the message to send
		$msg	= JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_EMAIL_MSG');

		$publLink = JURI::root().HTML5FlippingBookFrontHelper::htmlPublHelper(FALSE, FALSE, $publID, TRUE)->publicationLink;
		$publLink =  preg_replace("#(?<!^http:)/{2,}#i","/",$link);
		$body	= sprintf($msg, $SiteName, $sender, $from, $publLink);

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);

		// To send we need to use punycode.
		$from  = JStringPunycode::emailToPunycode($from);
		$from  = JMailHelper::cleanAddress($from);
		$email = JStringPunycode::emailToPunycode($email);

		// Send the email
		if ($mail->sendMail($from, $sender, $email, $subject, $body) !== true)
		{
			$this->setMessage(JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_EMAIL_NOT_SENT'), 'NOTICE');
			$this->setRedirect($link);
		}

		$this->setMessage(JText::_('COM_HTML5FLIPPINGBOOK_FE_MAILTO_EMAIL_SENT'));
		$this->setRedirect(JRoute::_('index.php?option=com_html5flippingbook&view=publication&id='.$publID.'&tmpl=component', FALSE, $uri->isSSL()));
	}

	public function jomShare()
	{
		include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
		include_once JPATH_ROOT.'/components/com_community/libraries/activities.php';
		include_once JPATH_ROOT.'/components/com_community/libraries/notification.php';
		include_once JPATH_ROOT.'/components/com_community/libraries/userpoints.php';

		$db   = JFactory::getDbo();
		$app  = JFactory::$application;
		$user = JFactory::getUser();

		$publID = $app->input->get('pubID', 0, 'INT');
		$action = $app->input->get('action', '', 'STRING');
		$response = array();

		if (!$publID)
		{
			$response = array("error" => 1, "message" => JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_PUB_ERROR'));

			echo json_encode($response);
			$app->close();
		}
		
		$query = $db->getQuery(true)
			->select('`c_title`')
			->from('`#__html5fb_publication`')
			->where('`c_id` = ' . $db->quote($publID));
		$db->setQuery($query);
		$title = $db->loadResult();

		$publLink = HTML5FlippingBookFrontHelper::htmlPublHelper(FALSE, FALSE, $publID, TRUE)->publicationLink;

		switch ($action)
		{
			case 'post':
				$act            = new stdClass();
				$act->cmd 	    = 'profile.status';
				$act->actor 	= $user->get('id');
				$act->target 	= 0;
				$act->title 	= JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_DEF_POST', $title, $publLink);
				$act->content 	= '';

				// Pay close attention on this
				$act->app 	    = 'profile';
				$act->access    = 0; // 0 = Public; 20 = Site members; 30 = Friends Only; 40 = Only Me
				$act->cid       = 0;
				$act->params	= '{}';

				$act->comment_type  = 'profile.status';
				$act->comment_id    = CActivities::COMMENT_SELF;

				$act->like_type     = 'profile.status';
				$act->like_id       = CActivities::LIKE_SELF;

				CActivityStream::add($act);
				$response = array('error' => 0, 'message' => JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_POST_ADDED'));
				break;
			case 'sendprivate':
				$data = $app->input->get('data', '', 'STRING');
				$data = json_decode($data, TRUE);

				$model = CFactory::getModel('Inbox');

				$data['subject'] = $data['subject'] == '' ? $title : $data['subject'];
				$data['body'] = $data['body'] == '' ? JText::sprintf('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FIELD_SUBJECT_DEF', $publLink) : $data['body'];

				$pattern 	 = "/<br \/>/i";
				$replacement = "\r\n";
				$data['body'] = preg_replace($pattern, $replacement, $data['body']);

				$msgid = $model->send($data);

				//add user points
				CUserPoints::assignPoint('inbox.message.send');

				// Add notification
				$params			= new CParameter( '' );
				$params->set('url' , 'index.php?option=com_community&view=inbox&task=read&msgid='. $msgid );

				$params->set( 'message' , $data['body'] );
				$params->set( 'title'	, $data['subject'] );
				$params->set('msg_url' , 'index.php?option=com_community&view=inbox&task=read&msgid='. $msgid );
				$params->set('msg' , JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));

				CNotificationLibrary::add('inbox_create_message', $user->id, $data[ 'to' ], JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE'), '', 'inbox.sent', $params);
				$response = array('error' => 0, 'message' => JText::_('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_MESS_SENT'));
				break;
		}

		echo json_encode($response);
		$app->close();
	}
}