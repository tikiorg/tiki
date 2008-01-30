<h1><a class="pagetitle" href="tiki-pick_avatar.php{if $user ne $userwatch}?view_user={$userwatch}{/if}">{if $user ne $userwatch}{tr}Avatar:{/tr} {$userwatch}{else}{tr}Pick your avatar{/tr}{/if}</a></h1>

{if $user eq $userwatch}
{include file=tiki-mytiki_bar.tpl}
{else}
<div class="navbar"><span class="button2"><a href="tiki-user_preferences.php?view_user={$userwatch|escape}" class="linkbut">{tr}User Preferences{/tr}</a></span></div>
{/if}

<h2>{if $user eq $userwatch}{tr}Your current avatar{/tr}{else}{tr}Avatar{/tr}{/if}</h2>
<table class="normal">
<tr>
  <td class="formcolor">{if $avatar}{$avatar}{else}{tr}no avatar{/tr}{/if}</td>
{if sizeof($avatars) eq 0 and $avatar}
 <td class="formcolor"><a class="link" href="tiki-pick_avatar.php?reset=y" title="{tr}reset{/tr}">{icon _id='cross' alt='{tr}reset{/tr}'}</a></td>
{/if}
</tr>
</table>

{if sizeof($avatars) > 0}

{if $showall eq 'y'}
<h2>{if $user eq $userwatch}{tr}Pick avatar from the library{/tr}{else}{tr}Pick user Avatar{/tr}{/if} <a href="tiki-pick_avatar.php?showall=n">{tr}Hide all{/tr}</a> {$numav} {tr}icons{/tr}</h2>
<div class="normal">
{section name=im loop=$avatars}
<a href="tiki-pick_avatar.php?showall=n&amp;avatar={$avatars[im]|escape:"url"}&amp;uselib=use"><img src="{$avatars[im]}" alt=''/></a>
{/section}
</div>
{else} 

<script type='text/javascript'>
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

<h2>{tr}Pick avatar from the library{/tr} <a href="tiki-pick_avatar.php?showall=y">{tr}Show all{/tr}</a> {$numav} {tr}Items{/tr}</h2>
<form action="tiki-pick_avatar.php" method="post">
<input id="avatar" type="hidden" name="avatar" value="{$yours|escape}" />
{if $user ne $userwatch}<input type="hidden" name="view_user" value="{$userwatch|escape}" />{/if}
<table class="normal">
<tr>
 <td class="formcolor">
 <div align="center">
<a class="link" href="javascript:subavt();">{tr}Prev{/tr}</a>
<img id='avtimg' src="{$yours}" alt='{tr}Avatar Image{/tr}'/>
<a class="link" href="javascript:addavt();">{tr}Next{/tr}</a>
</div>
 </td>
</tr>
<tr>
 <td class="formcolor">
   <div align="center">
	 <input type="submit" name="rand" value="{tr}random{/tr}" />
	 <input type="submit" name="uselib" value="{tr}Use{/tr}" /> 
	 <input type="submit" name="reset" value="{tr}no avatar{/tr}" /> 
	 </div>
 </td>
</tr>
</table>
</form>
{/if}

{/if}

<h2>{tr}Upload your own avatar{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-pick_avatar.php" method="post">
{if $user ne $userwatch}<input type="hidden" name="view_user" value="{$userwatch|escape}" />{/if}
<table class="normal">
<tr><td class="formcolor">{tr}File{/tr} {tr}(Only .gif images, and aproximately 45px x 45px){/tr}:</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
<input name="userfile1" type="file" />
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}Upload{/tr}" /></td></tr>
</table>
</form>
