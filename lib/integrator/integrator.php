<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \file
 * \brief Tiki integrator support class
 */

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

include_once ('lib/tikilib.php');

class TikiIntegrator
{
    var $c_rep;                         //!< cached value for repository data
    
    /// Repository management
    //\{
    /// List all
    function list_repositories($visible_only)
    {
        global $tikilib;
        $values = array();
        $cond = '';
        if ($visible_only == true)
        {
            $cond = "where `visibility`=?";
            $values[] = 'y';
        }
        $query = "select * from `tiki_integrator_reps` ".$cond." order by `name`";
        $result = $tikilib->query($query, $values);
        $ret = array();
        while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) $ret[] = $res;
        return $ret;
    }
		
    /// Add/Update
function add_replace_repository($repID, $name, $path, $start, $css, $vis, $cacheable, $exp, $descr) {
	global $tikilib;
	$parms = array($name, $path, $start, $css, $vis, $cacheable, $exp, $descr);
	if (strlen($repID) == 0 || $repID == 0) {
		$query = "insert into `tiki_integrator_reps` (`name`,`path`,`start_page`,`css_file`, `visibility`,`cacheable`,`expiration`,`description`) values(?,?,?,?,?,?,?,?)";
	} else {
		$query = "update `tiki_integrator_reps` set `name`=?,`path`=?,`start_page`=?,`css_file`=?,`visibility`=?,`cacheable`=?,`expiration`=?,`description`=? where `repID`=?";
		$parms[] = (int) $repID;
	}
	$result = $tikilib->query($query, $parms);
	// Invalidate cached repository if needed
	if (isset($this->c_rep["repID"]) && ($this->c_rep["repID"] == $repID)) {
		unset($this->c_rep);
	}
}
		
    /// Get one entry by ID
    function get_repository($repID)
    {
        global $tikilib;
        // Check if we already cache requested repository info
        if (isset($this->c_rep["repID"]) && ($this->c_rep["repID"] == $repID))
            return $this->c_rep;
        else
        {   // Need to select it...
            $query = "select * from `tiki_integrator_reps` where `repID`=?";
            $result = $tikilib->query($query, array($repID));
            if (!$result->numRows()) return false;
            $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
            $c_rep = $res;
        }
        return $res;
    }
    /// Remove repository and all rules configured for it
    function remove_repository($repID)
    {
        global $tikilib;
        $query = "delete from `tiki_integrator_rules` where `repID`=?";
        $result = $tikilib->query($query, array($repID));
        $query = "delete from `tiki_integrator_reps` where `repID`=?";
        $result = $tikilib->query($query, array($repID));
        // Check if we remove cached repository
        if (isset($this->c_rep["repID"]) && ($this->c_rep["repID"] == $repID))
            unset($this->c_rep);
        // Clear cached pages for this repository
        $this->clear_cache($repID);
    }
    //\}
    /// Rules management
    //\{
    /// List rules for given repository
    function list_rules($repID)
    {
        global $tikilib;
        $query = "select * from `tiki_integrator_rules` where `repID`=? order by `ord`";
        $result = $tikilib->query($query, array($repID));
        $ret = array();
        while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) $ret[] = $res;
        return $ret;
    }
    /// Add or update rule for repository
    function add_replace_rule($repID, $ruleID, $ord, $srch, $repl, $type, $case, $rxmod, $en, $descr)
    {
        global $tikilib;

        if ($ord == 0)
        {
            $query = "select max(`ord`) from `tiki_integrator_rules` where `repID`=?";
            $ord = $tikilib->getOne($query, array($repID)) + 1;
        }
        if (strlen($ruleID) == 0 || $ruleID == 0) {
            $query = "insert into `tiki_integrator_rules`
                     (`repID`,`ord`,`srch`,`repl`,`type`,`casesense`,`rxmod`,`enabled`,`description`)
                      values(?,?,?,?,?,?,?,?,?)";
	    $qparms = array($repID, $ord, $srch, $repl, $type, $case, $rxmod, $en, $descr);
	} else {
	
            $query = "update `tiki_integrator_rules` 
                      set `repID`=?,`ord`=?,`srch`=?,`repl`=?,`type`=?,`casesense`=?,
                      `rxmod`=?,`enabled`=?,`description`=? where `ruleID`=?";
	    $qparms = array($repID, $ord, $srch, $repl, $type, $case, $rxmod, $en, $descr,(int) $ruleID);
	}
        $result = $tikilib->query($query, $qparms);
        // Clear cached pages for this repository
        $this->clear_cache($repID);
    }
    /// Get one entry by ID
    function get_rule($ruleID)
    {
        global $tikilib;
        $query = "select * from `tiki_integrator_rules` where `ruleID`=?";
        $result = $tikilib->query($query, array($ruleID));
        if (!$result->numRows()) return false;
        $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
        return $res;
    }
    /// Remove rule
    function remove_rule($ruleID)
    {
        global $tikilib;
        // Clear cached pages for this repository
        $rule = $this->get_rule($ruleID);
        $this->clear_cache($rule["repID"]);
        // Remove rule
        $query = "delete from `tiki_integrator_rules` where `ruleID`=?";
        $result = $tikilib->query($query, array($ruleID));
    }
    /// Apply rule to string
    function apply_rule(&$rep, &$rule, $data)
    {
        // Is there something to search? If no or rule disabled return original data
        if ((strlen($rule["srch"]) == 0) || ($rule["enabled"] != 'y')) return $data;
        // Prepare replace string (subst {path})
        $repl = str_replace('{path}', $rep["path"], $rule["repl"]);
        $repl = str_replace('{repID}', $rep["repID"], $repl);
        //
        $d = $data;
        if ($rule["type"] == 'y')
        {
            // regex rule. Do replace 'till we have smth to replace (if 'g' modifier present)...
            $g = !(strpos($rule["rxmod"], 'g') === false);
            $mod = str_replace('g', '', $rule["rxmod"]);
            do
            {
                $tmp = $d;
                $d = preg_replace('_'.$rule["srch"].'_'.$mod, $repl, $tmp);
            }
            while ((strcmp($d, $tmp) != 0) && ($g == true));
            unset($tmp);
        }
        else
        {
            // simple str_replace rule
            if ($rule["casesense"] == 'y')
              $d = str_replace($rule['srch'], $repl, $d);
            else
              // \todo Hmmm... where is str_ireplace() ???
              $d = str_replace($rule["srch"], $repl, $d);
        }
        return $d;
    }
    /// Apply all rules in defined order and returns a filtered text
    function apply_all_rules($repID, $data)
    {
        $rules = $this->list_rules($repID);
        // Get repository configuration data
        $rep = $this->get_repository($repID);
        if (is_array($rules))
            foreach ($rules as $rule)
                $data = $this->apply_rule($rep, $rule, $data);
        return $data;
    }
    //\}
    /// Build full path to file inside given repository
    function get_rep_file($rep, $file = '')
    {
        // Is repository path absolute? (start from www root ('/'))
        $p = '';
        if ((substr($rep["path"], 0, 7) == 'http://') 
         || (substr($rep["path"], 0, 8) == 'https://'))
        {
            // It is remote repository -- just copy configured path
            $p = $rep["path"];
        }
        elseif (substr($rep["path"], 0, 1) == '/')
            // Absolute path: prepend web server root
            $p = $_SERVER['DOCUMENT_ROOT'].$rep["path"];
        else
            // Relative Tiki base path: get tiki root and append repository path
            // note: little hack here -- assume that __this__ file placed exactly
            //       at 2nd dir level in Tiki base dir.
            $p = dirname(dirname(dirname(__FILE__))).'/'.$rep["path"];

        return $p.'/'.((strlen($file) > 0) ? $file : $rep["start_page"]);
    }
    /// Return CSS file for given repository
    function get_rep_css($repID)
    {
        global $style;
        global $style_base;

        // Return if no CSS file defined for repository
        $rep = $this->get_repository($repID);
        if (!isset($rep["css_file"]) || strlen($rep["css_file"]) == 0) return '';
        
        $tiki_root = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME']);
        // Fill array of dirs to scan (local filesystem, and web based)
        $dirs = array();
        $dirs[] = array('fs' => $tiki_root."/styles/".$style_base, 'rel' => "styles/".$style_base);
        $dirs[] = array('fs' => $tiki_root."/styles/integrator", 'rel' => "styles/integrator");
        $dirs[] = array('fs' => $tiki_root."/".$rep['path'], 'rel' => "/".$rep['path']);

        // Fill array of files to search
        $ts = preg_replace('|\.css|', '', $style);   // Tiki style w/o '.css' extension
        $is = preg_replace('|\.css|', '', $rep["css_file"]);
        
        $files = array();
        $files[] = $ts.'-'.$rep["css_file"];        // matrix-doxygen.css
        $files[] = $ts.'.'.$rep["css_file"];        // matrix.doxygen.css
        $files[] = $ts.'_'.$rep["css_file"];        // matrix_doxygen.css
        $files[] = $is.'-'.$style;                  // doxygen-matrix.css
        $files[] = $is.'.'.$style;                  // doxygen.matrix.css
        $files[] = $is.'_'.$style;                  // doxygen_matrix.css
        $files[] = $rep["css_file"];                // doxygen.css

        // Make full list of files to search (combine all dirs with all files)
        $candidates = array();
        foreach ($dirs as $dir) foreach ($files as $file)
            $candidates[] = array('fs' => $dir['fs'].'/'.$file, 'rel' => $dir['rel'].'/'.$file);

        // Search for CSS file
        foreach ($candidates as $candidate)
        {
          if (file_exists($candidate['fs'])) return $candidate['rel'];
        }
        // Nothing found...
        return '';
    }
    /**
     * \brief Copy rules from one repository to another
     *
     * Variant #1 (stupid but working):
     *  a) get rules list for repository 1
     *  b) fix repID for all elements of list
     *  c) insert new rules for repository 2
     *
     * Variant #2 (better but need smth special):
     *  a) create temporary type=hash table ... (stored in memory and
     *     auto deleted on connection close -- is all DBs support this?)
     *     a.1) create table name like original_table_name;
     *          work for MySQL > 4.1 (smth else?)
     *  b) select into it rules of rep 1
     *  c) fix it
     *  d) copy to main rules table
     *
     */
    function copy_rules($srcID, $dstID)
    {
        $rules = $this->list_rules($srcID);
        // 
        foreach ($rules as $rule)
            $this->add_replace_rule($dstID, 0, $rule["ord"], $rule["srch"], $rule["repl"],
                                    $rule["type"], $rule["casesense"], $rule["rxmod"],
                                    $rule["enabled"], $rule["description"]);
    }
    /**
     * \brief Filter file
     * Returns ready for integration data from given file. Cache used if neccessary.
     * \param $repID -- repository ID to get file from
     * \param $file -- file name to return data from
     * \param $use_cache -- use or not cache :)
     * \param $url -- URL to associate with cached data
     *
     * \note File is not checked for existence...
     */
    function get_file($repID, $file, $use_cache = 'y', $url = '')
    {
        global $tikilib;
        $data = '';
        // Try to get data from cache
        $cacheId = 0;
        if ($use_cache == 'y' && $url != '' && $tikilib->is_cached($url))
            $data = $tikilib->get_cache($cacheId = $tikilib->get_cache_id($url));

        $rep = $this->get_repository($repID);

        // If smth found in cache return it... else try to get it by usual way.
        if ($data != '' && isset($data["data"]) && ($data["data"] != '')
         && ($rep["expiration"] > 0 ? (time() - $data["refresh"]) < $rep["expiration"] : true))
            return $data["data"];

        // Get file content to string
        if ( preg_match('#^https?://#', $file) ) {
		$data = $tikilib->httprequest($file);
	} else {
		$data = @file_get_contents($file);
	}
        if (isset($php_errormsg))
            $data .= "ERROR: ".$php_errormsg;
        else
        {
            // Now we need to hack this file by applying all configured rules...
            $data = $this->apply_all_rules($repID, $data);
            // Add result to cache (remove prev if needed)
            if ($cacheId != 0) $tikilib->remove_cache($cacheId);
            $tikilib->cache_url($url, $data);
        }
        return $data;
    }
    /// Clear cache for given repository
    function clear_cache($repID)
    {
        global $tikilib;
        // Delete all cached URLs with word 'integrator' in a script
        // name and 'repID' parameter equal to function arg...
        $query = "delete from `tiki_link_cache` where `url` like ?";
        $result = $tikilib->query($query,
            array($tikilib->httpPrefix()."/%integrator%.php?%repID=".$repID."%"));
    }
    /// Clear cache of given file for given repository
    function clear_cached_file($repID, $file)
    {
        global $tikilib;
        // Delete all cached URLs with word 'integrator' in a script
        // name and 'repID' parameter equal to function arg...
        $query = "delete from `tiki_link_cache` where `url` like ?";
        $result = $tikilib->query($query,
            array($tikilib->httpPrefix()."/%integrator%.php?repID=".$repID.(strlen($file) > 0 ? "&file=".$file : '')));
    }
}
