<?PHP

error_reporting(E_ALL);

require_once 'SentenceSegmentor.php';

class BilingualAligner {

    var $l1_sentences = array();
    var $l2_sentences = array();
    var $nodes_at_current_level = array();
    var $nodes_at_next_level = array();
    var $cost_matrix = array();
           
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
	
	function _generate_shortest_path_matrix() {
//        print "-- _generate_shortest_path_matrix: invoked\n";

	    $this->nodes_at_current_level = array("-1m1|-1m1", "-1m2|-1m1", "-1m1|-1m2",
	                                   "-1s1|-1s0", "-1s0|-1s1");
//	    print "-- _generate_shortest_path_matrix: count(\$this->nodes_at_current_level)=".count($this->nodes_at_current_level)."\n";

	    while (count($this->nodes_at_current_level) > 0) {
//	       print "-- _generate_shortest_path_matrix: extending to next level.\n";
	       $this->_extend_shortest_path_matrix_by_one_level();
	    }

/*
        print "-- _generate_shortest_path_matrix: upon exit, \$this->cost_matrix=\n";var_dump($this->cost_matrix);print "\n";
        foreach (array_keys($this->cost_matrix) as $origin) {
           print "-- _generate_shortest_path_matrix: \$this->cost_matrix[$origin]=";var_dump($this->cost_matrix[$origin]);print"\n";
        }
*/
	}
	
	public function _extend_shortest_path_matrix_by_one_level() {
//	    print "-- _extend_shortest_path_matrix_by_one_level:  \$this->nodes_at_current_level=\n";var_dump($this->nodes_at_current_level);print "\n";
	    $this->nodes_at_next_level = array();
	    foreach ($this->nodes_at_current_level as $a_node_to_extend) {
	       $this->_extend_shortest_path_matrix_from_this_node($a_node_to_extend);
	    }
	    
	    // NOTE: Once this method is finished, replace line below by 
	    //       $this->nodes_at_current_level = $this->nodes_at_next_level;
	    //
	    $this->nodes_at_current_level = $this->nodes_at_next_level;
	}
	
	public function _extend_shortest_path_matrix_from_this_node($node_to_extend) {	
//	   print "-- _extend_shortest_path_matrix_from_this_node: \$node_to_extend='$node_to_extend'\n";   
	   $this->_match_current_l1_and_l2_sentences($node_to_extend, 1, 1);
	   $this->_match_current_l1_and_l2_sentences($node_to_extend, 1, 2);
	   $this->_match_current_l1_and_l2_sentences($node_to_extend, 2, 1);
	   $this->_skip_current_l1_or_l2_sentence($node_to_extend, 1, 0);
	   $this->_skip_current_l1_or_l2_sentence($node_to_extend, 0, 1);
     }


     public function _match_current_l1_and_l2_sentences($node_to_extend, 
                          $l1_n_matches, $l2_n_matches) {

//       print "-- _match_current_l1_and_l2_sentences: extending node: \$node_to_extend='$node_to_extend'\n";

       $sentences_this_node = $this->_sentences_at_this_node($node_to_extend);
       $l1_curr_sentence = $sentences_this_node[0];
       $l2_curr_sentence = $sentences_this_node[1];
    
       $new_node = $this->_generate_node_ID($l1_curr_sentence, 'm', $l1_n_matches,
                                            $l2_curr_sentence, 'm', $l2_n_matches);
//       print "-- _match_current_l1_and_l2_sentences: extending to \$new_node='$new_node'\n";

      if ($new_node != null) {
          array_push($this->nodes_at_next_level, $new_node);
          
          // For now, assume same cost for links between nodes.
          $this->cost_matrix[$node_to_extend][$new_node] = 'match_cost';
       } else {
          $this->cost_matrix[$node_to_extend]['END'] = 'goto_end_cost';
       }

//       print "-- _match_current_l1_and_l2_sentences: upon exit, \$this->cost_matrix=\n";var_dump($this->cost_matrix);print "\n";

	   return;
	}
	
	public function _skip_current_l1_or_l2_sentence($node_to_extend, 
                          $l1_n_skips, $l2_n_skips) {
       $sentences_this_node = $this->_sentences_at_this_node($node_to_extend);
       $l1_curr_sentence = $sentences_this_node[0];
       $l2_curr_sentence = $sentences_this_node[1];
    
       $new_node = $this->_generate_node_ID($l1_curr_sentence, 's', $l1_n_skips,
                                            $l2_curr_sentence, 's', $l2_n_skips);

       if ($new_node != null) {
          array_push($this->nodes_at_next_level, $new_node);
       
          // For now, assume same cost for links between nodes.
          $this->cost_matrix[$node_to_extend][$new_node] = 'skip_cost';
       } else {
          $this->cost_matrix[$node_to_extend]['END'] = 'goto_end_cost';       
       }

	   return;

	}
	
	/**
	 * Node info has following format:
	 *    $l1_previous_sentence$l1_operation$11_num_times|$l1_initial_sentence$l2_operation$12_num_times
	 *
	 * Where:
	 *    $l1_previous_sentence: Index of the L1 sentence which was active in previous node.
	 *    
	 *    $l1_operation: Operation performed on L1 sentence when moving to this node.
	 *       'm' = match $l1_previous_sentence to current L2 sentences.
	 *       's' = skip $l1_previous_sentence
	 *
	 *    $l1_num_times: Number of times that the above operation should be performed.
	 *
	 *    Fields for L2 are defined similarly as above.
	 *
	 * Example:
	 *    32m2|35m1: Means that when moving to this node, we are macthing L1 
	 *       sentences 32 and 33 to L2 sentence 35.
	 *    32s1|35s0: Means that when moving to this node, we are skipping L1
	 *       sentence 32, and remaining at L2 sentence 35.
	 */
	
    public function _parse_node_ID($node_id){
//       print "-- _parse_node_ID: \$node_id='$node_id'\n";
       preg_match('/([\-\d]+)([ms])([\d]+)\|([\-\d]+)([ms])([\d]+)/', $node_id, $info);
//       print "-- _parse_node_ID: \$info=\n";var_dump($info);print "\n";;
       return array($info[1], $info[2], $info[3], $info[4], $info[5], $info[6]);
    }    
    
    public function _generate_node_ID($l1_sentence, $l1_operation, $l1_n_times,
                                      $l2_sentence, $l2_operation, $l2_n_times) {
       $id = null;
       if ($l1_sentence + $l1_n_times < count($this->l1_sentences) &&
           $l2_sentence + $l2_n_times < count($this->l2_sentences)) {
           $id = "$l1_sentence$l1_operation$l1_n_times|$l2_sentence$l2_operation$l2_n_times";
       }
       return $id;
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
	
    function _sentences_at_this_node($node_id) {
       $node_info = $this->_parse_node_ID($node_id);
       $l1_sentence = $node_info[0];
       $l1_operation = $node_info[1];
       $l1_advance_by = $node_info[2];
       
       $l2_sentence = $node_info[3];
       $l2_operation = $node_info[4];
       $l2_advance_by = $node_info[5];

       $l1_next_sentence = $l1_sentence + $l1_advance_by;
       $l2_next_sentence = $l2_sentence + $l2_advance_by;
       
       return array($l1_next_sentence, $l2_next_sentence);
    }	
	
}

?>