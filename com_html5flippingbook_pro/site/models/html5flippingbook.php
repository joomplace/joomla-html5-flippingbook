<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class HTML5FlippingBookModelHTML5FlippingBook extends JModelList
{
	protected function getListQuery()
	{
		$params = JFactory::getApplication()->getParams();

		$category_id = (int) $params->get('c_category_id');

		$jinput = JFactory::getApplication()->input;
		$archive = $jinput->get('archive', '', 'STRING');
		$user = JFactory::getUser();

		if (!$archive)
		{
			$db = $this->getDbo();

			$query = $db->getQuery(true);

			$query->select('m.*, r.width, r.height, `up`.*');
			$query->from('`#__html5fb_publication` AS `m`');
			$query->join('LEFT', '`#__html5fb_resolutions` AS `r` ON r.id = m.c_resolution_id');
			$query->join('LEFT', '`#__html5fb_users_publ` AS `up` ON `up`.`publ_id` = `m`.`c_id`' . ($user->get('id') ? ' AND `up`.`uid` = ' . $user->get('id') : ''));
			if (isset($category_id)) if ($category_id > 0) $query->where('m.`c_category_id`='.$category_id);
			$query->where('m.`published`=1');
			$query->order($this->orderbyCategory());
			if (!$user->get('id'))
			{
				$query->group('`m`.`c_id`');
			}
		}
		else
		{
			$date = explode('-', $archive);
			$db = $this->getDbo();

			$query = $db->getQuery(true);

			$query->select('m.*, r.width, r.height, `up`.*');
			$query->from('`#__html5fb_publication` AS `m`');
			$query->join('LEFT', '`#__html5fb_resolutions` AS `r` ON m.c_resolution_id=r.id');
			$query->join('LEFT', '`#__html5fb_users_publ` AS `up` ON `up`.`publ_id` = `m`.`c_id`');
			$query->where("MONTH( m.c_created_time )=".$date[1]." AND YEAR( m.c_created_time )=".$date[0]." AND m.`published` != '0' ");
			$query->order($this->orderbyCategory());
		}

		return $query;
	}

	public function getItem($id = null)
	{
		if ( empty($this->_item) )
		{
			$this->_item = false;
			$params = JFactory::getApplication()->getParams();
			$id = (int) $params->get('c_category_id');

			if (empty($id)) return null;

			$table = JTable::getInstance('Categories', 'HTML5FlippingBookTable');

			if ($table->load($id))
			{
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');

				if ( !empty($this->_item->custom_metatags) )
					$this->_item->custom_metatags = unserialize( $this->_item->custom_metatags );

			}
			else if ($error = $table->getError())
			{
				$this->setError($error);

				return null;
			}
		}

		return $this->_item;
	}

    public function orderbyCategory()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/configuration.php');
        $configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
        $config = $configurationModel->GetConfig();

        $orderby = 'm.ordering';
        if(!empty($config->orderby_category)) {
            switch ($config->orderby_category) {
                case 'date' :
                    $orderby = 'm.c_created_time, m.ordering';
                    break;
                case 'rdate' :
                    $orderby = 'm.c_created_time DESC, m.ordering DESC';
                    break;
                case 'alpha' :
                    $orderby = 'm.c_title';
                    break;
                case 'ralpha' :
                    $orderby = 'm.c_title DESC';
                    break;
                case 'order' :
                    $orderby = 'm.ordering';
                    break;
                case 'rorder' :
                    $orderby = 'm.ordering DESC';
                    break;
                case 'author' :
                    $orderby = 'm.c_author, m.ordering';
                    break;
                case 'rauthor' :
                    $orderby = 'm.c_author DESC, m.ordering DESC';
                    break;
                default :
                    $orderby = 'm.ordering';
                    break;
            }
        }

        return $orderby;
    }
}