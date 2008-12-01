<?PHP


class SentenceSegmentor {

   public function segment($text) {
   
//      $result = split('\s*\.\s*', $text);
      $result = preg_split('([\.\!\?])', $text, -1, PREG_SPLIT_DELIM_CAPTURE);      
      return $result;

   }
}

