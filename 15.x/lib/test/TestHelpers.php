<?php
/**
 *
 * This class can be used to initialize Tiki to given start conditiosn for testing purposes.
 * The class also allows you to undo all the changes that were madtest DB for the particular needs of a given
 * unit or acceptance test, and then restore it to its initial state after that.
 */

class TestHelpers {

    private $orig_user;

    /*
     * Restore the application and DB to its original state.
     * - Undo any changes that may have been done to the DB through TestHelpers methods
     * - If any global libs were replaced by mock implementations, reset them to their
     *   real implementation.
     */
    public function reset_all()
    {
        global $tikilib;

        $tikilib = new TikiLib;
    }

    public function simulate_tiki_script_context($script_uri = 'tiki-index.php', $logged_on_as_user = 'some_user', $host = 'localhost')
    {

        global $_SERVER, $user, $prefs;

        $this->orig_user = $user;

        $_SERVER['HTTP_HOST'] = $host;
        $_SERVER['REQUEST_URI'] = $script_uri;
        $user = $logged_on_as_user;

        $prefs['site_language'] = 'en';
    }

    public function stop_simulating_tiki_script_context()
    {
        global $tikilib, $user;

        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
        $user = $this->orig_user;
    }

    /*
     * Like $tikilib->create_page(), except that it will delete the page if it already exists.
     */
    public function create_page($name, $hits, $data, $lastModif=null, $comment='', $user = 'admin', $ip = '0.0.0.0', $description = '', $lang='', $is_html = false, $hash=null, $wysiwyg=NULL, $wiki_authors_style='', $minor=0, $created='')
    {
        global $tikilib;

        if ($lastModif == null)
        {
            $lastModif = $tikilib->now;
        }

        if ($tikilib->page_exists($name))
        {
            $tikilib->remove_all_versions($name);
        }
        $tikilib->create_page($name, $hits, $data, $lastModif, $comment, $user, $ip, $description, $lang, $is_html, $hash, $wysiwyg, $wiki_authors_style, $minor, $created);
    }


    public function remove_all_versions($page_name)
    {
        global $tikilib;

        $tikilib->remove_all_versions($page_name);
    }

}

$testhelpers = new TestHelpers();