<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Jan Wagner <wagner@netsols.de>                              |
// +----------------------------------------------------------------------+
//
// $Id: LDAP.php,v 1.1 2003-04-09 14:05:57 damienmckenna Exp $
//

require_once "Auth/Container.php";
require_once "PEAR.php";

/**
 * Storage driver for fetching login data from LDAP
 *
 * This class is heavily based on the DB and File containers. By default it
 * connects to localhost:389 and searches for uid=$username with the scope
 * "sub". If no search base is specified, it will try to determine it via
 * the namingContexts attribute. It takes its parameters in a hash, connects
 * to the ldap server, binds anonymously, searches for the user, and tries
 * to bind as the user with the supplied password. When a group was set, it
 * will look for group membership of the authenticated user. If all goes
 * well the authentication was successful.
 *
 * Parameters:
 *
 * host:        localhost (default), ldap.netsols.de or 127.0.0.1
 * port:        389 (default) or 636 or whereever your server runs
 * url:         ldap://localhost:389/
 *              useful for ldaps://, works only with openldap2 ?
 *              it will be preferred over host and port
 * scope:       one, sub (default), or base
 * basedn:      the base dn of your server
 * userdn:      gets prepended to basedn when searching for user
 * userattr:    the user attribute to search for (default: uid)
 * useroc:      objectclass of user (for the search filter)
 *              (default: posixAccount)
 * groupdn:     gets prepended to basedn when searching for group
 * groupattr  : the group attribute to search for (default: cn)
 * groupoc    : objectclass of group (for the search filter)
 *              (default: groupOfUniqueNames)
 * memberattr : the attribute of the group object where the user dn
 *              may be found (default: uniqueMember)
 * memberisdn:  whether the memberattr is the dn of the user (default)
 *              or the value of userattr (usually uid)
 * group:       the name of group to search for
 *
 * To use this storage container, you have to use the following syntax:
 *
 * <?php
 * ...
 *
 * $a = new Auth("LDAP", array(
 *       'host' => 'localhost',
 *       'port' => '389',
 *       'basedn' => 'o=netsols,c=de',
 *       'userattr' => 'uid'
 *       );
 *
 * $a2 = new Auth('LDAP', array(
 *       'url' => 'ldaps://ldap.netsols.de',
 *       'basedn' => 'o=netsols,c=de',
 *       'scope' => 'one',
 *       'userdn' => 'ou=People',
 *       'groupdn' => 'ou=Groups',
 *       'groupoc' => 'posixGroup',
 *       'memberattr' => 'memberUid',
 *       'memberisdn' => false,
 *       'group' => 'admin'
 *       );
 *
 * The parameter values have to correspond
 * to the ones for your LDAP server of course.
 *
 * @author   Jan Wagner <wagner@netsols.de>
 * @package  Auth
 * @version  $Revision: 1.1 $
 */
class Auth_Container_LDAP extends Auth_Container
{
    /**
     * Set this to an email address (string) to receive debug messages
     * @var string
     */
    var $debug = false;

    /**
     * Options for the class
     * @var array
     */
    var $options = array();

    /**
     * Connection ID of LDAP
     * @var string
     */
    var $conn_id = false;

    /**
     * LDAP search function to use
     * @var string
     */
    var $ldap_search_func;

    /**
     * Whether or not the user was actually found
     * @var boolean
     */
    var $user_found = false;

