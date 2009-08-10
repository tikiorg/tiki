<?php

class Multilingual_MachineTranslation_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Multilingual_MachineTranslation');
        $suite->addTestSuite('Multilingual_MachineTranslation_GoogleTranslateWrapperTest');
        return $suite;
    }
}

