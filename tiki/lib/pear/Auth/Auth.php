<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
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
// | Authors: Martin Jansen <mj@php.net>                                  |
// +----------------------------------------------------------------------+
//
// $Id: Auth.php,v 1.1 2003-04-09 14:05:56 damienmckenna Exp $
//

require_once "PEAR.php";

/**
 * PEAR::Auth
 *
 * The PEAR::Auth class provides methods for creating an
 * authentication system using PHP.
 *
 * The constructor accepts these parameters:
 *
 *   Auth( string $storageDriver, mixed $options, 
 *         string $loginFunction, bool $showLogin )
 *
 *      $storageDriver  : Storage driver for user data 
 *                        (currently only DB is supported)
 *
 *      $options        : Either a valid dsn or an array of this form: 
 *                        array ( 
 *                            'table'         => $table_with_userdata,
 *                            'usernamecol'   => $column_with_username,
 *                            'passwordcol'   => $column_with_md5_pw
 *                        )
 *
 *      $loginFunction  : The name of a user defined function which draws
 *                        the login form (see drawLogin() for an example)
 *                        (optional, ignored if empty)
 *
 *      $showLogin      : Define if the login form should be displayed
 *                        if the user hasn't logged in already.
 *                        (optional, default: true)
 *                                  
 * Usage example:
 *
 *    require_once "Auth/Auth.php";
 *
 *    // We use a MySQL as storage container in this example
 *    $a = new Auth("DB","mysql://martin:test@example.com/test");
 *
 *    // Detect, if the user is already logged in. If not, draw the
 *    // login form.
 *    $a->start();
 *
 *    if ($a->getAuth()) {
 *        echo "Welcome user ".$a->getUsername()."!<br />\n";
 *        // output the content of your site, that is only visible
 *        // for users which have been authenticated successfully.
 *    }
 *
 *
 * Advanced example:
 *
 *  Set up the Auth class in an auto_prepend file like this:
 *
 *    require_once "Auth/Auth.php";
 *
 *    // define dsn
 *    $dsn = "mysql://martin:test@example.com/test";
 *  
 *    // set options
 *    $options = array(
 *      'table'         => 'myuser',
 *      'usernamecol'   => 'userlogin',
 *      'passwordcol'   => 'cryptpassword',
 *      'dns'           => $dsn
 *    );
 *
 *    // define login form function
 *    function myloginform( $username )
 *    {
 *      // see drawLogin for an example
 *    }
 *
 *    // create auth object
 *    $myauth = new Auth('DB', $options, 'myloginform', true);
 *
 *  You can now use the $myauth object on all your pages like 
 *  in the example above:
 *
 *    // start auth session
 *    $myauth->start();
 *
 *    if ($myauth->getAuth()) {
 *        // user has logged in 
 *    }
 *
 *  If you only want to check, if a user has logged in without
 *  displaying the login form,  use setShowLogin() before you 
 *  call $myauth->start() on your page:
 *
 *    // disable display of login form
 *    $myauth->setShowLogin( false );
 * 
 *    // start auth session
 *    $myauth->start();
 *
 *    if ($myauth->getAuth()) {
 *        // user has logged in 
 *    } else {
 *        // user has not logged in and NO login 
 *        // is displayed.
 *    }
 *
 *  To logout a user and redisplay the login form use a construct
 *  like this:
 *
 *    $myauth->start();
 *
 *    if ( $action == 'logout' ) {
 *        $myauth->logout();
 *        $myauth->start();
 *    }
 *
 * @author  Martin Jansen <mj@php.net>
 * @package Auth
 * @version $Revision: 1.1 $
 */

define("AUTH_LOGIN_OK",          2);
define("AUTH_IDLED",            -1);
define("AUTH_EXPIRED",          -2);
define("AUTH_WRONG_LOGIN",      -3);
define("AUTH_USER_NOT_FOUND",   -5);
define("AUTH_ACCOUNT_DISABLED", -6);
define("AUTH_USER_NOBODY",      "nobody");

class Auth
{
    var $debug = false;
    /**
     * Auth lifetime in seconds
     *
     * If this variable is set to 0, auth never expires
     *
     * @var  integer
     * @see  setExpire(), checkAuth()
     */
    var $expire = 0;

