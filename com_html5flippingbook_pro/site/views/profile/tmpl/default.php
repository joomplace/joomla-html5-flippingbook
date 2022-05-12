<?php
/**
 * HTML5FlippingBook Component
 * @package HTML5FlippingBook
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.html5-modal');
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.tooltip');

$user = JFactory::getUser();
$doc = JFactory::getDocument();

$app = JFactory::$application;
$jinput = $app->input;

require_once (COMPONENT_LIBS_PATH . 'Mobile_Detect.php');
$detectMobile = new Mobile_Detect_HTML5FB();

// Exclude tablets.
$isMobile = FALSE;
$isTablet = FALSE;
if ($detectMobile->isMobile() && !$detectMobile->isTablet())
{
	$isMobile = TRUE;
}
elseif ($detectMobile->isTablet())
{
	$isTablet = TRUE;
}

$doc->addScriptDeclaration('ht5popupWindow = function (a, b, c, d, f) { window.open(a, b, "height=" + d + ",width=" + c + ",top=" + (screen.height - d) / 2 + ",left=" + (screen.width - c) / 2 + ",scrollbars=" + f + ",resizable").window.focus() };');
$doc->addScript(COMPONENT_JS_URL .'jquery.cookie.min.js');

JText::script('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_UNREAD_PUBL');
JText::script('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_READ_PUBL');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_READ_TIP');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READ');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_USER');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_ACTION');
JText::script('COM_HTML5FLIPPINGBOOK_FE_MAILTO_ERROR');
JText::script('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_PUB_ERROR');
JText::script('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FRIEND_ERROR');

$downloadOptionAccess = $user->authorise('core.download', COMPONENT_OPTION);
?>
	<script type="text/javascript">
		var user = <?php echo $user->get('id'); ?>;
		var liveSite = '<?php echo JUri::root();?>';
	</script>
	<script type="text/javascript" src="<?php echo COMPONENT_JS_URL;?>profile.min.js"></script>
	<script type="text/javascript" src="<?php echo COMPONENT_JS_URL;?>jquery.actual.min.js"></script>
	<div id="html5flippingbook" class="html5fb-profile">
		<div class="current-book">
			<fieldset id="last-open-publ">
				<legend><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_LAST_PUBLICATION');?></legend>

				<?php if (!is_null($this->lastOpen)):?>
					<?php
					$downloadOptionAccessGranted = $user->authorise('core.download', COMPONENT_OPTION.'.publication.'.$this->lastOpen->c_id);
					$data = HTML5FlippingBookFrontHelper::htmlPublHelper($this->lastOpen, $isMobile, $isTablet);
					if ($downloadOptionAccess && $downloadOptionAccessGranted)
					{
						$downloadList = HTML5FlippingBookFrontHelper::generateDownloadOptions($this->lastOpen->c_id);
					}
					?>

					<ul class="html5fb-list">
						<li class="html5fb-list-item">
							<div class="html5fb-pict">
								<?php echo $data->viewPublicationLinkWithTitle; ?>
								<img class="html5fb-img" src="<?php echo $data->thumbnailUrl;?>" alt="<?php echo htmlspecialchars($this->lastOpen->c_title); ?>" />
								</a>
							</div>

							<div class="html5fb-descr">
								<h3 class="html5fb-name">
									<?php echo str_replace("thumbnail", "", $data->viewPublicationLinkWithTitle) . htmlspecialchars($this->lastOpen->c_title) . '</a>'; ?><br/>
									<small><?php echo $this->lastOpen->c_author;?></small>
								</h3>

								<?php if (strlen($this->lastOpen->c_pub_descr) != ""):?>
									<p>
										<?php
										if (strlen($this->lastOpen->c_pub_descr) <= 990)
										{
											echo $this->lastOpen->c_pub_descr;
										}
										else
										{
											echo substr($this->lastOpen->c_pub_descr, 0, strpos($this->lastOpen->c_pub_descr, ' ', 990)).' ...';
										}
										?>
									</p>
								<?php endif;?>

								<div class="html5fb-links">
									<?php echo $data->viewPublicationLink . JText::_('COM_HTML5FLIPPINGBOOK_FE_CONT_READ') . '</a>'; ?>
									<?php
									if ($downloadList)
									{
										echo '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
										echo JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_OPTIONS') . $downloadList;
									}
									?>
								</div>

								<?php
								$date = new JDate((int)$this->lastOpen->lastopen);
								$date = $date->toUnix();
								$dateString = gmdate("Y-m-d H:i:s", $date);
								?>

								<div class="stat well well-small">
									<h5> <?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_STATS') ?>:</h5>
								<span>
									<i class="fa fa-clock-o hasTooltip" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_LAST_OPEN_DATE');?>"></i> <?php echo $dateString; ?>
								</span>

									<?php if ((int)$this->lastOpen->page > 0):?>
										<span>
										<i class="fa fa-file-text-o hasTooltip" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_LAST_READ_PAGE');?>"></i>
											<?php echo str_replace("thumbnail", "", $data->viewPublicationLinkWithTitle) . JText::_('COM_HTML5FLIPPINGBOOK_FE_PAGE') . ' ' . $this->lastOpen->page . '</a>';?>
									</span>
									<?php endif;?>

									<?php if ((int)$this->lastOpen->spend_time > 0):?>
										<span>
										<i class="fa fa-history hasTooltip" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_SPENT_TIME');?>"></i> <?php echo HTML5FlippingBookFrontHelper::secsToString($this->lastOpen->spend_time); ?>
									</span>
									<?php endif;?>
								</div>
							</div>
						</li>
					</ul>
				<?php else: ?>
					<div class="alert alert-info">
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_NO_OPENED_PUBLICATION'); ?>
					</div>
				<?php endif;?>
			</fieldset>
		</div>

		<br/>

		<ul class="nav nav-tabs" id="viewTabs">
			<li><a href="#tab_reading" data-toggle="tab"><i class="fa fa-list"></i> <?php echo JText::_("COM_HTML5FLIPPINGBOOK_FE_TAB_READING_LIST");?></a></li>
			<li><a href="#tab_favorite" data-toggle="tab"><i class="fa fa-star"></i> <?php echo JText::_("COM_HTML5FLIPPINGBOOK_FE_TAB_FAVORITE_LIST");?></a></li>
			<li class="pull-right">
				<div class="btn-toolbar">
					<div class="btn-group">
						<a href="javascript: void(0);" id="read-publ" class="btn btn-small hasTooltip" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_READ_PUBL');?>"><i class="fa fa-eye-slash"></i></a>
						<a href="javascript: void(0);" id="list" class="btn btn-small active hasTooltip" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_LIST');?>"><i class="fa fa-list"></i></a>
						<a href="javascript: void(0);" id="bookshelf" class="btn btn-small hasTooltip" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_BOOKSHELF');?>"><i class="fa fa-book"></i></a>
					</div>
				</div>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane" id="tab_reading">
				<div class="bookshelf" style="display: none;">
					<div class="shelf">

						<div class="slide-navi" style="display: none">
							<a href="javascript: void(0)" class="htmlfb5-sl-prev">Prev</a>
							<a href="javascript: void(0)" class="htmlfb5-sl-next">Next</a>
						</div>

                        <?php
                        $shelf1_read = !empty($this->shelf1['read']) ? $this->shelf1['read'] : array();
                        $shelf2_read = !empty($this->shelf2['read']) ? $this->shelf2['read'] : array();
                        ?>
						<?php echo HTML5FlippingBookFrontHelper::createBookShelf($this->config,'reading', $shelf1_read, $isMobile, $isTablet, 1);?>
						<?php echo HTML5FlippingBookFrontHelper::createBookShelf($this->config,'reading', $shelf2_read, $isMobile, $isTablet, 2);?>

					</div>
				</div>

				<ul class="html5fb-list active" id="reading-list">
					<?php
					$data = HTML5FlippingBookFrontHelper::createPublicationList('reading', $this->readList, $this->viewPublicationButtonText, $this->config, $isMobile, $isTablet);
					$reading = $data[0];
					print_r($data[1]);
					?>
				</ul>
				<div class="content-loading">
					<img src="<?php echo COMPONENT_IMAGES_URL . 'progress.gif';?>" alt="Loading..."/>
				</div>

				<div class="alert alert-info" id="reading-alert" style="<?php echo (!$reading ? 'display: block;' : 'display: none;');?>">
					<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_NO_PUBLICATIONS');?>
				</div>
			</div>

			<div class="tab-pane" id="tab_favorite">
				<div class="bookshelf" style="display: none;">
					<div class="shelf">

						<div class="slide-navi" style="display: none">
							<a href="javascript: void(0)" class="htmlfb5-sl-prev">Prev</a>
							<a href="javascript: void(0)" class="htmlfb5-sl-next">Next</a>
						</div>

                        <?php
                        $shelf1_fav = !empty($this->shelf1['fav']) ? $this->shelf1['fav'] : array();
                        $shelf2_fav = !empty($this->shelf2['fav']) ? $this->shelf2['fav'] : array();
                        ?>
						<?php echo HTML5FlippingBookFrontHelper::createBookShelf($this->config,'favorite', $shelf1_fav, $isMobile, $isTablet, 1);?>
						<?php echo HTML5FlippingBookFrontHelper::createBookShelf($this->config,'favorite', $shelf2_fav, $isMobile, $isTablet, 2);?>

					</div>
				</div>

				<ul class="html5fb-list active" id="favorite-list">
					<?php
					$data = HTML5FlippingBookFrontHelper::createPublicationList('favorite', $this->favList, $this->viewPublicationButtonText, $this->config, $isMobile, $isTablet);
					$favorite = $data[0];
					print_r($data[1]);
					?>
				</ul>
				<div class="content-loading">
					<img src="<?php echo COMPONENT_IMAGES_URL . 'progress.gif';?>" alt="Loading..."/>
				</div>

				<div class="alert alert-info" id="favorite-alert" style="<?php echo (!$favorite ? 'display: block;' : 'display: none;');?>">
					<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_NO_PUBLICATIONS');?>
				</div>
			</div>
		</div>
	</div>

<?php
if ($this->config->social_email_use)
{
	echo $this->emaillayout->render(NULL);
}

if ($this->config->social_jomsocial_use)
{
	$data['friends'] = $this->userFriends;
	echo $this->sharelayout->render($data);
}
?>