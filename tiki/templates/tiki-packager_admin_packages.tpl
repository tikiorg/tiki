{popup_init src="lib/overlib.js"}
<a class="pagetitle" href="{$myURL}">{tr}Packager Admin{/tr}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=PackagerAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Packager Admin{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' />{/if}
{if $feature_help eq 'y'}</a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-packager_admin_packages.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Packager Admin{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' />{/if}
{if $feature_view_tpl eq 'y'}</a>{/if}
<br />

<br /><br /><br />

{cycle name=tabs values="1,2" print=false advance=false}
<div class="tabs">
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}List{/tr}</span>
<span id="tab{cycle name=tabs}" class="tab">{tr}Create{/tr}</span>
</div>

{cycle name=content values="1,2" print=false advance=false}

<div id="content{cycle name=content}" class="content">
<h3>{tr}List of packages{/tr}</h3>
<hr />

<form method="post" action="tiki-packager.php">
<table class="findtable"><tr>
<td><label for="groups_find">{tr}Find{/tr}</label></td>
<td><input type="text" name="find" id="groups_find" value="{$find|escape}" /></td>
<td>
<input type="submit" name="search" value="{tr}Find{/tr}" />
<td>{tr}Number of displayed rows{/tr}</td>
<td><input type="text" size="4" name="numrows" value="{$numrows|escape}">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
</tr></table>
</form>
<hr />
<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbut">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="{$myURL}?initial={$initials[ini]}{if $find}&amp;find={$find|escape:"url"}{/if}{if $offset}&amp;offset={$offset}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="{$myURL}?initial={if $find}&amp;find={$find|escape:"url"}{/if}{if $offset}&amp;offset={$offset}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{tr}All{/tr}</a>
</div>
<hr />
<br />
<form action="{$myURL}" method="POST">
<input type="hidden" value="{$offset}" name="offset" />
<input type="hidden" value="{$find}" name="find" />
<input type="hidden" value="{$initial}" name="initial" />
<input type="hidden" value="{$sort_mode}" name="sort_mode" />
<input type="hidden" value="{$numrows}" name="numrows" />
<table class="normal">
<thead>
<tr>
<td class="heading" style="width: 20px;">{tr}Edit selection{/tr}</td>
<td class="heading" style="width: 20px;">{tr}Edit{/tr}</td>
<td class="heading" style="width: 20px;">{tr}Remove All{/tr}</td>
<td class="heading"><a class="tableheading" href="{$myURL}?offset={$offset}&amp;sort_mode={if $sort_mode eq '1'}0{else}1{/if}">{tr}name{/tr}</a></td>
<td class="heading" style="width: 20px;">&nbsp;</td>
</tr>
</thead>
<tbody>
{section name=mf loop=$manifests}
{cycle name=mfRows values="even,odd" print=false}
<tr class="{cycle name=mfRows advance=false }">
<td align="center">
<input type="radio" name='package' value="{$manifests[mf]}"/></td>
<td align="center"><a class="link" href="{$myURL}?package={$manifests[mf]}" title="{tr}Click here to edit this manifest{/tr}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a></td>
<td align="center"><input type="checkbox" value="{$manifests[mf]}" name="packages[]" /></td>
<td><a class="link" href="{$myURL}?package={$manifests[mf]}" title="{tr}Click here to edit this manifest{/tr}">{$manifests[mf]}</a></td>
<td>
<a class="link" href="{$myURL}?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete-package&amp;package={$manifests[mf]}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this manifest?{/tr}')" 
title={tr}"Click here to delete this manifest"{/tr}><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
</td>
</tr>
{/section}
<tbody>
</table>
<br />
<input type="submit" name="action" value="{tr}Remove selected packages{/tr}" onclick="confirmTheLink(this, '{tr}Are you sure you want to delete these manifests?{/tr}');"/>
</form>
<br />
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="{$myURL}?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$count_my_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{$myURL}?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$count_my_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$numrows}
<a class="prevnext" href="{$myURL}?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$selector_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<div id="content{cycle name=content}" class="content">
<h3>{tr}Create new package{/tr}</h3>
<hr />

<form method="POST" action="tiki-packager.php">
<table>
<tr>
<td><label for="package">{tr}Package name{/tr}</label></td>
<td><input id="package" type="text" name="package" size="80"/>.mf</td>
</tr>
</table>
<hr />

<input id="save_draft" type="checkbox" name="save_draft" value="{$save_draft}" {if $save_draft eq '1'}checked="checked"{/if} />

<label for="save_draft">{tr}Save draft{/tr}</label>
<hr />
<input type="submit" name="action" value="{tr}Create package{/tr}"/>
</form>

</div>
