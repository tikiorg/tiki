<?php

class bablotron {
  var $words;
  var $lan;
  var $db;
  
 
  function bablotron($db,$lan)
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
    $this->lan = $lan;
  }
  
  function sql_error($query, $result) 
  {
    return;
  }

  function spellcheck_text($text, $threshold=5) 
  {
    $words = preg_split("/\s/",$text);
    $results = Array();
    foreach ($words as $word)
    {
      if(!$this->word_exists($word)) {
        $results[$word] = $this->find_similar_words($word,$threshold);
      }
    }
    return $results;
  }
  
  function spellcheck_word($word, $threshold=5) 
  {
    $results = Array();
    if(!$this->word_exists($word)) {
      $results[$word] = $this->find_similar_words($word,$threshold);
    }
    return $results;
  }
  
  function quick_spellcheck_text($text, $threshold=5) 
  {
    $words = preg_split("/\s/",$text);
    $results = Array();
    foreach ($words as $word)
    {
      if(!$this->word_exists($word)) {
        $results[] = $word;
      }
    }
    
    return $results;
  }
  
  
  function find_similar_words($word,$threshold) 
  {
    $similar = Array();
    $tbl = 'babl_words_'.$this->lan;
    $word=addslashes(trim($word));
    $sndx = substr($word,0,2);
    $query = "select word from $tbl where di = '$sndx'";
    @$result = $this->db->query($query);
    if(DB::isError($result)) return Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $tword = $res["word"];
      $lev = levenshtein($tword,$word);
      if(count($similar) < $threshold ) {
        $similar[$tword] = $lev;
        asort($similar);
      } else {
        // If the array is full then if the lev is better than the worst lev
        // then update
        $keys = array_keys($similar);
        $last_key = $keys[count($keys)-1];
        if($lev < $similar[$last_key]) {
          unset($similar[$last_key]);
          $similar[$tword] = $lev;
          asort($similar);
        }
      }
      
    }
    return $similar;
  }
  
  function word_exists($word)
  {
    $tbl = 'babl_words_'.$this->lan;
    $word=addslashes(trim($word));
    $query = "select word from $tbl where word='$word'";
    @$result = $this->db->query($query);
    if(DB::isError($result)) return true;
    return $result->numRows();
    
  }
  
  function find_similar($word, $threshold)
  {
  
  }
  
  

}
/*
require_once('DB.php');
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki';
$dsn = "mysql://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = DB::connect($dsn);
if (DB::isError($dbTiki)) {        
  die ($dbTiki->getMessage());
} 

$b = new bablotron($dbTiki,'en');
$b->spellcheck_text("this is atest of some interestng text that may be ok or not but doesn't matter
now we can writ more text an some inforation that can be usefl for our purposes");
*/
?>