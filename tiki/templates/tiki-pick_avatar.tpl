<a class="pagetitle" href="tiki-pick_avatar.php">{tr}Pick your avatar{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
<script>
var avatars = new Array();
{section name=ix loop=$avatars}
  avatars[{$smarty.section.ix.index}] = '{$avatars[ix]}';
{/section}

var pepe=1;

{literal}
function addavt() {
  pepe++;
  if(pepe > avatars.length-1) {
    pepe =0;
  }
  document.getElementById('avtimg').src=avatars[pepe]; 
  document.getElementById('avatar').value=avatars[pepe];
}

function subavt() {
  pepe--;
  if(pepe < 0 ) {
    pepe=avatars.length-1
  }
  document.getElementById('avtimg').src=avatars[pepe]; 
  document.getElementById('avatar').value=avatars[pepe];
}

{/literal}


</script>

<table class="normal">
<tr>
  <td class="formcolor">{tr}Your current avatar{/tr}:</td>
  <td class="formcolor">
    {$avatar}
  </td>
</tr>
</table>
<h2>{tr}Pick avatar from the library{/tr}</h2>
<form action="tiki-pick_avatar.php" method="post">
<input id=avatar type="hidden" name="avatar" value="img/avatars/000.gif" />
<table class="normal">
<tr>
 <td class="formcolor">
 <div align="center">
<a class="link" href="javascript:subavt();">{tr}prev{/tr}</a>
<img id='avtimg' src="img/avatars/000.gif" />
<a class="link" href="javascript:addavt();">{tr}next{/tr}</a>
</div>
 </td>
</tr>
<tr>
 <td class="formcolor">
   <div align="center"><input type="submit" name="uselib" value="{tr}use{/tr}" /></div>
 </td>
</tr>
</table>
</form>
<h2>{tr}Upload your own avatar{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-pick_avatar.php" method="post">
<table class="normal">
<tr>
<tr><td class="formcolor">{tr}File{/tr}:</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
<input name="userfile1" type="file">
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
