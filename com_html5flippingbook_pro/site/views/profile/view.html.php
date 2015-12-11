<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/VarsHelper.php');

class HTML5FlippingBookViewProfile extends JViewLegacy
{
	protected $readList;
	protected $favList;
	protected $readListBS;
	protected $favListBS;
	protected $lastOpen;
	protected $config;
	protected $userFriends;
	protected $state;
	protected $emaillayout;
	protected $sharelayout;
	protected $menuItemParams;
	protected $viewPublicationButtonText;
	protected $readPagination;
	protected $favPagination;

	protected $shelf1 = array();
	protected $shelf2 = array();
	private $row1 = array(
		"read" => array(0,1,2,3),
		"fav"  => array(0,1,2,3)
	);
	private $row2 = array(
		"read" => array(4,5,6,7),
		"fav"  => array(4,5,6,7)
	);

	public function display($tpl = null)
	{
		$document = JFactory::$document;

		$document->addStyleSheet(COMPONENT_CSS_URL . 'html5flippingbook.css');
		$document->addStyleSheet(COMPONENT_CSS_URL . 'font-awesome.min.css');

		$this->emaillayout = new JLayoutFile('email', $basePath = JPATH_COMPONENT .'/layouts');
		$this->sharelayout = new JLayoutFile('share', $basePath = JPATH_COMPONENT .'/layouts');

		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/configuration.php');
		$configurationModel = JModelLegacy::getInstance('Configuration', COMPONENT_MODEL_PREFIX);
		$this->config = $configurationModel->GetConfig();

		$this->readList = $this->get('ReadList');
		$this->favList  = $this->get('FavoriteList');
		$this->lastOpen = $this->get('LastOpenPublication');

		if ($this->config->social_jomsocial_use)
		{
			$this->userFriends = $this->get('UserJSFriends');
		}

		$model = $this->getModel();
		$model->_bookshelf = TRUE;

		$this->readListBS = $this->get('ReadList');
		$this->getBookShelfPubID('read', $this->readListBS);

		$this->favListBS  = $this->get('FavoriteList');
		$this->getBookShelfPubID('fav', $this->favListBS);

		$this->state = $this->get('State');

		// Processing menu item parameters.
		$this->menuItemParams = $this->state->get('parameters.menu');

		if (isset($this->menuItemParams))
		{
			$viewPublicationButtonTextParam = $this->menuItemParams->get('viewPublicationButtonText');
			$this->viewPublicationButtonText = (isset($viewPublicationButtonTextParam) ? $viewPublicationButtonTextParam : null);
		}

		parent::display($tpl);
	}

	protected function getBookShelfPubID($type, $list)
	{
		$k = 0;
		foreach ($list as $i => $item)
		{
			if (in_array($i, $this->row1[$type]) || $i == $this->row1[$type][$k] + 8)
			{
				$this->row1[$type][$k] = $i;
				$this->shelf1[$type][] = $list[$i];
			}

			if (in_array($i, $this->row2[$type]) || $i == $this->row2[$type][$k] + 8)
			{
				$this->row2[$type][$k] = $i;
				$this->shelf2[$type][] = $list[$i];
			}

			if ($k < 3)
			{
				$k++;
			}
			else
			{
				$k = 0;
			}
		}
	}
}