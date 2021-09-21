<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookTableCategories extends JTable
{
	//----------------------------------------------------------------------------------------------------
	function __construct(&$db) 
	{
		parent::__construct('#__html5fb_category', 'c_id', $db);
		$this->_trackAssets = true;
	}
	//----------------------------------------------------------------------------------------------------
	public function delete($pk = null)
	{
		$db = $this->_db;
		
		$query = "SELECT COUNT(`c_id`)" .
			" FROM `#__html5fb_publication`" .
			" WHERE `c_category_id` = " . $this->c_id;
		$db->setQuery($query);
		$count = $db->loadResult();
		
		if ($count > 0)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HTML5FLIPPINGBOOK_BE_CATEGORIES_DELETION_FORBIDDEN', $this->c_category, $count), 'error');
			return false;
		}
		
		return parent::delete($pk);
	}

	//----------------------------------------------------------------------------------------------------
	public function store($updateNulls = false)
	{
        $input = JFactory::getApplication()->input;
		$jform = $input->get('jform', array(), 'ARRAY');
        $task = $input->get('task');

		//==================================================
		// Access rules.
		//==================================================

		$this->c_id = $input->getInt('c_id');
		$this->asset_id = $input->getInt('asset_id');

        if($task == 'save2copy') {
            $this->c_id = 0;
        }

		if (!$this->c_id) {
			if ($this->asset_id) {
				$rules = JAccess::getAssetRules((int) $this->asset_id);
				$rules->title = $input->getString('c_title');
				$this->setRules($rules);
			}
		} else {

			$asset = JTable::getInstance('Asset');
			$asset->loadByName( $this->_getAssetName() );
			$this->asset_id = $asset->id;

			$result = JAccess::getAssetRules($this->asset_id);
			$rules_form = $jform['rules'];

			$output2 = array();
			foreach ($rules_form as $key => $actions) {
				$output1 = array();
				foreach ($actions as $i => $value) {
					if($value == '') continue;
					$output1[$i] = $value;
				}
				$output2[$key] = $output1;
			}

			$rules = new JAccessRules( $output2 );
			$rules->mergeCollection($result);

			$asset->rules = (string) $rules;
			$asset->name = $this->_getAssetName();
			$asset->title = $this->_getAssetTitle();
			$asset->parent_id = $this->_getAssetParentId();

			if (!$asset->check() || !$asset->store()) {
				$this->setError($asset->getError());
				return false;
			}
		}

		return parent::store($updateNulls);
	}

	//----------------------------------------------------------------------------------------------------
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return COMPONENT_OPTION.'.category.'.(int) $this->$k;
	}
		//----------------------------------------------------------------------------------------------------
	protected function _getAssetTitle()
	{
		return $this->c_category;
	}
		//----------------------------------------------------------------------------------------------------
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetsTable = JTable::getInstance('Asset', 'JTable');
		$assetsTable->loadByName(COMPONENT_OPTION);

		return (int)$assetsTable->id;
	}

}