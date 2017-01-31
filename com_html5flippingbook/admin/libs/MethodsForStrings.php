<?php
/*
* HTML5FlippingBook component for Joomla
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright (C) JoomPlace, www.joomplace.com
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

defined("_JEXEC") or die("Restricted access");

class MethodsForStrings
{
	//----------------------------------------------------------------------------------------------------
	// Escape quotes.
	//
	// @param	string	Source string.
	// @return	string	Escaped string.
	//
	public static function EscapeQuotes($s)
	{
		$s = preg_replace('/\"/', '\\"', $s);
		
		return $s;
	}
	//----------------------------------------------------------------------------------------------------
	// Escape apostrophes.
	//
	// @param	string	Source string.
	// @return	string	Escaped string.
	//
	public static function EscapeApos($s)
	{
		$s = preg_replace('/\'/', '\\\'', $s);
		
		return $s;
	}
	//----------------------------------------------------------------------------------------------------
	// Transform number to string with spaces after each 3 symbols.
	//
	public static function SeparateThousands($value)
	{
		$result = "";
		
		$value = (string) $value;
		
		$partLength = 3;
		$len = strlen($value);
		
		$i = $len - $partLength;
		
		while ($i > 0)
		{
			$result = substr($value, $i, $partLength) . ($result == "" ? "" : " ") . $result;
			$i -= $partLength;
		}
		
		if ($i > -1 * $partLength)
		{
			$result = substr($value, 0, $i + $partLength) . ($result == "" ? "" : " ") . $result;
		}
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	// Cut too long string.
	// 
	// string - source string
	// int - maximum allowed length
	// boolean - show triple dot at the end
	//
	public static function FixStringLength($s, $maximumLength, $showDots = true)
	{
		$result = $s;
		
		if ($maximumLength >= 1 && strlen($s) > $maximumLength)
		{
			if ($showDots && $maximumLength >= 4) $result = substr($s, 0, $maximumLength - 3) . "...";
			else $result = substr($s, 0, $maximumLength);
		}
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	// Make string required length and align it.
	// 
	// string - source string
	// int - required string length
	// string - alignment ("left", "right", "center")
	// string - symbol, being used to fill gaps
	//
	public static function AlignString($s, $requiredLength, $alignment, $patternSymbol = " ")
	{
		$result = "";
		
		$len = strlen($s);
		
		if ($len < $requiredLength)
		{
			$indentLength = ($alignment == "center" ? floor(($requiredLength - $len) / 2) : $requiredLength - $len);
			
			$indent = "";
			
			for ($i = 0; $i < $indentLength; $i++)
			{
				$indent .= $patternSymbol;
			}
			
			switch ($alignment)
			{
				case "center":
					$result = ($indentLength * 2 == $requiredLength - $len ? $indent . $s . $indent : $indent . $s . $indent . $patternSymbol);
					break;
				case "left":
					$result = $s . $indent;
					break;
				case "right":
					$result = $indent . $s;
					break;
				default: $result = $s;
			}
		}
		else
		{
			$result = substr($s, 0, $requiredLength);
		}
		
		return $result;
	}
	//----------------------------------------------------------------------------------------------------
	public static function GenerateRandomString($stringLength, $case = 'all')
	{
		$chars = '';
		
		switch ($case)
		{
			case 'all': $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"; break;
			case 'upper': $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; break;
			case 'lower': $chars = "0123456789abcdefghijklmnopqrstuvwxyz"; break;
		}
		
		$numChars = strlen($chars);
		
		$s = "";
		
		for ($i = 0; $i < $stringLength; $i++)
		{
			$index = rand(0, $numChars - 1);
			
			$s .= substr($chars, $index, 1);
		}
		
		return $s;
	}
	//----------------------------------------------------------------------------------------------------
	// Cuts html attribute value from html string.
	// For example, you might need to get src attribute value from tag: <img src="http://www.host.com/image.png" width="468" height="60" />.
	//
	public static function GetHtmlAttributeValue($htmlString, $attributeName)
	{
		$result = null;
		
		$matches = array();
		
		$res = preg_match("/" . $attributeName . "=\"[^\"]*\"/", $htmlString, $matches);
		
		if ($res !== false && count($matches) > 0)
		{
			$match = $matches[0];
			
			$result = substr($match, strlen($attributeName) + 2, -1);
		}
		
		return $result;
	}
}