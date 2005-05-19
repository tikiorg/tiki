<?php

if (version_compare(phpversion(), '5.0') === -1 && !function_exists('clone')) {
    eval('
    function clone($object)
    {
        // Sanity check
        if (!is_object($object)) {
            user_error(\'clone() __clone method called on non-object\', E_USER_WARNING);
            return;
        }

        // Use serialize/unserialize trick to deep copy the object
        $object = unserialize(serialize($object));

        // If there is a __clone method call it on the "new" class
        if (method_exists($object, \'__clone\')) {
            $object->__clone();
        }

        return $object;
    }');
}

// $Id: Toc.php,v 1.2 2005-05-19 20:33:14 papercrane Exp $

class Text_Wiki_Render_Xhtml_Toc extends Text_Wiki_Render {
    
    var $conf = array(
        'css_list' => null,
        'css_item' => null,
        'title' => '<strong>Table of Contents</strong>',
        'div_id' => 'toc'
    );
    
    var $min = 1;
    
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
        // type, id, level, count, attr
        
        $html = '<div';
            
        $css = $this->getConf('css_list');
        if ($css) {
            $html .= " class=\"$css\"";
        }
            
        $div_id = $this->getConf('div_id');
        if ($div_id) {
            $html .= " id=\"$div_id\"";
        }
            
        $html .= '>'.$this->getConf('title');

        $headings = array();
        $heading = array();
        foreach ($this->wiki->getTokens(array('Heading'), true) as $key => $val) {
            $heading[$val[1]['type']] = array('token' => $val, 'id' => $key);
            switch ($val[1]['type']) {
            case 'start':
                break;
            case 'end':
                $headings[] = $heading;
                $heading = array();
                break;
            }
        }
        foreach ($headings as $heading) {
            $start = strpos($this->wiki->source, $this->wiki->delim.$heading['start']['id'].$this->wiki->delim)
                + strlen($this->wiki->delim.$heading['start']['id'].$this->wiki->delim);
            $end = strpos($this->wiki->source, $this->wiki->delim.$heading['end']['id'].$this->wiki->delim);
            /*//echo htmlentities($this->wiki->source).'<br/><br/>';
            echo $heading['start']['id'].' '.$heading['end']['id'].'<br/>';
            echo $start.' '.$end.'<br/>';
            echo substr($this->wiki->source, $start, 5).'<br/>';
            echo substr($this->wiki->source, $end - 5, 5).'<br/>';
            echo substr($this->wiki->source, $start, $end - $start).'<br/>';*/
            $text = substr($this->wiki->source, $start, $end - $start);

            $wiki = clone($this->wiki);
            $wiki->source = $text;
            $text = html_entity_decode($wiki->render('Xhtml'));

            //$text = $heading['start']['token'][1]['text'];

            $html .= '<div';
            
            $css = $this->getConf('css_item');
            if ($css) {
                $html .= " class=\"$css\"";
            }
            
            $html .= ' style="margin-left: '.($heading['start']['token'][1]['level'] - $this->min).'em;">'.
                '<a href="#'.$heading['start']['token'][1]['id'].'">'.$text."</a></div>\n";
        }
        
        $html .= "</div>\n";

        return $html;
    }
}
?>