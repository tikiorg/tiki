<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**************************************************************************/
 #  Mailbox 0.9.2a   by Sivaprasad R.L (http://netlogger.net)             #
 #  eMailBox 0.9.3   by Don Grabowski  (http://ecomjunk.com)              #
 #          --  A pop3 client addon for phpnuked websites --              #
 #                                                                        #
 # This program is distributed in the hope that it will be useful,        #
 # but WITHOUT ANY WARRANTY; without even the implied warranty of         #
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
 # GNU General Public License for more details.                           #
 #                                                                        #
 # You should have received a copy of the GNU General Public License      #
 # along with this program; if not, write to the Free Software            #
 # Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.              #
 #                                                                        #
 #             Copyright (C) by Sivaprasad R.L                            #
 #            Script completed by Ecomjunk.com 2001                       #
/**************************************************************************/

#########################################################
#  Class DecodeMessage: Mime message decoder            #
#  by AKAN NKWEINI                                      #
#  akan@p3mail.com                                      #
#  http://www.p3mail.com                                #
#########################################################

class DecodeMessage
{
  var $header;
  var $body;
  var $fullmessage;
  var $auto_decode = true;
  var $attachment_path;
  var $choose_best = true;
  var $best_format = "text/html";


  function InitHeaderAndBody($header, $body) {
    $this->header = $header;
    $this->body = $body;
    $this->fullmessage = chop($header)."\t\n\t\n".ltrim($body);
  }
  function Body() {
    return trim($this->body);
  }
  function InitMessage($msg) {
    global $download_dir;
    $i = 0;
    $m = "";
    $messagebody = "";
    $line = explode("\n",trim($msg));
    for ($j=0;$j<count($line);$j++) {
      if (chop($line[$j]) == ""  AND $i < 1) :
        $this->header = $m;
        $i++;
      endif;
      if ($i > 0)
        $messagebody .= $line[$j]."\n";
      $m .= $line[$j]."\n";
    }
    $this->body = $messagebody;
    $this->fullmessage = $msg;
    $this->attachment_path = $download_dir;
  }

  function Headers($field="") {
    if ($field == "") :
      return $this->header;
    else :
      $hd = "";
      $field = $field.":";
      $start = 0;
      $j=0;
      $header = preg_replace("/\r/i", "\n", $this->header);
      $p = explode("\n", $header);
      do {
        for ($i=$start;$i<count($p);$i++) {
          if (preg_match("/^($field)/", $p[$i]))  :
              $position = $i;
              $hd .= preg_replace("/$field/", "",$p[$i]);
              break;
            endif;
        }
        if (preg_match("/^($field)/", $p[$i]))  :
          for ($i=$position+1;$i<count($p);$i++) {
            $tok = strtok($p[$i], ' ');
            if (preg_match('/:$/', $tok) AND (!(preg_match("/^($field)/i", $tok))))
              break;
            $hd .= preg_replace("/$field/", '',$p[$i]);
          }
          $start=$i+1;
        endif;
      } while ($j++ < count($p));
    return $hd;
    endif;
  }

