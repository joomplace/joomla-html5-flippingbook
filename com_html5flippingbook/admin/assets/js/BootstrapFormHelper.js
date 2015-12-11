BootstrapFormHelper =
{
	addOptionToSelectList : function(elementId, optionValue, optionName, setAsSelected)
	{
		var selectedValue = jQuery('#' + elementId).val();
		
		// Adding option.
		
		jQuery('#' + elementId).append(new Option(optionValue, optionName));
		
		// Sorting options.
		
		var options = jQuery('#' + elementId + ' option');
		
		options.sort(function (a, b) { return (a.text == b.text ? 0 : (a.text > b.text ? 1 : -1)); });
		
		jQuery('#' + elementId).empty().append(options);
		
		// Selecting new option if required.
		
		if (setAsSelected) jQuery('#' + elementId).val(optionValue);
		else jQuery('#' + elementId).val(selectedValue);
		
		// Updating bootstrap control.
		
		jQuery('#' + elementId).trigger('liszt:updated');
		
		// Triggering event if required.
		
		if (setAsSelected) jQuery('#' + elementId).trigger('onchange');
	},
	
	selectOptionInSelectList : function(elementId, optionValue)
	{
		jQuery('#' + elementId).val(optionValue);
		jQuery('#' + elementId).trigger('liszt:updated');
		jQuery('#' + elementId).trigger('onchange');
	},
	
	getSelectedListOption : function(elementId)
	{
		var selectList = document.getElementById(elementId);
		
		return selectList.options[selectList.selectedIndex];
	},
	
	getRadioGroupValue : function(radioGroupId)
	{
		var value = null;
		
		var fieldsetId = radioGroupId;
		
		var i = 0;
		
		while (true)
		{
			var radioInput = document.getElementById(fieldsetId + i);
			
			if (radioInput != null)
			{
				if (radioInput.checked)
				{
					value = radioInput.value;
					break;
				}
			}
			else
			{
				break;
			}
			
			i += 1;
		}
		
		return value;
	},
	
	getOpenedTab : function(paneId)
	{
		paneId = (typeof paneId !== 'undefined' ? paneId : 'viewTabs');
		
		var tabContentObj = jQuery('#' + paneId + ' + div.tab-content');
		var tabContentElement = tabContentObj.get(0);
		
		var allOpenedTabs = tabContentObj.find('div.tab-pane.active').get();
		
		var result = null;
		
		for (var i = 0; i < allOpenedTabs.length; i++)
		{
			var tab = allOpenedTabs[i];
			
			if (tab.parentNode == tabContentElement)
			{
				result = tab;
				break;
			}
		}
		
		return result;
	},
	
	openTab : function(tabId, paneId)
	{
		paneId = (typeof paneId !== 'undefined' ? paneId : 'viewTabs');
		
		var openedTab = this.getOpenedTab(paneId);
		
		if (openedTab == null || openedTab.id != tabId)
		{
			var targetTab = jQuery('#' + tabId);
			var currentTabNav = jQuery('#' + paneId + ' li.active');
			var targetTabNav = jQuery('#' + paneId + ' li a[href="#' + tabId + '"]').parent();
			
			if (openedTab != null) openedTab.removeClass('active');
			targetTab.addClass('active');
			
			currentTabNav.removeClass('active');
			targetTabNav.addClass('active');
		}
	},
	
	openElementTab : function(elementId)
	{
		var panes = [];
		
		var tabChildElementId = elementId;
		
		while (true)
		{
			var tab = jQuery('#' + tabChildElementId).closest('div.tab-pane').get(0);
			var pane = (tab == null ? null : jQuery(tab).closest('div.tab-content').parent().find('ul.nav-tabs').get(0));
			
			if (tab == null || pane == null) break;
			
			panes.splice(0, 0, { id : pane.id, tabId : tab.id});
			
			tabChildElementId = pane.id;
		}
		
		for (var i = 0; i < panes.length; i++)
		{
			var pane = panes[i];
			
			this.openTab(pane.tabId, pane.id);
		}
	}
}
