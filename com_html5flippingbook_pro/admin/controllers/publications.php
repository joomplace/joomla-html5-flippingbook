<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class HTML5FlippingBookControllerPublications extends JControllerAdmin
{
	//----------------------------------------------------------------------------------------------------
	public function getModel($name = 'Publication', $prefix = COMPONENT_MODEL_PREFIX, $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

    public function delete()
    {
        if( parent::delete() ){
            //delete the corresponding folder for svg-files of pages this publication && preview-folder
            $cid = $this->input->get('cid', array(), 'array');
            $cid = ArrayHelper::toInteger($cid);
            foreach($cid as $id){
                Html5flippingbookImagehandlerHelper::deleteFolder( Html5flippingbookImagehandlerHelper::$path_folder_svg.'/'.(int)$id);
                Html5flippingbookImagehandlerHelper::deleteFolder( Html5flippingbookImagehandlerHelper::$path_folder_preview.'/'.(int)$id);
            }
        }
        return true;
    }

    public function save_order_ajax()
	{
		@ob_clean();
		header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/plain; charset=utf-8');
		
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);
		
		$model = $this->getModel();
		
		$return = $model->saveorder($pks, $order);

		echo ($return ? '1' : '0');
		
		jexit();
	}
	//----------------------------------------------------------------------------------------------------
	public function show_upload()
	{
		$dir                 = $this->input->getString('dir', '');
		$pubId               = $this->input->getInt('pubid');
		$elementId           = $this->input->getString('elementId', '');
		$extensions          = $this->input->getString('extensions', '');
		$linkedElementIdsStr = $this->input->getString('linkedElementIds', '');
		
		JFactory::getApplication()->redirect('index.php?option='.COMPONENT_OPTION.'&tmpl=component&view=upload_file'.
			'&dir='.rawurlencode($dir).
			'&pubid='.$pubId.
			'&extensions='.rawurlencode($extensions).
			'&elementId='.rawurlencode($elementId).
			'&linkedElementIds='.rawurlencode($linkedElementIdsStr),
			'', '');
	}
	//----------------------------------------------------------------------------------------------------
	public function check_file_existence()
	{
		@ob_clean();
		header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/xml; charset=utf-8');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/MethodsForXml.php');
		
		$error = '';
		
		$fileExists = false;
		
		try
		{
			$jinput = JFactory::getApplication()->input;
			
			$dir = $jinput->get('dir', '', 'STRING');
			$fileName = $jinput->get('fileName', '', 'STRING');
			
			jimport('joomla.filesystem.file');
			
			$fileExists = JFile::exists(JPATH_SITE.'/'.$dir.'/'.$fileName);
		}
		catch (Exception $ex)
		{
			$error = $ex->getMessage();
		}
		
		$xml = array();
		
		$xml[] = "<\x3fxml version=\"1.0\" encoding=\"UTF-8\"\x3f>";
		$xml[] = '<root>';
		$xml[] = 	'<error>' . MethodsForXml::XmlEncode($error) . '</error>';
		
		if ($error == '')
		{
			$xml[] = '<fileExists>' . MethodsForXml::XmlEncode($fileExists ? '1' : '0') . '</fileExists>';
		}
		
		$xml[] = '</root>';
		
		print(implode('', $xml));
		
		jexit();
	}
}