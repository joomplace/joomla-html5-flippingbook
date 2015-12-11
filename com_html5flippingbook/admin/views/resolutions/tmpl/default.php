<?php defined('_JEXEC') or die('Restricted Access');
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
	'resolution_name' => JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_NAME'),
	'width' => JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_WIDTH'),
	'height' => JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_HEIGHT'),
	'id' => JText::_('JGLOBAL_FIELD_ID_LABEL'),
	);

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
	
	<div id="j-main-container" class="<?php echo (empty($this->sidebar) ? '' : 'span10'); ?> html5fb_resolutions">
		
		<div id="filter-bar" class="btn-toolbar">
			<div class="btn-group pull-right">
			</div>
			<div class="btn-group pull-right">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
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
		
		<table class="table table-striped html5fb_table html5fb_resolutions_table">
			<thead>
				<tr>
					<th>
						<input type="checkbox" name="checkall-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_NAME', 'resolution_name', $listDirn, $listOrder); ?> 
					</th>
					 <th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_WIDTH', 'width', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_HEIGHT', 'height', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_FIELD_ID_LABEL', 'id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if (count($this->items) > 0)
				{
					foreach($this->items as $i => $item)
					{
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td>
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td>
								<a href="<?php echo JRoute::_('index.php?option='.COMPONENT_OPTION.'&task=resolution.edit&id='.$item->id); ?>"><?php
									echo $this->escape($item->resolution_name); ?></a>
							</td>
							<td>
								<?php echo $item->width; ?>
							</td>
							<td>
								<?php echo $item->height; ?>
							</td>
							<td>
								<?php echo $item->id; ?>
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
						$html[] = JText::_('COM_HTML5FLIPPINGBOOK_BE_RESOLUTIONS_NOITEMS') . ' – ' .
							'<a onclick="javascript:Joomla.submitbutton(\'resolution.add\')" href="javascript:void(0);">' .
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
		
	</div>
	
</form>