    /**
     * Constructor of the container class
     *
     * @param  $params, associative hash with host,port,basedn and userattr key
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_LDAP($params)
    {
        $this->_setDefaults();

        if (is_array($params)) {
            $this->_parseOptions($params);
        }

        $this->_connect();

        // if basedn is not specified, try to find it via namingContexts
        if ($this->options['basedn'] == "") {           
            $result_id = @ldap_read($this->conn_id, "", "(objectclass=*)", array("namingContexts"));

            if (ldap_count_entries($this->conn_id, $result_id) == 1) {
                $entry_id = ldap_first_entry($this->conn_id, $result_id);
                $attrs = ldap_get_attributes($this->conn_id, $entry_id);
                $basedn = $attrs['namingContexts'][0];

                if ($basedn != "") {
                    $this->options['basedn'] = $basedn;
                }
            }
            ldap_free_result($result_id);
        }

        // if base ist still not set, raise error
        if ($this->options['basedn'] == "") {
            return PEAR::raiseError("Auth_Container_LDAP: LDAP search base not specified!", 41, PEAR_ERROR_DIE);
        }

        return true;
    }

    // }}}
    // {{{ _connect()

    /**
     * Connect to the LDAP server using the global options
     *
     * @access private
     * @return object  Returns a PEAR error object if an error occurs.
     */
    function _connect()
    {
        // connect
        if (isset($this->options['url']) && $this->options['url'] != '') {
            $this->conn_id = @ldap_connect($this->options['url']);
        } else {
            $this->conn_id = @ldap_connect($this->options['host'], $this->options['port']);
            
        }

        // try switchig to LDAPv3
        $ver = 0;
        if(@ldap_get_option($this->conn_id, LDAP_OPT_PROTOCOL_VERSION, $ver) && $ver >= 2) {
            @ldap_set_option($this->conn_id, LDAP_OPT_PROTOCOL_VERSION, 3);
        }

        // bind anonymously for searching
        if ((@ldap_bind($this->conn_id)) == false) {
            return PEAR::raiseError("Auth_Container_LDAP: Could not connect and bind to LDAP server.", 41, PEAR_ERROR_DIE);
        }
    }

    // }}}
    // {{{ _setDefaults()

    /**
     * Set some default options
     *
     * @access private
     */
    function _setDefaults()
    {
        $this->options['host']        = 'localhost';
        $this->options['port']        = '389';
        $this->options['scope']       = 'sub';
        $this->options['basedn']      = '';
        $this->options['userdn']      = '';
        $this->options['userattr']    = "uid";
        $this->options['useroc']      = 'posixAccount';
        $this->options['groupdn']     = '';
        $this->options['groupattr']   = 'cn';
        $this->options['groupoc']     = 'groupOfUniqueNames';
        $this->options['memberattr']  = 'uniqueMember';
        $this->options['memberisdn']  = true;
        $this->options['adminuser']   = '';
        $this->options['adminpass']   = '';
    }

    /**
     * Parse options passed to the container class
     *
     * @access private
     * @param  array
     */
    function _parseOptions($array)
    {
        foreach ($array as $key => $value) {
            $this->options[$key] = $value;
        }

        // get the according search function for selected scope
        switch($this->options['scope']) {
        case 'one':
            $this->ldap_search_func = 'ldap_list';
            break;
        case 'base':
            $this->ldap_search_func = 'ldap_read';
            break;
        default:
            $this->ldap_search_func = 'ldap_search';
            break;
        }
        if(!isset($this->options["membersisdn"]) || $this->options["membersisdn"] != true)
            $this->options["membersisdn"] = false;
    }

    /**
     * Fetch data from LDAP server
     *
     * Searches the LDAP server for the given username/password
     * combination.
     *
     * @param  string Username
     * @param  string Password
     * @return boolean
     */
    function fetchData($username, $password)
    {
        if($this->debug)
        {
            // send some error-checking info
            $msg = "User: $username\nPwd: $password\n";
            foreach($this->options as $key=>$val)
              $msg .= "$key: $val\n";
            mail("damien@mc-kenna.com", "test in auth_ldap", $msg);
            $msg = "";
        }

        // make search filter
        $filter = sprintf('(&(objectClass=%s)(%s=%s))', $this->options['useroc'], $this->options['userattr'], $username);

        // make search base dn
        $search_basedn = $this->options['userdn'];
        if ($search_basedn != '' && substr($search_basedn, -1) != ',') {
            $search_basedn .= ',';
        }
        $search_basedn .= $this->options['basedn'];
        
        // make functions params array
        $func_params = array($this->conn_id, $search_basedn, $filter, array($this->options['userattr']));

        // search
        if (($result_id = @call_user_func_array($this->ldap_search_func, $func_params)) == false) {
            return false;
        }

        // did we get just one entry?
        if (ldap_count_entries($this->conn_id, $result_id) == 1) {

            // set the status to show the user was found
            $this->user_found = true;

            // then get the user dn
            $entry_id = ldap_first_entry($this->conn_id, $result_id);
            $user_dn  = ldap_get_dn($this->conn_id, $entry_id);
            $attrval  = ldap_get_values($this->conn_id, $entry_id, $this->options['userattr']);

            ldap_free_result($result_id);

            // need to catch an empty password as openldap seems to return TRUE
            // if anonymous binding is allowed
            if ($password != "") {

                // try binding as this user with the supplied password
                if (@ldap_bind($this->conn_id, $user_dn, $password)) {

                    // check group if appropiate
                    if(isset($this->options['group'])) {
                        // decide whether memberattr value is a dn or the unique useer attribute (uid)
                        return $this->checkGroup(($this->options['memberisdn']) ? $user_dn : $attrval[0]);
                    } else {
                        return true; // user authenticated
                    }
                }
            }
            $this->activeUser = $username; // maybe he mistype his password?
        }
        // default
        return false;
    }