    /**
     * Has the auth session expired?
     *
     * @var   bool
     * @see   checkAuth(), drawLogin()
     */
    var $expired = false;

    /**
     * Maximum time of idleness in seconds
     *
     * The difference to $expire is, that the idletime gets
     * refreshed each time, checkAuth() is called. If this
     * variable is set to 0, idle time is never checked.
     *
     * @var integer
     * @see setIdle(), checkAuth()
     */
    var $idle = 0;

    /**
     * Is the maximum idletime over?
     *
     * @var boolean
     * @see checkAuth(), drawLogin();
     */
    var $idled = false;

    /**
     * Storage object
     *
     * @var object
     * @see Auth(), validateLogin()
     */
    var $storage = "";

    /**
     * Function defined by the user, that creates the login screen
     *
     * @var string
     */
    var $loginFunction = "";

    /**
     * Should the login form be displayed?
     *
     * @var   bool
     * @see   setShowlogin()
     */
    var $showLogin = true;

    /**
     * Current authentication status
     *
     * @var string
     */
    var $status = "";

    /**
     * Username
     *
     * @var string
     */
    var $username = "";

    /**
     * Password
     *
     * @var string
     */
    var $password = "";

    /**
     * Login callback function name
     *
     * @var string
     * @see setLoginCallback()
     */
    var $loginCallback = "";

    /**
     * Logout callback function name
     *
     * @var string
     * @see setLogoutCallback()
     */
    var $logoutCallback = "";
        
    // {{{ Constructor

    /**
     * Constructor
     *
     * Set up the storage driver.
     *
     * @param string    Type of the storage driver
     * @param mixed     Additional options for the storage driver
     *                  (example: if you are using DB as the storage
     *                   driver, you have to pass the dsn string here)
     *
     * @param string    Name of the function that creates the login form
     * @param boolean   Should the login form be displayed if neccessary?
     * @return void
     */
    function Auth($storageDriver = "DB", $options = "", $loginFunction = "", $showLogin = true, $user = "", $pass = "")
    {
        if ($loginFunction != "" && function_exists($loginFunction)) {
            $this->loginFunction = $loginFunction;
        }

        if (is_bool($showLogin)) {
            $this->showLogin = $showLogin;
        }

        if ($user != "") {
            $this->username = (get_magic_quotes_gpc() == 1 ? stripslashes($user) : $user );
            $this->password = (get_magic_quotes_gpc() == 1 ? stripslashes($pass) : $pass );
        }

        $this->storage = $this->_factory($storageDriver, $options);
    }

    // }}}
    // {{{ _factory()

    /**
     * Return a storage driver based on $driver and $options
     *
     * @access private
     * @static
     * @param  string $driver  Type of storage class to return
     * @param  string $options Optional parameters for the storage class
     * @return object Object   Storage object
     */
    function _factory($driver, $options = "")
    {
        $storage_path = "Auth/Container/" . $driver . ".php";
        $storage_class = "Auth_Container_" . $driver;

        require_once $storage_path;

        return new $storage_class($options);
    }

    // }}}
    // {{{ assignData()

    /**
     * Assign data from login form to internal values
     *
     * This function takes the values for username and password
     * from $HTTP_POST_VARS and assigns them to internal variables.
     * If you wish to use another source apart from $HTTP_POST_VARS,
     * you have to derive this function.
     *
     * @access private
     * @global $HTTP_POST_VARS
     * @see    Auth
     * @return void
     */
    function assignData()
    {
        $post = &$this->_importGlobalVariable("post");

        if (isset($post['username']) && $post['username'] != "") {
            $this->username = $post['username'];
        }

        if (isset($post['password']) && $post['password'] != "") {
            $this->password = $post['password'];
        }
    }

    // }}}
    // {{{ start()

    /**
     * Start new auth session
     *
     * @access public
     * @return void
     */
    function start()
    {
        if ($this->username != "")
            $this->assignData();

        session_start();

        if (!$this->checkAuth()) {
            $this->login();
        }
    }

    // }}}
    // {{{ login()

