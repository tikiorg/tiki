<?php

class Multilingual_Aligner_SentenceSegmentor {

   public function segment($text) {
   
      $sentences_and_separators = preg_split('/([\.\!\?]+|\n\s*\n|\n(?=\*))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
      $sentences = array();
      $ii = 0;
      // Concatenate each sentence with the separator that follows it.
      while ($ii < sizeof($sentences_and_separators)) {
         $this_sentence = $sentences_and_separators[$ii];
         if (strcmp("", $this_sentence) == 0 && 
             $ii == sizeof($sentences_and_separators) - 1) {
             
             // There may be an empty constituent left after last sentence separator
             // Ignore it.
             break;
         }
         if ($ii+1 < sizeof($sentences_and_separators)) {
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

