<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/integrator/integrator.php,v 1.13 2003-11-03 02:47:53 zaufi Exp $
 * 
 * \brief Tiki integrator support class
 *
 */


class TikiIntegrator extends TikiLib
{
    var $c_rep;                         //!< cached value for repository data
    
    function TikiIntegrator($db)
    {
        if (!$db) die("Invalid db object passed to TikiIntegrator constructor");
        $this->db = $db;
        $c_rep = false;
    }
    /// Repository management
    //\{
    /// List all
    function list_repositories($visible_only)
    {
        $cond = ($visible_only == true) ? "where visibility='y'" : '';
        $query = "select * from tiki_integrator_reps ".$cond." order by 'name'";
        $result = $this->query($query);
        $ret = Array();
        while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) $ret[] = $res;
        return $ret;
    }
    /// Add/Update
    function add_replace_repository($repID, $name, $path, $start, $css, $vis, $cachable, $descr)
    {
        $name  = addslashes($name);
        $path  = addslashes($path);
        $start = addslashes($start);
        $css   = addslashes($css);
        $descr = addslashes($descr);
        if (strlen($repID) == 0 || $repID == 0)
            $query = "insert into tiki_integrator_reps(name,path,start_page,css_file,visibility,cachable,description)
                      values('$name','$path','$start','$css','$vis','$cachable','$descr')";
        else
            $query = "update tiki_integrator_reps 
                      set name='$name',path='$path',start_page='$start',
                      css_file='$css',visibility='$vis',cachable='$cachable',
                      description='$descr' where repID='$repID'";
        $result = $this->query($query);
        // Invalidate cached repository if needed
        if (isset($this->c_rep["repID"]) && ($this->c_rep["repID"] == $repID))
            unset($this->c_rep);
    }
    /// Get one entry by ID
    function get_repository($repID)
    {
        // Check if we already cache requested repository info
        if (isset($this->c_rep["repID"]) && ($this->c_rep["repID"] == $repID))
            return $this->c_rep;
        else
        {   // Need to select it...
            $query = "select * from tiki_integrator_reps where repID='$repID'";
            $result = $this->query($query);
            if (!$result->numRows()) return false;
            $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
            $c_rep = $res;
        }
        return $res;
    }
    /// Remove repository and all rules configured for it
    function remove_repository($repID)
    {
        $query = "delete from tiki_integrator_rules where repID=$repID";
        $result = $this->query($query);
        $query = "delete from tiki_integrator_reps where repID=$repID";
        $result = $this->query($query);
        // Check if we remove cached repository
        if (isset($this->c_rep["repID"]) && ($this->c_rep["repID"] == $repID))
            unset($this->c_rep);
    }
    //\}
    /// Rules management
    //\{
    /// List rules for given repository
    function list_rules($repID)
    {
        $query = "select * from tiki_integrator_rules where repID='$repID' order by 'ord'";
        $result = $this->query($query);
        $ret = Array();
        while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) $ret[] = $res;
        return $ret;
    }
    /// Add or update rule for repository
    function add_replace_rule($repID, $ruleID, $ord, $srch, $repl, $type, $case, $rxmod, $descr)
    {
        $srch  = addslashes($srch);
        $repl  = addslashes($repl);
        $rxmod = addslashes($rxmod);
        $descr = addslashes($descr);

        if ($ord == 0)
        {
            $query = "select max(ord) from tiki_integrator_rules where repID='$repID'";
            $ord = $this->getOne($query) + 1;
        }
        if (strlen($ruleID) == 0 || $ruleID == 0)
            $query = "insert into tiki_integrator_rules(repID,ord,srch,repl,type,casesense,rxmod,description)
                      values('$repID','$ord','$srch','$repl','$type','$case','$rxmod','$descr')";
        else
            $query = "update tiki_integrator_rules 
                      set repID='$repID',ord='$ord',srch='$srch',repl='$repl',
                      type='$type',casesense='$case',rxmod='$rxmod',description='$descr'
                      where ruleID='$ruleID'";
        $result = $this->query($query);
    }
    /// Get one entry by ID
    function get_rule($ruleID)
    {
        $query = "select * from tiki_integrator_rules where ruleID='$ruleID'";
        $result = $this->query($query);
        if (!$result->numRows()) return false;
        $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
        return $res;
    }
    /// Remove rule
    function remove_rule($ruleID)
    {
        $query = "delete from tiki_integrator_rules where ruleID=$ruleID";
        $result = $this->query($query);
    }
    /// Apply rule to string
    function apply_rule(&$rep, &$rule, $data)
    {
        // Is there something to search? If no return original data
        if (strlen($rule["srch"]) == 0) return $data;
        // Prepare replace string (subst {path})
        $repl = str_replace('{path}', $rep["path"], $rule["repl"]);
        $repl = str_replace('{repID}', $rep["repID"], $repl);
        //
        $d = $data;
        if ($rule["type"] == 'y')
        {
            // regex rule
            $d = preg_replace('_'.$rule["srch"].'_'.$rule["rxmod"], $repl, $data);
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
    function get_rep_css($rep)
    {
        global $style;
        global $style_base;

        // Return if no CSS file defined for repository
        if (strlen($rep["css_file"]) == 0) return '';
        
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
                                    $rule["description"]);
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
    function get_file($repID, $file, $use_cache = true, $url = '')
    {
        global $tikilib;
        $data = '';
        // Try to get data from cache
        if ($use_cache && $url != '' && $tikilib->is_cached($url))
            $data = $tikilib->get_cache($tikilib->get_cache_id($url));

        // If smth found in cache return it... else try to get it by usual way.
        if ($data != '' && isset($data["data"]) && ($data["data"] != '')) return $data["data"];

        // Get file content to string
        $data = @file_get_contents($file);
        if (isset($php_errormsg))
            $data .= "ERROR: ".$php_errormsg;
        else
        {
            // Now we need to hack this file by applying all configured rules...
            $data = $this->apply_all_rules($repID, $data);
            // Add result to cache
            $tikilib->cache_url($url, $data);
        }
        return $data;
    }
    /// Clear cache for given repository (specified by URL mask)
    function clear_cache($urlmask)
    {
        $query = "delete from tiki_link_cache where url like '$urlmask'";
        $result = $this->query($query);
    }
}

$integrator = new TikiIntegrator($dbTiki);

?>