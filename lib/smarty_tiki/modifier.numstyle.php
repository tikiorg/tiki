<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Converts a positive integer into a selected format.
 * Obe may choose a CSS list-style-type value. Some language specific options have not been implemented.
 *
 * One may also select a 'footnote' value, which will use traditional footnote markers.
 *
 * Eastern languages are not implemented, they require a different formula as they use a modifier
 * before 10 instead of repeating it.
 *
 * @param $num int the number to be converted
 * @param $type string the CSS List-Style-Type to format the number to
 *
 * @return string The returned number in the given format
 *
 * eg. {14|numStyle:'upper-roman'}
 */

function smarty_modifier_numStyle($num, $type)
{
	$style = new StyleType;
	$num = (int)$num;                     // some basic filtering, using negative or 0 may result in failure, depending on selection
	if ($num < 1) {
		return $num;
	}
	switch (strtolower($type)) {
		case "decimal-leading-zero":
			return '0' . $num;
		case 'lower-alpha':
		case 'lower-latin':
			return $style->toAlpha($num, range('a', 'z'));
		case 'upper-alpha':
		case 'upper-latin':
			return $style->toAlpha($num, range('A', 'Z'));
		case 'lower-greek':
			$range = ['α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω'];
			return $style->toAlpha($num, $range);
		case 'upper-greek':
			$range = ['Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω'];
			return $style->toAlpha($num, $range);
		case 'lower-roman':
			return strtolower($this->numerals($num));
		case 'upper-roman':
			return $style->numerals($num);
		case 'hebrew':
			return $style->numerals($num, ['א׳א׳‎' => 1000000 ,'א׳ק‎' => 100000 ,'א׳י‎' => 10000 ,'ה׳‎' => 5000 ,'ב׳‎' => 2000 ,'א׳‎' => 1000 ,'ץ‎' => 900 ,'ף‎' => 800 ,'ן‎' => 700 ,'ם‎' => 600 ,'ך‎' => 500 ,'ת‎' => 400 ,'ש‎' => 300 ,'ר‎' => 200 ,'ק‎' => 100 ,'צ‎' => 90 ,'פ‎' => 80 ,'ע‎' => 70 ,'ס‎' => 60 ,'נ‎' => 50 ,'מ‎' => 40 ,'ל‎' => 30 ,'כ‎' => 20 ,'יט‎' => 19 ,'יח‎' => 18 ,'יז‎' => 17 ,'ט״ז‎' => 16 ,'ט״ו‎' => 15 ,'יד‎' => 14 ,'יג‎' => 13 ,'יב‎' => 12 ,'יא‎' => 11 ,'י‎' => 10 ,'ט‎' => 9 ,'ח‎' => 8 ,'ז‎' => 7,'ו‎' => 6 ,'ה‎' => 5 ,'ד‎' => 4 ,'ג‎' => 3 ,'ב‎' => 2 ,'א‎' => 1]);
		case 'georgian':
			return $style->numerals($num, ['ჵ' => 10000, 'ჰ' => 9000, 'ჯ' => 8000, 'ჴ' => 7000, 'ხ' => 6000, 'ჭ' => 5000, 'წ' => 4000, 'ძ' => 3000, 'ც' => 2000, 'ჩ' => 1000, 'შ' => 900, 'ყ' => 800, 'ღ' => 700, 'ქ' => 600, 'ფ' => 500, 'ჳ' => 400, 'ტ' => 300, 'ს' => 200, 'რ' => 100, 'ჟ' => 90, 'პ' => 80, 'ო' => 70, 'ჲ' => 60, 'ნ' => 50, 'მ' => 40, 'ლ' => 30, 'კ' => 20, 'ი' => 10, 'თ' => 9, 'ჱ' => 8, 'ზ' => 7, 'ვ' => 6, 'ე' => 5, 'დ' => 4, 'გ' => 3, 'ბ' => 2, 'ა' => 1]);
		case 'footnote':
			return $style->toAlpha($num, ['*','†','‡','§','ƒ']);
		case 'disc':
			return '●';
		case 'circle':
			return '○';
		case 'square':
			return '■';
		case 'none':
			return '';
		default:
			return $num;
	}
}

/**
 * Class StyleType
 *
 * functions for converting numbers to different formats defined by css List-Style-Type
 *
 *
 */
class StyleType
{

	/**
	 * Takes a positive integer and returns the equivalent alpha value.
	 *
	 * eg. The below returns a lower case alphabetic value for 90.
	 * toAlpha(90,range('a','z')
	 *
	 * eg. The below returns a lower case greek letters for 128.
	 * toAlpha(128,array('α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω'));
	 *
	 * @param $number int The number to be turned into characters
	 *
	 * @param $alphabet array set of letters
	 *
	 * @return string alpha representation of $number
	 */
	public function toAlpha($number, $alphabet)
	{

		$count = count($alphabet);
		if ($number <= $count) {
			return $alphabet[$number - 1];
		}
		$alpha = '';
		while ($number > 0) {
			$modulo = ($number - 1) % $count;
			$alpha  = $alphabet[$modulo] . $alpha;
			$number = floor((($number - $modulo) / $count));
		}
		return $alpha;
	}


	/**
	 *
	 * Turns a positive integer into a uppercase roman numeral.
	 * @param $number int The value to be converted into a roman numeral
	 * @param $table array An array of roman numerals, (zero normally specified)
	 *
	 * @return string|int   Roman numeral of $number
	 */
	public function numerals($number, $table = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1])
	{
		$return = '';
		while ($number > 0) {
			foreach ($table as $rom => $value) {
				if ($number >= $value) {
					$number -= $value;
					$return .= $rom;
					break;
				}
			}
		}
		return $return;
	}
}