    /**
     * Login function
     *
     * @access private
     * @return void
     */
    function login()
    {
        $login_ok = false;

        /**
         * When the user has already entered a username,
         * we have to validate it.
         */
        if (!empty($this->username)) {
            if (true === $this->storage->fetchData($this->username, $this->password)) {
                $this->status = AUTH_LOGIN_OK;
                $login_ok = true;
            }
        }

        if (!empty($this->username) && $login_ok) {
            $this->setAuth($this->username);
            if (!empty($this->loginCallback)) {
            	call_user_func($this->loginCallback,$this->username);
            }
        }

        /**
         * If the login failed or the user entered no username,
         * output the login screen again.
         */
        if (!empty($this->username) && !$login_ok && $this->storage->user_found == false) {
            $this->status = AUTH_USER_NOT_FOUND;
        }
        elseif (!empty($this->username) && !$login_ok) {
            $this->status = AUTH_WRONG_LOGIN;
        }

        if ((empty($this->username) || !$login_ok) && $this->showLogin) {
            $this->drawLogin($this->storage->activeUser);
            return;
        }
    }

    // }}}
    // {{{ setExpire()

    /**
     * Set the maximum expire time
     * 
     * @access public
     * @param  integer time in seconds
     * @param  bool    add time to current expire time or not
     * @return void
     */
    function setExpire($time, $add = false)
    {
        if ($add) {
            $this->expire += $time;
        } else {
            $this->expire = $time;
        }
    }

    // }}}
    // {{{ setIdle()

    /**
     * Set the maximum idle time
     * 
     * @access public
     * @param  integer time in seconds
     * @param  bool    add time to current maximum idle time or not
     * @return void
     */
    function setIdle($time, $add = false)
    {
        if ($add) {
            $this->idle += $time;
        } else {
            $this->idle = $time;
        }
    }
    
    // }}}
    // {{{ setSessionname()

    /**
     * Set name of the session to a customized value.
     *
     * If you are using multiple instances of PEAR Auth
     * on the same domain, you can change the name of
     * session per application via this function.
     *
     * @access public
     * @param  string New name for the session
     * @return void
     */
    function setSessionname($name = "PHPSESSID")
    {
        @session_name($name);
    }

    // }}}
    // {{{ setShowLogin()

    /**
     * Should the login form be displayed if neccessary?
     *
     * @access public
     * @param  bool    show login form or not
     * @return void
     */
    function setShowLogin($showLogin = true)
    {
        $this->showLogin = $showLogin;
    }

    /**
     * Register a callback function to be called on user login.
     * The function will receive a single parameter, the username.
     *
     * @access public
     * @param  string  callback function name
     * @return void
     * @see    setLogoutCallback()
     */
    function setLoginCallback($loginCallback)
    {
        $this->loginCallback = $loginCallback;
    }

    /**
     * Register a callback function to be called on user logout.
     * The function will receive a single parameter, the username.
     *
     * @access public
     * @param  string  callback function name
     * @return void
     * @see    setLoginCallback()
     */
    function setLogoutCallback($logoutCallback)
    {
        $this->logoutCallback = $logoutCallback;
    }
        
    // }}}
    // {{{ setAuthData()
    
    /**
     * Register additional information that is to be stored
     * in the session.
     *
     * @access public
     * @param  mixed  Additional information. This parameter can
     *                have any type (integer, string, array etc).
     * @return mixed  Previous value.
     */
    function setAuthData($data)
    {
        $session = &Auth::_importGlobalVariable("session");

        if (isset($session['auth']['data'])) {
            $olddata = $session['auth']['data'];
        } else {
            $olddata = null;
        }
        $session['auth']['data'] = $data;
        return $olddata;
    }
    
    // }}}
    // {{{ getAuthData()
    
    /**
     * Get additional information that is stored in the session.
     *
     * @access public
     * @return mixed  Additional information.
     */
    function getAuthData($data)
    {
        $session = &Auth::_importGlobalVariable("session");

        if (isset($session['auth'][$data])) {
            return $session['auth'][$data];
        } else {
            return null;
        }        
    }
    
    // }}}
    // {{{ setAuth()

