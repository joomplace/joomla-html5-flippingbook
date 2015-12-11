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
	'm.ordering' => JText::_('JGRID_HEADING_ORDERING'),
	'm.c_title' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_NAME'),
	'c.c_category' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CATEGORY'),
	't.template_name' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_TEMPLATE'),
	'r.resolution_name' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_RESOLUTION'),
	'm.c_created_time' => JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CREATION_TIME'),
	'm.c_id' => JText::_('JGLOBAL_FIELD_ID_LABEL'),
	);
$sortedByOrder = ($listOrder == 'm.ordering');

if ($sortedByOrder)
{
	$saveOrderingUrl = 'index.php?option='.COMPONENT_OPTION.'&task=publications.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'publicationsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
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
	
	<div id="j-main-container" class="<?php echo (empty($this->sidebar) ? '' : 'span10'); ?> html5fb_publications">
		
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_SEARCH_BY_NAME'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_SEARCH_BY_NAME'); ?>" value="<?php
					echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_SEARCH_BY_NAME'); ?>" />
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
		
		<table id="publicationsList" class="table table-striped html5fb_table html5fb_publications_table">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'm.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th>
						<input type="checkbox" name="checkall-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_NAME', 'm.c_title', $listDirn, $listOrder); ?> 
					</th>
					 <th>
						<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'm.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PAGES'); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_TEMPLATE', 't.template_name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_RESOLUTION', 'r.resolution_name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CREATION_TIME', 'm.c_created_time', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PARAMETERS'); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_FIELD_ID_LABEL', 'm.c_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if (count($this->items) > 0)
				{
					foreach ($this->items as $i => $item)
					{
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
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							</td>
							<td>
								<?php echo JHtml::_('grid.id', $i, $item->c_id); ?>
							</td>
							<td>
								<a href="<?php echo JRoute::_('index.php?option='.COMPONENT_OPTION.'&task=publication.edit&c_id='.$item->c_id); ?>"><?php
									echo $this->escape($item->c_title); ?></a>
								<div class="small"><?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_CATEGORY') . ': ' . $this->escape($item->c_category); ?></div>
							</td>
							<td>
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'publications.', true); ?>
							</td>
							<td>
								<?php echo $item->page_count; ?>
								<a title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_VIEW_PAGES');?>" href="<?php
									echo JRoute::_('index.php?option='.COMPONENT_OPTION.'&task=pages.redirect_from_publications&pubId='.$item->c_id); ?>" target="_blank">
									<img src="<?php echo COMPONENT_IMAGES_URL.'view_pages.png'; ?>" />
								</a>
							</td>
							<td>
								<?php echo $this->escape($item->template_name); ?>
							</td>
							<td>
								<?php echo $this->escape($item->resolution_name); ?>
							</td>
							<td>
								<?php echo $this->escape($item->c_created_time); ?>
							</td>
							<td>
								<?php
								$html = array();
								

								$html[] = '<i class="icon-file' . ($item->c_enable_pdf ? '' : ' disabled') . '" title="' . ($item->c_enable_pdf ?
									JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PDF_ENABLED') :
									JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_PDF_DISABLED')) . '"></i>' . ' ';

								$html[] = '<i class="icon-screen' . ($item->c_enable_fullscreen ? '' : ' disabled') . '" title="' . ($item->c_enable_fullscreen ?
									JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_FULLSCREEN_ENABLED') :
									JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_FULLSCREEN_DISABLED')) . '"></i>' . ' ';

								echo implode('', $html);
								?>
							</td>
							<td>
								<?php echo $item->c_id; ?>
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
						$html[] = JText::_('COM_HTML5FLIPPINGBOOK_BE_PUBLICATIONS_NOITEMS') . ' – ' .
							'<a onclick="javascript:Joomla.submitbutton(\'publication.add\')" href="javascript:void(0);">' .
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