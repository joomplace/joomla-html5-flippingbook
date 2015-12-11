<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerSample_Data extends JControllerLegacy
{
	//----------------------------------------------------------------------------------------------------
	public function install()
	{
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');

		$jinput = JFactory::getApplication()->input;
		
		$jinput->set('view', 'sample_data');
		
		$joomlaConfig = JFactory::getConfig();
		
		//==================================================
		// Updating files.
		//==================================================
		
		$archiveName = "html5fb_sampledata_30_001.zip";
		
		// Changing PHP settings.
		
		if ((int) ini_get('memory_limit') < 128)
		{
			ini_set("memory_limit", "128M");
		}
		
		if ((int) ini_get('max_execution_time') < 600)
		{
			ini_set("max_execution_time", "0");
            set_time_limit(0);
		}


		// Downloading archive.
		
		$sourceUrl = "https://www.joomplace.com/media/".$archiveName;

		$downloadResult = JInstallerHelper::downloadPackage($sourceUrl);

		if (!$downloadResult)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HTML5FLIPPINGBOOK_BE_SAMPLEDATA_INVALID_URL'), 'error');
			return false;
		}
		
		// Extracting archive.
		
		$archiveFileFullName = $joomlaConfig->get("tmp_path")."/".$archiveName;
		
		$package = JArchive::extract($archiveFileFullName, COMPONENT_MEDIA_PATH);

		if (!$package)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('CANT_EXTRACT_ARCHIVE'), 'error');
			return false;
		}
		else
		{
			JFile::delete($archiveFileFullName);
		}

        //==================================================
		// Updating database.
		//==================================================
		
		$db = JFactory::getDBO();
		
		// Category.
		
		$query = "SELECT * FROM `#__html5fb_category` WHERE `c_category` = 'sample_data'";
		$db->setQuery($query);
        $categoryId = $db->loadResult();

		if (!$categoryId)
		{
			$query = "INSERT INTO `#__html5fb_category`" .
				" (`c_category`, `c_instruction`)" .
				" VALUES ('sample_data', 'This category contains sample publications.')";
			$db->setQuery($query);
			$db->execute();

            $categoryId = $db->insertid();
		}

		// Resolutions.
		
		$query = "SELECT `id`" .
				 " FROM `#__html5fb_resolutions`" .
				 " WHERE `resolution_name` = 'Magazine (460 x 600)'";
		$db->setQuery($query);
		$resolutionMagazineId = $db->loadResult();
		
		if (!$resolutionMagazineId)
		{
			$query ="INSERT INTO `#__html5fb_resolutions` (`resolution_name`, `height`, `width`) VALUES" .
				"('Magazine (460 x 600)', 600, 462)";
			$db->setQuery($query);
			$db->execute();

            $resolutionMagazineId = $db->insertid();
		}

		// Template
		$db->setQuery("SELECT `id` FROM `#__html5fb_templates` WHERE `template_name` = 'Magazine'");
		$templMagazineID = $db->loadResult();

		if (!$templMagazineID)
		{
			$db->setQuery("INSERT INTO `#__html5fb_templates` (`template_name`, `hard_cover`, `page_background_color`, `background_color`, `text_color`, `fontfamily`, `fontsize`, `display_slider`, `display_pagebox`, `display_title`, `display_topicons`, `display_nextprev`, `p_margin`, `p_lineheight`, `slider_thumbs`, `show_shadow`)
							   VALUES ('Magazine', 0, '', '', '#000000', 0, '14px', 1, 1, 1, 1, 1, '0', '15px', 1, 1);");
			$db->execute();

			$templMagazineID = $db->insertid();
		}

		// Preparing ordering data.
		
		$query = "SELECT MAX(`ordering`) FROM `#__html5fb_publication`";
		$db->setQuery($query);
		$publicationsMaxOrdering = $db->loadResult();
		
		if (!isset($publicationsMaxOrdering) || $publicationsMaxOrdering == "") $publicationsMaxOrdering = 0;
		
		// Preparing access rules data.
		
		$query = "SELECT `id` FROM `#__assets`" .
			" WHERE `name` = " . $db->quote(COMPONENT_OPTION);
		$db->setQuery($query);
		$componentRootAssetId = $db->loadResult();
		
		$newPublicationAccessRule = '{"core.view":[]}';


		//==================================================
		// sample_online_shopping.
		//==================================================
		
		$title = "Online Shopping Sample";
		
		$query = "SELECT * FROM `#__html5fb_publication` WHERE `c_title` = " . $db->quote($title);
		$db->setQuery($query);
		
		if (!$db->loadResult())
		{
			$subfolderName = 'sample_online_shopping';
			$thumbFileName = 'sample_data_online_shopping.jpg';
			$displayMode = PublicationDisplayMode::PopupWindow;
			$description = '<p>Sample data of ikea bad collection</p>';
			$enablePdf = '1';
			$author = 'IKEA';
			$pdfFileName = 'sample_data_online_shopping.pdf';
			$publicationsMaxOrdering += 1;
			
			$query = "INSERT INTO `#__html5fb_publication`" .
				" (`c_category_id`, `c_title`, `c_author`, `c_template_id`, `c_resolution_id`," .
				" `c_enable_pdf`, `c_background_pdf`, " .
				" `c_created_time`, `c_pub_descr`, `c_thumb`, `c_popup`, `c_imgsubfolder`," .
				" `published`, `c_imgsub`, `c_metadesc`, `c_metakey`, `ordering` )" .
				" VALUES (" .
                $categoryId.", ".$db->quote($title).", ".$db->quote($author).", ".$templMagazineID.", ".$resolutionMagazineId.", " .
				$enablePdf.", ".$db->quote($pdfFileName).", " .
				$db->quote(gmdate("Y-m-d H:i:s")).", ".$db->quote($description).", ".$db->quote($thumbFileName).", ".$displayMode.", ".$db->quote($subfolderName).", " .
				"1, 1, '', '', ".$publicationsMaxOrdering.");";

			$db->setQuery($query);
			$db->execute();
			
			// Creating access rule.
			
			$publicationId =  $db->insertid();
			
			$asset = JTable::getInstance('Asset', 'JTable');
			$asset->name = COMPONENT_OPTION.'.publication.'.$publicationId;
			$asset->title = $title;
			$asset->rules = $newPublicationAccessRule;
			$asset->setLocation($componentRootAssetId, 'last-child');
			$asset->store();
			
			$query = "UPDATE `#__html5fb_publication`" .
				" SET `asset_id` = " . $db->quote($asset->id) .
				" WHERE `c_id` = " . $db->quote($publicationId);
			$db->setQuery($query);
			$db->execute();
			
			unset($asset);

			// Creating pages.
			
			$pageOrder = 1;
			
			$pages = array(
                (object) array("image" => "sample_data_online_shopping_001.jpg", "title" => "Online Shopping page 001"),
                (object) array("image" => "sample_data_online_shopping_002.jpg", "title" => "Online Shopping page 002"),
                (object) array("image" => "sample_data_online_shopping_003.jpg", "title" => "Online Shopping page 003"),
                (object) array("image" => "sample_data_online_shopping_004.jpg", "title" => "Online Shopping page 004"),
                (object) array("image" => "sample_data_online_shopping_005.jpg", "title" => "Online Shopping page 005"),
                (object) array("image" => "sample_data_online_shopping_006.jpg", "title" => "Online Shopping page 006"),
                (object) array("image" => "sample_data_online_shopping_007.jpg", "title" => "Online Shopping page 007"),
                (object) array("image" => "sample_data_online_shopping_008.jpg", "title" => "Online Shopping page 008"),
                (object) array("image" => "sample_data_online_shopping_009.jpg", "title" => "Online Shopping page 009"),
                (object) array("image" => "sample_data_online_shopping_010.jpg", "title" => "Online Shopping page 010"),
				);
			
			foreach ($pages as $page)
			{
				$query = "INSERT INTO `#__html5fb_pages`" .
					" (`publication_id`, `page_title`, `ordering`, `c_enable_image`, `page_image`, `c_enable_text`, `c_text`)" .
					" VALUES" .
					" (".$publicationId.", ".$db->quote($page->title).", ".$pageOrder.", 1, ".$db->quote($page->image).", 0, NULL)";
				$db->setQuery($query);
				$db->execute();
				
				$pageOrder += 1;
			}
		}


		//==================================================
		// sample steve jobs
		//==================================================

		//Resolution
		$query = "SELECT `id`" .
			" FROM `#__html5fb_resolutions`" .
			" WHERE `resolution_name` = 'HardBook (480 x 600)'";
		$db->setQuery($query);
		$resolutionHardBookId = $db->loadResult();

		if (!$resolutionHardBookId)
		{
			$query ="INSERT INTO `#__html5fb_resolutions` (`resolution_name`, `height`, `width`) VALUES" .
				"('HardBook (480 x 600)', 600, 480)";
			$db->setQuery($query);
			$db->execute();

			$resolutionHardBookId = $db->insertid();
		}

		// Template
		$db->setQuery("SELECT `id` FROM `#__html5fb_templates` WHERE `template_name` = 'HardBook'");
		$templHardbookID = $db->loadResult();

		if (!$templHardbookID)
		{
			$db->setQuery("INSERT INTO `#__html5fb_templates` (`template_name`, `hard_cover`, `page_background_color`, `background_color`, `text_color`, `fontfamily`, `fontsize`, `display_slider`, `display_pagebox`, `display_title`, `display_topicons`, `display_nextprev`, `p_margin`, `p_lineheight`, `slider_thumbs`, `show_shadow`)
							   VALUES ('HardBook', 1, '', '', '#000000', 8, '14px', 1, 1, 1, 1, 0, '0', '15px', 1, 1);");
			$db->execute();

			$templHardbookID = $db->insertid();
		}

		$title = "Steve Jobs";

		$query = "SELECT * FROM `#__html5fb_publication` WHERE `c_title` = " . $db->quote($title);
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$subfolderName = 'sample_steve_jobs';
			$thumbFileName = 'sample_steve_jobs.jpg';
			$author = 'Walter Isaacson';
			$displayMode = PublicationDisplayMode::ModalWindow;
			$description = '<p>Steve Jobs is the authorized biography of Steve Jobs. The biography was written at the request of Jobs by acclaimed biographer Walter Isaacson, a former executive at CNN and Time who has written best-selling biographies about Benjamin Franklin and Albert Einstein.</p>';
			$enablePdf = '0';
			$pdfFileName = '';
			$publicationsMaxOrdering += 1;

			$query = "INSERT INTO `#__html5fb_publication`" .
				" (`c_category_id`, `c_title`, `c_author`, `c_template_id`, `c_resolution_id`," .
				" `c_enable_pdf`, `c_background_pdf`, " .
				" `c_created_time`, `c_pub_descr`, `c_thumb`, `c_popup`, `c_imgsubfolder`," .
				" `published`, `c_imgsub`, `c_metadesc`, `c_metakey`, `ordering` )" .
				" VALUES (" .
                $categoryId.", ".$db->quote($title).", ".$db->quote($author).", ".$templHardbookID.", ".$resolutionHardBookId.", " .
				$enablePdf.", ".$db->quote($pdfFileName).", " .
				$db->quote(gmdate("Y-m-d H:i:s")).", ".$db->quote($description).", ".$db->quote($thumbFileName).", ".$displayMode.", ".$db->quote($subfolderName).", " .
				"1, 1, '', '', ".$publicationsMaxOrdering.");";

			$db->setQuery($query);
			$db->execute();

			// Creating access rule.

			$publicationId =  $db->insertid();

			$asset = JTable::getInstance('Asset', 'JTable');
			$asset->name = COMPONENT_OPTION.'.publication.'.$publicationId;
			$asset->title = $title;
			$asset->rules = $newPublicationAccessRule;
			$asset->setLocation($componentRootAssetId, 'last-child');
			$asset->store();

			$query = "UPDATE `#__html5fb_publication`" .
				" SET `asset_id` = " . $db->quote($asset->id) .
				" WHERE `c_id` = " . $db->quote($publicationId);
			$db->setQuery($query);
			$db->execute();

			unset($asset);

			// Creating pages.

			$pageOrder = 1;

            $pages = array(
                (object) array("image" => "cover.jpg", "title" => "Cover page"),
				);

			foreach ($pages as $page)
			{
				$query = "INSERT INTO `#__html5fb_pages`" .
					" (`publication_id`, `page_title`, `ordering`, `c_enable_image`, `page_image`, `c_enable_text`, `c_text`)" .
					" VALUES" .
					" (".$publicationId.", ".$db->quote($page->title).", ".$pageOrder.", 1, ".$db->quote($page->image).", 0, NULL)";
				$db->setQuery($query);
				$db->execute();

				$pageOrder += 1;
			}

            $htmlFiles = glob(COMPONENT_MEDIA_PATH.'/html/sample_steve_jobs/*.html');
            natsort($htmlFiles);

            $pages = 0;
			foreach ($htmlFiles as $page)
			{
                $pages++;
                $page = file_get_contents($page);
				$page = str_replace('/media/', JURI::root().'media/', $page);

				$query = "INSERT INTO `#__html5fb_pages`" .
					" (`publication_id`, `page_title`, `ordering`, `c_enable_image`, `page_image`, `c_enable_text`, `c_text`)" .
					" VALUES" .
					" (".$publicationId.", ".$db->quote('Page '.$pages).", ".$pageOrder.", 0, NULL, 1, ".$db->quote($page).")";
				$db->setQuery($query);
				$db->execute();

				$pageOrder += 1;
			}
            jFolder::delete(COMPONENT_MEDIA_PATH.'/html/');
		}

		$this->setRedirect('index.php?option='.COMPONENT_OPTION.'&view=publications', JText::_("COM_HTML5FLIPPINGBOOK_BE_SAMPLEDATA_INSTALLED"), 'message');
		return true;
	}
}