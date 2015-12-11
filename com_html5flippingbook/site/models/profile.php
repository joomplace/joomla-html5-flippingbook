<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class HTML5FlippingBookModelProfile extends JModelList
{
	protected $user_id;
	protected $_read_list = FALSE;
	protected $_fav_list  = FALSE;
	protected $_lastOpen  = FALSE;
	public $_startF        = 0;
	public $_startR        = 0;
	public $_limitF        = 3;
	public $_limitR        = 3;
	public $_bookshelf    = FALSE;

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->user_id = JFactory::getUser()->get('id');
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// List state information
		$this->setState('list.reading.limit', $this->_limitR);
		$this->setState('list.reading.start', $this->_startR);

		$this->setState('list.favorite.limit', $this->_limitF);
		$this->setState('list.favorite.start', $this->_startF);
	}

	public function getReadList()
	{
		$this->_read_list = TRUE;
		$this->_fav_list  = FALSE;
		$this->_lastOpen  = FALSE;

		$query = $this->getListQuery();
		if ($this->_bookshelf)
		{
			$this->_db->setQuery($query);
//			$this->_bookshelf = FALSE;
		}
		else
		{
			$this->_db->setQuery($query, (int)$this->getState('list.reading.start'), (int)$this->getState('list.reading.limit'));
		}

		return $this->_db->loadObjectList();
	}

	public function getFavoriteList()
	{
		$this->_fav_list  = TRUE;
		$this->_read_list = FALSE;
		$this->_lastOpen  = FALSE;

		$query = $this->getListQuery();
		if ($this->_bookshelf)
		{
			$this->_db->setQuery($query);
			$this->_bookshelf = FALSE;
		}
		else
		{
			$this->_db->setQuery($query, (int)$this->getState('list.favorite.start'), (int)$this->getState('list.favorite.limit'));
		}

		return $this->_db->loadObjectList();
	}

	protected function getListQuery()
	{
		$db = $this->_db;

		$query = $db->getQuery(true)
			->clear()
			->select('`pub`.*')
			->select('`upub`.`uid`, `upub`.`lastopen`, `upub`.`page`, `upub`.`read_list`, `upub`.`fav_list`, `upub`.`read`, `upub`.`spend_time`')
			->select('`res`.`width`, `res`.`height`')
			->from('`#__html5fb_publication` AS `pub`')
			->innerJoin('`#__html5fb_users_publ` AS `upub` ON `upub`.`publ_id` = `pub`.`c_id`')
			->leftJoin('`#__html5fb_resolutions` AS `res` ON `pub`.`c_resolution_id` = `res`.`id`')
			->where('`upub`.`uid` = ' . (int)$this->user_id . ($this->_read_list ? ' AND `upub`.`read_list` = 1' : ($this->_fav_list ? ' AND `upub`.`fav_list` = 1' : '')));

		if ($this->_lastOpen)
		{
			$query->where('`upub`.`lastopen` != 0');
			$query->order('`upub`.`lastopen` DESC');
		}

		return $query;
	}

	protected function getTotalRows($list)
	{
		if ($list == 'reading')
		{
			$this->_read_list = TRUE;
			$this->_fav_list  = FALSE;
			$this->_lastOpen  = FALSE;
		}
		elseif ($list == 'favorite')
		{
			$this->_fav_list  = TRUE;
			$this->_read_list = FALSE;
			$this->_lastOpen  = FALSE;
		}

		$query = $this->getListQuery();
		$this->_db->setQuery($query);

		return count($this->_db->loadObjectList());
	}

	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @param   string   Tab name where publication should display
	 * @return  integer  The starting number of items available in the data set.
	 *
	 * @since   12.2
	 */
	public function getPageStart($list)
	{
		$start = $this->getState('list.' . $list . '.start');
		$limit = $this->getState('list.' . $list . '.limit');
		$total = $this->getTotalRows($list);

		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		return $start;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   12.2
	 */
	public function getReadingPagination()
	{
		// Create the pagination object.
		$limit = (int) $this->getState('list.reading.limit') - (int) $this->getState('list.reading.links');
		$page = new JPagination($this->getTotalRows('reading'), $this->getPageStart('reading'), $limit, 'reading');

		return $page;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   12.2
	 */
	public function getFavoritePagination()
	{
		// Create the pagination object.
		$limit = (int) $this->getState('list.favorite.limit') - (int) $this->getState('list.favorite.links');
		$page = new JPagination($this->getTotalRows('favorite'), $this->getPageStart('favorite'), $limit, 'favorite');

		return $page;
	}

	public function getLastOpenPublication()
	{
		$this->_fav_list    = FALSE;
		$this->_read_list   = FALSE;
		$this->_lastOpen    = TRUE;

		$query = $this->getListQuery();
		$this->_db->setQuery($query, 0, 1);

		return $this->_db->loadObject();
	}

	public function getUserJSFriends()
	{
		include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
		$model = CFactory::getModel('Friends');

		return $model->getFriends($this->user_id, 'name', FALSE);
	}
}