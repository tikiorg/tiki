<?php
require_once 'amazon_types.php';
require_once 'amazon_modes.php';

class Amazon {
    var $_devkey;
    var $_associateid;
    var $_clientType;
    var $_client = null;
    var $_pagesize = 10; // amazon doesn't let us set this
    
    function Amazon($type, $devkey, $associateid='webservices-20') {
        $this->_devkey = $devkey;
        $this->_associateid = $associateid;
        $this->_clientType = $type;
    }

    function _getClient()
    {
        if ($this->_client) return;
        switch($this->_clientType) {
        case 'soap':
            require_once 'amazon_soap.php';
            $this->_client = new AmazonSearchService($this->_devkey,$this->_associateid);
            break;
            
        /* we could develop the REST interface as well,
           and using the same API as the amazon soap class,
           offer the choice of methods for making the data
           retrieval. */
        }
    }
    
    function _selectOption($name, $value, $select)
    {
        $selected=$select?' selected':'';
        return "<option value='$value'$selected>$name</option>\n";
    }
    
    function _doListItem($listname,$value, $name, &$info) {
        $select = isset($info[$listname]) && $info[$listname] == $value;
        return $this->_selectOption($name,$value,$select);
    }
    
    function SearchForm($info=array())
    {
        global $amazon_modes;

        $modelist = '';
        foreach($amazon_modes as $name=>$mode) {
            $modelist .= $this->_doListItem('search_mode',$mode,$name,$info);
        }
 
        $classlist = $this->_doListItem('search_class','keyword','Keywords',$info);
        $classlist .= $this->_doListItem('search_class','asin','ASIN',$info);
        $classlist .= $this->_doListItem('search_class','upc','UPC',$info);
        $classlist .= $this->_doListItem('search_class','artist','Artist',$info);
        $classlist .= $this->_doListItem('search_class','actor','Actor',$info);
        $classlist .= $this->_doListItem('search_class','author','Author',$info);
        $classlist .= $this->_doListItem('search_class','director','Director',$info);
        $classlist .= $this->_doListItem('search_class','manufacturer','Manufacturer',$info);
        $classlist .= $this->_doListItem('search_class','similarity','Similarity (ASIN)',$info);

        $typelist = $this->_doListItem('search_type','lite','Summary',$info);
        $typelist .= $this->_doListItem('search_type','heavy','Detailed',$info);

        $searchwords = isset($info['search_words'])?htmlentities(stripslashes($info['search_words']),ENT_QUOTES):'';
echo <<< EOF
        <table border='1'><tr><td>
        Amazon Search<br/>
        <form action='{$_SERVER["PHP_SELF"]}' method='post'>
        <select name='search_class'>
        $classlist
        </select>
        <input type='text' name='search_words' value="$searchwords"/>
        <input type='hidden' name='search_page' value='1'/>
        <select name='search_mode'>
        $modelist
        </select>
        <select name='search_type'>
        $typelist
        </select>
        <input type='submit'/></form></td></tr></table>
EOF;
    }
    
    function _pageLink($page, &$info)
    {
        $q = array();
        $info['search_page'] += $page;
        foreach($info as $k=>$v) {
            $q[] = "$k=".htmlentities(stripslashes($v),ENT_QUOTES);
        }
        $q = join($q,'&');
        if ($page == 1) $dir = "Next >>>";
        else $dir = "<<< Prev";
        return "<a href='{$_SERVER['PHP_SELF']}?$q'>$dir</a>";
    }
    
    function Search(&$info)
    {
        $search_function = "{$info['search_class']}SearchRequest";
        $search_class = "{$info['search_class']}Request";
        if (!$this->_client) $this->_getClient();
        if ($this->_client &&
            method_exists($this->_client,$search_function)) {
            
            if (!isset($info['search_devtag'])) $info['search_devtag'] = $this->_devkey;
            if (!isset($info['search_tag'])) $info['search_tag'] = $this->_associateid;
            
            $searchObj = new $search_class($info);
            
            $ProductInfo = call_user_func(
                            array(&$this->_client,$search_function),
                            $searchObj);
            
            if (PEAR::isError($ProductInfo)) {
                $errmsg = $ProductInfo->getMessage()."<br/>\n".$ProductInfo->getUserInfo()."<br/>\n";
                print $errmsg;
                print $this->_client->client->wire;
                return false;
            } else {
                $ProductInfo->displayList($info['search_type']);
                $pl = array();
                if (!isset($info['search_page'])) $info['search_page'] = $searchObj->page;
                if ($searchObj->page > 1)
                    $pl[] = $this->_PageLink(-1,$info);
                if (isset($searchObj->page) &&
                    count($ProductInfo->Details) == $this->_pagesize) {
                    $pl[] = $this->_PageLink(1,$info);
                }
                foreach($pl as $link) {
                    echo $link.' &nbsp; ';
                }
                echo "<br/>\n";
                return true;
            }
        } else {
            print "Invalid Search Type";
        }
        return false;
    }
}


?>
