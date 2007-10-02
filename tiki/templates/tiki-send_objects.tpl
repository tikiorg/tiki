{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-send_objects.tpl,v 1.27 2007-10-02 17:27:06 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-send_objects.php">{tr}Send objects{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/CommunicationsCenterDoc" target="tikihelp" class="tikihelp" title="{tr}Help on Communication Center{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-send_objects.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Send Objects tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" height="16" width="16" alt='{tr}Edit template{/tr}' /></a>{/if}</h1>

{if $msg}
<div class="cbox">
<div class="cbox-title">
{tr}Transmission results{/tr}
</div>
<div class="cbox-data">
{$msg}
</div>
</div>
{/if}
<br />
<form method="post" action="tiki-send_objects.php">

<div class="cbox">
<div class="cbox-title">
{tr}Filter{/tr}
</div>
<div class="cbox-data">
{tr}Filter{/tr}:<input type="text" name="find" value="{$find|escape}"/><input type="submit" name="filter" value="{tr}Filter{/tr}" /><br />
</div>
</div>
<br />

{if $tiki_p_send_pages eq 'y'}
<div class="cbox">
<div class="cbox-title">
{tr}Send Wiki Pages{/tr}
</div>
<div class="cbox-data">
<div class="simplebox">
<b>{tr}Pages{/tr}</b>: 
{section name=ix loop=$sendpages}
{$sendpages[ix]}&nbsp;
{/section}
</div>
<select name="pageName">
{section name=ix loop=$pages}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName}</option>
{/section}
</select>
<input type="submit" name="addpage" value="{tr}Add Page{/tr}" />
<input type="submit" name="clearpages" value="{tr}Clear{/tr}" />
</div>
</div>

{if count($structures)}
<div class="cbox">
<div class="cbox-title">
{tr}Send a structure{/tr}
</div>
<div class="cbox-data">
<div class="simplebox">
<b>{tr}Structures{/tr}</b>: 
{section name=ix loop=$sendstructures_names}
{$sendstructures_names[ix]}&nbsp;
{/section}
</div>
<select name="structure">
{foreach item=struct from=$structures}
<option value="{$struct.page_ref_id|escape}">{$struct.pageName|escape}{if $struct.page_alias} (alias: {$struct.page_alias}){/if}</option>
{/foreach}
</select><input type="submit" name="addstructure" value="{tr}Add Structure{/tr}" />
<input type="submit" name="clearstructures" value="{tr}Clear{/tr}" />
</div>
</div>
{/if}

{/if}

<br />

{if $tiki_p_send_articles eq 'y'}
<div class="cbox">
<div class="cbox-title">
{tr}Send Articles{/tr}
</div>
<div class="cbox-data">
<div class="simplebox">
<b>{tr}Articles{/tr}</b>: 
{section name=ix loop=$sendarticles}
{$sendarticles[ix]}&nbsp;
{/section}
</div>
<select name="articleId">
{section name=ix loop=$articles}
<option value="{$articles[ix].articleId|escape}">{$articles[ix].articleId}:{$articles[ix].title}</option>
{/section}
</select>
<input type="submit" name="addarticle" value="{tr}Add Article{/tr}" />
<input type="submit" name="cleararticles" value="{tr}Clear{/tr}" />
</div>
</div>
{/if}

<div class="cbox">
<div class="cbox-title">
{tr}Send objects to this site{/tr}
</div>
<div class="cbox-data">
<input type="hidden" name="sendpages" value="{$form_sendpages|escape}" />
<input type="hidden" name="sendstructures" value="{$form_sendstructures|escape}" />
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />
<table>
<tr><td class="form">{tr}Site{/tr}:</td><td class="form"><input type="text" name="site" value="{$site|escape}" /></td></tr>
<tr><td class="form">{tr}Path{/tr}:</td><td class="form"><input type="text" name="path" value="{$path|escape}" /></td></tr>
<tr><td class="form">{tr}Username{/tr}:</td><td class="form"><input type="text" name="username" value="{$username|escape}" /></td></tr>
<tr><td class="form">{tr}Password{/tr}:</td><td class="form"><input type="password" name="password" value="{$password|escape}" /></td></tr>
<tr><td align="center" colspan="2" class="form"><input type="submit" name="send" value="{tr}Send{/tr}" /></td></tr>
</table>
</div>
</div>

</form>
