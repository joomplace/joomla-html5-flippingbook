BootstrapFormValidator =
{
	trim : function(s)
	{
		return s.replace(/^\s+|\s+$/g, '');
	},
	
	restoreControlsDefaultState : function(elements)
	{
		for (var i = 0; i < elements.length; i++)
		{
			var element = elements[i];
			
			if (element != null)
			{
				switch (element.tagName)
				{
					case 'INPUT':
					{
						jQuery('#' + element.id).removeClass('invalid');
						break;
					}
					case 'SELECT':
					{
						jQuery('#' + element.id + '_chzn').removeClass('invalid');
						break;
					}
				}
				
				jQuery('#' + element.id + '-lbl').removeClass('invalid');
			}
		}
	},
	
	setControlsErrorState : function(elements, msg)
	{
		for (var i = 0; i < elements.length; i++)
		{
			var element = elements[i];
			
			if (element != null)
			{
				switch (element.tagName)
				{
					case 'INPUT':
					{
						jQuery('#' + element.id).addClass('invalid');
						jQuery('html, body').animate({ scrollTop : jQuery('#' + element.id).offset().top - 200 }, 200);
						break;
					}
					case 'SELECT':
					{
						jQuery('#' + element.id + '_chzn').addClass('invalid');
						jQuery('html, body').animate({ scrollTop : jQuery('#' + element.id + '_chzn').offset().top - 200 }, 1);
						break;
					}
				}
				
				BootstrapFormHelper.openElementTab(element.id);
				jQuery('#' + element.id + '-lbl').addClass('invalid');
			}
		}
		
		window.setTimeout(function() { alert(msg); }, 20);
		
		if (element != null && element.tagName == 'INPUT') element.focus();
	},
	
	checkSelectControlsEmptyValues : function(elements, msg)
	{
		for (var i = 0; i < elements.length; i++)
		{
			var element = elements[i];
			
			if (element != null)
			{
				if (element.tagName == 'SELECT')
				{
					if (element.value == 0 || element.value == '')
					{
						jQuery('#' + element.id + '_chzn').addClass('invalid');
						jQuery('#' + element.id + '-lbl').addClass('invalid');
						
						BootstrapFormHelper.openElementTab(element.id);
						jQuery('html, body').animate({ scrollTop : jQuery('#' + element.id + '_chzn').offset().top - 200 }, 1);
						
						window.setTimeout(function() { alert(msg); }, 20);
						
						return true;
					}
				}
			}
		}
		
		return false;
	},
	
	checkEmptyValues : function(inputs, msg)
	{
		for (var i = 0; i < inputs.length; i++)
		{
			var input = inputs[i];
			
			if (input != null)
			{
				if (input.value == '')
				{
					jQuery('#' + input.id).addClass('invalid');
					jQuery('#' + input.id + '-lbl').addClass('invalid');
					
					BootstrapFormHelper.openElementTab(input.id);
					jQuery('html, body').animate({ scrollTop : jQuery('#' + input.getAttribute('id')).offset().top - 200 }, 1);
					
					window.setTimeout(function() {
						alert(msg);
						input.focus(); }, 20);
					
					return true;
				}
			}
		}
		
		return false;
	},
	
	checkTrimmedEmptyValues : function(inputs, msg)
	{
		for (var i = 0; i < inputs.length; i++)
		{
			var input = inputs[i];
			
			if (input != null)
			{
				if (this.trim(input.value) == '')
				{
					jQuery('#' + input.id).addClass('invalid');
					jQuery('#' + input.id + '-lbl').addClass('invalid');
					
					BootstrapFormHelper.openElementTab(input.id);
					jQuery('html, body').animate({ scrollTop : jQuery('#' + input.id).offset().top - 200 }, 1);
					
					window.setTimeout(function() {
						alert(msg);
						input.focus(); }, 20);
					
					return true;
				}
			}
		}
		
		return false;
	},
	
	checkSpaces : function(inputs, msg)
	{
		for (var i = 0; i < inputs.length; i++)
		{
			var input = inputs[i];
			
			if (input != null)
			{
				if (input.value.indexOf(' ') != -1)
				{
					jQuery('#' + input.getAttribute('id')).addClass('invalid');
					jQuery('#' + input.getAttribute('id') + '-lbl').addClass('invalid');
					
					BootstrapFormHelper.openElementTab(input.id);
					jQuery('html, body').animate({ scrollTop : jQuery('#' + input.id).offset().top - 200 }, 1);
					
					window.setTimeout(function() {
						alert(msg);
						input.focus(); }, 20);
					
					return true;
				}
			}
		}
		
		return false;
	},
	
	checkPatterns : function(inputs, pattern, msg, cleanValues)
	{
		// cleanValues parameter is used to clean file input values from redundant path (in Chrome, specifically).
		cleanValues = (typeof cleanValues !== 'undefined' ? cleanValues : false);
		
		for (var i = 0; i < inputs.length; i++)
		{
			var input = inputs[i];
			
			if (input != null)
			{
				var value = input.value;
				
				if (cleanValues)
				{
					var slashLastIndex = value.lastIndexOf('/');
					
					if (slashLastIndex != -1) value = value.substring(slashLastIndex + 1);
					
					var backslashLastIndex = value.lastIndexOf('\\');
					
					if (backslashLastIndex != -1) value = value.substring(backslashLastIndex + 1);
				}
				
				if (!pattern.test(value))
				{
					jQuery('#' + input.getAttribute('id')).addClass('invalid');
					jQuery('#' + input.getAttribute('id') + '-lbl').addClass('invalid');
					
					BootstrapFormHelper.openElementTab(input.id);
					jQuery('html, body').animate({ scrollTop : jQuery('#' + input.id).offset().top - 200 }, 1);
					
					window.setTimeout(function() {
						alert(msg);
						input.focus(); }, 20);
					
					return true;
				}
			}
		}
		
		return false;
	},
	
	checkMinimumLength : function(inputs, minLength, msg)
	{
		for (var i = 0; i < inputs.length; i++)
		{
			var input = inputs[i];
			
			if (input != null)
			{
				if (input.value.length < minLength)
				{
					jQuery('#' + input.getAttribute('id')).addClass('invalid');
					jQuery('#' + input.getAttribute('id') + '-lbl').addClass('invalid');
					
					BootstrapFormHelper.openElementTab(input.id);
					jQuery('html, body').animate({ scrollTop : jQuery('#' + input.id).offset().top - 200 }, 1);
					
					window.setTimeout(function() {
						alert(msg);
						input.focus(); }, 20);
					
					return true;
				}
			}
		}
		
		return false;
	},
	
	checkNumbersValidityAndLimits : function(inputs, minValue, maxValue, nanMsg, limitsMsg, castTo)
	{
		castTo = (typeof castTo !== 'undefined' ? (['int', 'float'].indexOf(castTo) != -1 ? castTo : 'float') : 'float');
		
		for (var i = 0; i < inputs.length; i++)
		{
			var input = inputs[i];
			
			if (input != null)
			{
				var value = (castTo == 'int' ? parseInt(input.value) : parseFloat(input.value));
				
				if (isNaN(value))
				{
					jQuery('#' + input.getAttribute('id')).addClass('invalid');
					jQuery('#' + input.getAttribute('id') + '-lbl').addClass('invalid');
					
					BootstrapFormHelper.openElementTab(input.id);
					jQuery('html, body').animate({ scrollTop : jQuery('#' + input.id).offset().top - 200 }, 1);
					
					window.setTimeout(function() {
						alert(nanMsg);
						input.focus(); }, 20);
					
					return true;
				}
				else
				{
					if (value < minValue || value > maxValue)
					{
						jQuery('#' + input.getAttribute('id')).addClass('invalid');
						jQuery('#' + input.getAttribute('id') + '-lbl').addClass('invalid');
						
						BootstrapFormHelper.openElementTab(input.id);
						jQuery('html, body').animate({ scrollTop : jQuery('#' + input.id).offset().top - 200 }, 1);
						
						window.setTimeout(function() {
							alert(limitsMsg);
							input.focus(); }, 20);
						
						return true;
					}
					else
					{
						input.value = value;
					}
				}
			}
		}
	}
}
