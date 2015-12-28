<?php defined('_JEXEC') or die('Restricted access');

class MethodsForXml
{
	//----------------------------------------------------------------------------------------------------
	// Encode string for XML.
	//
	// @param	string	Source string.
	// @return	string	Encoded string.
	//
	public static function XmlEncode($s)
	{
		$s = preg_replace('/\&/', '&amp;', $s);
		$s = preg_replace('/</', '&lt;', $s);
		$s = preg_replace('/>/', '&gt;', $s);
		$s = preg_replace('/\'/', '&apos;', $s);
		$s = preg_replace('/\"/', '&quot;', $s);
		$s = preg_replace('/\n/', '&#xD;', $s);
		
		return $s;
	}
	//----------------------------------------------------------------------------------------------------
	// Transform xml-node into formatted string with header.
	// 
	// @param	SimpleXMLElement 	Node.
	// @return	string				Formatted string.
	//
	public static function XmlNodeToStringForFile($node)
	{
		return '<?xml version="1.0" encoding="utf-8"?>' . "\n" . self::XmlNodeToString($node, 0, "\t");
	}
	//----------------------------------------------------------------------------------------------------
	// Transform xml-node into formatted string. Recursive method.
	// 
	// @param	SimpleXMLElement 	Node.
	// @param	int					Indent level.
	// @param	string				Indent pattern.
	// @return	string				Formatted string.
	//
	public static function XmlNodeToString($node, $indentLevel, $indentPattern)
	{
		$indent = "";
		
		for ($i = 0; $i < $indentLevel; $i++)
		{
			$indent .= $indentPattern;
		}
		
		$s = "<" . $node->getName();
		
		$numAttributes = count($node->attributes());
		
		if ($numAttributes > 0)
		{
			foreach ($node->attributes() as $name => $value)
			{
				$s .= " " . $name . "=\"" . $value . "\"";
			}
			
		}
		
		$s .= ">";
		
		$numChildren = count($node->children());
		
		if ($numChildren == 0)
		{
			$s .= (string) $node;
		}
		else
		{
			foreach ($node->children() as $childNode)
			{
				$s .= "\r\n" . $indent . $indentPattern . self::XmlNodeToString($childNode, $indentLevel + 1, $indentPattern);
			}
			
			$s .= "\r\n" . $indent;
		}
		
		$s .= "</" . $node->getName() . ">";
		
		return $s;
	}
}