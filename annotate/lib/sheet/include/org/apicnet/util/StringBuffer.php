<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/**
* @version 2.0
* @author Nicolas BUI <nbui@wanadoo.fr>
* 
* This source file is part of JPHP Library Project.
* Copyright: 2002 Vitry sur Seine/FRANCE
*
* The latest version can be obtained from:
* http://www.jphplib.org
*/

class StringBuffer extends Object{
	var $str;
	
	/**
	* create an instance of a StringBuffer
	* @param	string|core.StringBuffer	string source
	* @access	public
	*/
	function StringBuffer( $str = '' ) {
		$this->setString($str);
	}
	
	function setString($str){
		$this->str  = $str;
	}
	function prepend($str){
		$this->str = ( StringBuffer::validClass($str) ? $str->toString() : $str ) . $this->str;
	}
	
	function append($str) {
		$this->str .= ( StringBuffer::validClass($str) ? $str->toString() : $str );
	}
	
	function toString() {
		return $this->str;
	}
	
	function length() {
		return strlen( $this->str );
	}
	
	function charAt($index) {
		if (is_integer($index) && $index>=0 && $index<$this->length()) {
			return($this->str[$index]);
		}
		return;
	}
	
	function  insertAt($index, $string){
		if (StringBuffer::validClass($string)) {
			$string = $string->toString();
		}
		settype($index, 'integer');
		if ($index<=0){
			return new StringBuffer($string . $this->str);
		} else if ($index>=$this->length()){
			return new StringBuffer($this->str . $string);
		} else {
			$str_a = $this->substring(0, $index);
			$str_b = $this->substring($index);
			return new StringBuffer($str_a->toString() . $string . $str_b->toString());
		}
	}
	
	function remove($from, $to){
		settype($from, 'integer');
		settype($to, 'integer');
		if ($from>$to){
			$a = $from;
			$from = $to;
			$to = $a;
		}
		$string = $this->str;
		if ($from<=0 && $to>=$this->length()){
			return FALSE;
		} else if ($from<=0 && $to<$this->length()){
			return new StringBuffer($this->substring($to));
		} else if ($from>0 && $to>=$this->length()){
			return new StringBuffer($this->substring(0, $from));
		} else if($from>0 && $to<$this->length()){
			$str_a = $this->substring(0, $from);
			$str_b = $this->substring($to);
			return new StringBuffer($str_a->toString() . $str_b->toString());
		}
		return FALSE;
	}
	
	/**
	* extract a part of a string using index start to index stop
	* @param $source string		the source string
	* @param $from string 			start index(inlude) to extract
	* @param $to string 			end index (exlude) to extract
	* @return string 				the part of the string that have been extracted
	**/
	function substring($from, $to = -1) {
		$result = '';
		if ($to>=$from){
			$result = substr($this->str, $from, ($to-$from));
		} else {
			$result = substr($this->str, $from);
		}
		
		return new StringBuffer($result);
	}
	
	/**
	* extract a part of a string using index start to number length
	* @param $source string		the source string
	* @param $start string 		start index(inlude) to extract
	* @param $length string 		numbers of characters to be extracted from the start index
	* @return core.StringBuffer 	the part of the string that have been extracted
	**/
	function substr($start, $length = 0) {
		$result = '';
		if ($length>$start){
			$result = substr($this->str, $start, $length);
		} else {
			$result = substr($this->str, $start);
		}
		
		return new StringBuffer($result);
	}
	
	function leftTrim(){
		return new StringBuffer(ltrim($this->toString()));
	}
	
	function rightTrim(){
		return new StringBuffer(chop($this->toString()));
	}
	
	function trimAll(){
		return new StringBuffer(trim($this->toString()));
	}
	
	function indexOf($str, $offset = 0){
		$str = StringBuffer::toStringBuffer($str);
		if (!isset($str) || $offset>=$this->length()) {
			return -1;
		}
		$pos = strpos($this->toString(), $str->toString(), $offset);
		if ($pos === FALSE) {
			return -1;
		} else {
			return $pos;
		}
	}
	
	function lastIndexOf($str){
		$res = $this->allIndexOf($str);
		if ($res!="" && is_array($res) && count($res)>0){
			return $res[count($res)-1];
		}
		return -1;
	}
	
	function allIndexOf($str){
		$res = array();
		$pos = 0;
		$offset = 0;
		while(($pos = $this->indexOf($str, $offset))>=0){
			$offset = $pos+strlen($str);
			$res[] = $pos;
		}
		return $res;
	}
	
	function countAllIndexOf($str){
		return count($this->allIndexOf($str));
	}
	
	function endsWith($value, $ignorecase=FALSE){
		$value = StringBuffer::toStringBuffer($value);
		$pattern = '/('.str_replace("/","\\/",preg_quote($value->toString())).')$/'.($ignorecase==TRUE?'i':'');
		return @preg_match($pattern, $this->str)>0;
	}
	
	function startsWith($value, $ignorecase=FALSE){
		$value = StringBuffer::toStringBuffer($value);
		return @preg_match('/^('.str_replace("/","\\/",preg_quote($value->toString())).')/'.($ignorecase==TRUE?'i':''), $this->str)>0;
	}
	
	function equalsIgnoreCase($str){
		return $this->equals($str, TRUE);
	}
	
