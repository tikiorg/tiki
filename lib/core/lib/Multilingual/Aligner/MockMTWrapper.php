<?php

require_once 'SentenceAlignments.php';
require_once 'lib/core/lib/Multilingual/MachineTranslation/GoogleTranslateWrapper.php';

class Multilingual_Aligner_MockMTWrapper extends Multilingual_Aligner_SentenceAlignments
{
	public function getTranslationInOtherLanguage($source_lng_sentence, $source_lng) {
	
		if($source_lng == 'en')
			$k = 1;
		elseif ($source_lng == 'fr')
			$k = 0;

		foreach ($this->alignment_table as $key=>$val) {
			if ($k==1) {
				if ($key==$source_lng_sentence)
					return $val;
			} else {
				if ($val==$source_lng_sentence)
					return $key;
			}
		}
		return 'NULL';
	}

}//class ends
