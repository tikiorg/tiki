<a href="tiki-admin_links.php" class="pagetitle">{tr}Featured links{/tr}</a>

<!-- the help link info -->
  
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=FeaturedLinksAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin featured links{/tr}">{$helpIcon $helpIconDesc}</a>{/if}

<!-- link to tpl -->

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_links.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin featured links tpl{/tr}"><img alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>{/if}

<!-- begin -->
<br /><br />
<a class="linkbut" href="tiki-admin_links.php?generate=1">{tr}Generate positions by hits{/tr}</a>
<h3>{tr}List of featured links{/tr}</h3>
<table>
<tr>
<th>{tr}url{/tr}</th>
<th>{tr}title{/tr}</th>
<th>{tr}hits{/tr}</th>
<th>{tr}position{/tr}</th>
<th>{tr}type{/tr}</th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$links}
<tr>
<td class="{cycle advance=false}">{$links[user].url}</td>
<td class="{cycle advance=false}">{$links[user].title}</td>
<td class="{cycle advance=false}">{$links[user].hits}</td>
<td class="{cycle advance=false}">{$links[user].position}</td>
<td class="{cycle advance=false}">{$links[user].type}</td>
<td class="{cycle}">
 <a href="tiki-admin_links.php?remove={$links[user].url|escape:"url"}"><img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
 <a href="tiki-admin_links.php?editurl={$links[user].url|escape:"url"}"><img alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{sectionelse}
<tr><td colspan="2"><b>{tr}No records found{/tr}</b></td></tr>
{/section}
</table>
<br />
{if $editurl eq 'n'}
<h3>{tr}Add Featured Link{/tr}</h3>
{else}
<h3>{tr}Edit this Featured Link:{/tr} {$title}</h3>
<a href="tiki-admin_links.php">{tr}Create new Featured Link{/tr}</a>
{/if}
<form action="tiki-admin_links.php" method="post">
<table>
{if $editurl eq 'n'}
<tr><td>URL</td><td><input type="text" name="url" /></td></tr>
{else}
<tr><td>URL</td><td>{$editurl}
<input type="hidden" name="url" value="{$editurl|escape}" />
</td></tr>
{/if}
<tr><td>{tr}Title{/tr}</td><td><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td>{tr}Position{/tr}</td><td><input type="text" size="3" name="position" value="{$position|escape}" /> (0 {tr}disables the link{/tr})</td></tr>
<tr><td>{tr}Link type{/tr}</td><td>
<select name="type">
<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}replace current page{/tr}</option>
<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}framed{/tr}</option>
<option value="n" {if $type eq 'n'}selected="selected"{/if}>{tr}open new window{/tr}</option>
</select>
</td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="add" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
