<a class="pagetitle" href="tiki-pick_avatar.php">{tr}Pick your avatar{/tr}</a><br /><br />
{include file=tiki-mytiki_bar.tpl}
<br /><br />
<table class="normal">
<tr>
  <td class="formcolor">{tr}Your current avatar{/tr}:</td>
  <td class="formcolor">
    {$avatar}
  </td>
</tr>
</table>

{if $showall eq 'y'}
<h2>{tr}Pick avatar from the library{/tr} <a href="tiki-pick_avatar.php?showall=n">{tr}Hide all{/tr}</a> {$numav} {tr}items{/tr}</h2>
<div class="normal">
{section name=im loop=$avatars}
<a href="tiki-pick_avatar.php?showall=n&amp;avatar={$avatars[im]|escape:"url"}&amp;uselib=use"><img src="{$avatars[im]}"></a>
{/section}
</div>
{else} 

<script language='Javascript' type='text/javascript'>
var avatars = new Array();
{section name=ix loop=$avatars}
  avatars[{$smarty.section.ix.index}] = '{$avatars[ix]}';
{if $smarty.section.ix.index eq $yours}
{assign var="yours" value=$avatars[ix]}
{/if}
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

<h2>{tr}Pick avatar from the library{/tr} <a href="tiki-pick_avatar.php?showall=y">{tr}Show all{/tr}</a> {$numav} {tr}items{/tr}</h2>
<form action="tiki-pick_avatar.php" method="post">
<input id="avatar" type="hidden" name="avatar" value="{$yours|escape}" />
<table class="normal">
<tr>
 <td class="formcolor">
 <div align="center">
<a class="link" href="javascript:subavt();">{tr}prev{/tr}</a>
<img id='avtimg' src="{$yours}" />
<a class="link" href="javascript:addavt();">{tr}next{/tr}</a>
</div>
 </td>
</tr>
<tr>
 <td class="formcolor">
   <div align="center">
	 <input type="submit" name="rand" value="{tr}random{/tr}" />
	 <input type="submit" name="uselib" value="{tr}use{/tr}" /> 
	 </div>
 </td>
</tr>
</table>
</form>
{/if}


<h2>{tr}Upload your own avatar{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-pick_avatar.php" method="post">
<table class="normal">
<tr><td class="formcolor">{tr}File{/tr}:</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
<input name="userfile1" type="file">
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
