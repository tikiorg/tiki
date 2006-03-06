<?php

function ajax_test_content_type() { return "scalar"; }

function ajax_test_content() {

    $sourceCode = file_get_contents("templates/tiki-ajax_example.tpl");
    
    return "<pre>" . htmlspecialchars($sourceCode) . "</pre>";
}

?>