MyAjax.activeRequests = [];

MyAjax.generateRandomString = function ()
{
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ";
	var stringLength = 16;
	
	var s = "";
	
	for (var i = 0; i < stringLength; i++)
	{
		var index = Math.floor(Math.random() * chars.length);
		
		s += chars.substring(index, index + 1);
	}
	
	return s;
}

MyAjax.makeRequest = function(url, data, syncObject, timeout, dataCallback, timeoutCallback)
{
	var request = null;
	
	var httpRequest = false;
	
	if (window.XMLHttpRequest) // Firefox.
	{ 
		httpRequest = new XMLHttpRequest();
		
		if (httpRequest.overrideMimeType)
		{
			httpRequest.overrideMimeType("text/xml");
		}
	}
	else if (window.ActiveXObject) // Internet Explorer.
	{
		try
		{
			httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
			}
		}
	}
	
	if (httpRequest)
	{
		request = new MyAjax();
		
		MyAjax.activeRequests.push(request);
		
		var intervalId = setTimeout(function () { MyAjax.onTimeout(request, intervalId, syncObject, timeoutCallback); }, timeout);
		
		httpRequest.onreadystatechange = function() { MyAjax.onData(httpRequest, request, intervalId, syncObject, dataCallback); };
		httpRequest.open("POST", url, true);
		httpRequest.setRequestHeader("Content-type", "text/xml");
		httpRequest.send(data);
	}
	else
	{
		alert("httpRequest is not defined");
	}
	
	return request;
}

MyAjax.onData = function (httpRequest, request, intervalId, syncObject, dataCallback)
{
	if (httpRequest.readyState == 4 && httpRequest.status == 200)
	{
		clearInterval(intervalId);
		
		if (!request.completed)
		{
			request.completed = true;
			
			dataCallback(request, syncObject, httpRequest.responseText);
		}
	}
}

MyAjax.onTimeout = function (request, intervalId, syncObject, timeoutCallback)
{
	clearInterval(intervalId);
	
	if (!request.completed)
	{
		request.completed = true;
		
		timeoutCallback(request, syncObject);
	}
	
	MyAjax.activeRequests.splice(MyAjax.activeRequests.indexOf(request), 1);
}

function MyAjax()
{
	this.gui = MyAjax.generateRandomString();
	this.completed = false;
}


