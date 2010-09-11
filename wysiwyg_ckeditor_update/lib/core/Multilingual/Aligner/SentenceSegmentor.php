<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Multilingual_Aligner_SentenceSegmentor
{

   public function segment($text) {
   
      $sentences_and_separators = preg_split('/([\.\!\?]+|\n\s*\n|\n(?=\*))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
      $sentences = array();
      $ii = 0;
      // Concatenate each sentence with the separator that follows it.
      while ($ii < count($sentences_and_separators)) {
         $this_sentence = $sentences_and_separators[$ii];
         if (strcmp("", $this_sentence) == 0 && 
             $ii == count($sentences_and_separators) - 1) {
             
             // There may be an empty constituent left after last sentence separator
             // Ignore it.
             break;
         }
         if ($ii+1 < count($sentences_and_separators)) {
            $separator = $sentences_and_separators[$ii+1];
            $this_sentence = $this_sentence.$separator;
            $ii++;
         }
         $sentences[] = $this_sentence;
         $ii++;
      }      
      return $sentences;

   }

}