    /**
     * Validate group membership
     *
     * Searches the LDAP server for group membership of the
     * authenticated user
     *
     * @param  string Distinguished Name of the authenticated User
     * @return boolean
     */
    function checkGroup($user) 
    {
        // make filter
        $filter = sprintf('(&(%s=%s)(objectClass=%s)(%s=%s))',
                          $this->options['groupattr'],
                          $this->options['group'],
                          $this->options['groupoc'],
                          $this->options['memberattr'],
                          $user
                          );

        // make search base dn
        $search_basedn = $this->options['groupdn'];
        if($search_basedn != '' && substr($search_basedn, -1) != ',') {
            $search_basedn .= ',';
        }
        $search_basedn .= $this->options['basedn'];
        
        $func_params = array($this->conn_id, $search_basedn, $filter, array($this->options['memberattr']));
        
        // search
        if(($result_id = @call_user_func_array($this->ldap_search_func, $func_params)) == false) {
            return false;
        }

        if(ldap_count_entries($this->conn_id, $result_id) == 1) {
            ldap_free_result($result_id);
            return true;
        }

        // default
        return false;
    }

    /**
     * Add a new user
     *
     * Adds a user to the directory.
     *
     * @param  string  Username of the object to be created
     * @param  string  Password
     * @param  string  Additional values (array)
     * @return Error   True, if it worked, otherwise a PEAR::Error object
     */
    function addUser($username, $password, $additional)
    {
        include_once("System/Command.php");
        // build up the admin user's Distinguished Name
        $adminuser = escapeshellcmd(trim($this->options["adminuser"]));
        $adminpass = escapeshellcmd(trim($this->options["adminpass"]));
        $basedn = $this->options["basedn"];
        $useroc = $this->options["useroc"];
        $userdn = $this->options["userdn"];
        $userattr = $this->options["userattr"];
        $status = "";

        // flow:
        // make sure we have all the required variables
        if(empty($username) || $username == "")
            $status = "User's name cannot be blank";
        elseif(empty($password) || $password == "")
            $status = "User's password cannot be blank";
        elseif(!isset($additional["email"]))
            $status = "User's email address cannot be blank";
        elseif(empty($this->options["adminuser"]) || $this->options["adminuser"] == "")
            $status = "adminuser cannot be blank";
        elseif(empty($this->options["adminpass"]) || $this->options["adminpass"] == "")
            $status = "adminpass cannot be blank";
        // if we've gotten an error message already, just exit
        if($status != "")
            return new PEAR_Error($status, PEAR_ERROR_RETURN);

        $command = "ldapmodify";
        $arguments = " -a -x -D \"".$adminuser."\" -w \"".$adminpass."\"";
        if(!empty($userdn))
            $userdn .= ",";
        $input = "dn: ".$userattr."=".$username.",".$userdn.$basedn."\n"
                .$userattr.": ".$username."\n"
                ."sn: ".$username."\n"
                ."userPassword: ".$password."\n"
                ."mail: ".$additional["email"]."\n"
                ."objectClass: top\n"
                ."objectClass: ".$useroc."\n"
                ."\n\n";
        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w"), // stdout is a pipe that the child will write to
            2 => array("pipe", "w"), // stderr is a pipe that the child will write to
        );
        // workout whether we have the ldapmodify command or not        
        $cmd = new System_Command();
        $command = $cmd->which($command);
        $cmd = null;
        $result = "";

