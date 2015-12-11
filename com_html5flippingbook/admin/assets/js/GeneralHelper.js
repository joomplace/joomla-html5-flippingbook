function GeneralHelper() {}

GeneralHelper.addEventListener = function(element, type, eventHandle)
{
	if (element == null || element == undefined) return;
	
	if (element.addEventListener) element.addEventListener(type, eventHandle, false);
	else if (element.attachEvent) element.attachEvent("on" + type, eventHandle);
}

GeneralHelper.addLibrary = function(url, objectNameToCheck, onLoadCallback)
{
	if (typeof window[objectNameToCheck] !== 'undefined')
	{
		onLoadCallback();
		return;
	}
	
	var head = document.getElementsByTagName('head')[0];
	
	var element = document.createElement('script');
	element.setAttribute('type', 'text/javascript');
	element.setAttribute('src', url);
	head.appendChild(element);
	
	var timePassed = 0;
	var intervalId = window.setInterval(function() { checkLibraryLoading(intervalId, timePassed, objectNameToCheck, onLoadCallback); }, 50);
	
	function checkLibraryLoading(intervalId, timePassed, objectNameToCheck, onLoadCallback)
	{
		if (typeof window[objectNameToCheck] !== 'undefined')
		{
			window.clearInterval(intervalId);
			onLoadCallback();
		}
		else if (timePassed > 10000)
		{
			window.clearInterval(intervalId);
		}
	 }
}

GeneralHelper.addClass = function(element, className)
{
	var classStr = element.getAttribute('class');
	
	var classNames = (classStr == null ? [] : classStr.split(' '));
	
	var index = classNames.indexOf(className);
	
	if (index == -1) classNames.push(className);
	
	element.setAttribute('class', classNames.join(' '));
}

GeneralHelper.removeClass = function(element, className)
{
	var classStr = element.getAttribute('class');
	
	if (classStr != null)
	{
		var classNames = classStr.split(' ');
		
		var index = classNames.indexOf(className);
		
		if (index != -1) classNames.splice(index, 1);
		
		element.setAttribute('class', classNames.join(' '));
	}
}

GeneralHelper.getChildNodes = function(parentElement)
{
	var result = [];
	
	for (var i = 0; i < parentElement.childNodes.length; i++)
	{
		var child = parentElement.childNodes[i];
		
		if (child.nodeType == 1) result.push(child);
	}
	
	return result;
}

GeneralHelper.getChildNodeByClassName = function(parentElement, className)
{
	var result = null;
	
	for (var i = 0; i < parentElement.childNodes.length; i++)
	{
		var child = parentElement.childNodes[i];
		
		if (child.nodeType == 1)
		{
			var classNames = child.className.split(' ');
			
			if (classNames.indexOf(className) != -1)
			{
				result = child;
				break;
			}
		}
	}
	
	return result;
}

GeneralHelper.getParentNodeByTagName = function(childNode, tagName)
{
	var result = null;
	
	var tagName = tagName.toUpperCase();
	
	var parentNode = childNode.parentNode;
	
	while (parentNode != null)
	{
		if (parentNode.tagName == tagName)
		{
			result = parentNode;
			break;
		}
		
		parentNode = parentNode.parentNode;
	}
	
	return result;
}

GeneralHelper.getAbsoluteClientPosition = function(element)
{
	var xx = 0;
	var yy = 0;
	
	while (element && !isNaN(element.offsetLeft) && !isNaN(element.offsetTop))
	{
		xx += element.offsetLeft - element.scrollLeft;
		yy += element.offsetTop - element.scrollTop;
		
		element = element.offsetParent;
	}
	
	return { x : xx, y : yy };
}

GeneralHelper.dispathOnWindowResizeEvent = function()
{
	if (document.createEvent)
	{
		var resizeEvent = document.createEvent("Event");
		resizeEvent.initEvent("resize", true, false);
		window.dispatchEvent(resizeEvent);
	}
	else // IE < 9.
	{
		 var resizeEvent = document.createEventObject(window.event);
		 window.fireEvent("resize", resizeEvent);
	}
}

GeneralHelper.dispatchMouseEvent = function(targetElement, mouseEventType)
{
	var event = null;
	
	if (document.createEvent)
	{
		// All browsers except IE before version 9.
		
		event = document.createEvent("MouseEvent");
		
		var args = {
			eventType : mouseEventType,
			bubblesFlag : false,
			cancelableFlag : true,
			view : window,
			detailVal : 0,
			screenX : 0,
			screenY : 0,
			clientX : 0,
			clientY : 0,
			ctrlKeyFlag : false,
			altKeyFlag : false,
			shiftKeyFlag : false,
			metaKeyFlag : false,
			buttonCode : 0,
			relatedTargetNodeRef : null
			};
		
		event.initMouseEvent(args.eventType, args.bubblesFlag, args.cancelableFlag, args.view, args.detailVal, args.screenX, args.screenY, args.clientX, args.clientY,
			args.ctrlKeyFlag, args.altKeyFlag, args.shiftKeyFlag, args.metaKeyFlag, args.buttonCode, args.relatedTargetNodeRef);
		
		targetElement.dispatchEvent(event);
	}
	else if (document.createEventObject)
	{
		event = document.createEventObject(window.event);
		
		event.button = 1;
		
		targetElement.fireEvent (mouseEventType, event);
	}
	else
	{
		alert("Your browser is too old to display this page correctly");
	}
}
