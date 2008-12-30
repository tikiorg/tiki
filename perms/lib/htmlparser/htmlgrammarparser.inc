<?php
if (!defined("_ECHOSERVER_HTML_GRAMMARPARSER")) {
define("_ECHOSERVER_HTML_GRAMMARPARSER",1);

class HtmlGrammarParser {
  var $line,
      $column,
      $pos,
      $length,
      $data,
      $pg,
      $pgpos,
      $parpos,
      $incomment,
      $name,
      $allreadyparsed,
      $errors,
      $errpos,
      $quotstate,
      $firstprev,
      $secondprev,
      $firststate,
      $secondstate,
      $iseof,
      $mode,
      $tagname,
      $parname;
/**********************************************************************************
 * Class constructor
 **********************************************************************************/
  function HtmlGrammarParser($data) {
    $this->firstprev=array("state"=>0,"word"=>"");
    $this->secondprev=array("state"=>0,"word"=>"");
    $this->line=0;
    $this->pos=0;
    $this->errors=array();
    $this->errpos=-1;
    $this->incomment=-1;
    $this->allreadyparsed=0;
    $this->pg=array();
    $this->pgpos=-1;
    $this->quotstate=-1;
    $this->iseof=false;
    $this->firststate=0;
    $this->secondstate=0;
    $this->mode=1;

    if(gettype($this->data)=="array") {
      $this->pg=&$data;
      $this->allreadyparsed=1;
      return;
    }
    clearstatcache();
    $this->name=$data;
    if (!file_exists($this->name)) {
      $this->SetError(1,"File $this->name not exists.",0,0,"Error");
      return;
    }
    if (!$fp=fopen($this->name,"r")) {
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
 *  Store parser's errors and warnings
 ********************************************************************************************/
  function SetError($e,$str,$line=0,$column=0,$errtype="Warning") {
    $this->errors[++$this->errpos]["type"]=$errtype;
    $this->errors[$this->errpos]["code"]=$e;
    $this->err=$e;
    $this->errstr="<b>$errtype:</b> $e, $str";
    if ($line) {
      if (strlen($this->name))
        $this->errstr.="object <font color=\"red\">$this->name</font>";
      $this->errstr.=" Line <b>$line</b>, Column <b>$column</b>";
    }
    $this->errors[$this->errpos]["str"]=$this->errstr."<br>\r\n";
  }

/********************************************************************************************
 *  Print parser's errors and warnings
 ********************************************************************************************/
  function PrintErrors() {
    for ($i=0;$i<=$this->errpos;$i++)
      print $this->errors[$i]["str"];
  }

/********************************************************************************************
 *  Get word from data
 ********************************************************************************************/
  function GetWord($word) {
    $word="";
    $found=0;
    $iter=0;
    if ($this->pos>$this->length)
      return false;
    while (!$found) {
      if ($this->pos>$this->length)
        return false;
      if ($this->pos==$this->length) {
        $this->pos++;
        return $word;
      }
      switch($this->data[$this->pos]) {
        case "*":
          if ($this->quotstate==1) {
            $word.=$this->data[$this->pos++];
            $this->column++;
            break;
          }
          $this->column++;
          $this->pos++;
          if ($word[0]=="/")
            $found=1;
          $word.=$this->data[$this->pos-1];
          break;
        case "/":
          if ($this->quotstate==1) {
            $word.=$this->data[$this->pos++];
            $this->column++;
            break;
          }
          $this->column++;
          $this->pos++;
          if ($word[0]=="*")
            $found=1;
          $word.=$this->data[$this->pos-1];
          break;
        case " ":
        case "\r":
        case "\t":
          if ($this->quotstate==1) {
            $word.=$this->data[$this->pos++];
            $this->column++;
            break;
          }
          $this->column++;
          $this->pos++;
          if (strlen($word))
            $found=1;
          break;
        case "\n":
          if ($this->quotstate==1) {
            $word.=$this->data[$this->pos++];
            $this->column++;
            break;
          }
          $this->column=0;
          $this->line++;
          $this->pos++;
          if (strlen($word))
            $found=1;
          break;
        case ">":
        case "<":
        case "=":
          if ($this->quotstate==1) {
            $word.=$this->data[$this->pos++];
            $this->column++;
          } else {
            if (!strlen($word)) {
              $word=$this->data[$this->pos++];
              $this->column++;
            }
            $found=1;
          }
          break;
        case "\"":
          if ($this->pos) {
            if ($this->data[$this->pos-1]=="\\") {
              $word.=$this->data[$this->pos++];
              $this->column++;
            } else {
              if (!strlen($word)) {
                $this->quotstate*=-1;
                $word=$this->data[$this->pos++];
                $this->column++;
              }
              $found=1;
            }
          } else { 
            $word=$this->data[$this->pos++];
            $this->column++;
            $found=1;
          }
          break;
        default:
          $this->column++;
          $word.=$this->data[$this->pos++];
      }
    }
    return true;
  }

/********************************************************************************************
 *  Parse grammar first step
 ********************************************************************************************
Parse
< [] [] >

in/state 0  1  2  3
<	 1 -1 -1  1
[	-1  2 -1 -1
]	-1 -1  1 -1
>	-1  3 -1 -1
word	-1  1  2 -1
EOF	-1 -1 -1 -2

-2 end parse
 0 begin parse, waiting '<'
 1 got '<' need to parse parameters, or wait '>' or wait '['
 2 got '[' or ']' need to parse parameters
 3 got '>', waiting eof or '<'
 
 ********************************************************************************************/
  function ParseFirst($word) {
    if ($this->iseof) {
      $this->firstprev["state"]=0;
      $this->firstprev["word"]="";
      return true;
    }
    $automat=array(
      "0"=>array( 1, -1, -1,  1),
      "1"=>array(-1,  2, -1, -1),
      "2"=>array(-1, -1,  1, -1),
      "3"=>array(-1,  3, -1, -1),
      "4"=>array(-1,  1,  2, -1),
      "5"=>array(-1, -1, -1, -2)
    );
    switch($word) {
      case "<":
        $instate=0;
        $this->pgpos++;
        $this->parpos=-1;
        break;
      case "[":
        $this->parpos++;
        $instate=1;
        break;
      case "]":
        $instate=2;
        break;
      case ">":
        $instate=3;
        break;
      default:
        $instate=4;
        break;
    }
    $this->firststate=$automat[$instate][$this->firststate];
    if ($this->firststate==-1) return false;
    switch ($this->firststate) {
      case 1:
        $this->mode=1;
        if ($this->firstprev["state"]==1)
          if (!$this->ParseSecond($word)) return false;
        break;
      case 2:
        switch($this->firstprev["state"]) {
          case 1:
            $this->mode=2;
            break;
          case 3:
          case 2:
            if ($this->firstprev["state"]==2)
              $this->mode=2;
            else
              $this->mode=1;
            break;
        }
        if ($this->firstprev["state"]==2)
          if (!$this->ParseSecond($word)) return false;
        break;
      case 3:
        if (isset($this->pg[$this->pgpos]["tag"]["nohavesametag"]))
          $this->pg[$this->pgpos]["tag"]["closeon"]["in"][]=$this->tagname;
        break;
    }
    $this->firstprev["state"]=$this->firststate;
    $this->firstprev["word"]=$word;
    return true;
  }

/********************************************************************************************
 *  Parse grammar second step
 ********************************************************************************************

Parse
par1="value" par2=value

in/state 0  1  2  3  4
=	      -1  2 -1  3 -1
"	      -1 -1  3  4 -1
word	   1 -1  4  3  1
EOF	    -1 -1 -1 -1 -1

-3 end parse by '>'
-2 end parse by ']'
 0 begin parse waiting parname
 1 got parname, waiting '=' or new parname
 2 got '=' waiting any word as value or first '"'
 3 collect words to next '"'
 4 got parvalue, waiting new parname
 ********************************************************************************************/
  function ParseSecond($word) {
    if ($this->iseof) return false;
    $automat=array(
      "0"=>array(-1,  2, -1,  3, -1),
      "1"=>array(-1, -1,  3,  4, -1),
      "2"=>array( 1,  1,  4,  3,  1),
      "3"=>array(-1, -1, -1, -1, -1)
    );
    switch($word) {
      case "=":
        $instate=0;
        break;
      case "\"":
        $instate=1;
        break;
      default:
        $instate=2;
        break;
    }
    $this->secondstate=$automat[$instate][$this->secondstate];
    if ($this->secondstate==-1) return false;
    switch ($this->secondstate) {
      case 1:
        $this->parname=$word;
        if (!ereg("[a-zA-Z_-]+([0-9]+)?",$word)) {
          $this->SetError(1,"Fatal error.",$this->line,$this->column,"Error");
          return false;
        }
        switch($this->mode) {
          case 1:
            $this->pg[$this->pgpos]["tag"][$this->parname]="";
            break;
          case 2:
            $this->pg[$this->pgpos]["pars"][$this->parpos][$this->parname]="";
            break;
        }
        break;
      case 4:
        switch($this->mode) {
          case 1:
            if ($this->secondprev["state"]==3)
              $this->pg[$this->pgpos]["tag"][$this->parname]=$this->secondprev["word"];
            else
              $this->pg[$this->pgpos]["tag"][$this->parname]=$word;
            if ($this->parname=="closeon") {
              $notexists=array();
              $exists=array();
              $this->ParseCloseOn($this->pg[$this->pgpos]["tag"][$this->parname],&$notexists,&$exists);
              $this->pg[$this->pgpos]["tag"][$this->parname]=array();
              $this->pg[$this->pgpos]["tag"][$this->parname]["notin"]=$notexists;
              $this->pg[$this->pgpos]["tag"][$this->parname]["in"]=$exists;
            } elseif ($this->parname=="tag") 
              $this->tagname=$this->pg[$this->pgpos]["tag"]["tag"];
            break;
          case 2:
            if ($this->secondprev["state"]==3)
              $this->pg[$this->pgpos]["pars"][$this->parpos][$this->parname]=$this->secondprev["word"];
            else
              $this->pg[$this->pgpos]["pars"][$this->parpos][$this->parname]=$word;
            break;
        }
        break;
    }
    $this->secondprev["state"]=$this->secondstate;
    $this->secondprev["word"]=$word;
    return true;
  }

/********************************************************************************************
 *  Parse closeon structure
 ********************************************************************************************/
  function ParseCloseOn($str,$notexists,$exists) {
    $arr=explode("|",$str);
    if (!is_array($arr)) {
      if (!strlen($str)) 
        return;
      else
        $arr[]=$str;
    }
    for ($i=0;$i<sizeof($arr);$i++) {
      if ($arr[$i][0]=="!")
        $notexists[]=substr($arr[$i],1,strlen($arr[$i])-1);
      else
        $exists[]=$arr[$i];
    }
  }

/********************************************************************************************
 *  Parse grammar
 ********************************************************************************************/
  function Parse() {
    if ($this->allreadyparsed) return true;
    $this->line=1;
    while(1) {
      $isword=$this->GetWord(&$word);
      if (!$isword) $this->iseof=true;
      switch (strtolower($word)) {
        case "/*";
          $this->incomment*=-1;
          break;
        case "*/";
          if ($this->incomment!=1) {
            $this->SetError(1,"Not found begin of comment operator.",$this->line,$this->column,"Error");
            return;
          }
          $this->incomment*=-1;
          break;
        default:
          if ($this->incomment==1) break;            
          if (!$this->ParseFirst($word)) {
            $this->SetError(1,"Fatal error",$this->line,$this->column,"Error");
            return false;
          }
          break;
      }
      if ($this->iseof) break;
    }
    if ($this->incomment==1) {
      $this->SetError(1,"Not found end of comment operator.",$this->line,$this->column,"Error");
      return false;
    }
    $this->PrepareGrammar();
    return true;
  }
/********************************************************************************************
 *  Prepare grammar for future using
 ********************************************************************************************/
  function PrepareGrammar() {
    $edittagsaftertable=$this->ScanGrammar();
    $l=sizeof($this->pg);
    for ($i=0;$i<$l;$i++) {
      $this->pg[$this->pg[$i]["tag"]["tag"]]=$this->pg[$i]["tag"];
      if (isset($this->pg[$i]["pars"])) {
        $n=sizeof($this->pg[$i]["pars"]);
        for ($j=0;$j<$n;$j++)
          $this->pg[$this->pg[$i]["tag"]["tag"]]["pars"][$this->pg[$i]["pars"][$j]["par"]]=$this->pg[$i]["pars"][$j];
      } else
        $this->pg[$this->pg[$i]["tag"]["tag"]]["pars"]=array();
      unset($this->pg["$i"]);
    }
    $this->pg["EDIT_TAGS_AFTER_TABLE"]=$edittagsaftertable;
  }  
/********************************************************************************************
 *  Scan grammar for creating edittagsafter table
 ********************************************************************************************/
  function ScanGrammar() {
    $edittagsaftertable=array();
    for ($i=0;$i<sizeof($this->pg);$i++)
      if (isset($this->pg[$i]["tag"]["edittagsafter"]))
        if (!in_array($this->pg[$i]["tag"]["edittagsafter"],$edittagsaftertable)) $edittagsaftertable[]=$this->pg[$i]["tag"]["edittagsafter"];
    return $edittagsaftertable;
  }
/********************************************************************************************
 *  Save precompiled grammar in file
 ********************************************************************************************/
  function SaveGrammar($name) {
    $str=serialize($this->pg);
    if (!$fp=fopen($name,"w"))
      print "<br>Error: Can't create file $name. Unable to save grammar.<br>";
    flock($fp,2);
    fwrite($fp,$str,strlen($str));
    flock($fp,3);
    fclose($fp);
  }
}

} //_ECHOSERVER_HTML_GRAMMARPARSER
?>
