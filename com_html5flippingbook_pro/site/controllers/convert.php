<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class HTML5FlippingBookControllerConvert extends JControllerLegacy
{
	protected $_type;
	protected $_contentTypes = array(
		"fb2"  => "text/x-fictionbook2",
		"txt"  => "text/plain",
		"epub" => "application/epub+zip",
		"mobi" => "application/x-mobipocket-ebook",
		"pdf"  => "application/pdf",
		"rtf"  => "application/rtf",
		"azw3" => "application/x-mobipocket-ebook",
		"lrf"  => "application/x-sony-bbeb",
		"pdb"  => "application/vnd.palm"
	);

	public function __construct($config = array())
	{
		parent::__construct($config);

		$task   = $this->input->getCmd('task', '');
		$target = $this->input->getCmd('target', '');

		if ($target == 'cloud')
		{
			$this->_type = str_replace('get', '', $task);
			$this->registerTask($task,	'cloudConvertBook');
		}
		else
		{
			$this->_type = str_replace('get', '_', $task);
			$this->registerTask($task,	'convertBook');
		}
	}

	/**
	 * Main method for convert publication
	 *
	 * @return bool
	 */
	public function convertBook()
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$db     = JFactory::getDBO();
		$app    = JFactory::getApplication();
		$uri    = JUri::getInstance();
		$webapp = JApplicationWeb::getInstance();

		$publicationId = $this->input->get('id', 0, 'INT');

		if (!$publicationId)
		{
			$this->setMessage(JText::_('Publication not found!'), 'WARNING');
			$this->setRedirect(JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()));
			return true;
		}

		$query = $db->getQuery(true)
			->select('`setting_value`')
			->from('`#__html5fb_config`')
			->where('`setting_name` = "component_version"');
		$db->setQuery($query);
		$progversion = $db->loadResult();

		$query = $db->getQuery(true)
			->select('`c_id`, `c_title`, `c_author`, `c_pub_descr`, `c_thumb`, `c_imgsub`, `c_imgsubfolder`')
			->from('`#__html5fb_publication`')
			->where('`c_id` = ' . $publicationId);
		$db->setQuery($query);
		$publication = $db->loadObject();

		$query = $db->getQuery(true)
			->select('`page_title`, `c_enable_image`, `page_image`, `c_enable_text`, `c_text`')
			->from('`#__html5fb_pages`')
			->where('`publication_id` = ' . $publicationId);
		$db->setQuery($query);
		$pages = $db->loadObjectList();

		if (isset($this->_contentTypes[str_replace("_", "", $this->_type)]))
		{
			$methodToConvert = $this->_type;

			if (!JFolder::exists(COMPONENT_MEDIA_PATH . '/converted_publications'))
			{
				JFolder::create(COMPONENT_MEDIA_PATH . '/converted_publications', 0757);
			}

			$newFileName = str_replace(" ", "_", strtolower(trim(strip_tags($publication->c_title)))) . "." . str_replace("_", "", $this->_type);

			//Check if publication already converted
			if (!$this->_sendConvertedFile($publication))
			{
				$result = $this->$methodToConvert($publication, $pages, $progversion);
			}

			if ($methodToConvert != '_epub')
			{
				$file = fopen(COMPONENT_MEDIA_PATH . '/converted_publications/' . $newFileName, 'wb');
				fwrite($file, $result);
				fclose($file);

				$webapp->setHeader("Content-Type", $this->_contentTypes[str_replace("_", "", $this->_type)]);
				$webapp->setHeader("Content-Length", strlen($result));
				$webapp->setHeader("Content-Disposition", "attachment; filename=" . $newFileName);
				$webapp->setHeader("Content-Description", "Publication converted with HTML5 Flipping Book Component");
				$webapp->sendHeaders();

				print $result;
			}
			$app->close();
		}
		else
		{
			$this->setMessage(JText::_('Content type is not allowed here!'), 'WARNING');
			$this->setRedirect(JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()));
			return false;
		}

		return true;
	}

	/**
	 * Convert publication to a FictionBook format (fb2)
	 *
	 * @param object $publication
	 * @param object $pages
	 * @param string $progversion
	 *
	 * @return string
	 */
	protected function _fb2($publication, $pages, $progversion)
	{
		require_once (COMPONENT_LIBS_PATH . 'class.html2fb2.inc');

		$author = explode(" ", $publication->c_author);

		// Preparing Publication's thumbnail.
		$thumbnailPath = COMPONENT_MEDIA_PATH.'/thumbs/'.$publication->c_thumb;
		if ($publication->c_thumb == "" || !is_file($thumbnailPath))
		{
			$thumbnailPath = COMPONENT_IMAGES_PATH."no_image.png";
		}

		$info   = getimagesize($thumbnailPath);
		$mime   = $info['mime'];

		$coverImageData = base64_encode(file_get_contents($thumbnailPath));

		$html       = array();
		$result     = '';

		foreach ($pages as $page)
		{
			if ($page->c_enable_image == 1)
			{
				$html[] = '<p><img src="' . COMPONENT_MEDIA_URL. 'images/' . ($publication->c_imgsub ? $publication->c_imgsubfolder . '/' : '') . $page->page_image . '" /></p>';
			}
			elseif ($page->c_enable_text == 1)
			{
				$page->c_text = str_replace('src="http://localhost/flipbook/', 'src="' . JUri::root() . '', $page->c_text);
				$page->c_text = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page->c_text);
				$page->c_text = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page->c_text);
				$html[] = $page->c_text;
			}
		}

		$str = implode(" ", $html);
		$str = $this->_clearHTML($str);

		if (count($html))
		{
			$html2fb = new HTML2FB2();
			$result = $html2fb->ParseText($str, TRUE);
		}

		$xmlStr = '';
		$xmlStr .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xmlStr .= '<FictionBook xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.gribuser.ru/xml/fictionbook/2.0">' . "\n";
		$xmlStr .= "\t" . '<description>' . "\n";
		$xmlStr .= "\t" . "\t" . '<title-info>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . '<genre></genre>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . '<author>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . "\t" . '<first-name>' . (isset($author[0]) && !empty($author[0]) ? trim(strip_tags($author[0])) : '') . '</first-name>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . "\t" . '<middle-name>' . (isset($author[1]) && !empty($author[1]) && isset($author[2]) && !empty($author[2]) ? trim(strip_tags($author[1])) : '') . '</middle-name>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . "\t" . '<last-name>' . (isset($author[2]) && !empty($author[2]) ? trim(strip_tags($author[2])) : (isset($author[1]) && !empty($author[1]) ? trim(strip_tags($author[1])) : '')) . '</last-name>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . '</author>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . '<book-title>' . trim(strip_tags($publication->c_title)) . '</book-title>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . '<annotation>' . trim(strip_tags($publication->c_pub_descr)) . '</annotation>' . "\n";
		$xmlStr .= "\t" . "\t" . "\t" . '<coverpage><image xlink:href="#cover.jpg"/></coverpage>' . "\n";
		$xmlStr .= "\t" . "\t" . '</title-info>' . "\n";
		$xmlStr .= "\t" . '</description>' . "\n";
		$xmlStr .= "\t" . '<document-info>' . "\n";
		$xmlStr .= "\t" . "\t" . '<program-used>HTML5 Flipping Book v' . $progversion . '</program-used>' . "\n";
		$xmlStr .= "\t" . '</document-info>' . "\n";

		$xmlStr .= "\t" . $result;

		$xmlStr .= "\t" . '<binary id="cover.jpg" content-type="' . $mime . '">' . $coverImageData . '</binary>' . "\n";
		$xmlStr .= '</FictionBook>';

		return $xmlStr;
	}

	/**
	 * Convert publication to a TXT format
	 *
	 * @param object $publication
	 * @param object $pages
	 * @param string $progversion
	 *
	 * @return string
	 */
	protected function _txt($publication, $pages, $progversion)
	{
		require_once (COMPONENT_LIBS_PATH . 'class.html2text.inc');

		$html   = array();
		$html[] = '---------------------------------------------------------------------------------------------<br/>';
		$html[] = $publication->c_author . '. ' . $publication->c_title . '.<br/>';
		$html[] = 'Converted with HTML5 Flipping Book v' . $progversion . '<br/>';
		$html[] = '---------------------------------------------------------------------------------------------<br/><br/>';

		foreach ($pages as $page)
		{
			if ($page->c_enable_image == 1)
			{
				$html[] = '<p><img src="' . COMPONENT_MEDIA_URL. 'images/' . ($publication->c_imgsub ? $publication->c_imgsubfolder . '/' : '') . $page->page_image . '" /></p>';
			}
			elseif ($page->c_enable_text == 1)
			{
				$page->c_text = str_replace('src="http://localhost/flipbook/', 'src="' . JUri::root() . '', $page->c_text);
				$page->c_text = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page->c_text);
				$page->c_text = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page->c_text);
				$html[] = $page->c_text;
			}
		}

		$html2txt = new html2text(implode(" ", $html));
		return $html2txt->get_text();
	}

	/**
	 * Convert publication to a Kindle(Pocket) book format
	 *
	 * @param object $publication
	 * @param object $pages
	 * @param string $progversion
	 *
	 * @return string
	 */
	protected function _mobi($publication, $pages, $progversion)
	{
		if (!$this->_sendConvertedFile($publication))
		{
			$app = JFactory::getApplication();
			$uri = JUri::getInstance();

			if (false !== strpos(ini_get("disable_functions"), "exec"))
			{
				$app->enqueueMessage(JText::_('COM_HTML5FLIPPINGBOOK_FE_DOWNLOAD_OPTION_ERROR_EXEC_FUNC'), 'WARNING');
				$app->redirect(JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()));
				return false;
			}

			if (false !== strpos(ini_get("disable_functions"), "chmod"))
			{
				$app->enqueueMessage(JText::_('COM_HTML5FLIPPINGBOOK_FE_DOWNLOAD_OPTION_ERROR_CHMOD_FUNC'), 'WARNING');
				$app->redirect(JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()));
				return false;
			}
			else
			{
				if (substr(sprintf('%o', fileperms(JPATH_SITE . '/components/com_html5flippingbook/libs/mobi/kindlegen')), -4) != '0755')
				{
					chmod(COMPONENT_LIBS_PATH . 'mobi/kindlegen', 0755);
				}
			}

			include COMPONENT_LIBS_PATH . "mobi/vendor/autoload.php";

			$content = array();
			foreach ($pages as $i => $page)
			{
				if ($page->c_enable_image == 1)
				{
					$content[] = array(
						'id' => $i,
						'name' => '',
						'content' => array(
							// Array of articles
							array(
								'title' => '',
								'content' => '<p style="text-align: center;"><img src="' . COMPONENT_MEDIA_PATH. '/images/' . ($publication->c_imgsub ? $publication->c_imgsubfolder . '/' : '') . $page->page_image . '" /></p>'
							)
						)
					);
				}
				elseif ($page->c_enable_text == 1)
				{
					$page->c_text = str_replace('src="media/', 'src="' . COMPONENT_MEDIA_PATH . '/', $page->c_text);
					$page->c_text = str_replace('src="images/', 'src="' . JPATH_SITE . '/images/', $page->c_text);

					$content[] = array(
						'id' => $i,
						'name' => '',
						'content' => array(
							// Array of articles
							array(
								'title' => '',
								'content' => $page->c_text
							)
						)
					);
				}
			}

			$ebook = new \Kindle\Periodical(array(
				"outputFolder"  => COMPONENT_MEDIA_PATH . '/converted_publications',
				"kindleGenDir"  => JPATH_SITE . '/components/com_html5flippingbook/libs/mobi',
				"downloadUrl"   => COMPONENT_MEDIA_URL,

				// Optional arguments:
				"shell" => false,
				"debug" => false
			));

			$ebook->setFilename(str_replace(" ", "_", strtolower(trim(strip_tags($publication->c_title)))));

			$ebook->setMeta(array(
				'title'       => $publication->c_title,
				'creator'     => $publication->c_author,
				'publisher'   => "HTML5 Flipping Book v" . $progversion,
				'subject'     => "",
				'description' => $publication->c_pub_descr
			));

			// Generates the file
			$ebook->setContent($content);

			// Download the file
			return $ebook->downloadFile();
		}

		return '';
	}

	/**
	 * Convert publication to a ePub book format
	 *
	 * @param object $publication
	 * @param object $pages
	 *
	 * @return string
	 */
	protected function _epub($publication, $pages)
	{
		if (!$this->_sendConvertedFile($publication))
		{
			require_once (COMPONENT_LIBS_PATH . 'ePub/EPub.php');

			$uri    = JUri::getInstance();
			$config = JFactory::getConfig();

			$book = new EPub();

			$book->setTitle($publication->c_title);
			$book->setIdentifier(JRoute::_('index.php?option=com_html5flippingbook&view=publication&id=' . $publication->c_id, FALSE, $uri->isSSL()), EPub::IDENTIFIER_URI); // Could also be the ISBN number, prefered for published books, or a UUID.
			$book->setDescription($publication->c_pub_descr);
			$book->setAuthor($publication->c_author, "");
			$book->setPublisher($config->get('sitename'), JUri::root());
			$book->setSourceURL(JRoute::_('index.php?option=com_html5flippingbook&view=publication&id=' . $publication->c_id, FALSE, $uri->isSSL()));

			$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";
			$book->addCSSFile("Styles/styles.css", "css1", $cssData);

			$content_start =
				"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
				. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
				. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
				. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
				. "<head>"
				. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
				. "<link rel=\"stylesheet\" type=\"text/css\" href=\"../Styles/styles.css\" />\n"
				. "<title>Test Book</title>\n"
				. "</head>\n"
				. "<body>\n";

			$bookEnd = "</body>\n</html>\n";

			$html = '';
			foreach ($pages as $page)
			{
				if ($page->c_enable_image == 1)
				{
					$html = $content_start . '<p style="text-align: center;"><img src="' . COMPONENT_MEDIA_URL. 'images/' . ($publication->c_imgsub ? $publication->c_imgsubfolder . '/' : '') . $page->page_image . '" /></p>' . $bookEnd;
				}
				elseif ($page->c_enable_text == 1)
				{
					$page->c_text = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page->c_text);
					$page->c_text = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page->c_text);
					$html = $content_start . $page->c_text . $bookEnd;
				}

				$html = $this->_clearHTML($html);
				$book->addChapter('', $page->page_title . '.html', $html, true, EPub::EXTERNAL_REF_ADD);
			}

			$fileName = str_replace(" ", "_", strtolower(trim(strip_tags($publication->c_title))));

			$book->finalize(); // Finalize the book, and build the archive.
			$book->saveBook($fileName, COMPONENT_MEDIA_PATH . '/converted_publications');
			$book->sendBook($fileName);
		}

		return '';
	}

	/**
	 * Get data from publication PDF-file
	 *
	 * @param object $publication   A publication data
	 * @param object $pages         Pages from current publication
	 *
	 * @return string
	 */
	protected function _pdf($publication, $pages)
	{
		$output = '';

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('`a`.*, `b`.*')
			->from('`#__html5fb_publication` AS `a`')
			->leftJoin('`#__html5fb_templates` AS `b` ON `b`.`id` = `a`.`c_template_id`')
			->where('`a`.`c_id` = ' . $db->quote($publication->c_id));
		$db->setQuery($query);
		$row = $db->loadObject();

		@ob_clean();

		if ($row->c_enable_pdf == "1" && !empty($row->c_background_pdf))
		{
			$pdfFileFullName = COMPONENT_MEDIA_PATH . '/pdf/' . $row->c_background_pdf;

			if (file_exists($pdfFileFullName))
			{
				$file = @fopen($pdfFileFullName, "rb");
				$size = filesize($pdfFileFullName);
				if (is_resource($file))
				{
					$output = fread($file, $size);  // Read data from file handle
					@fclose($file);
				}
			}
			else
			{
				return $this->_convertToPDF($publication, $pages);
			}
		}
		else
		{
			return $this->_convertToPDF($publication, $pages);
		}

		return $output;
	}

	/**
	 * Convert publication to a PDF format
	 *
	 * @param object $publication   A publication data
	 * @param object $pages         Pages from current publication
	 *
	 * @return string
	 */
	private function _convertToPDF($publication, $pages)
	{
		if (!$this->_sendConvertedFile($publication))
		{
			require_once (COMPONENT_LIBS_PATH . 'mPDF/mpdf.php');

			$html       = array();
			foreach ($pages as $i => $page)
			{
				if ($page->c_enable_image == 1)
				{
					$html[] = '<p style="text-align: center;"><img src="' . COMPONENT_MEDIA_URL. 'images/' . ($publication->c_imgsub ? $publication->c_imgsubfolder . '/' : '') . $page->page_image . '" /></p>';
					if ($i == 0)
					{
						$html[] = '<pagebreak />';
					}
				}
				elseif ($page->c_enable_text == 1)
				{
					$page->c_text = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page->c_text);
					$page->c_text = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page->c_text);
					$html[] = $page->c_text;
					if ($i == 0)
					{
						$html[] = '<pagebreak />';
					}
				}
			}

			$str = implode(" ", $html);
			$str = $this->_clearHTML($str);

			$pdf = new mPDF();
			$pdf->SetHeader($publication->c_author . '|' . $publication->c_title . '|{PAGENO}');
			$pdf->WriteHTML($str);

			return $pdf->Output('', 'S');
		}

		return '';
	}


	/*****************************************/
	/* Integration with CloudConvert service */
	/*****************************************/

	public function cloudConvertBook()
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$db     = JFactory::getDBO();
		$app    = JFactory::getApplication();
		$uri    = JUri::getInstance();
		$webapp = JApplicationWeb::getInstance();

		$publicationId = $this->input->get('id', 0, 'INT');

		if (!$publicationId)
		{
			$this->setMessage(JText::_('Publication not found!'), 'WARNING');
			$this->setRedirect(JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()));
			return true;
		}

		$query = $db->getQuery(true)
			->select('`c_id`, `c_title`, `c_author`, `c_pub_descr`, `c_thumb`, `c_imgsub`, `c_imgsubfolder`, `cloudconvert_api`')
			->from('`#__html5fb_publication`')
			->where('`c_id` = ' . $publicationId);
		$db->setQuery($query);
		$publication = $db->loadObject();

		$newFileName    = str_replace(" ", "_", strtolower(trim(strip_tags($publication->c_title))));
		$inputFile      = JPATH_SITE . '/tmp/' . $newFileName . '.html';
		$outputFile     = COMPONENT_MEDIA_PATH . '/converted_publications/' . $newFileName . '.' . $this->_type;

		if (!JFolder::exists(COMPONENT_MEDIA_PATH . '/converted_publications'))
		{
			JFolder::create(COMPONENT_MEDIA_PATH . '/converted_publications', 0757);
		}

		if (JFile::exists($outputFile))
		{
			$output = '';
			$file = @fopen($outputFile, "rb");
			$size = filesize($outputFile);
			if (is_resource($file))
			{
				$output = fread($file, $size);  // Read data from file handle
				@fclose($file);
			}

			$webapp->setHeader("Content-Type", $this->_contentTypes[$this->_type]);
			$webapp->setHeader("Content-Length", strlen($output));
			$webapp->setHeader("Content-Disposition", "attachment; filename=" . $newFileName . '.' . $this->_type);
			$webapp->setHeader("Content-Description", "Publication converted with HTML5 Flipping Book Component");
			$webapp->sendHeaders();

			print $output;
			$app->close();
		}

		$query = $db->getQuery(true)
			->select('`setting_value`')
			->from('`#__html5fb_config`')
			->where('`setting_name` = "component_version"');
		$db->setQuery($query);
		$progversion = $db->loadResult();

		$query = $db->getQuery(true)
			->select('`page_title`, `c_enable_image`, `page_image`, `c_enable_text`, `c_text`')
			->from('`#__html5fb_pages`')
			->where('`publication_id` = ' . $publicationId);
		$db->setQuery($query);
		$pages = $db->loadObjectList();

		require_once (COMPONENT_LIBS_PATH . 'CloudConvert.class.php');

		$html[] = '<h1>' . $publication->c_title . '</h1>';
		foreach ($pages as $page)
		{
			if ($page->c_enable_image == 1)
			{
				$html[] = '<p style="text-align: center;"><img src="' . COMPONENT_MEDIA_URL. 'images/' . ($publication->c_imgsub ? $publication->c_imgsubfolder . '/' : '') . $page->page_image . '" /></p>';
			}
			elseif ($page->c_enable_text == 1)
			{
				$page->c_text = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page->c_text);
				$page->c_text = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page->c_text);
				$html[] = $page->c_text;
			}
		}

		$str = implode("\n", $html);
		$str = $this->_clearHTML($str);

		//Prepare publication to upload to the CloudConvert service
		JFile::copy(COMPONENT_LIBS_PATH . 'convert_tpl/content.html', $inputFile);

		$f = fopen($inputFile, 'rb+');
		$content = fread($f, filesize($inputFile));

		$content = str_replace('{version}', $progversion, $content);
		$content = str_replace('{description}', strip_tags($publication->c_pub_descr), $content);
		$content = str_replace('{title}', $publication->c_title, $content);
		$content = str_replace('{content}', $str, $content);

		rewind($f);
		fwrite($f, $content);
		fclose($f);

		$process = CloudConvert::createProcess("html", $this->_type, $publication->cloudconvert_api);
		$process->upload($inputFile, $this->_type);

		if ($process->waitForConversion())
		{
			$process->download($outputFile);

			$file = @fopen($outputFile, "rb");
			$size = filesize($outputFile);
			if (is_resource($file))
			{
				$output = fread($file, $size);  // Read data from file handle
				@fclose($file);
			}

			$webapp->setHeader("Content-Type", $this->_contentTypes[$this->_type]);
			$webapp->setHeader("Content-Length", strlen($output));
			$webapp->setHeader("Content-Disposition", "attachment; filename=" . $newFileName . '.' . $this->_type);
			$webapp->setHeader("Content-Description", "Publication converted with HTML5 Flipping Book Component");
			$webapp->sendHeaders();

			print $output;
		}
		else
		{
			throw new Exception('Error during converting file');
		}

		unlink($inputFile);
		$app->close();
	}

	/**
	 * Send converted publication file if it exist
	 *
	 * @param object $publication
	 *
	 * @return bool
	 */
	private function _sendConvertedFile($publication)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$newFileName = str_replace(" ", "_", strtolower(trim(strip_tags($publication->c_title)))) . "." . str_replace("_", "", $this->_type);
		$outputFile = COMPONENT_MEDIA_PATH . '/converted_publications/' . $newFileName;

		if (JFile::exists($outputFile))
		{
			$file = @fopen($outputFile, "rb");
			$size = filesize($outputFile);
			if (is_resource($file))
			{
				$output = fread($file, $size);  // Read data from file handle
				@fclose($file);
			}

			$app = JFactory::getApplication();
			$webapp = JApplicationWeb::getInstance();
			$webapp->setHeader("Content-Type", $this->_contentTypes[str_replace("_", "", $this->_type)]);
			$webapp->setHeader("Content-Length", strlen($output));
			$webapp->setHeader("Content-Disposition", "attachment; filename=" . $newFileName);
			$webapp->setHeader("Content-Description", "Publication converted with HTML5 Flipping Book Component");
			$webapp->sendHeaders();

			print $output;
			$app->close();
		}

		return false;
	}

	/**
	 * Process HTML text
	 *
	 * @param string $html HTML string
	 *
	 * @return mixed
	 */
	private function _clearHTML($html)
	{
		$html = preg_replace('/(<br[^>]*>\s*)+/i', '\1', $html);         # replace any <br/> tags to one
		$html = preg_replace("~\s{2,}~is", ' ', $html);                  # more than 2 spaces - to 1 space
		$html = preg_replace("~\.{2,}~is", '.', $html);                  # mare than 2 dots - to 1 dot
		$html = preg_replace("~,{2,}~is", ',', $html);                   # more than 2 commas - to 1 comma
		$html = preg_replace("~(\.)(,)~is", '.', $html);                 # ".," -> "."
		$html = preg_replace("~(,)(\.)~is", '.', $html);                 # ",." -> "."
		$html = preg_replace("~(\.)\s{0,}(,)\s{0,}(\.)~is", '.', $html); # ".,." or ". , ." -> "."
		$html = preg_replace("~(\.)\s{0,}(\.)~is", '.', $html);          # ".." or ". ." -> "."
		$html = preg_replace("~\s{1,}(\.)~is", '.', $html);              # " ." -> "."
		$html = preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $html);  # delete empty <p> tag, i.e. <p></p>

		return $html;
	}
}