    /**
     * Register variable in a session telling that the user
     * has logged in successfully
     *
     * @access public
     * @param  string Username
     * @param  mixed  [Deprecated] Additional information that
     *                is stored in the session. This parameter
     *                can have any type (integer, string, array
     *                etc).
     * @return void
     */
    function setAuth($username, $data = null)
    {
        $session = &Auth::_importGlobalVariable("session");

        if (!isset($session['auth']) && !isset($_SESSION)) {
            session_register("auth");
        }

        if (!isset($session['auth']) || !is_array($session['auth'])) {
            $session['auth'] = array();
        }

        $session['auth']['registered'] = true;
        $session['auth']['username']   = $username;
        $session['auth']['timestamp']  = time();
        $session['auth']['idle']       = time();

        if (!empty($data)) {
            Auth::setAuthData($data);
        }
    }
    
    // }}}
    // {{{ checkAuth()

    /**
     * Checks if there is a session with valid auth information.
     *
     * @access private
     * @return boolean  Whether or not the user is authenticated.
     */
    function checkAuth()
    {
        $session = &$this->_importGlobalVariable("session");

        if (isset($session['auth'])) {

            /** Check if authentication session is expired */
            if ($this->expire > 0 &&
                isset($session['auth']['timestamp']) &&
                ($session['auth']['timestamp'] + $this->expire) < time()) {

                $this->logout();
                $this->expired = true;
                $this->status = AUTH_EXPIRED;

                return false;
            }

            /** Check if maximum idle time is reached */
            if ($this->idle > 0 &&
                isset($session['auth']['idle']) &&
                ($session['auth']['idle'] + $this->idle) < time()) {

                $this->logout();
                $this->idled = true;
                $this->status = AUTH_IDLED;

                return false;
            }

            if (isset($session['auth']['registered']) &&
                isset($session['auth']['username']) &&
                $session['auth']['registered'] == true &&
                $session['auth']['username'] != "") {

                Auth::updateIdle();

                return true;
            }
        }

        return false;
    }

    // }}}
    // {{{ getAuth()

    /**
     * Has the user been authenticated?
     *
     * @access public
     * @return bool  True if the user is logged in, otherwise false.
     */
    function getAuth()
    {
        $session = &$this->_importGlobalVariable("session");

        if (!empty($session) && 
            (isset($session['auth']['registered']) && 
             $session['auth']['registered'] === true))
        {
            return true;
        } else {
            return false;
        }
    }

    // }}}
    // {{{ drawLogin()

    /**
     * Draw the login form
     *
     * Normally you will not use this output in your application,
     * because you can pass a different function name to the
     * constructor. For more information on this, please
     * consult the documentation.
     *
     * @access private
     * @param  string  Username if already entered
     * @return void
     */
    function drawLogin($username = "")
    {
        if ($this->loginFunction != "") {
            call_user_func($this->loginFunction, $username, $this->status);
        } else {
            $server = &$this->_importGlobalVariable("server");

            echo "<center>\n";

            if (!empty($this->status) && $this->status == AUTH_EXPIRED) {
                echo "<i>Your session expired. Please login again!</i>\n";
            } else if (!empty($this->status) && $this->status == AUTH_IDLED) {
                echo "<i>You have been idle for too long. Please login again!</i>\n";
            } else if (!empty ($this->status) && $this->status == AUTH_WRONG_LOGIN) {
                echo "<i>Wrong login data!</i>\n";
            }

            PEAR::raiseError("You are using the built-in login screen of PEAR::Auth.<br/>See the <a href=\"http://pear.php.net/manual/\">manual</a> for details on how to create your own login function.", null, PEAR_ERROR_PRINT);
                    
            echo "<form method=\"post\" action=\"" . $server['PHP_SELF'] . "\">\n";
            echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
            echo "<tr>\n";
            echo "    <td colspan=\"2\" bgcolor=\"#eeeeee\"><b>Login:</b></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "    <td>Username:</td>\n";
            echo "    <td><input type=\"text\" name=\"username\" value=\"" . $username . "\"></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "    <td>Password:</td>\n";
            echo "    <td><input type=\"password\" name=\"password\"></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "    <td colspan=\"2\" bgcolor=\"#eeeeee\"><input type=\"submit\"></td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "</center>\n\n";
        }
    }

    // }}}
    // {{{ logout()

