<?php
require_once 'PHPUnit/Framework.php';
require_once 'BilingualAligner.php';
 
class  BilingualAlignerTest extends PHPUnit_Framework_TestCase
{
    
    public function testThisIsHowYouCreateABilingualAligner() {
       $aligner = new BilingualAligner();
    }
    
    public function testThisIsHowYouAlignTwoTexts() {
       $aligner = new BilingualAligner();
       $en_entences = array();
       $en_entences[0] = "Hello.";
       $en_entencest[0] = "World.";
       $fr_sentences[0] = "Bonjour.";
       $fr_sentences[0] = "Le monde.";
       $aligned_sentences = $aligner->align($en_entences, $fr_sentences);
    }
   
}
?>