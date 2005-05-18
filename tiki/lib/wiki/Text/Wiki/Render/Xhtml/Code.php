<?php

class Text_Wiki_Render_Xhtml_Code extends Text_Wiki_Render {
    
    var $conf = array(
        'css'      => null, // class for <pre>
        'css_code' => null, // class for generic <code>
        'css_php'  => null, // class for PHP <code>
        'css_html' => null // class for HTML <code>
    );
    
    /**
    * 
    * Renders a token into text matching the requested format.
    * 
    * @access public
    * 
    * @param array $options The "options" portion of the token (second
    * element).
    * 
    * @return string The text rendered from the token options.
    * 
    */
    
    function token($options)
    {
        $text = $options['text'];
        $attr = $options['attr'];
        $type = strtolower($attr['type']);

        $css      = $this->formatConf(' class="%s"', 'css');
        $css_code = $this->formatConf(' class="%s"', 'css_code');
        $css_php  = $this->formatConf(' class="%s"', 'css_php');
        $css_html = $this->formatConf(' class="%s"', 'css_html');
        
        if ($type == 'php' && substr($options['text'], 0, 5) != '<?php') {
            
            // PHP code example:
            // add the PHP tags
            $text = "<?php\n" . $options['text'] . "\n?>"; // <?php
            
        }

        if ($type == 'c++') {
            $type = 'cpp';
        }

        if (!in_array($type, array('php', 'mysql', 'sql', 'xml', 'dtd', 'javascript', 'css', 'cpp', 'perl', 'python', 'ruby', 'java', 'none'))) {
            $type = 'none';
        }

        if ($type != 'none') {
            require_once 'Text/Highlighter.php';
            require_once 'Text/Highlighter/Renderer/Html.php';
            $options = array(/*'numbers' => HL_NUMBERS_LI,*/ 'tabsize' => 4,);
            $renderer =& new Text_Highlighter_Renderer_HTML($options);
            $hl =& Text_Highlighter::factory($type);
            $hl->setRenderer($renderer);
            $code = '<div class="codelisting">
'.$hl->highlight($text).'
</div>
<style type="text/css">
/*.hl-main {
font-family: monospace, "Courier New", Courier;
font-size:13px;
}*/
.hl-gutter {
background-color: #CCCCCC; padding-right: 10px; 
font-family: monospace, "Courier New", Courier;
font-size:13px;
}
/*.hl-table {
    border: solid 1px #000000; 
}*/
.hl-default {
    color: #000000; 
}
.hl-code {
    color: #7f7f33;
}
.hl-brackets {
    color: #009966;
}
.hl-comment {
    color: #7F7F7F;
}
.hl-quotes {
    color: #00007F;
}
.hl-string {
    color: #7F0000;
}
.hl-identifier {
    color: #000000;
}
.hl-reserved {
    color: #7F007F;
}
.hl-inlinedoc {
    color: #0000FF;
}
.hl-var {
    color: #0066FF;
}
.hl-url {
    color: #FF0000;
}
.hl-special {
    color: #0000FF;
}
.hl-number {
    color: #007F00;
}
.hl-inlinetags {
    color: #FF0000;
}
</style>
';
        } else {
            if (strpos($text, "\n") === false) {
                $eol = "\r";
            } else {
                $eol = "\n";
            }
            $code = '<code>
<ol>';
            foreach (explode($eol, $text) AS $line) {
                $code .= '<li style="white-space: pre">' .htmlentities($line). '</li>';
            }
            $code .= '</ol>
</code>';
        }        
        return "\n$code\n\n";
    }
}
?>