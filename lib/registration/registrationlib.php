<?php
class RegistrationLib extends TikiLib {

  function RegistrationLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to RegistrationLib constructor");  
    }
    $this->db = $db;  
  }
  
    // Validate emails...
  function SnowCheckMail($Email,$Debug=false)
  {
    $HTTP_HOST=$_SERVER['HTTP_HOST'];
    $Return =array();
    // Variable for return.
    // $Return[0] : [true|false]
    // $Return[1] : Processing result save.

//    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $Email)) {
    //Fix by suilinma
    if (!eregi("^[-_a-z0-9]+(\\.[-_a-z0-9]+)*\\@([-a-z0-9]
+\\.)*([a-z]{2,4})$", $Email)) {
        $Return[0]=false;
        $Return[1]="${Email} is E-Mail form that is not right.";
        if ($Debug) echo "Error : {$Email} is E-Mail form that is not right.<br>";
        return $Return;
    }
    else if ($Debug) echo "Confirmation : {$Email} is E-Mail form that is not right.<br>";

    // E-Mail @ by 2 by standard divide. if it is $Email this "lsm@ebeecomm.com"..
    // $Username : lsm
    // $Domain : ebeecomm.com
    // list function reference : http://www.php.net/manual/en/function.list.php
    // split function reference : http://www.php.net/manual/en/function.split.php
    list ( $Username, $Domain ) = split ("@",$Email);

    // That MX(mail exchanger) record exists in domain check .
    // checkdnsrr function reference : http://www.php.net/manual/en/function.checkdnsrr.php
    if ( checkdnsrr ( $Domain, "MX" ) )  {
        if($Debug) echo "Confirmation : MX record about {$Domain} exists.<br>";
        // If MX record exists, save MX record address.
        // getmxrr function reference : http://www.php.net/manual/en/function.getmxrr.php
        if ( getmxrr ($Domain, $MXHost))  {
      if($Debug) {
                echo "Confirmation : Is confirming address by MX LOOKUP.<br>";
              for ( $i = 0,$j = 1; $i < count ( $MXHost ); $i++,$j++ ) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result($j) - $MXHost[$i]<BR>";
        }
            }
        }
        // Getmxrr function does to store MX record address about $Domain in arrangement form to $MXHost.
        // $ConnectAddress socket connection address.
        $ConnectAddress = $MXHost[0];
    }
    else {
        // If there is no MX record simply @ to next time address socket connection do .
        $ConnectAddress = $Domain;
        if ($Debug) echo "Confirmation : MX record about {$Domain} does not exist.<br>";
    }

    // fsockopen function reference : http://www.php.net/manual/en/function.fsockopen.php
    $Connect = fsockopen ( $ConnectAddress, 25 );

    // Success in socket connection
    if ($Connect)
    {
        if ($Debug) echo "Connection succeeded to {$ConnectAddress} SMTP.<br>";
        // Judgment is that service is preparing though begin by 220 getting string after connection .
        // fgets function reference : http://www.php.net/manual/en/function.fgets.php
        if ( ereg ( "^220", $Out = fgets ( $Connect, 1024 ) ) ) {

            // Inform client's reaching to server who connect.
            fputs ( $Connect, "HELO $HTTP_HOST\r\n" );
                if ($Debug) echo "Run : HELO $HTTP_HOST<br>";
            $Out = fgets ( $Connect, 1024 ); // Receive server's answering cord.

            // Inform sender's address to server.
            fputs ( $Connect, "MAIL FROM: <{$Email}>\r\n" );
                if ($Debug) echo "Run : MAIL FROM: &lt;{$Email}&gt;<br>";
            $From = fgets ( $Connect, 1024 ); // Receive server's answering cord.

            // Inform listener's address to server.
            fputs ( $Connect, "RCPT TO: <{$Email}>\r\n" );
                if ($Debug) echo "Run : RCPT TO: &lt;{$Email}&gt;<br>";
            $To = fgets ( $Connect, 1024 ); // Receive server's answering cord.

            // Finish connection.
            fputs ( $Connect, "QUIT\r\n");
                if ($Debug) echo "Run : QUIT<br>";

            fclose($Connect);

                // Server's answering cord about MAIL and TO command checks.
                // Server about listener's address reacts to 550 codes if there does not exist
                // checking that mailbox is in own E-Mail account.
                if ( !ereg ( "^250", $From ) || !ereg ( "^250", $To )) {
                    $Return[0]=false;
                    $Return[1]="${Email} is address done not admit in E-Mail server.";
                    if ($Debug) echo "{$Email} is address done not admit in E-Mail server.<br>";
                    return $Return;
                }
        }
    }
    // Failure in socket connection
    else {
        $Return[0]=false;
        $Return[1]="Can not connect E-Mail server ({$ConnectAddress}).";
        if ($Debug) echo "Can not connect E-Mail server ({$ConnectAddress}).<br>";
        return $Return;
    }
    $Return[0]=true;
    $Return[1]="{$Email} is E-Mail address that there is no any problem.";
    return $Return;
  }

  
  
}

$registrationlib= new RegistrationLib($dbTiki);

?>
