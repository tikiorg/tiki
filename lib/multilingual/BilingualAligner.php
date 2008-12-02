<?PHP

require_once 'SentenceSegmentor.php';

class BilingualAligner {

    var $l1_sentences = array();
    var $l2_sentences = array();
    
	public function align($l1_sentences, $l2_sentences) {
	    return;
	}
	
	public function _segment_into_sentences($text) {
	    $segmentor = new SentenceSegmentor();
	    $sentences = $segmentor->segment($text);
	    return $sentences;
	}
	
	public function _segment_parallel_texts_to_sentences($l1_text, $l2_text) {
	   $this->l1_sentences = $this->_segment_into_sentences($l1_text);
	   $this->l2_sentences = $this->_segment_into_sentences($l2_text);
	}
	
	public function _sentence_length_delta($l1_sentence, $l2_sentence) {
	   $l1_length = strlen($l1_sentence);
	   $l2_length = strlen($l2_sentence);
	   $delta = 0;
	   if ($l1_length != 0) {
	   	  $delta = abs($l1_length - $l2_length)/$l1_length;
	   } else {
	      if ($l2_length == 0) {
	         $delta = 0;
	      } else {
	         $delta = 1;
	      }
	   } 
	   return $delta;
	}
	
}

?>