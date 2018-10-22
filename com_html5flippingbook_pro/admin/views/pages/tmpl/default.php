<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$sortFields = array(
	'p.ordering' => JText::_('JGRID_HEADING_ORDERING'),
	'p.page_title' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_NAME'),
	'm.c_title' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_PUBLICATION'),
	'p.id' => JText::_('JGLOBAL_FIELD_ID_LABEL'),
	);
$sortedByOrder = ($listOrder == 'p.ordering');

if ($sortedByOrder)
{
	$saveOrderingUrl = 'index.php?option='.COMPONENT_OPTION.'&task=pages.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'pagesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<script type="text/javascript">
	
	Joomla.orderTable = function()
	{
		table = document.getElementById('sortTable');
		direction = document.getElementById('directionTable');
		order = table.options[table.selectedIndex].value;
		
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		
		Joomla.tableOrdering(order, dirn, '');
	}
	
	Joomla.submitbutton = function(task)
	{
		if (task == 'page.add')
		{
			var publicationId = <?php echo JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.pages.filter.publication_id', 0); ?>;
			
			if (publicationId == 0)
			{
				alert('<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_PUBLICATION_CHOOSE_TO_CREATE'); ?>');
				return;
			}
		}
		
		Joomla.submitform(task, document.adminForm);
	}
	
</script>

<?php echo HtmlHelper::getMenuPanel(); ?>

<form name="adminForm" id="adminForm" action="<?php echo 'index.php?option='.COMPONENT_OPTION.'&view='.$this->getName(); ?>" method="post" autocomplete="off">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	
	<?php if (!empty($this->sidebar)) { ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<?php } ?>
	
	<div id="j-main-container" class="<?php echo (empty($this->sidebar) ? '' : 'span10'); ?> html5fb_pages">
		
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_SEARCH_BY_NAME'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_SEARCH_BY_NAME'); ?>" value="<?php
					echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_SEARCH_BY_NAME'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php
					echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right">
			</div>
			<div class="btn-group pull-right">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		
		<div class="clearfix"></div>
		
		<table id="pagesList" class="table table-striped html5fb_table html5fb_pages_table">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'p.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th>
						<input type="checkbox" name="checkall-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_PAGES_NAME', 'p.page_title', $listDirn, $listOrder); ?> 
					</th>
					<th>
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_TYPE'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_CONTENT'); ?>
					</th>

                    <?php if ( JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.pages.filter.publication_id', 0) != 0 ) { ?>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'p.ordering', $listDirn, $listOrder); ?>
						<?php if ($listOrder == 'p.ordering') :?>
							<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'pages.save_order_input'); ?>
						<?php endif; ?>
					</th>
                    <?php } ?>
					<th width="1%">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_FIELD_ID_LABEL', 'p.id', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_CONTENTS'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if (count($this->items) > 0)
				{
					foreach ($this->items as $i => $item)
					{
						$type = '';
						$content = '';
						
						if ($item->c_enable_image == 1)
						{
							$type = JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_TYPE_IMAGE');
							$content = $item->page_image;
						}
						else
						{
							$type = JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_TYPE_TEXT');
							$content = substr($item->c_text, 0, 50);
						}
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="order nowrap center hidden-phone">
								<?php
								$disabledLabel = '';
								$disabledClassName = '';
								
								if (!$sortedByOrder)
								{
									$disabledLabel = JText::_('JORDERINGDISABLED');
									$disabledClassName = 'inactive tip-top';
								}
								?>
								<span class="sortable-handler hasTooltip <?php echo $disabledClassName; ?>" title="<?php echo $disabledLabel; ?>">
									<i class="icon-menu"></i>
								</span>
							</td>
							<td>
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td>
								<a href="<?php echo JRoute::_('index.php?option='.COMPONENT_OPTION.'&task=page.edit&id='.$item->id); ?>"><?php
									echo $this->escape($item->page_title); ?></a>
								<div class="small"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_PUBLICATION') . ': ' . $this->escape($item->publication_title); ?></div>
							</td>
							<td>
								<?php echo $this->escape($type); ?>
							</td>
							<td>
								<?php echo $this->escape($content); ?>
							</td>
								<?php if ( JFactory::getApplication()->getUserState(COMPONENT_OPTION.'.pages.filter.publication_id', 0) != 0 ) { ?>
									<td class="order">
										<div class="input-prepend">
											<?php if ($listOrder == 'p.ordering') :?>
												<?php if ($listDirn == 'asc') : ?>
													<span class="add-on"><?php echo $this->pagination->orderUpIcon($i, true, 'pages.orderup', 'JLIB_HTML_MOVE_UP', ($listOrder == 'p.ordering')); ?></span>
													<span class="add-on"><?php echo $this->pagination->orderDownIcon($i, count($this->items), true, 'pages.orderdown', 'JLIB_HTML_MOVE_DOWN', ($listOrder == 'p.ordering')); ?></span>
												<?php elseif ($listDirn == 'desc') : ?>
													<span class="add-on"><?php echo $this->pagination->orderUpIcon($i, true, 'pages.orderdown', 'JLIB_HTML_MOVE_UP', ($listOrder == 'p.ordering')); ?></span>
													<span class="add-on"><?php echo $this->pagination->orderDownIcon($i, count($this->items), true, 'pages.orderup', 'JLIB_HTML_MOVE_DOWN', ($listOrder == 'p.ordering')); ?></span>
												<?php endif; ?>
											<?php endif; ?>
											<?php $disabled = ($listOrder == 'p.ordering') ? '' : 'disabled="disabled"'; ?>
											<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="width-20 text-area-order" />
										</div>
									</td>
								<?php } ?>
							<td>
								<?php echo $item->id; ?>
							</td>
							<td class="center">
								<?php if ( !$item->is_contents ) { ?><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','pages.set_contents')" title="<strong><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CONTENTS');?></strong><br /><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CONTENTS_DESC');?>" class="hasTooltip"> <?php } ?>
									<i class="icon-<?php if ( !$item->is_contents ) echo 'un'; ?>publish"></i>
								<?php if ( !$item->is_contents ) { ?></a><?php } ?>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
				<?php
				if (count($this->items) == 0)
				{
					$html = array();
					
					$html[] = '<td colspan="100%" class="_html5fb_noitems">';
					
					if ($this->numAllItems == 0)
					{
						$html[] = JText::_('COM_HTML5FLIPPINGBOOK_BE_PAGES_NOITEMS') . ' – ' .
							'<a onclick="javascript:Joomla.submitbutton(\'page.add\')" href="javascript:void(0);">' .
							JText::_('COM_HTML5FLIPPINGBOOK_BE_CREATE_NEW_ONE') . '</a>';
					}
					else
					{
						$html[] = JText::_('COM_HTML5FLIPPINGBOOK_BE_NOITEMS');
					}
					
					$html[] = '</td>';
					
					echo implode('', $html);
				}
				else
				{
					echo '<td colspan="100%" class="_html5fb_pagination">' . $this->pagination->getListFooter() . '</td>';
				}
				?>
				</tr>
			</tfoot>
		</table>
		
		<?php echo $this->loadTemplate('batch'); ?>
		
	</div>
	
</form>