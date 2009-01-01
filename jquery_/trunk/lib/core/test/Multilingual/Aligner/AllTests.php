<?php

class Multilingual_Aligner_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Multilingual_Aligner');
        $suite->addTestSuite('Multilingual_Aligner_BilingualAlignerTest');
        $suite->addTestSuite('Multilingual_Aligner_SentenceSegmentorTest');
        $suite->addTestSuite('Multilingual_Aligner_ShortestPathFinderTest');
        return $suite;
    }
}

