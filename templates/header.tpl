<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
    <title>Tiki!</title>
    {literal}
<script type="text/javascript">
<!--
function chgArtType() {
  if(document.getElementById('articletype').value=='Article') {
    
    document.getElementById('isreview').style.display="none";
  } else {
    document.getElementById('isreview').style.display="block";
    
  }
}

function setMenuCon(foo1,foo2) {
  document.getElementById('menu_url').value=foo1;
  document.getElementById('menu_name').value=foo2;
}

function genPass(w1,w2,w3){
  vo="aeiou";
  co="bcdfgjklmnprstvwxz";
  s=Math.round(Math.random());
  l=Math.round(Math.random()*3)+4;
  p='';
  for(i=0; i < l; i++){
    if (s){
      letra=vo.charAt(Math.round( 
      Math.random() * (vo.length - 1) ));
      s=0;
    }else{
      letra=co.charAt(Math.round( 
      Math.random() * (co.length - 1) ));
      s=1;
    }
    p=p + letra;
  }
  document.getElementById(w1).value=p;
  document.getElementById(w2).value=p;
  document.getElementById(w3).value=p;
}

function setUserModule(foo1) {
  document.getElementById('usermoduledata').value=foo1;
}

function setSomeElement(fooel,foo1) {
  document.getElementById(fooel).value=document.getElementById(fooel).value + foo1;
}

function replaceSome(fooel,what,repl) {
  document.getElementById(fooel).value = document.getElementById(fooel).value.replace(what,repl);
}

function setUserModuleFromCombo(id) {
  document.getElementById('usermoduledata').value=document.getElementById('usermoduledata').value + document.getElementById(id).options[document.getElementById(id).selectedIndex].value;
  //document.getElementById('usermoduledata').value='das';
}


function flip(foo) {
  //document.getElementById(foo).style.visibility="hidden";
  if( document.getElementById(foo).style.display == "none") {
    document.getElementById(foo).style.display="block";
  } else {
    document.getElementById(foo).style.display="none";
  }
}
function show(foo) {
  
    document.getElementById(foo).style.display="block";

}
function hide(foo) {
  
    document.getElementById(foo).style.display="none";

}
// -->
</script>
{/literal}

  </head>
  <body {if $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page}';"{/if}>  
