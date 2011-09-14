<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty money_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     money_format<br>
 * Purpose:  Format a currency amount. Based on code posted at http://www.php.net/manual/en/function.money-format.php#89060.
 *           Mimics money_format, which can't be used on windows servers, with some enhancements for alignment.
 * @link based on money_format(): http://www.php.net/manual/en/function.money-format.php#89060
 * @author   lindon
 * @param number
 * @param locale: currency locale for formatting (default=en_US)
 * @param format: format string
 * @oaram all: whether all amounts in list have a currency symbol or on first item
 * @return formatted number
 */

function smarty_modifier_money_format($number, $local, $currency, $format = '%(#10n', $display) 
{ 
		
	if (empty($local)) {
		if (setlocale(LC_MONETARY, 0) == 'C') { 
        	setlocale(LC_MONETARY, '');
		}
	} else {
		$ret = setlocale(LC_MONETARY, $local);
		if ($ret===FALSE) {
			echo "'$local' is not supported by this system.\n";
			return;
    	}
	} 
	
	$locale = localeconv();
	
	if (!empty($currency)) {
		$locale['int_curr_symbol'] = $currency;
	}
	
	//regex for format string
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
    
    
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
    foreach ($matches as $fmatch) { 
        $value = floatval($number); 
        $flags = array( 
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
                           $match[1] : ' ', 
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
                           $match[0] : '+', 
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
        ); 
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
        $conversion = $fmatch[5]; 

        $positive = true; 
        if ($value < 0) { 
            $positive = false; 
            $value  *= -1; 
        } 
        $letter = $positive ? 'p' : 'n'; 

        $prefix = $suffix = $cprefix = $csuffix = $signal = ''; 
        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
        switch (true) { 
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
                $prefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
                $suffix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
                $cprefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
                $csuffix = $signal; 
                break; 
            case $flags['usesignal'] == '(': 
            case $locale["{$letter}_sign_posn"] == 0: 
            	if ($positive == false) {
                	$prefix = '('; 
                	$suffix = ')';
             	}
                break; 
        } 

        if (!$flags['nosimbol']) { 
            $currency = $cprefix . 
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
                        $csuffix; 
        } else { 
            $currency = ''; 
        } 
        $space  = $locale["{$letter}_sep_by_space"] && !empty($currency) ? ' ' : ''; 

        $value = number_format($value, $right, $locale['mon_decimal_point'], 
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
        $value = @explode($locale['mon_decimal_point'], $value); 

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
        if ($left > 0 && $left > $n) { 
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
        } 
        $value = implode($locale['mon_decimal_point'], $value);

        if ($locale["{$letter}_cs_precedes"]) { 
            $value = $prefix . $currency . $space . $value . $suffix; 
            //create right pad for alignment
            $rightpad = empty($suffix) && $flags['usesignal'] == '(' ? ')' : '';
        } else {	
            $value = $prefix . $value . $space . $currency . $suffix;
            //create right pad for alignment
            $rightpad = $locale['n_sep_by_space'] == true && $locale['n_cs_precedes'] == false && strlen($space) == 0 ? ' ' : '';
        	$currpad = $conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol'];
            $rightpad .= ($locale['p_cs_precedes'] == false || $locale['n_cs_precedes'] == false) && empty($currency) ? $currpad : '';
            $rightpad .= empty($suffix) && $flags['usesignal'] == '(' ? ')' : '';
        }
 
        $format = str_replace($fmatch[0], $value, $format); 
        $format = !empty($rightpad) ? $format .= '<span style="visibility:hidden">' . $rightpad .  '</span>' : $format;
    } 
    return $format; 
}
