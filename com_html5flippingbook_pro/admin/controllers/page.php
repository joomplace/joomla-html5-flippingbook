<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerPage extends JControllerForm
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

	public function saveandnew()
	{
		$this->save();
		$this->setRedirect('index.php?option='.COMPONENT_OPTION.'&view=page&layout=edit');
	}
	//----------------------------------------------------------------------------------------------------
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$error = (object) array('msg' => '', 'severity' => 0);
		
		$jinput = JFactory::getApplication()->input;
		
		$pageIds = $jinput->get('cid', array(), 'ARRAY');
		$targetPublicationId = $jinput->get('targetPublicationId', -1, 'INT');
		$batchAction = $jinput->get('batchAction', 'move', 'STRING');
		
		if (count($pageIds) == 0) $error->msg = JText::_('JGLOBAL_NO_ITEM_SELECTED');
		if ($error->msg == '') if ($targetPublicationId == -1) $error->msg = JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_BATCH_NO_PUBLICATION');
		
		$msg = '';
		$msgType = 'message';
		
		if ($error->msg == '')
		{
			$pageModel = $this->getModel('Page', '', array());
			
			switch ($batchAction)
			{
				case 'move':
				{
					try
					{
						$pageModel->move($pageIds, $targetPublicationId);
						$msg = JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_BATCH_MOVED');
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
						$pageModel->copy($pageIds, $targetPublicationId);
						$msg = JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_BATCH_COPIED');
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
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&view=pages' . $this->getRedirectToListAppend(), $msg, $msgType);
	}
}