  function ContentType() {
    $c = $this->Headers("Content-Type");
    $ct = preg_replace('/[[:space:]]/', '', $c);
    if (!(preg_match('/;/', $ct))) :
      $content["type"] = trim($ct);
    else :
     $p = explode (";", $ct);
      for ($i=0;$i<count($p);$i++) {
        if (preg_macth('/^(text)/i', $p[$i])) :
          $content["type"] = $p[$i];
        elseif (preg_match('/^(multipart)/i', $p[$i])) :
          $content["type"] = $p[$i];
        elseif (preg_match('/^(application)/i', $p[$i])) :
          $content["type"] = $p[$i];
        elseif (preg_match('/^(message)/i', $p[$i])) :
          $content["type"] = $p[$i];
        elseif (preg_match('/^(image)/i', $p[$i])) :
          $content["type"] = $p[$i];
        elseif (preg_match('/^(audio)/i', $p[$i])) :
          $content["type"] = $p[$i];
        elseif (preg_match('/^(charset)/i', $p[$i])) :
          $content["charset"] = preg_replace('/(charset=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(report-type)/i', $p[$i])) :
          $content["report-type"] = preg_replace('/(report-type=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(type)/i', $p[$i])) :
          $content["subtype"] = preg_replace('/(type=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(boundary)/i', $p[$i])) :
          $content["boundary"] = preg_replace('/(boundary=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(name)/i', $p[$i])) :
          $content["name"] = preg_replace('/(name=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(access-type)/i', $p[$i])) :
          $content["access-type"] = preg_replace('/(access-type=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(site)/i', $p[$i])) :
          $content["site"] = preg_replace('/(site=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(directory)/i', $p[$i])) :
          $content["directory"] = preg_replace('/(directory=)|(")/i', '', $p[$i]);
        elseif (preg_match('/^(mode)/i', $p[$i])) :
          $content["mode"] = preg_replace('/(mode=)|(")/i', '', $p[$i]);
        endif;
      }
    endif;
    return $content;
  }
  function ContentDisposition() {
    $c = $this->Headers("Content-Disposition");
    $c = preg_replace('/[[:space:]]/', '', $c);
    if (!(preg_match('/;/', $c))) :
      $cd["type"] = $c;
    else :
      $p = explode(";", $c);
      for ($i=0;$i<count($p);$i++) {
        if (preg_match('/^(inline)/i', $p[$i])) :
          $cd["type"] = $p[$i];
        elseif (preg_match('/^(attachment)/i', $p[$i])) :
          $cd["type"] = $p[$i];
        elseif(preg_match('/^(filename)/i', $p[$i])) :
          $cd["filename"] = preg_replace('/(filename=)|(")/i', '', $p[$i]);
        endif;
  }
    endif;
    return $cd;
  }
  function my_array_shift(&$array) {
    reset($array);
    $key = key($array);
    $val = current($array);
    unset($array[$key]);
    return $val;
  }
  function my_array_compact(&$array) {
    while (list($key, $val) = each($array)) :
        if (chop($val) == '')
          unset($array[$key]);
    endwhile;
  }
   function my_in_array($value, $array) {
    while (list($key, $val) = each($array)) :
        if (strcmp($value, $val) == 0)
          return true;
    endwhile;
    return false;
  }
  function Result() {
    global $attachments_view;
    $is_multipart_alternative = false;
    $is_multipart_related = false;
    $found_best = false;
    do {
      $next_message = "";
      do {
        $next_multipart = "";
        $content = $this->ContentType();
        $cd = $this->ContentDisposition();
        if ( preg_match('/^(multipart)/i', $content["type"]) ) :
          if ( preg_match('#multipart/alternative#i', $content["type"]) ) :
            $is_multipart_alternative = true;
          endif;
          if ( preg_match('#multipart/related#i', $content["type"]) ) :
            $is_multipart_related = true;
          endif;
          $boundary = "--".$content["boundary"];
          $p = explode($boundary, $this->body);
          for ($i=0;$i<count($p);$i++) {
            $this->InitMessage($p[$i]);
            $content = $this->ContentType();
            $this->ContentDisposition();

            if ($is_multipart_related AND (chop($this->Headers("Content-ID")) != '')) :
              $cont["id"] = preg_replace('/[<>]/', '', $this->Headers("Content-ID"));
              $cont["name"] = $content["name"];
             $contentid[] = $cont;
              unset($cont);
            endif;
            if (preg_match('/multipart/', $content["type"])) :
              $multiparts[] = $p[$i];
            elseif (preg_match('/message/', $content["type"])) :
              $messages[] = $p[$i];
            elseif ($this->choose_best AND preg_match('#text/plain#i', $content["type"]) AND $is_multipart_alternative  AND !($found_best)) :
              $best = $p[$i];
            elseif ($this->choose_best AND preg_match('/'.$this->best_format.'/i', $content["type"]) AND $is_multipart_alternative ) :
              if (preg_match('/[[:alpha:]]/i', chop($p[$i]))) :
                $best = $p[$i];
                $found_best = true;
              endif;
            elseif (chop($content["type"]) != '' AND chop($this->body) !='') :
              $parts[] = $p[$i];
            endif;
            #echo "<pre>($i)###".htmlspecialchars($this->header)."</pre>--###<hr>";
          }
          if (chop($best) != '') :
            $parts[] = $best;
          endif;
        else :
          if (preg_match('/(message)/i', $content["type"])) :
            $messages[] = $this->fullmessage;
          elseif (chop($this->body) != '') :
            $parts[] = $this->fullmessage;
          endif;
        endif;
        unset($is_multipart_alternative);
        unset($best);
        unset($found_best);
        if (count($multiparts) > 0) :
          $next_multipart = $this->my_array_shift($multiparts);
          $this->InitMessage($next_multipart);
        endif;
      } while ($next_multipart != "");
        if (chop($parts) != '') :

          for ($i=0;$i<count($parts);$i++) {;
            $this->InitMessage($parts[$i]);
            $ct = $this->ContentType();
            $cd = $this->ContentDisposition();

            if (preg_match('#text/html#i', $ct["type"]) AND count($contentid > 0)) :

              for ($k=0;$k<count($contentid);$k++) {
                if (ini_get(file_uploads) AND $attachments_view == 1) {
                    $filelocation = $this->attachment_path."/".$contentid[$k]["name"];
                }
                $cid = $contentid[$k]["id"];
         $cid = preg_replace('/[[:space:]]/', '', $cid);
                $this->body = str_replace("cid:", '', $this->body);
                if (ini_get(file_uploads) AND $attachments_view == 1) {
                    $this->body = str_replace($cid, $filelocation, $this->body);
                }
              }
            endif;
            if ($this->auto_decode
              AND preg_match('/attachment/i', $cd["type"])
              OR preg_match('/base64/i', $this->Headers("Content-Transfer-Encoding"))
              ) :
                $filename = chop($ct["name"]) ? $ct["name"] : $cd["filename"];
                if (preg_match('/base64/i', $this->Headers("Content-Transfer-Encoding"))) :
                  $file = base64_decode($this->body);
                elseif (preg_match('/quoted-printable/i', $this->Headers("Content-Transfer-Encoding"))) :
                  $file = quoted_printable_decode($this->body);
                  $file = preg_replace("/(=\n)/", '', $this->body);
                  $file = $this->body;
                elseif (preg_match('/7bit/i', $this->Headers("Content-Transfer-Encoding"))) :
                  $file = $this->body;
                endif;
            if (ini_get(file_uploads) AND $attachments_view == 1) {
                $filepath = $this->attachment_path."/".$filename;
                @unlink($filepath);
                if (chop($filename != '')) :
                  $fp = @fopen($filepath, "a") OR die("Cannot open file \"$filepath\"");
                  fwrite($fp, $file);
                  fclose($fp);
                  if (preg_match('/attachment/i', $cd["type"]) OR preg_match('/inline/i', $cd["type"])) :
                    #echo "\n<p><a href=\"$filepath\">$filename</a><p>";
                    $decoded_part["attachments"] = $filename;
                  endif;
                endif;
            }
            endif;
            if (preg_match('/^(text)/i', $ct["type"] )
                AND !(preg_match('#text/html#i', $ct["type"] ))
                AND !(preg_match('/attachment/i', $cd["type"] ))
                OR (chop($ct["type"]) == "")
               ) :
              $decoded_part["body"]["type"] = $ct["type"];
              $decoded_part["body"]["body"] = $this->body;
            elseif (preg_match('#text/html#i', $ct["type"] ) AND !(preg_match('/attachment/i', $cd["type"] ))) :
              $decoded_part["body"]["type"] = $ct["type"];
              $decoded_part["body"]["body"] = $this->body;
              #echo "<pre>($parts_count)###".htmlspecialchars($ct["type"])."</pre>--###<hr>";

            endif;
             $dp[] = $decoded_part;
             unset($decoded_part);
           }

        endif;
        $message[] = $dp;
        unset($dp);
        unset($is_multpart_related);
        unset($contentid);
        unset($parts);
        if (count($messages) > 0) :
          $this->my_array_compact($messages);
          $next_message = $this->my_array_shift($messages);
          $this->InitMessage($next_message);
          $this->InitMessage($this->body);
        endif;
    } while ($next_message != "");
    return $message;
  }
  function MessageID() {
    return preg_replace('/[<>]/', '', $this->Headers("Message-ID"));
  }
};
