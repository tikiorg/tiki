<a href="tiki-admin_links.php" class="pagetitle">{tr}Featured Links{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=FeaturedLinksAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin featured links{/tr}">
<img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_links.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin featured links tpl{/tr}">
<img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>{/if}

<!-- begin -->




<br /><br />
<a class="linkbut" href="tiki-admin_links.php?generate=1">[{tr}Generate positions by hits{/tr}]</a>
<h3>{tr}List of featured links{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}URL{/tr}</td>
<td class="heading">{tr}Title{/tr}</td>
<td class="heading">{tr}Hits{/tr}</td>
<td class="heading">{tr}Position{/tr}</td>
<td class="heading">{tr}Type{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
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
 <a class="link" href="tiki-admin_links.php?remove={$links[user].url|escape:"url"}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this link?{/tr}')"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
 <a class="link" href="tiki-admin_links.php?editurl={$links[user].url|escape:"url"}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{sectionelse}
<tr><td colspan="2">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
{if $editurl eq 'n'}
<h3>{tr}Add Featured Link{/tr}</h3>
{else}
<h3>{tr}Edit this Featured Link:{/tr} {$title}</h3>
<a class="linkbut" href="tiki-admin_links.php">[{tr}Create new Featured Link{/tr}]</a><br /><br />
{/if}
<form action="tiki-admin_links.php" method="post">
<table class="normal">
{if $editurl eq 'n'}
<tr><td class="formcolor">URL</td><td class="formcolor"><input type="text" name="url" /></td></tr>
{else}
<tr><td class="formcolor">URL</td><td class="formcolor">{$editurl}
<input type="hidden" name="url" value="{$editurl|escape}" />
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Title{/tr}</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Position{/tr}</td><td class="formcolor"><input type="text" size="3" name="position" value="{$position|escape}" /> (0 {tr}disables the link{/tr})</td></tr>
<tr><td class="formcolor">{tr}Link type{/tr}</td><td class="formcolor">
<select name="type">
<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}replace current page{/tr}</option>
<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}framed{/tr}</option>
<option value="n" {if $type eq 'n'}selected="selected"{/if}>{tr}open new window{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="add" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>