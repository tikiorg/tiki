<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class bablotron extends TikiLib {
	var $words;

	var $lan;
	var $tbl;

	function bablotron($db, $lan) {
		$this->TikiLib($db);
		$this->lan = preg_replace('/-/','_',$lan);
		$this->tbl = 'babl_words_' . $this->lan;
	}

	function spellcheck_text($text, $threshold = 5) {
		$words = preg_split("/\s/", $text);

		$results = array();

		foreach ($words as $word) {
			if (!$this->word_exists($word)) {
				$results[$word] = $this->find_similar_words($word, $threshold);
			}
		}

		return $results;
	}

	function spellcheck_word($word, $threshold = 5) {
		$results = array();

		if (!$this->word_exists($word)) {
			$results[$word] = $this->find_similar_words($word, $threshold);
		}

		return $results;
	}

	function quick_spellcheck_text($text, $threshold = 5) {
		$words = preg_split("/\s/", $text);

		$results = array();

		foreach ($words as $word) {
			if (!$this->word_exists($word)) {
				$results[] = $word;
			}
		}

		return $results;
	}

	function find_similar_words($word, $threshold) {
		$similar = array();

		$word = addslashes(trim($word));
		$sndx = substr($word, 0, 2);
		$query = "select `word` from `{$this->tbl}` where `di`=?";
		@$result = $this->query($query, array($sndx));

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$tword = $res["word"];

			$lev = levenshtein($tword, $word);

			if (count($similar) < $threshold) {
				$similar[$tword] = $lev;

				asort ($similar);
			} else {
				// If the array is full then if the lev is better than the worst lev
				// then update
				$keys = array_keys($similar);

				$last_key = $keys[count($keys) - 1];

				if ($lev < $similar[$last_key]) {
					unset ($similar[$last_key]);

					$similar[$tword] = $lev;
					asort ($similar);
				}
			}
		}

		return $similar;
	}

	function word_exists($word) {
		$word = addslashes(trim($word));
		$query = "select `word` from `{$this->tbl}` where `word`=? or `word`=?";
		@$result = $this->query($query,array($word,strtolower($word)));

		return $result->numRows();
	}

	function find_similar($word, $threshold) {
	}
}

?>
