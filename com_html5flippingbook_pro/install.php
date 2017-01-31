<?php defined('_JEXEC') or die('Restricted access');
/**
* HTML5publicationDeluxe Component
* @package HTML5publicationDeluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

define('COMPONENT_OPTION', 'com_html5flippingbook');
define('COMPONENT_IMAGES_URL', JURI::root().'administrator/components/'.COMPONENT_OPTION.'/assets/images/');
define('COMPONENT_MEDIA_PATH', JPATH_SITE.'/media/'.COMPONENT_OPTION);

class com_html5flippingbookInstallerScript
{
	private $newVersion;
	private $dbName;
	private $dbPrefix;
	//----------------------------------------------------------------------------------------------------
	public function __construct()
	{
		jimport('joomla.filesystem.file');
		preg_match('/<version>([^<]+)/is', file_get_contents(dirname(__FILE__).'/html5flippingbook.xml'), $this->newVersion);

		$this->newVersion = $this->newVersion[1];

		$joomlaConfig = new JConfig();
		
		$this->dbName = $joomlaConfig->db;
		$this->dbPrefix = $joomlaConfig->dbprefix;
	}
	//----------------------------------------------------------------------------------------------------
	public function preflight($type, $parent) 
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		//==================================================
		// Cleaning component's directories.
		//==================================================
		
		$componentAdminDirName = JPATH_ADMINISTRATOR.'/components/'.COMPONENT_OPTION;
		$componentSiteDirName = JPATH_SITE.'/components/'.COMPONENT_OPTION;
		
		// Cleaning component's admin directory.
		
		if (is_dir($componentAdminDirName))
		{
			$adminSubfolderNames = JFolder::folders($componentAdminDirName);
			$adminFileNames = JFolder::files($componentAdminDirName);
			
			foreach ($adminSubfolderNames as $folderName)
			{
				JFolder::delete($componentAdminDirName.'/'.$folderName);
			}
			
			foreach ($adminFileNames as $fileName)
			{
				JFile::delete($componentAdminDirName.'/'.$fileName);
			}
		}
		
		// Cleaning component's site directory.
		
		if (is_dir($componentSiteDirName))
		{
			$siteSubfolderNames = JFolder::folders($componentSiteDirName);
			$siteFileNames = JFolder::files($componentSiteDirName);
			
			foreach ($siteSubfolderNames as $folderName)
			{
				JFolder::delete($componentSiteDirName.'/'.$folderName);
			}
			
			foreach ($siteFileNames as $fileName)
			{
				JFile::delete($componentSiteDirName.'/'.$fileName);
			}
		}

	}
	//----------------------------------------------------------------------------------------------------
	public function postflight($type, $parent) 
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$db = JFactory::getDBO();

		//==================================================
		// Delete old tables (by database).
		//==================================================

		$query = "DROP TABLE IF EXISTS  `#__html5fb_templ_type`;";
		$db->setQuery($query);
		$db->execute();

		//==================================================
		// Config table.
		//==================================================
		
		// Renaming html5fb_version table (previously was called so) and it's field. Or creating new table.
		
		$query = "SELECT `TABLE_SCHEMA`, `TABLE_NAME` FROM INFORMATION_SCHEMA.TABLES" .
			" WHERE `TABLE_SCHEMA` = " . $db->quote($this->dbName) . " AND `TABLE_NAME` = '" . $this->dbPrefix . "html5fb_version" . "'";
		$db->setQuery($query);
		$row = $db->loadObject();
		
		$oldVersionTableExists = isset($row);
		
		if ($oldVersionTableExists)
		{

		}
		else
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_config` (" .
				" `setting_name` varchar(50) NOT NULL DEFAULT ''," .
				" `setting_value` varchar(255) NOT NULL DEFAULT ''," .
				" UNIQUE KEY `setting_name` (`setting_name`)" .
				") ENGINE = MyISAM DEFAULT CHARSET = utf8;";
			$db->setQuery($query);
			$db->execute();
		}
		
		// Updating version.
		
		$query = "SELECT COUNT(*) FROM `#__html5fb_config` WHERE `setting_name` = 'component_version'";
		$db->setQuery($query);
		$result = (int) $db->loadResult();
		
		if ($result > 0)
		{
			$query = "UPDATE `#__html5fb_config` SET `setting_value` = " . $db->quote($this->newVersion) . " WHERE `setting_name` = 'component_version'";
			$db->setQuery($query);
			$db->execute();
		}
		else
		{
			$query = "INSERT INTO `#__html5fb_config` (`setting_name`, `setting_value`) VALUES ('component_version', " . $db->quote($this->newVersion) . ")";
			$db->setQuery($query);
			$db->execute();
		}

		// Adding other rows.
		
		$db->setQuery("SELECT `setting_name` FROM `#__html5fb_config`");
		$results = $db->loadObjectList();
		
		$existingRows = array();
		
		foreach ($results as $result)
		{
			$existingRows[] = $result->setting_name;
		}

		$rows = array(
			(object) array("setting_name" => "social_google_plus_use",			"setting_value" => "0"),
			(object) array("setting_name" => "social_google_plus_size",			"setting_value" => "medium"),
			(object) array("setting_name" => "social_google_plus_annotation",	"setting_value" => "bubble"),
			(object) array("setting_name" => "social_google_plus_language",		"setting_value" => "en-US"),
			
			(object) array("setting_name" => "social_twitter_use",				"setting_value" => "0"),
			(object) array("setting_name" => "social_twitter_size",				"setting_value" => "standart"),
			(object) array("setting_name" => "social_twitter_annotation",		"setting_value" => "horizontal"),
			(object) array("setting_name" => "social_twitter_language",			"setting_value" => "en"),
			
			(object) array("setting_name" => "social_linkedin_use",				"setting_value" => "0"),
			(object) array("setting_name" => "social_linkedin_annotation",		"setting_value" => "right"),
			
			(object) array("setting_name" => "social_facebook_use",				"setting_value" => "0"),
			(object) array("setting_name" => "social_facebook_verb",			"setting_value" => "like"),
			(object) array("setting_name" => "social_facebook_layout",			"setting_value" => "button_count"),
			(object) array("setting_name" => "social_facebook_font",			"setting_value" => "arial"),
			(object) array("setting_name" => "social_jomsocial_use",			"setting_value" => "0"),
			(object) array("setting_name" => "social_email_use",			    "setting_value" => "0"),
			);
		
		foreach ($rows as $row)
		{
			if (!in_array($row->setting_name, $existingRows))
			{
				$query = "INSERT INTO `#__html5fb_config` (`setting_name`, `setting_value`)" .
					" VALUES (" .
					$db->quote($row->setting_name) . ", " .
					$db->quote($row->setting_value) . ")";
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		//==================================================
		// Category table.
		//==================================================
		
		$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_category` (
				  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `c_category` varchar(255) NOT NULL DEFAULT '',
				  `c_instruction` text NOT NULL,
				  `c_metadesc` text NOT NULL,
				  `c_metakey` text NOT NULL,
				  `opengraph_use` tinyint(1) NOT NULL DEFAULT '1',
				  `opengraph_title` varchar(80) DEFAULT NULL,
				  `opengraph_author` varchar(80) DEFAULT NULL,
				  `opengraph_image` varchar(80) DEFAULT NULL,
				  `opengraph_description` text,
				  `custom_metatags` text NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `asset_id` int(10) NOT NULL,
				  PRIMARY KEY (`c_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->execute();
		
		$insertSql = array (
			"DELETE FROM `#__html5fb_category` WHERE `c_id` = 1;",
			"INSERT INTO `#__html5fb_category` (c_id, c_category) VALUES (1, 'Uncategorised');",
		);
		foreach ( $insertSql as $sql)
		{
			$db->setQuery($sql);
			$db->execute();
		}
		
		//==================================================
		// Publication table.
		//==================================================
		
		$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_publication` (
			  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `c_category_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `c_user_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `published` tinyint(1) NOT NULL DEFAULT '1',
			  `c_title` varchar(255) NOT NULL DEFAULT '',
			  `c_author` varchar(255) NOT NULL DEFAULT '',
			  `c_imgsub` tinyint(1) NOT NULL DEFAULT '0',
			  `c_imgsubfolder` varchar(50) DEFAULT '',
			  `c_template_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `navi_settings` tinyint(1) NOT NULL DEFAULT '1',
			  `c_resolution_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `c_show_cdate` tinyint(1) NOT NULL DEFAULT '1',
			  `c_created_time` date NOT NULL DEFAULT '0000-00-00',
			  `c_enable_pdf` tinyint(1) NOT NULL DEFAULT '0',
			  `c_background_pdf` varchar(50) DEFAULT NULL,
			  `c_pub_descr` text,
			  `c_thumb` varchar(50) DEFAULT NULL,
			  `c_popup` int(2) DEFAULT '1',
			  `c_metadesc` text NOT NULL,
			  `c_metakey` text NOT NULL,
			  `hide_shadow` tinyint(1) NOT NULL DEFAULT '1',
			  `c_enable_fullscreen` tinyint(1) NOT NULL DEFAULT '1',
			  `fullscreen_mode` tinyint(3) unsigned NOT NULL DEFAULT '0',
			  `right_to_left` tinyint(1) NOT NULL DEFAULT '0',
			  `opengraph_use` tinyint(1) NOT NULL DEFAULT '1',
			  `opengraph_title` varchar(80) DEFAULT NULL,
			  `opengraph_author` varchar(80) DEFAULT NULL,
			  `opengraph_image` varchar(80) DEFAULT NULL,
			  `opengraph_description` text,
			  `c_enable_frontpage` tinyint(1) NOT NULL DEFAULT '0',
			  `c_author_image` varchar(50) DEFAULT NULL,
			  `c_author_email` varchar(50) DEFAULT NULL,
			  `c_author_description` text,
			  `c_author_logo` varchar(50) DEFAULT NULL,
			  `custom_metatags` text NOT NULL,
			  `convert` TINYINT(1) NOT NULL DEFAULT '0',
			  `convert_formats` VARCHAR(255) NULL DEFAULT NULL,
			  `cloudconvert` TINYINT(1) NOT NULL DEFAULT '0',
			  `cloudconvert_api` VARCHAR(150) NULL DEFAULT NULL,
			  `cloudconvert_formats` VARCHAR(255) NULL DEFAULT NULL,
			  PRIMARY KEY (`c_id`),
			  KEY `c_user_id` (`c_user_id`),
			  KEY `c_template_id` (`c_template_id`),
			  KEY `c_resolution_id` (`c_resolution_id`),
			  KEY `c_author` (`c_author`),
			  KEY `c_category_id` (`c_category_id`),
			  FULLTEXT KEY `c_metakey` (`c_metakey`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->execute();
		
		// Adding columns.
		
		$db->setQuery("SHOW COLUMNS FROM `#__html5fb_publication`");
		$results = $db->loadObjectList();
		
		$existingColumns = array();
		
		foreach ($results as $result)
		{
			$existingColumns[] = $result->Field;
		}
		
		$columns = array(
            (object) array("name" => "c_id",				    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT"),
            (object) array("name" => "c_category_id",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_category_id` int(11) unsigned NOT NULL DEFAULT '0'"),
            (object) array("name" => "asset_id",			    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `asset_id` int(11) unsigned NOT NULL DEFAULT '0'"),
            (object) array("name" => "c_user_id",			    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_user_id` int(11) unsigned NOT NULL DEFAULT '0'"),
            (object) array("name" => "ordering",			    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `ordering` int(11) NOT NULL DEFAULT '0'"),
            (object) array("name" => "published",			    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `published` tinyint(1) NOT NULL DEFAULT '1'"),
            (object) array("name" => "c_title",				    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_title` varchar(255) NOT NULL DEFAULT ''"),
            (object) array("name" => "c_author",			    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_author` varchar(255) NOT NULL DEFAULT ''"),
            (object) array("name" => "c_imgsub",			    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_imgsub` tinyint(1) NOT NULL DEFAULT '0'"),
            (object) array("name" => "c_imgsubfolder",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_imgsubfolder` varchar(50) DEFAULT ''"),
            (object) array("name" => "c_template_id",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_template_id` int(11) unsigned NOT NULL DEFAULT '0'"),
			(object) array("name" => "navi_settings",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `navi_settings` TINYINT(1) NOT NULL DEFAULT '1' AFTER `c_template_id`;"),
            (object) array("name" => "c_resolution_id",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_resolution_id` int(11) unsigned NOT NULL DEFAULT '0'"),
            (object) array("name" => "c_show_cdate",	        "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_show_cdate` tinyint(1) NOT NULL DEFAULT '1'"),
            (object) array("name" => "c_created_time",	   	    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_created_time` date NOT NULL DEFAULT '0000-00-00'"),
            (object) array("name" => "c_enable_pdf",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_enable_pdf` tinyint(1) NOT NULL DEFAULT '0'"),
            (object) array("name" => "c_background_pdf",		"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_background_pdf` varchar(50) DEFAULT NULL"),
            (object) array("name" => "c_enable_frontpage",		"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_enable_frontpage` tinyint(1) NOT NULL DEFAULT '0'"),
            (object) array("name" => "c_author_image",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_author_image` varchar(50) DEFAULT NULL"),
            (object) array("name" => "c_author_email",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_author_email` varchar(50) DEFAULT NULL"),
            (object) array("name" => "c_author_description",	"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_author_description` text"),
            (object) array("name" => "c_author_logo",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_author_logo` varchar(50) DEFAULT NULL"),
            (object) array("name" => "c_pub_descr",				"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_pub_descr` text"),
            (object) array("name" => "c_thumb",				    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_thumb` varchar(50) DEFAULT NULL"),
            (object) array("name" => "c_popup",				    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_popup` int(2) DEFAULT '1'"),
            (object) array("name" => "c_metadesc",				"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_metadesc` text NOT NULL"),
            (object) array("name" => "c_metakey",				"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_metakey` text NOT NULL"),
            (object) array("name" => "hide_shadow",				"sql" => "ALTER TABLE `#__html5fb_publication` ADD `hide_shadow` tinyint(1) NOT NULL DEFAULT '1'"),
            (object) array("name" => "c_enable_fullscreen",		"sql" => "ALTER TABLE `#__html5fb_publication` ADD `c_enable_fullscreen` tinyint(1) NOT NULL DEFAULT '1'"),
            (object) array("name" => "fullscreen_mode",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `fullscreen_mode` tinyint(3) unsigned NOT NULL DEFAULT '0'"),
            (object) array("name" => "right_to_left",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `right_to_left` tinyint(1) NOT NULL DEFAULT '0'"),
            (object) array("name" => "opengraph_use",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `opengraph_use` tinyint(1) NOT NULL DEFAULT '1'"),
            (object) array("name" => "opengraph_title",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `opengraph_title` varchar(80) DEFAULT NULL"),
            (object) array("name" => "opengraph_author",		"sql" => "ALTER TABLE `#__html5fb_publication` ADD `opengraph_author` varchar(80) DEFAULT NULL"),
            (object) array("name" => "opengraph_image",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `opengraph_image` varchar(80) DEFAULT NULL"),
            (object) array("name" => "opengraph_description",	"sql" => "ALTER TABLE `#__html5fb_publication` ADD `opengraph_description` text"),
            (object) array("name" => "custom_metatags",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `custom_metatags` text"),
			(object) array("name" => "convert",			        "sql" => "ALTER TABLE `#__html5fb_publication` ADD `convert` TINYINT(1) NOT NULL DEFAULT '0'"),
			(object) array("name" => "convert_formats",		    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `convert_formats` VARCHAR(255) NULL DEFAULT NULL"),
			(object) array("name" => "cloudconvert",			"sql" => "ALTER TABLE `#__html5fb_publication` ADD `cloudconvert` TINYINT(1) NOT NULL DEFAULT '0'"),
			(object) array("name" => "cloudconvert_api",	    "sql" => "ALTER TABLE `#__html5fb_publication` ADD `cloudconvert_api` VARCHAR(150) NULL DEFAULT NULL"),
			(object) array("name" => "cloudconvert_formats",	"sql" => "ALTER TABLE `#__html5fb_publication` ADD `cloudconvert_formats` VARCHAR(255) NULL DEFAULT NULL"),

        );

		foreach ($columns as $column)
		{
			if (!in_array($column->name, $existingColumns))
			{
				$query = $column->sql;
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		// Removing columns.
		
		$columns = array(
			(object) array("name" => "c_language",			"sql" => "ALTER TABLE `#__html5fb_publication` DROP COLUMN `c_language`"),
			(object) array("name" => "hide_shadow",			"sql" => "ALTER TABLE `#__html5fb_publication` DROP COLUMN `hide_shadow`"),
			);
		
		foreach ($columns as $column)
		{
			if (in_array($column->name, $existingColumns))
			{
				$query = $column->sql;
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		// Fixing columns.
		
		$columns = array(
			(object) array("name" => "published",			"sql" => "ALTER TABLE `#__html5fb_publication` CHANGE `published` `published` tinyint(1) NOT NULL DEFAULT '1' AFTER `ordering`"),
			);
		
		foreach ($columns as $column)
		{
			$query = $column->sql;
			$db->setQuery($query);
			$db->execute();
		}
		
		//==================================================
		// Pages table.
		//==================================================
		
		$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_pages` (" .
			" `id` int(11) NOT NULL auto_increment," .
			" `publication_id` int(11) unsigned NOT NULL DEFAULT '0'," .
			" `page_title` varchar(255) DEFAULT NULL," .
			" `ordering` int(11) DEFAULT '0'," .
			" `c_enable_image` tinyint(1) NOT NULL DEFAULT '0'," .
			" `page_image` varchar(255) DEFAULT NULL," .
			" `c_enable_text` tinyint(1) NOT NULL DEFAULT '0'," .
			" `c_text` text," .
			" PRIMARY KEY (`id`)," .
			" KEY `publication_id` (`publication_id`)" .
			") ENGINE = MyISAM DEFAULT CHARSET = utf8;";
		$db->setQuery($query);
		$db->execute();
		
		// Adding columns.
		
		$db->setQuery("SHOW COLUMNS FROM `#__html5fb_pages`");
		$results = $db->loadObjectList();
		
		$existingColumns = array();
		
		foreach ($results as $result)
		{
			$existingColumns[] = $result->Field;
		}
		
		$columns = array(
			(object) array("name" => "is_contents",			    "sql" => "ALTER TABLE `#__html5fb_pages` ADD `is_contents` TINYINT( 1 ) NOT NULL DEFAULT '0'"),
			);
		
		foreach ($columns as $column)
		{
			if (!in_array($column->name, $existingColumns))
			{
				$query = $column->sql;
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		// Removing columns.
		
		$columns = array(
			(object) array("name" => "page_sound",			"sql" => "ALTER TABLE `#__html5fb_pages` DROP COLUMN `page_sound`"),
			);
		
		foreach ($columns as $column)
		{
			if (in_array($column->name, $existingColumns))
			{
				$query = $column->sql;
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		// Fixing columns.
		
		$columns = array(
			(object) array("name" => "page_title",		"sql" => "ALTER TABLE `#__html5fb_pages` CHANGE `page_title` `page_title` varchar(255) DEFAULT NULL AFTER `publication_id`"),
			);
		
		foreach ($columns as $column)
		{
			$query = $column->sql;
			$db->setQuery($query);
			$db->execute();
		}
		
		//==================================================
		// Resolutions table.
		//==================================================
		
		$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_resolutions` (" .
			" `id` int(11) unsigned NOT NULL auto_increment," .
			" `resolution_name` varchar(250) NOT NULL," .
			" `height` int(11) unsigned NOT NULL DEFAULT '800'," .
			" `width` int(11) unsigned NOT NULL DEFAULT '600'," .
			" PRIMARY KEY (`id`)" .
			") ENGINE = MyISAM DEFAULT CHARSET = utf8;";
		$db->setQuery($query);
		$db->execute();
		
		// Adding rows.
		
		$resolutions = array(
			(object) array("name" => "Magazine (460 x 600)", "width" => 462, "height" => 600),
			(object) array("name" => "HardBook (480 x 600)", "width" => 480, "height" => 600),
			);
		
		for ($i = 0; $i < count($resolutions); $i++)
		{
			$resolution = $resolutions[$i];
			
			$query = "SELECT `id` FROM `#__html5fb_resolutions` WHERE width = " . $resolution->width . " AND height = " . $resolution->height;
			$db->setQuery($query);
			$id = $db->loadResult();
			
			if (!$id)
			{
				$query = "INSERT INTO `#__html5fb_resolutions` (resolution_name, width, height) VALUES (" .
                $db->quote($resolution->name) . ", " . $resolution->width . ", " . $resolution->height . ")";
				$db->setQuery($query);
				$db->execute();
			}
			else
			{
				$query = "UPDATE `#__html5fb_resolutions`
						  SET `resolution_name` = " . $db->quote($resolution->name) . "
						  WHERE `id` = " . $id;
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		//==================================================
		// Templates table.
		//==================================================
		
		$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_templates` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `template_name` varchar(255) NOT NULL DEFAULT '',
				  `hard_cover` tinyint(1) NOT NULL DEFAULT '0',
				  `doublepages` BOOLEAN NOT NULL DEFAULT '0',
				  `page_background_color` varchar(10) NOT NULL DEFAULT '',
				  `background_color` varchar(10) NOT NULL DEFAULT '',
				  `text_color` varchar(10) NOT NULL DEFAULT '',
				  `fontfamily` int(11) NOT NULL DEFAULT '0',
				  `fontsize` varchar(10) NOT NULL DEFAULT '14px',
				  `display_slider` tinyint(1) NOT NULL DEFAULT '1',
				  `display_pagebox` tinyint(1) NOT NULL DEFAULT '1',
				  `display_title` tinyint(1) NOT NULL DEFAULT '1',
				  `display_topicons` tinyint(1) NOT NULL DEFAULT '1',
				  `display_nextprev` tinyint(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->execute();
		
		// Synchronizing columns.
		$columns = array(
			(object) array("name" => "p_margin",		"sql" => "ALTER TABLE `#__html5fb_templates` ADD `p_margin` VARCHAR(10) NOT NULL DEFAULT ''"),
			(object) array("name" => "hard_cover",		"sql" => "ALTER TABLE `#__html5fb_templates` ADD `hard_cover` TINYINT(1) NOT NULL DEFAULT '0'"),
			(object) array("name" => "p_lineheight",	"sql" => "ALTER TABLE `#__html5fb_templates` ADD `p_lineheight` VARCHAR(10) NOT NULL DEFAULT ''"),
			(object) array("name" => "slider_thumbs",	"sql" => "ALTER TABLE `#__html5fb_templates` ADD `slider_thumbs` TINYINT(1) NOT NULL DEFAULT 1"),
			(object) array("name" => "show_shadow",	    "sql" => "ALTER TABLE `#__html5fb_templates` ADD `show_shadow` TINYINT(1) NOT NULL DEFAULT 1"),
			);
		
		$existingColumnNames = array();
		
		$query = "SHOW COLUMNS FROM #__html5fb_templates";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row)
		{
			$existingColumnNames[] = $row->Field;
		}
		
		foreach ($columns as $column)
		{
			if (!in_array($column->name, $existingColumnNames))
			{
				$db->setQuery($column->sql);
				$db->execute();
			}
		}

		// Removing columns.

		$columns = array(
			(object) array("name" => "template_type",		"sql" => "ALTER TABLE `#__html5fb_templates` DROP COLUMN `template_type`"),
		);

		foreach ($columns as $column)
		{
			if (in_array($column->name, $existingColumnNames))
			{
				$db->setQuery( $column->sql );
				$db->execute();
			}
		}
		
		// Creating templates.
		
		$templates = array(
			(object) array("name" => "Magazine"),
			(object) array("name" => "Hardbook"),
		);
		
		for ($i = 0; $i < count($templates); $i++)
		{
			$template = $templates[$i];
			
			$db->setQuery("SELECT COUNT(*) FROM `#__html5fb_templates` WHERE template_name = " . $db->quote($template->name));
			$count = $db->loadResult();
			
			if ($count == 0)
			{
				$db->setQuery("INSERT INTO `#__html5fb_templates` (`template_name`, `hard_cover`, `page_background_color`, `background_color`, `text_color`, `fontfamily`, `fontsize`, `display_slider`, `display_pagebox`, `display_title`, `display_topicons`, `display_nextprev`, `p_margin`, `p_lineheight`, `slider_thumbs`, `show_shadow`)
							   VALUES (".$db->quote($template->name).", ".($template->name == 'Hardbook' ? 1 : 0).", '', '', '#000000', ".($template->name == 'Hardbook' ? 8 : 0).", '14px', 1, 1, 1, 1, ".($template->name == 'Hardbook' ? 0 : 1).", '0', '15px', 1, 1);");
				$db->execute();
			}
		}

		//=============================================
		// Users publications table
		//=============================================
		$query = "CREATE TABLE IF NOT EXISTS `#__html5fb_users_publ` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `uid` int(11) NOT NULL,
				  `publ_id` int(11) NOT NULL,
				  `lastopen` int(15) NOT NULL DEFAULT '0',
				  `page` int(11) NOT NULL DEFAULT '0',
				  `read_list` tinyint(1) NOT NULL DEFAULT '0',
				  `fav_list` tinyint(1) NOT NULL DEFAULT '0',
				  `read` tinyint(1) NOT NULL DEFAULT '0',
				  `spend_time` int(15) NOT NULL,
				  `settings` text NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `publ_id` (`publ_id`),
				  KEY `uid` (`uid`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->execute();

		//==================================================
		// Fixing / adding permissions.n
		//==================================================
		
		$query = "SELECT `rules`" .
			" FROM `#__assets`" .
			" WHERE `name` = " . $db->quote(COMPONENT_OPTION);
		$db->setQuery($query);
		$componentRulesJson = $db->loadResult();
		
		if (!empty($componentRulesJson))
		{
			$componentRules = json_decode($componentRulesJson);
			
			$coreView = (isset($componentRules->{'core.view'}) ? $componentRules->{'core.view'} : null);
			$corePreview = (isset($componentRules->{'core.preview'}) ? $componentRules->{'core.preview'} : null);
			$coreDownload = (isset($componentRules->{'core.download'}) ? $componentRules->{'core.download'} : null);
			
			$rules = (object) array(
				'core.view' => (isset($coreView) ? $coreView : (object) array('1' => 1)),
				'core.preview' => (isset($corePreview) ? $corePreview : (object) array('1' => 1)),
				'core.download' => (isset($coreDownload) ? $coreDownload : (object) array('1' => 1)),
			);
			
			$rulesJson = json_encode($rules);
			
			$query = "UPDATE `#__assets`" .
				" SET `rules` = " . $db->quote($rulesJson) .
				" WHERE `name` = " . $db->quote(COMPONENT_OPTION);
			$db->setQuery($query);
			$db->execute();
		}

		if (!JFile::exists(JPATH_SITE . '/components/com_html5flippingbook/libs/class.html2fb2.inc'))
		{
			jimport('joomla.installer.helper');
			jimport('joomla.filesystem.archive');

			$archiveName = "html5fb_libs.zip";

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
			$sourceUrl = "https://www.joomplace.com/media/" . $archiveName;
			$downloadResult = JInstallerHelper::downloadPackage($sourceUrl);

			if (!$downloadResult)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_HTML5FLIPPINGBOOK_BE_SAMPLEDATA_INVALID_URL'), 'error');
				return false;
			}

			// Extracting archive.
			$joomlaConfig = JFactory::getConfig();
			$archiveFileFullName = $joomlaConfig->get("tmp_path") . "/" . $archiveName;

			$package = JArchive::extract($archiveFileFullName, JPATH_SITE . '/components/com_html5flippingbook/libs');

			if (!$package)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('CANT_EXTRACT_ARCHIVE'), 'error');
				return false;
			}
			else
			{
				JFile::delete($archiveFileFullName);
			}
		}
	}
	//----------------------------------------------------------------------------------------------------
	public function install($parent)
	{
		?>
		<div class="well">
			<style type="text/css">
				.nav-pills li:hover a { background-color: #0088CC; color: #fff; }
			</style>

			<div style="clear:both; font-size:1.8em; color:#55AA55;"><?php echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SUCCESS"); ?></div>
			<div style="margin:6px 0 0 0; clear:both; font-size:1.0em; color:#000000;"><?php echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_VERSION") . '&nbsp;' .$this->newVersion; ?></div>

			<br>
			
			<div style="background-color:#ffffff; text-align:left; font-size:16px; font-weight:400; line-height:18px;border-radius:5px; padding:7px;">
				<img style="" src="<?php echo COMPONENT_IMAGES_URL."tick.png"; ?>">
				<spanstyle="margin:0 0 0 8px; font-weight:bold;"><?php echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_HELPFULLLINKS"); ?></span>
			</div>

			<div style="font-size:1.2em;padding-left: 20px; padding-top: 10px;">
				<ul class="nav nav-pills nav-stacked">
					<li><a href="index.php?option=<?php echo COMPONENT_OPTION; ?>&view=sample_data"><?php
							echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAMPLEDATA"); ?></a></li>
					<li><a href="index.php?option=<?php echo COMPONENT_OPTION; ?>&view=publications"><?php
							echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_MANAGEPUBLICATIONS"); ?></a></li>
					<li><a href="index.php?option=<?php echo COMPONENT_OPTION; ?>&view=pages"><?php
							echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_MANAGEPAGES"); ?></a></li>
					<li><a href="index.php?option=<?php echo COMPONENT_OPTION; ?>&view=help"><?php
							echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_HELP"); ?></a></li>
					<li><a href="http://www.joomplace.com/forum/joomla-components/joomlahtml5fbazine.html" target="_blank"><?php //TODO: !! exists link
							echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_VISITSUPPORTFORUM"); ?></a></li>
					<li><a href="http://www.joomplace.com/helpdesk/ticket_submit.php" target="_blank"><?php
							echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SUBMITREQUEST"); ?></a></li>
				</ul>
			</div>

			<div style="background-color:#ffffff; text-align:left; font-size:16px; font-weight:400; line-height:18px;border-radius:5px; padding:7px;">
				<img src="<?php echo COMPONENT_IMAGES_URL."tick.png"; ?>">
				<div style="display: inline-block; margin:0 0 0 8px; font-weight:bold;"><?php echo JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAYTHANKYOU"); ?></div>
			</div>

			<div style="font-size:1.0em; padding: 10px">
				<?php
				echo '<b>' . JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAYTHANKYOU") . '</b>' . ' ' .
					JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAYTHANKYOU_PART2") .	' ' .
					'<b>' . JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAYTHANKYOU_PART3") . '</b>' . ' ' .
					JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAYTHANKYOU_PART4") . ' ' .
					'<a href="http://extensions.joomla.org" target="_blank">http://extensions.joomla.org</a>' . ' ' .
					JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_SAYTHANKYOU_PART5") .
					'<br/>' .
					'<a href="http://extensions.joomla.org/extensions/directory-a-documentation/portfolio/11307" target="_blank">' .
					'<img src="' . COMPONENT_IMAGES_URL.'rate_us.png' . '"' .
					' title="' . JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_RATE_US") . '"' .
					' style="margin:10px 0 0 0;" />' .
					'</a>';
				?>
			</div>

		</div>

		<?php
	}
	//----------------------------------------------------------------------------------------------------
	public function update($parent) 
	{
		echo '<div class="well">
			<div style="background-color: #0088CC;padding:5px 15px;border-radius: 5px;color: #FFFFFF;cursor: default;font: bold 16px/1.4em helvetica;">' . JText::_("COM_HTML5FLIPPINGBOOK_UPDATE_SUCCESS") . '</div>
			<div style="padding-top: 10px; margin: 0px;text-align:center;" class="lead">' . JText::_("COM_HTML5FLIPPINGBOOK_INSTALL_VERSION") . '&nbsp;' . $this->newVersion . '</div>
			</div>';

		echo '<div style="clear:both;"></div>';
	}
	//----------------------------------------------------------------------------------------------------
	public function uninstall($parent) 
	{
	}
	//----------------------------------------------------------------------------------------------------
	// Removes directory with all subfolders and files. Method is recursive.
	// Parameters:
	// $directoryFullName - string - full name of the directory.
	//
	private function DeleteEntireDirectory($directoryFullName)
	{
		if (is_dir($directoryFullName))
		{
			$directoryHandle = opendir($directoryFullName);
			
			while (($fileName = readdir($directoryHandle)) !== false)
			{
				if ($fileName != "." && $fileName != "..")
				{
					$fileFullName = $directoryFullName."/".$fileName;
					
					if (is_dir($fileFullName))
					{
						$this->DeleteEntireDirectory($fileFullName); 
					}
					else
					{
						@unlink($fileFullName);
					}
				}
			}
			
			closedir($directoryHandle);
			
			@rmdir($directoryFullName);
		}
	}
	//----------------------------------------------------------------------------------------------------
	private function CheckIfVersionIsLessThan($version, $value)
	{
		$v1Array = $this->ConvertVersionToArray($version);
		$v2Array = $this->ConvertVersionToArray($value);
		
		$v1Len = count($v1Array);
		$v2Len = count($v2Array);
		
		if ($v1Len != $v2Len)
		{
			$maxLen = ($v1Len > $v2Len ? $v1Len : $v2Len);
			
			while (count($v1Array) < $maxLen)
			{
				$v1Array[] = 0;
			}
			
			while (count($v2Array) < $maxLen)
			{
				$v2Array[] = 0;
			}
		}
		else
		{
			$maxLen = $v1Len;
		}
		
		$result = false;
		
		for ($i = 0; $i < $maxLen; $i++)
		{
			$valueOne = $v1Array[$i];
			$valueTwo = $v2Array[$i];
			
			if ($valueOne < $valueTwo)
			{
				$result = true;
				break;
			}
			else if ($valueOne > $valueTwo)
			{
				$result = false;
				break;
			}
		}
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	private function ConvertVersionToArray($str)
	{
		$str = str_replace(' (build ', '.', $str);
		$str = str_replace(')', '', $str);
		
		$array = explode('.', $str);
		
		foreach ($array as $i => $element)
		{
			$array[$i] = (int) $element;
		}
		
		return $array;
	}
}