	function equals($str, $ignorecase = FALSE){
		$str = StringBuffer::toStringBuffer($str);
		$pattern = '/^('.preg_quote($str->toString()).')$/';
		if ($ignorecase){
			$pattern .= 'i';
		}
		return @preg_match($pattern, $this->str)>0;
	}
	
	/**
	* filter a string to make it all lower case
	* @param $source string		the source string
	* @return StringBuffer			the new lower case string
	**/
	function toLowerCase($source=""){
		return new StringBuffer(preg_replace('/([À-Ý]|[A-Z])/e','chr(ord(\'\\1\')+32)', $this->str));
	}
	
	/**
	* filter a string to make it all upper case
	* @param $source string		the source string
	* @return string 				the new upper case string
	**/
	function toUpperCase($source=""){
		return new StringBuffer(preg_replace('/([à-ý]|[a-z])/e','chr(ord(\'\\1\')-32)', $this->str));
	}
	
	/**
	* replace an substring with a new subtring in a string
	* @param $source string		the source string to perform replace
	* @param $search string 		occurence to search for
	* @param $replace string 		string use to replace the occurences found
	* @return string 				the new string resulting from the replacement
	**/
	function replace($oldstr, $newstr){
		return new StringBuffer(str_replace($oldstr, $newstr, $this->str));
	}
	
	function loadFromStream($filename){
		$buffers = new StringBuffer();
		$file = NULL;
		if (File::validClass($filename)){
			$file = $filename;
		} else {
			$file = new File($filename);
		}
		$filereader = new FileReader($file);
		while(($c = $filereader->read())){
			$buffers->append($c);
		}
		$this->str = $buffers->toString();
	}
	
	function toArray($delim, $source = ''){
		$s = ($source!=""?$source:$this->toString());
		if (StringBuffer::validClass($source)){
			$s = $s->toString();
		}
		$key = explode($delim, $s);
		return $key;
	}
	
	function split($delim, $source = ''){
		return $this->toArray($delim, $source);
	}
	
	/**
	* @return core.StringBuffer
	**/
	function keepSpaceOnly(){
		$s = preg_replace('/(\r|\n|\t|\s{2,})/',' ',$this->str);
		$s = preg_replace('/(\s+)/',' ',$s);
		return new StringBuffer($s);
	}
	
	/**
	* remove all accent from a string 
	* @return the processed string without accent
	**/
	function removeAccents(){
		return new StringBuffer(strtr('AAAAAAaaaaaaOOOOO0ooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn','ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ', $this->str));
	}
	
	/**
	* calculate the soundex value of the string
	* @param $lang string			calculate soundex using a specific language specification (actually french/us only)
	* @param $source string 		string to calculate the soundex 
	* @return string 				the soundex value
	**/
	function createSoundex($lang='en'){ 
		$s = $this->toString();
		if ($lang=='fr'){
			if (strlen($s)>0){
				$s = StringBuffer::removeAccents($s);
				$s = StringBuffer::keepSpaceOnly($s);
				$s = StringBuffer::toUpperCase($s);
				$s = preg_replace('/(.)\\1/', '\\1', $s); 
				$first_letter = $s[0];
				$s = ereg_replace('AEIOUYHW', '', $s); 
				$s = strtr('112223345567788899', 'BPCKQDTLMNRGJXZSFV', $s); 
				$s = $first_letter . $s;
				if (strlen($s)<4){
					$s = $s . str_repeat('0', 4 - strlen($s));
				} else {
					$s = substr($s, 0, 4);
				}
				return new StringBuffer($s);
			}
			return FALSE;
		} else {
			return new StringBuffer(soundex($s));
		}
	}
	
	function match($pattern){
		$result = array();
		preg_match($pattern, $this->toString(), $result);
		return $result;
	}
	
	function toStringBuffer($object){
		if (StringBuffer::validClass($object)) {
			return $object;
		}
		if (isset($object) && $object!=''){
			if (is_object($object) && method_exists($object, 'tostring')){
				return new StringBuffer($object->toString());
			} else {
				return new StringBuffer($object);
			}
		}
		return NULL;
	}
	
	function generateKey($length = 10, $keytype = ""){
		$length = (int)$length;
		if ($length<=0) {
			return FALSE;
		}
		mt_srand((double)microtime()*1000000);
		$key = "";
		while(strlen($key)!=$length){
			$c = mt_rand(0,2);
			switch($keytype){
				case 'number':
						$key .= mt_rand(0,9);
						break;
				case 'ustring':
						$key .= chr(mt_rand(65,90));
						break;
				case 'lstring':
						$key .= chr(mt_rand(97,122));
						break;
				case 'mixstring':
						if ($c==0){
							$key .= chr(mt_rand(65,90));
						} elseif ($c==1){
							$key .= chr(mt_rand(97,122));
						}
						break;
				default:
						if ($c==0){
							$key .= chr(mt_rand(65,90));
						} elseif ($c==1){
							$key .= chr(mt_rand(97,122));
						} else {
							$key .= mt_rand(0,9);
						}
			}
		}
		return $key;
	} 
	
	
	function intValue(){
		$value = $this->toString();
		settype($value, 'integer');
		return $value;
	}
	
	function boolValue(){
		return (bool)$this->str;
	}
	
	function charToHex($char){
		return dechex(ord($char));
	}
	
	function hexToChar($hex){
		return chr(hexdec($hex));
	}
	
	function validClass($object){
		return Object::validClass($object, 'stringbuffer');
	}

}
