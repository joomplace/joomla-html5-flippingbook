function MethodsForXml()
{
}

MethodsForXml.xmlEncode = function (s)
{
    return s.replace(/\&/g,'&'+'amp;').replace(/</g,'&'+'lt;').replace(/>/g,'&'+'gt;').replace(/\'/g,'&'+'apos;').replace(/\"/g,'&'+'quot;').replace(/\n/g,'&'+'#xD;');
}

MethodsForXml.getNodeValue = function (node)
{
	if (node.firstChild == null) return "";
	else return node.firstChild.nodeValue;
}

MethodsForXml.getXmlDocFromString = function (xmlString)
{
	var xmlDoc = null;
	
	if (window.DOMParser)
	{
		var parser = new DOMParser();
		xmlDoc = parser.parseFromString(xmlString, "text/xml");
		
		if (xmlDoc.documentElement.nodeName == "parsererror")
		{
			throw { message : xmlDoc.documentElement.childNodes[0].nodeValue };
		}
	}
	else // Internet Explorer.
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async = "false";
		xmlDoc.loadXML(xmlString);
		
		if (xmlDoc.parseError.errorCode != 0)
		{
			throw { message : "Error in line " + xmlDoc.parseError.line +
				" position " + xmlDoc.parseError.linePos +
				"\nError Code: " + xmlDoc.parseError.errorCode +
				"\nError Reason: " + xmlDoc.parseError.reason +
				"Error Line: " + xmlDoc.parseError.srcText };
		}
	}
	
	return xmlDoc;
}
