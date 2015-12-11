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
	//----------------------------------------------------------------------------------------------------
	function __construct($config = array())
	{
		$this->default_view = 'html5flippingbook';
		
		parent::__construct($config);
	}
	//----------------------------------------------------------------------------------------------------
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
	}

	//----------------------------------------------------------------------------------------------------
	public function getpdf()
	{
		$db = JFactory::getDBO();
		
		$jinput = JFactory::getApplication()->input;
		
		$publicationId = $jinput->get('id', 0, 'INT');
		$fileName = $jinput->get('filename', 0, 'STRING');
		
		$query = "SELECT a.*, b.* FROM `#__html5fb_publication` AS a" .
			" LEFT JOIN `#__html5fb_templates` AS b ON b.`id` = a.`c_template_id`" .
			" WHERE a.`c_id` = " . $db->quote($publicationId);
		$db->setQuery($query);
		$row = $db->loadObject();
		
		@ob_clean();
		header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		
		if ($row->c_enable_pdf == "1" && !empty($row->c_background_pdf))
		{
			$pdfFileFullName = COMPONENT_MEDIA_PATH.'/pdf/'.$row->c_background_pdf;
			
			if (file_exists($pdfFileFullName))
			{
				$fileSize = filesize($pdfFileFullName);
				
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $fileName . '"');
				header('Content-Length: ' . $fileSize);

                // readfile($pdfFileFullName); -- 5MB> ???
                $file = @fopen($pdfFileFullName,"rb");
                if ($file) {
                    while( !feof($file) ) {
                        print( fread($file, 1024*8) );
                        flush();
                        if ( connection_status() ) {
                            @fclose( $file );
                            jexit();
                        }
                    }
                    @fclose($file);
                }

				readfile($pdfFileFullName);
                jexit();
			}
			else
			{
				header('Content-Type: text/plain');
				echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PDF_NOT_FOUND');
			}
		}
		else
		{
			header('Content-Type: text/plain');
			echo JText::_('COM_HTML5FLIPPINGBOOK_FE_PDF_NOT_FOUND');
		}
		
		jexit();
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

		$pubID = $this->input->getInt('pubID', 0);
		$page  = $this->input->getInt('page', 1);

		$publCache = $cache->get('publicationStore:' . $pubID);

		if (!$publCache || !count($publCache['pages']) || !($publCache['subfolder']))
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
				->where('`c_id` = ' . $pubID);
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
}