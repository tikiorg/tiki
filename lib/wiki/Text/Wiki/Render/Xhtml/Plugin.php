<?php

class Text_Wiki_Render_Xhtml_Plugin extends Text_Wiki_Render {
    
    var $conf = array(
        'css'      => null, // class for <pre>
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
        $func = 'wikiplugin_'.strtolower($options['plugin']);
        if (!function_exists($func)) {
            $file = 'lib/wiki-plugins/'.$func.'.php';
            $paths = explode(PATH_SEPARATOR, ini_get('include_path'));
            $found = false;
            foreach ($paths as $path) {
                if (file_exists($path.DIRECTORY_SEPARATOR.$file)) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                require_once($file);
                if (!function_exists($func)) {
                    return print_r($options, true);
                }
            } else {
                return print_r($options, true);
            }
        }
        return $func($options['text'], $options['attr']);
    }
}
?>