    /**
     * Logout function
     *
     * This function clears any auth tokens in the currently
     * active session and executes the logout callback function,
     * if any
     *
     * @access public
     * @return void
     */
    function logout()
    {
        if (!empty($this->logoutCallback)) {
            call_user_func($this->logoutCallback,$this->username);
        }
        
        $this->username = "";
        $this->password = "";

        $session = &$this->_importGlobalVariable("session");
        $session['auth'] = array();
        if (isset($_SESSION)) {
            unset($session['auth']);
        } else {
            session_unregister("auth");
        }        
    }

    // }}}
    // {{{ updateIdle()

    /**
     * Update the idletime
     *
     * @access private
     * @return void
     */
    function updateIdle()
    {
        $session = &$this->_importGlobalVariable("session");
        $session['auth']['idle'] = time();
    }

    // }}}
    // {{{ getUsername()

    /**
     * Get the username
     *
     * @access public
     * @return string
     */
    function getUsername()
    {
        $session = &$this->_importGlobalVariable("session");
        if (!isset($session['auth']['username'])) {
            return "";
        }
        return $session['auth']['username'];
    }

    // }}}
    // {{{ getStatus()

    /**
     * Get the current status
     *
     * @access public
     * @return string
     */
    function getStatus()
    {
        return $this->status;
    }

    // }}}
    // {{{ sessionValidThru()

    /**
     * Returns the time up to the session is valid
     *
     * @access public
     * @return integer
     */
    function sessionValidThru()
    {
        $session = &$this->_importGlobalVariable("session");
        if (!isset($session['auth']['idle'])) {
            return 0;
        }
        return ($session['auth']['idle'] + $this->idle);
    }

    // }}}
    // {{{ listUsers()

    /**
     * List all users that are currently available in the storage
     * container
     *
     * @access public
     * @return array
     */
    function listUsers()
    {
        return $this->storage->listUsers();
    }

    // }}}
    // {{{ addUser()

    /**
     * Add user to the storage container
     *
     * @access public
     * @param  string Username
     * @param  string Password
     * @param  mixed  Additional parameters
     * @return mixed  True on success, PEAR error object on error
     *                and AUTH_METHOD_NOT_SUPPORTED otherwise.
     */    
    function addUser($username, $password, $additional = "")
    {
        return $this->storage->addUser($username, $password, $additional);
    }

    // }}}
    // {{{ removeUser()

    /**
     * Remove user from the storage container
     *
     * @access public
     * @param string Username
     * @return mixed  True on success, PEAR error object on error
     *                and AUTH_METHOD_NOT_SUPPORTED otherwise.
     */
    function removeUser($username)
    {
        return $this->storage->removeUser($username);
    }

    // }}}
    // {{{ addUser()

    /**
     * Set a new password for a given user
     *
     * @access public
     * @param  string Username
     * @param  string Password
     * @param  mixed  Additional parameters
     * @return mixed  True on success, PEAR error object on error
     *                and AUTH_METHOD_NOT_SUPPORTED otherwise.
     */    
    function setPassword($username, $password, $additional = "")
    {
        return $this->storage->setPassword($username, $password, $additional);
    }

    // }}}
    // {{{ _importGlobalVariable()

    /**
     * Import variables from special namespaces.
     *
     * @access private
     * @param string Type of variable (server, session, post)
     * @return array
     */
    function &_importGlobalVariable($variable) 
    {
        $var = null;

        switch (strtolower($variable)) {

            case "server" :
                if (isset($_SERVER)) {
                    $var = &$_SERVER;
                } else {
                    $var = &$GLOBALS['HTTP_SERVER_VARS'];
                }
                break;

            case "session" :
                if (isset($_SESSION)) {
                    $var = &$_SESSION;
                } else {
                    $var = &$GLOBALS['HTTP_SESSION_VARS'];
                }
                break;

            case "post" :
                if (isset($_POST)) {
                    $var = &$_POST;
                } else {
                    $var = &$GLOBALS['HTTP_POST_VARS'];
                }
                break;

            case "cookie" :
                if (isset($_COOKIE)) {
                    $var = &$_COOKIE;
                } else {
                    $var = &$GLOBALS['HTTP_COOKIE_VARS'];
                }
                break;

            case "get" :
                if (isset($_GET)) {
                    $var = &$_GET;
                } else {
                    $var = &$GLOBALS['HTTP_GET_VARS'];
                }
                break;

            default:
                break;

        }

        return $var;
    } 

    // }}}
}
?>
