<?php

class Multilingual_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('MultilingualSuite');

        $suite->addTest(Multilingual_Aligner_AllTests::suite());
        $suite->addTest(Multilingual_MachineTranslation_AllTests::suite());

        return $suite;
    }
}

?>
