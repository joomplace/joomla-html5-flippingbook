<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerPublication extends JControllerForm
{
	//----------------------------------------------------------------------------------------------------
	public function add()
	{
		parent::add();
	}
	//----------------------------------------------------------------------------------------------------
	public function save($key = null, $urlVar = null)
	{
		parent::save($key, $urlVar);
	}
	//----------------------------------------------------------------------------------------------------
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$error = (object) array('msg' => '', 'severity' => 0);
		
		$jinput = JFactory::getApplication()->input;
		
		$publicationIds = $jinput->get('cid', array(), 'ARRAY');
		$targetCategoryId = $jinput->get('targetCategoryId', -1, 'INT');
		$batchAction = $jinput->get('batchAction', 'move', 'STRING');
		
		if (count($publicationIds) == 0) $error->msg = JText::_('JGLOBAL_NO_ITEM_SELECTED');
		if ($error->msg == '') if ($targetCategoryId == -1) $error->msg = JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_BATCH_NO_CATEGORY');
		
		$msg = '';
		$msgType = 'message';
		
		if ($error->msg == '')
		{
			$publicationModel = $this->getModel('Publication', '', array());
			
			switch ($batchAction)
			{
				case 'move':
				{
					try
					{
						$publicationModel->move($publicationIds, $targetCategoryId);
						
						$msg = JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_BATCH_MOVED');
					}
					catch (Exception $ex)
					{
						$error->msg = $ex->getMessage();
						$error->severity = 1;
					}
					
					break;
				}
				case 'copy':
				{
					try
					{
						$publicationModel->copy($publicationIds, $targetCategoryId);
						
						$msg = JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_BATCH_COPIED');
					}
					catch (Exception $ex)
					{
						$error->msg = $ex->getMessage();
						$error->severity = 1;
					}
					
					break;
				}
			}
		}
		
		if ($error->msg != '')
		{
			$msg = JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $error->msg);
			$msgType = ($error->severity == 0 ? 'warning' : 'error');
		}
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=publications' . $this->getRedirectToListAppend(), $msg, $msgType);
	}
}