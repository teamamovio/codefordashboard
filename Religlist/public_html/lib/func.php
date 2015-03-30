<?php

// Commonly used functions

// Generate string with length between $minLen and $maxLen containing random letters of different case and digits
// If only one argument is specified this argument specifies the exact length of the string
// If no arguments are specified at all this will generate string with length 32
function genRand($minLen = 32, $maxLen = null) {
	if (is_null($maxLen)) $maxLen = $minLen;
	$len1 = min($minLen, $maxLen);
	$len2 = max($minLen, $maxLen);
	$len = mt_rand($len1, $len2);
	
	$ret = "";
	for ($i = 0; $i < $len; $i++) {
		$type = mt_rand(0, 2);
		if ($type == 0) {
			// Uppercase letters
			$ret .= chr(mt_rand(ord("A"), ord("Z")));
		} elseif ($type == 1) {
			// Lowercase letters
			$ret .= chr(mt_rand(ord("a"), ord("z")));
		} elseif ($type == 2) {
			// Digits
			$ret .= chr(mt_rand(ord("0"), ord("9")));
		}
	}
	return $ret;
}

// Encode characters within text which have special meaning in HTML
// Use this function to insert pure text into HTML
function t2h($txt) {
	return htmlspecialchars($txt, ENT_QUOTES, "UTF-8");
}

// Shortcut for `print($txt);` or `echo $txt;`
function e($txt) { print($txt); }

// Safely insert pure text into HTML, inspired by RoR function with similar name
function h($txt) { e(t2h($txt)); }

// Report error. It just prints error message in development version.
// It is possible to change this function for production to save error message into log file.
function err($errMsg, $file, $line, $fatal = true) {
	e("<div class=\"sys_error\"><b>Error:</b> ".t2h($errMsg)." in file ".t2h($file)." on line ".t2h($line)."</div>");
	if ($fatal) exit;
}

// Unicode version of trim
function mb_trim($txt) {
	$txt = preg_replace('#^\\s+#Duis', "", $txt);
	$txt = preg_replace('#\\s+$#Duis', "", $txt);
	return $txt;
}

function like($s, $e = '=') {
    return str_replace(array($e, '_', '%'), array($e.$e, $e.'_', $e.'%'), $s);
}

