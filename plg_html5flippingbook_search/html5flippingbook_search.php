<?php
/**
 * JoomBlog component for Joomla 1.6 & 1.7
 * @package   JoomBlog
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * JoomBlog Search plugin
 *
 */
class plgSearchhtml5flippingbook_search extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onContentSearchAreas()
	{
		static $areas = array(
			'html5flippingbook' => 'PLG_SEARCH_HTML5FLIPPINGBOOK_BOOKS',
			'html5flippingbook_page' => 'PLG_SEARCH_HTML5FLIPPINGBOOK_BOOK_PAGES'
		);
		return $areas;
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		$db = JFactory::getDbo();
		$uri = JUri::getInstance();
		$app = JFactory::getApplication();

		$searchText = $text;
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		$limit = $this->params->def('search_limit', 50);

		$text = trim($text);
		if ($text == '')
		{
			return array();
		}

		switch ($phrase)
		{
			case 'exact':
				$text = $db->quote('%' . $db->escape($text, true) . '%', false);
				$wheres2 = array();
				$wheres2[] = '`pg`.`page_title` LIKE ' . $text;
				$wheres2[] = '`pg`.`c_text` LIKE ' . $text;
				$wheres2[] = '`pub`.`c_title` LIKE ' . $text;
				$wheres2[] = '`pub`.`c_pub_descr` LIKE ' . $text;
				$wheres2[] = '`pub`.`c_metakey` LIKE ' . $text;
				$wheres2[] = '`pub`.`c_metadesc` LIKE ' . $text;
				$where = '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word)
				{
					$word = $db->quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = '`pg`.`page_title` LIKE ' . $word;
					$wheres2[] = '`pg`.`c_text` LIKE ' . $word;
					$wheres2[] = '`pub`.`c_title` LIKE ' . $word;
					$wheres2[] = '`pub`.`c_pub_descr` LIKE ' . $word;
					$wheres2[] = '`pub`.`c_metakey` LIKE ' . $word;
					$wheres2[] = '`pub`.`c_metadesc` LIKE ' . $word;
					$wheres[] = implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		switch ($ordering)
		{
			case 'oldest':
				$order = '`pub`.`c_created_time` ASC';
				break;

			case 'alpha':
				$order = '`pub`.`c_title` ASC';
				break;

			case 'category':
				$order = '`cat`.`c_category` ASC, `pub`.`c_title` ASC';
				break;

			case 'newest':
			default:
				$order = '`pub`.`c_created_time` DESC';
				break;
		}

		//Get component menu id
		$query = $db->getQuery(true)
			->select('`id`')
			->from('`#__menu`')
			->where('`link` = "index.php?option=com_html5flippingbook&view=html5flippingbook" AND `type` = "component" AND `published` = 1');
		$db->setQuery($query);
		$Itemid = $db->loadResult();
		$Itemid = ($Itemid ? $Itemid : $app->input->getInt('Itemid'));

		$rows = array();

		// search
		if ($limit > 0)
		{
			$query = $db->getQuery(true);
			$query->clear();

			if (in_array('html5flippingbook', (array)$areas) || is_null($areas)) //Find only in books(publications) title and desc
			{
				$query->select('`pub`.`c_id` AS `pub_id`, `pub`.`c_title` AS `title`, `pub`.`c_pub_descr` AS `text`, `pub`.`c_created_time` AS `created`')
					->select('`pub`.`c_metakey` AS `metakey`, `pub`.`c_metadesc` AS `metadesc`')
					->select('"1" AS `browsernav`, "HTML5 Flipping Book" AS `section`, "1" AS `book`')
					->from('`#__html5fb_publication` AS `pub`')
					->innerJoin('`#__html5fb_category` AS `cat` ON `cat`.`c_id` = `pub`.`c_category_id`')
					->innerJoin('`#__html5fb_pages` AS `pg` ON `pg`.`publication_id` = `pub`.`c_id`')
					->where('(' . $where . ') AND `pub`.`published` = 1')
					->group('`pub_id`')
					->order($order);
				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if (isset($list))
				{
					foreach ($list as $key => $item)
					{
						$list[$key]->href = JRoute::_('index.php?option=com_html5flippingbook&view=publication&id=' . $item->pub_id . '&keyword=' . $text . '&Itemid=' . $Itemid, false, (int)$uri->isSSL());
					}
				}
				$rows[] = $list;
			}

			if (in_array('html5flippingbook_page', (array)$areas) || is_null($areas)) //Find only in books pages
			{
				$query = $db->getQuery(true);
				$query->clear();

                $query->select('`pub`.`c_id` AS `pub_id`, `pg`.`id` AS `page_id`, CONCAT("Book - ", `pub`.`c_title`, ": ", `pg`.`page_title`) AS `title`')
                    ->select('`pg`.`c_text` AS `text`, `pub`.`c_created_time` AS `created`, `tmpl`.`hard_cover`, `pg`.`ordering` AS `ord`')
                    ->select('`pub`.`c_metakey` AS `metakey`, `pub`.`c_metadesc` AS `metadesc`')
                    ->select('"1" AS `browsernav`, "HTML5 Flipping Book" AS `section`, "1" AS `page`')
                    ->from('`#__html5fb_publication` AS `pub`')
                    ->innerJoin('`#__html5fb_category` AS `cat` ON `cat`.`c_id` = `pub`.`c_category_id`')
                    ->innerJoin('`#__html5fb_pages` AS `pg` ON `pg`.`publication_id` = `pub`.`c_id`')
                    ->innerJoin('`#__html5fb_templates` AS `tmpl` ON `tmpl`.`id` = `pub`.`c_template_id`')
                    ->where('(' . $where . ') AND `pub`.`published` = 1')
                    ->order($order);
                $db->setQuery($query, 0, $limit);
                $list = $db->loadObjectList();

				if (isset($list))
				{
					foreach ($list as $key => $item)
					{
						$query = $db->getQuery(true)
							->select('`ordering`')
							->from('`#__html5fb_pages`')
							->where('`publication_id` = ' . $item->pub_id)
                            ->order('`ordering`');
						$db->setQuery($query);
						$pages = $db->loadColumn();

                        $indx = array_search($item->ord, $pages);
                        $pageN = $indx + ($item->hard_cover ? 3 : 1);

						$list[$key]->href = JRoute::_('index.php?option=com_html5flippingbook&view=publication&id=' . $item->pub_id . '&keyword=' . $text . '&Itemid=' . $Itemid . '#page/' . $pageN, false, (int)$uri->isSSL());
					}
				}
				$rows[] = $list;
			}
		}

		$results = array();
		if (count($rows))
		{
			foreach ($rows as $row)
			{
				$new_row = array();
				foreach ($row AS $article)
				{
					$isBook = isset($article->book) ? TRUE : FALSE;
					if (isset($article))
						if ($this->_checkNoHTML($article, $searchText, array('text', 'title', 'metakey', 'metadesc'), $isBook))
						{
							$new_row[] = $article;
						}
				}

				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}

	/**
	 * Checks an object for search terms (after stripping fields of HTML)
	 *
	 * @param   object  $object      The object to check
	 * @param   string  $searchTerm  Search words to check for
	 * @param   array   $fields      List of object variables to check against
	 *
	 * @return  boolean True if searchTerm is in object, false otherwise
	 */
	protected function _checkNoHtml($object, $searchTerm, $fields, $isBook = FALSE)
	{
		$searchRegex = array(
			'#<script[^>]*>.*?</script>#si',
			'#<style[^>]*>.*?</style>#si',
			'#<!.*?(--|]])>#si',
			'#<[^>]*>#i'
		);
		$terms = explode(' ', $searchTerm);

		if (empty($fields))
		{
			return false;
		}

		foreach ($fields as $field)
		{
			if (!isset($object->$field))
			{
				continue;
			}

			$text = SearchHelper::remove_accents($object->$field);

			foreach ($searchRegex as $regex)
			{
				$text = preg_replace($regex, '', $text);
			}

			foreach ($terms as $term)
			{
				$term = SearchHelper::remove_accents($term);

				if (JString::stristr($text, $term) !== false || $isBook)
				{
					return true;
				}
			}
		}

		return false;
	}
}
