<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/integrator/integrator.php,v 1.7 2003-10-17 16:17:24 zaufi Exp $
 * 
 * \brief Tiki integrator support class
 *
 */


class TikiIntegrator extends TikiLib
{
    function TikiIntegrator($db)
    {
        if (!$db) die("Invalid db object passed to TikiIntegrator constructor");
        $this->db = $db;
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
    function add_replace_repository($repID, $name, $path, $start, $css, $vis, $descr)
    {
        $name  = addslashes($name);
        $path  = addslashes($path);
        $start = addslashes($start);
        $css   = addslashes($css);
        $descr = addslashes($descr);
        if (strlen($repID) == 0 || $repID == 0)
            $query = "insert into tiki_integrator_reps(name,path,start_page,css_file,visibility,description)
                      values('$name','$path','$start','$css','$vis','$descr')";
        else
            $query = "update tiki_integrator_reps 
                      set name='$name',path='$path',start_page='$start',
                      css_file='$css',visibility='$vis',description='$descr'
                      where repID='$repID'";
        $result = $this->query($query);
    }
    /// Get one entry by ID
    function get_repository($repID)
    {
        $query = "select * from tiki_integrator_reps where repID='$repID'";
        $result = $this->query($query);
        if (!$result->numRows()) return false;
        $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
        return $res;
    }
    /// Remove repository and all rules configured for it
    function remove_repository($repID)
    {
        $query = "delete from tiki_integrator_rules where repID=$repID";
        $result = $this->query($query);
        $query = "delete from tiki_integrator_reps where repID=$repID";
        $result = $this->query($query);
    }
    //\}
    /// Rules management
    //\{
    /// List rules for given repository
    function list_rules($repID)
    {
        $query = "select * from tiki_integrator_rules where repID='$repID'";
        $result = $this->query($query);
        $ret = Array();
        while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) $ret[] = $res;
        return $ret;
    }
    /// Add or update rule for repository
    function add_replace_rule($repID, $ruleID, $srch, $repl, $type, $case, $rxmod, $descr)
    {
        $srch  = addslashes($srch);
        $repl  = addslashes($repl);
        $rxmod = addslashes($rxmod);
        $descr = addslashes($descr);
        if (strlen($ruleID) == 0 || $ruleID == 0)
            $query = "insert into tiki_integrator_rules(repID,srch,repl,type,casesense,rxmod,description)
                      values('$repID','$srch','$repl','$type','$case','$rxmod','$descr')";
        else
            $query = "update tiki_integrator_rules 
                      set repID='$repID',srch='$srch',repl='$repl',
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
    //\}
    /// Build full path to file inside given repository
    function get_rep_file($rep, $file = '')
    {
        return $_SERVER['DOCUMENT_ROOT'].'/'.$rep["path"].'/'.((strlen($file) > 0) ? $file : $rep["start_page"]);
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
            $this->add_replace_rule($dstID, 0, $rule["srch"], $rule["repl"],
                                    $rule["type"], $rule["casesense"],
                                    $rule["rxmod"], $rule["description"]);
    }
}

$integrator = new TikiIntegrator($dbTiki);

?>