<?php
if (!defined("_ECHOSERVER_HTML_PARSER")) {
define("_ECHOSERVER_HTML_PARSER",1);

class HtmlParser {
  var $pos,
      $tagpos,
      $length,
      $data,
      $stacktag,
      $stacktagpos,
      $name,
      $quotstate,
      $quottype,
      $parname,
      $pars,
      $tagname,
      $content,
      $contentpos,
      $allreadyparsed,
      $pg,
      $dc,
      $nc,
      $qc,
      $prevstate,
      $processtag,
      $processpar,
      $processparvalue,
      $c,
      $cp,
      $text,
      $incomment,
      $skipto,
      $tagreg,
      $wasquot;
/**********************************************************************************
 * Class constructor
 **********************************************************************************/
  function HtmlParser($data,$grammar,$name="",$datatype=0) {
    $this->dc=array(" ","\t","\r","\n","<",">","\"","'","=","/");
    $this->nc=array("<",">","=","/");
    $this->qc=array("\"","'");
    $this->sc=array("\r","\n"," ","\t");
    $this->prevstate=array("state"=>0,"word"=>"");
    $this->pg=&$grammar;
    $this->pos=0;
    $this->stacktag=array();
    $this->stacktagpos=-1;
    $this->content=array();
    $this->content["contentpos"]=-1;
    $this->c=&$this->content;
    $this->cp=-1;
    $this->quotstate=-1;
    $this->allreadyparsed=0;
    $this->text="";
    $this->processtag=0;
    $this->processpar=0;
    $this->processparvalue=0;
    $this->slevel=array(0); 
    $this->slevelpos=0;
    $this->quottype="";
    $this->skipto="";
    $this->incomment=0;
    $this->tagreg=array();
    $this->wasquot=0;

    if(isset($this->data) && is_array($this->data)) {
      $this->content=&$data;
      $this->allreadyparsed=1;
      return;
    }
    clearstatcache();
    $this->name=$data;
    if (!$datatype) {
      $this->name=$name;
      $this->data=$data;
      $this->length=strlen($this->data);
      return;
    }
    if (!$fp=fopen($this->name,"rb")) {
      $this->SetError(1,"Can't open file $this->name.",0,0,"Error");
      return;
    }
    flock($fp,1);
    $this->data=fread($fp,filesize($this->name));
    flock($fp,3);
    fclose($fp);
    $this->length=strlen($this->data);
  }

/********************************************************************************************
 *  Get word from data
 ********************************************************************************************/
  function GetWord(&$word) {
    $word="";
    $this->wasquot=0;
    if ($this->pos>$this->length) return false;
    while (1) {
      if ($this->pos>$this->length) return false;
      if ($this->pos==$this->length) {
        $this->pos++;
        return true;
      }
      if ($this->data[$this->pos]=="<") {
        if ($this->data[$this->pos+1]=="!")
          if ($this->length>6 && $this->length-$this->pos+1>6) {
            if (substr($this->data,$this->pos,4)=="<!--") {
              $this->incomment=1;
              while($this->pos<$this->length-3) {
                if (substr($this->data,$this->pos,3)=="-->") {
                  $word.="-->";
                  $this->pos+=3;
                  break;
                } else
                  $word.=$this->data[$this->pos++];
              }
              if ($this->incomment) break;
            }
          }
      }
      if (!$this->processtag) {
        if ($this->data[$this->pos]=="<") {
          $this->processtag=1;
          $this->tagpos=strlen($this->text);
        } else {
          $this->text.=$this->data[$this->pos++];
          continue;
        }
      }
      if (in_array($this->data[$this->pos],$this->dc)) {
        if (($this->data[$this->pos]=="<" || $this->data[$this->pos]==">") && $this->quotstate==-1 && $this->processparvalue) {
          $this->processparvalue=0;
          return true;
        }
        if (in_array($this->data[$this->pos],$this->sc) && $this->quotstate==-1) {
          $this->text.=$this->data[$this->pos++];
          if (strlen($word)) {
            if ($this->processparvalue) $this->processparvalue=0;
            return true;
          } else
            continue;
        }
        if (!strlen($word)) {
          if (in_array($this->data[$this->pos],$this->qc) && $this->processpar) {
            if ($this->quotstate==-1) {
              $this->wasquot=1;
              $this->quotstate*=-1;
              $this->quottype=$this->data[$this->pos];
              $this->text.=$this->data[$this->pos++];
              continue;
            } elseif ($this->quottype==$this->data[$this->pos]) {
              $this->quotstate*=-1;
              $this->quottype=$this->data[$this->pos];
              $this->processpar=$this->processparvalue=0;
              $this->text.=$this->data[$this->pos++];
              return true;
            }
          } elseif (in_array($this->data[$this->pos],$this->nc)) {
            $word.=$this->data[$this->pos];
            $this->text.=$this->data[$this->pos++];
            if ($this->processparvalue)
              continue;
            else
              return true;
          }
        } else {
          if (in_array($this->data[$this->pos],$this->qc) && $this->processpar) {
            if ($this->quotstate==1) {
              if ($this->data[$this->pos]==$this->quottype && $this->processparvalue) {
                $this->quotstate*=-1;
                $this->quottype=$this->data[$this->pos];
                $this->processpar=$this->processparvalue=0;
                $this->text.=$this->data[$this->pos++];
//                continue;
              } else {
                if ($this->data[$this->pos]==$this->quottype) {
                  $this->quotstate*=-1;
                  $this->quottype="";
                }
                $word.=$this->data[$this->pos];
                $this->text.=$this->data[$this->pos++];
                continue;
              }
            }
            return true;
          } else {
            if (in_array($this->data[$this->pos],$this->nc)) {
              if ($this->quotstate==-1) {
                if ($this->processparvalue) {
                  if($this->data[$this->pos]!="/" && $this->data[$this->pos]!="=") return true;
                  $word.=$this->data[$this->pos];
                  $this->text.=$this->data[$this->pos++];
                  continue;
                }
              } else {
                $word.=$this->data[$this->pos];
                $this->text.=$this->data[$this->pos++];
                continue;
              }
              return true;
            } elseif ($this->quotstate==-1 && $this->processparvalue && strlen($word)) {
              if ($this->data[$this->pos]==" ") {
                $this->text.=$this->data[$this->pos++];
                $this->processparvalue=0;
                return true;
              }
            }
          }
        }
      }
      $word.=$this->data[$this->pos];
      $this->text.=$this->data[$this->pos++];
    }
    return true;
  }

/********************************************************************************************
 *  Parse HTML code
 ********************************************************************************************
<tagname [parname=|parnane=["|']parvalue["|']|parname][/]> |
<[/]tagname>

in/state 0  1  2  3  4  5  6  7  8
<	       1 -1 -1 -1 -1 -1 -1 -1 -1
/       -1  7  6  6  6  6 -1 -1 -1
=       -1 -1 -1  4 -1 -1 -1 -1 -1
>       -1 -1 -2 -2 -2 -2 -2 -1 -3
anyword -1  2  3  3  5  3 -1  8 -1

-3 end parse close tag
-2 end parse open tag
-1 error
 0 begin parse
 1 got '<', waiting '/' or any word as tag name
 2 got any word as tagname, waiting '/' or '>' or any word as parameter name
 3 got any word as parameter name, waiting '/' or '>' or '=' or any word as parameter name
 4 got '=' waiting '/' or '>' or any word as parameter value
 5 got any word as parameter value, waiting '/' or '>' or any word as parameter name
 6 got '/' waiting '>'
 7 got '/', waiting any word as close tagname
 8 got any word as close tag name, waiting '>'
 ********************************************************************************************/
  function Parse() {
    $automat=array(
// states         0   1   2   3   4   5   6   7   8
      "0"=>array( 1, -1, -1, -1, -1, -1, -1, -1, -1),// <	     
      "1"=>array(-1,  7,  6,  6,  6,  6, -1, -1, -1),// /      
      "2"=>array(-1, -1, -1,  4, -1, -1, -1, -1, -1),// =      
      "3"=>array(-1, -1, -2, -2, -2, -2, -2, -1, -3),// >      
      "4"=>array(-1,  2,  3,  3,  5,  3, -1,  8, -1) // any word
    );                                     
    if (!strlen($this->data)) return;
    $instates=array("<"=>0,"/"=>1,"="=>2,">"=>3);
    $parcount=0;
    $state=0;
    $this->c=&$this->content;
    $this->cp=&$this->content["contentpos"];
    $this->stacktag[0]["tag"]=&$this->c;
    $this->stacktag[0]["level"]=&$this->slevel;
    $this->stacktag[0]["levelpos"]=0;
    $this->stacktagpos=0;
    while(1) {
      if (!$isword=$this->GetWord($word)) break;
      $w=strtolower($word);
      if (!isset($instates[$w]))
        $instate=4;
      else
        $instate=$instates[$w];
//print htmlspecialchars($word).",$state,$instate,$this->quottype<br>";
      $state=$automat[$instate][$state];
      if ($this->wasquot && $state==6) $state=5;
//print htmlspecialchars($word).",$state<br>";
      switch($state) {
        case -3:// end parse close tag
          if (strlen($this->skipto) && $this->tagname!=$this->skipto) {
            $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
            $this->pars=array();
            break;
          } else
            $this->skipto="";
          $script=($this->tagname=="script") ? 1:0;
          $this->AddNewText(substr($this->text,0,$this->tagpos),$script);
          $this->AddNewTag(0);
          $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
          $this->quottype="";
          $this->quotstate=-1;
          $this->text="";
          $this->pars=array();
          $this->tagpos=0;
          break;
        case -2:// end parse open tag
          if (strlen($this->skipto)) {
            $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
            $this->pars=array();
            break;
          }
          $this->AddNewText(substr($this->text,0,$this->tagpos));
          $this->AddNewTag(1,$xmlclose);
          $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
          $this->quottype="";
          $this->quotstate=-1;
          $this->text="";
          $this->pars=array();
          $this->tagpos=0;
          if (isset($this->pg[$this->tagname]["nohavetags"]) && !strlen($this->skipto)) $this->skipto=$this->tagname;
          break;
        case -1:// Error found
          $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
          $this->pars=array();
          if ($this->incomment) {
            if (strlen($this->text)) {
              $this->AddNewText($this->text);
              $this->text="";
              $this->tagpos=0;
            }
            $this->AddNewText($word,0,1);
            $this->incomment=0;
            break;
          }
          if ($word=="<") {
            $state=1;
            $this->processtag=1;
            $this->processparvalue=0;
            $this->tagpos=strlen($this->text)-1;
            $this->quottype="";
            $this->quotstate=-1;
          }
          break;
        case 2:// got any word as tagname, waiting '/' or '>' or any word as parameter name
          $this->tagname=$w;
          $xmlclose=0;
          if (!ereg("^[a-zA-Z0-9!_-]+$",$this->tagname) || strlen($this->skipto)) {
            $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
            $this->quottype="";
            $this->quotstate=-1;
            $this->pars=array();
            break;
          }
          break;
        case 3:// got any word as parameter name, waiting '/' or '>' or '=' or any word as parameter name
          $this->parname=$w;
          if (!ereg("^[a-zA-Z0-9!_-]+$",$this->parname) || strlen($this->skipto)) {
            $parcount=$state=$this->processpar=$this->processparvalue=$this->processtag=0;
            $this->quottype="";
            $this->quotstate=-1;
            $this->pars=array();
            break;
          }
          $this->processpar=1;
          if ($w!="/") {
            $parcount++;
            $this->pars[$this->parname]["single"]=1;
          } else
            $xmlclose=1;
          break;
        case 4:// got '=' waiting '/' or '>' or any word as parameter value
          $this->processparvalue=1;
          break;
        case 5:// got any word as parameter value, waiting '/' or '>' or any word as parameter name
          if ($this->parname!="/") {
            unset($this->pars[$this->parname]["single"]);
            $this->pars[$this->parname]["value"]=$word;
            $this->pars[$this->parname]["quot"]=$this->quottype;
          }
          $this->quottype="";
          $this->processpar=$this->processparvalue=0;
          break;
        case 6:// got '/' waiting '>'
          $xmlclose=1;
          break;
        case 8:// got any word as close tag name, waiting '>'
          $this->tagname=$w;
          break;
      }
      $this->prevstate["states"]=$state;
      $this->prevstate["word"]=$word;
    }
    if (strlen($this->text)) $this->AddNewText($this->text);
  }
/********************************************************************************************
 *  Add new tag
 ********************************************************************************************/
  function AddNewTag($open,$xmlclose=0) {
    $actionclose=0;
    if (!$open && in_array( $this->tagname, $this->pg ) && $this->pg[$this->tagname]["endtag"]!="absent") $actionclose=1;

    if ($open)
      for ($i=$this->stacktagpos;$i>0;$i--) {
        $ct=&$this->stacktag[$i]["tag"];
        $t=&$ct[$ct["contentpos"]];
        $tagname=$t["data"]["name"];
        if (isset($this->pg[$tagname]["closeon"])) {
          if (isset($this->pg[$tagname]["closeon"]["in"]) && sizeof($this->pg[$tagname]["closeon"]["in"]) && in_array($this->tagname,$this->pg[$tagname]["closeon"]["in"]) 
						|| isset($this->pg[$tagname]["closeon"]["notin"]) && sizeof($this->pg[$tagname]["closeon"]["notin"]) && !in_array($this->tagname,$this->pg[$tagname]["closeon"]["notin"])) {
            $actionclose=2;
            break;
          }
        }
        if ($actionclose!=2) $i=-1;
      }

    if ($actionclose) {
      if ($actionclose==1) {
        $i=$this->FindTag($this->tagname);
        if ($i>-1)
          if ($this->tagreg[$this->tagname]!=$this->stacktag[$i]["num"])
            $i=-1;
      }
      if ($i>-1) {
        $this->c=&$this->stacktag[$i]["tag"];
        $this->cp=&$this->c["contentpos"];
        $this->stacktagpos=$i;
        if ($actionclose==1) {
          $c=&$this->c[$this->c["contentpos"]]["content"];
          $cp=&$this->c[$this->c["contentpos"]]["content"]["contentpos"];
          $cp++;
          $c[$cp]["type"]="tag";
          $c[$cp]["data"]["name"]=$this->tagname;
          $c[$cp]["data"]["type"]="close";
          if (isset($this->tagreg[$this->tagname]))
            if ($this->tagreg[$this->tagname])
              $this->tagreg[$this->tagname]--;
          $this->stacktag[$this->stacktagpos]["num"]=$this->tagreg[$this->tagname];
          $this->stacktagpos--;
        }
        if ($this->stacktagpos<sizeof($this->stacktag))
          for ($i=$this->stacktagpos+1;$i<sizeof($this->stacktag);$i++)
            unset($this->stacktag[$i]);
        if ($actionclose==1) return;
      }
    }
    $this->cp++;
    $this->c[$this->cp]["type"]="tag";
    $this->c[$this->cp]["data"]["name"]=$this->tagname;
    $this->c[$this->cp]["data"]["type"]=($open) ? "open" : "close";
    if (!$open)
      if (isset($this->tagreg[$this->tagname]))
        if ($this->tagreg[$this->tagname])
          $this->tagreg[$this->tagname]--;
    if ($xmlclose) $this->c[$this->cp]["xmlclose"]=1;
    if (sizeof($this->pars)) $this->c[$this->cp]["pars"]=$this->pars;
    if ($open && !$xmlclose && in_array( $this->tagname, $this->pg ) && $this->pg[$this->tagname]["endtag"]!="absent") {
      if (!isset($this->tagreg[$this->tagname])) $this->tagreg[$this->tagname]=0;
      $this->tagreg[$this->tagname]++;
      $this->stacktagpos++;
      $this->stacktag[$this->stacktagpos]["tag"]=&$this->c;
      $this->stacktag[$this->stacktagpos]["num"]=$this->tagreg[$this->tagname];
      $this->c[$this->cp]["content"]=array();
      $this->c[$this->cp]["content"]["contentpos"]=-1;
      $this->c=&$this->c[$this->cp]["content"];
      $this->cp=&$this->c["contentpos"];
    }
  }

/********************************************************************************************
 *  Add new text
 ********************************************************************************************/
  function AddNewText($text,$script=0,$comment=0) {
    if (!strlen($text)) return;
    $this->cp++;
    if (!$comment)
      $this->c[$this->cp]["type"]="text";
    else
      $this->c[$this->cp]["type"]="comment";
    if ($script) {
      $inputarray=array("/_top/","/top.location.href/","/([ \n]+)?window\.name/","/parent.location/");
      $replarray=array("_echoserver_file_space","parent.frames('_echoserver_file_space').src","//window.name","parent.frames('_echoserver_file_space').src");
/*
      $text=str_replace("_top","_echoserver_file_space",$text);
      $text=str_replace("top.location.href","parent.frames('_echoserver_file_space').src",$text);
      $text=preg_replace("/([ \n]+)?window\.name/","//window.name",$text);
*/
      $text=preg_replace($inputarray,$replarray,$text);

    }
    $this->c[$this->cp]["data"]=$text;
    $this->text="";
  }

/********************************************************************************************
 *  Find first tag in stack
 ********************************************************************************************/
  function FindTag($tagname) {
    for($i=$this->stacktagpos;$i>=0;$i--)  
      if ($this->stacktag[$i]["tag"][$this->stacktag[$i]["tag"]["contentpos"]]["data"]["name"]==$tagname)
        return $i;
    return -1;
  }
}

} //_ECHOSERVER_HTML_PARSER
?>
