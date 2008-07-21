<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/mail/maillib.php,v 1.2 2007-03-02 19:49:19 luciash Exp $

/* Common shared mail functions */

/* 
 * function encode_headers()
 *
 * Encode non-ASCII email headers for mail() function to display 
 * them properly in email clients.
 * Original code by <gordon at kanazawa-gu dot ac dot jp>.
 * See 'User Contributed Notes' at
 * http://php.benscom.com/manual/en/function.mail.php
 * Rewritten for Tikiwiki by <luci at sh dot ground dot cz>
 *
 * For details on Message Header Extensions see
 * http://www.faqs.org/rfcs/rfc2047.html
 */

$charset = 'utf-8'; // What charset we do use in Tiki
$in_str = '';

function encode_headers($in_str, $charset) {
   $out_str = $in_str;
   if ($out_str && $charset) {

       // define start delimimter, end delimiter and spacer
       $end = "?=";
       $start = "=?" . $charset . "?b?";
       $spacer = $end . "\r\n" . $start;

       // determine length of encoded text within chunks
       // and ensure length is even
       $length = 71 - strlen($spacer); // no idea why 71 but 75 didn't work
       $length = floor($length/2) * 2;

       // encode the string and split it into chunks
       // with spacers after each chunk
       $out_str = base64_encode($out_str);
       $out_str = chunk_split($out_str, $length, $spacer);

       // remove trailing spacer and
       // add start and end delimiters
       $spacer = preg_quote($spacer);
       $out_str = preg_replace("/" . $spacer . "$/", "", $out_str);
       $out_str = $start . $out_str . $end;
   }
   return $out_str;
}// end function encode_headers
?>
