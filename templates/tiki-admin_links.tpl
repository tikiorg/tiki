<h1><a href="tiki-admin_links.php" class="pagetitle">{tr}Featured links{/tr}</a>
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}FeaturedLinksAdmin" target="tikihelp" class="tikihelp" title="{tr}Admin Featured Links{/tr}">
{icon _id='help'}</a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_links.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Featured Links Template{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To use these links, you must assign the featured_links <a class="rbox-link" href="tiki-admin_modules.php"> module</a>.{/tr}</div>
</div>
<br />


<a class="linkbut" href="tiki-admin_links.php?generate=1">{tr}Generate positions by hits{/tr}</a>
<h2>{tr}List of featured links{/tr}</h2>
<table class="normal">
<tr>
<td class="heading">{tr}Url{/tr}</td>
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
 <a title="{tr}Edit{/tr}" class="link" href="tiki-admin_links.php?editurl={$links[user].url|escape:"url"}">{icon _id='page_edit'}</a> &nbsp;
 <a title="{tr}Delete{/tr}" class="link" href="tiki-admin_links.php?remove={$links[user].url|escape:"url"}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
</td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
{if $editurl eq 'n'}
<h2>{tr}Add Featured Link{/tr}</h2>
{else}
<h2>{tr}Edit this Featured Link:{/tr} {$title}</h2>
<a href="tiki-admin_links.php">{tr}Create new Featured Link{/tr}</a>
{/if}
<form action="tiki-admin_links.php" method="post">
<table class="normal">
{if $editurl eq 'n'}
<tr class="formcolor"><td>URL</td><td><input type="text" name="url" /></td></tr>
{else}
<tr class="formcolor"><td>URL</td><td>{$editurl}
<input type="hidden" name="url" value="{$editurl|escape}" />
</td></tr>
{/if}
<tr class="formcolor"><td>{tr}Title{/tr}</td><td><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Position{/tr}</td><td><input type="text" size="3" name="position" value="{$position|escape}" /> (0 {tr}disables the link{/tr})</td></tr>
<tr class="formcolor"><td>{tr}Link type{/tr}</td><td>
<select name="type">
<option value="r" {if $type eq 'r'}selected="selected"{/if}>{tr}replace current page{/tr}</option>
<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}framed{/tr}</option>
<option value="n" {if $type eq 'n'}selected="selected"{/if}>{tr}open new window{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="add" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