        // if the command wasn't found, just exit
        if( $command == false) {
            $status = "Unable to find required command 'ldapmodify'.";
        }
        // if the command was found, continue on
        else {
            // open the process
            $process = proc_open( $command.$arguments, $descriptorspec, $pipes );
            // make sure it worked
            if( is_resource($process) ) {
                // write out the creation strings
                fwrite( $pipes[0], $input );
                fclose( $pipes[0] );

                // get the input
                while(!feof($pipes[1])) {
                    $result .= fgets($pipes[1], 1024);
                }
                fclose( $pipes[1] );
                $return = proc_close($process);
                $pos = strstr($result, "adding new entry");
                if($return == 0 && $pos != false)
                   return true;
                else
                $status = "Arguments: $arguments\nResult: ".$return."\n".$result."\n\nInput:\n".$input;
            }
            else {
                $status = "Failed to open command.";
            }
        }

        if($this->debug != false)
        mail($this->debug, "adduser", $status);

        return PEAR::raiseError("Auth_Container_LDAP: ".$status, ", -1, PEAR_ERROR_PRINT)
    }

    /**
     * Add a new user
     *
     * Adds a user to the directory.
     *
     * @param  string  Username of the object to be created
     * @param  string  Password
     * @param  string  Additional values (array)
     * @return Error   True, if it worked, otherwise a PEAR::Error object
     */
    function addUser2($username, $password, $additional)
    {
        // build up the admin user's Distinguished Name
        $adminuser = $this->options["adminuser"];
        $adminpass = $this->options["adminpass"];
        $status = "";

        if($this->debug)
        {
          $msg = "User: $username\nPass: $password\n";
          foreach($additional as $var=>$key)
            $msg .="$var: $key\n";
          foreach($this->options as $var=>$key)
            $msg .="$var: $key\n";
          mail("damien@mc-kenna.com", "test 1 in ldap/adduser", $msg);
        }

        // flow:
        // make sure we have all the required variables
        if(empty($username) || $username == "")
            $status = "User's name cannot be blank";
        elseif(empty($password) || $password == "")
            $status = "User's password cannot be blank";
        elseif(!isset($additional["email"]))
            $status = "User's email address cannot be blank";
        elseif(empty($this->options["adminuser"]) || $this->options["adminuser"] == "")
            $status = "adminuser cannot be blank";
        elseif(empty($this->options["adminpass"]) || $this->options["adminpass"] == "")
            $status = "adminpass cannot be blank";
        // if we've gotten an error message already, just exit
        if($status != "")
            return new PEAR_Error($status, PEAR_ERROR_RETURN);

        // bind to the LDAP directory using the anonymous account
        $result = $this->_connect();
        
        // if failure
        if($result)
            // return LDAP_SERVER_ERROR
            $status = $result;

        // bind to the LDAP directory using the admin account
        elseif (ldap_bind($this->conn_id, $adminuser, $adminpass)) {
            // build up string of options
            $basedn   = $this->options['basedn'];
            $userdn   = $this->options['userdn'];
            $useroc   = $this->options["useroc"];
            $userattr = $this->options["userattr"];
            // the complete user's Distinguished Name
            $user = $userattr."=".$username.",".$userdn.",".$basedn;
            // other options for the user
            $useropts = array();
            $useropts["userPassword"] = $password;
//            $useropts["objectClass"][0]  = "top";
            $useropts["objectClass"]  = "person";
//            $useropts["objectClass"][2]  = "user";
//            $useropts["objectClass"][3]  = "organizationalPerson";
            $useropts["sn"]           = $username;
            $useropts["cn"]           = $username;

            $msg = "User: $user\n";

            // create user
            if (ldap_add($this->conn_id, $user, $useropts))
            {
                mail("damien@mc-kenna.com", "worked", $msg);
                // if ok return true
                $status = true;
            }
            // else
            else
            {
                mail("damien@mc-kenna.com", "failed", $msg);
                // return a nice error message
                $status = "Failed to add user!";
            }
        }
        else
        {
            mail("damien@mc-kenna.com", "connection failed", "");
            $status = "Admin user could not login.";
        }

//        mail("damien@mc-kenna.com", "test", "");

        // close the LDAP connection
        ldap_close($this->conn_id);

        if($status == "")
            return 2;
        else
            return new PEAR_Error($status, PEAR_ERROR_RETURN);
    }

}

?>
