{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-directory_admin_sites.tpl,v 1.36.2.4 2008-02-05 16:14:44 jyhem Exp $ *}
<h1><a class="pagetitle" href="tiki-directory_admin_sites.php?parent={$parent}">{tr}Admin sites{/tr}</a>
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Directory" target="tikihelp" class="tikihelp" title="{tr}Admin Directory Sites{/tr}">
{icon _id='help'}</a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-directory_admin_sites.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Directory Sites tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit Tpl{/tr}'}</a>{/if}</h1>

{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
{* Navigation bar to admin, admin related, etc *}

<h2>{tr}Parent category{/tr}:</h2>
{* Display the path adding manually the top category id=0 *}
<form name="path" method="post" action="tiki-directory_admin_sites.php">
<select name="parent" onchange="javascript:path.submit();">
<option value="0" {if $parent eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
{section name=ix loop=$categs}
<option value="{$categs[ix].categId|escape}" {if $parent eq $categs[ix].categId}selected="selected"{/if}>{$categs[ix].path}</option>
{/section}
</select>
<input type="submit" name="go" value="{tr}Go{/tr}" />
</form>

{* Dislay a form to add or edit a site *}
<h2>{tr}Add or edit a site{/tr}</h2>
<form action="tiki-directory_admin_sites.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
<input type="hidden" name="siteId" value="{$siteId|escape}" />
<table class="normal">
  <tr>
    <td class="formcolor">{tr}Name{/tr}:</td>
    <td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Description{/tr}:</td>
    <td class="formcolor"><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}URL{/tr}:</td>
    <td class="formcolor"><input type="text" name="url" value="{$info.url|escape}" /></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Categories{/tr}:</td>
    <td class="formcolor">
    <select name="siteCats[]" multiple="multiple" size="4">
    {section name=ix loop=$categs}
      <option value="{$categs[ix].categId|escape}" {if $categs[ix].belongs eq 'y'}selected="selected"{/if}>{$categs[ix].path}</option>
    {/section}
    </select>
    </td>
  </tr>
{if $prefs.directory_country_flag eq 'y'}
  <tr>
    <td class="formcolor">{tr}Country{/tr}:</td>
    <td class="formcolor">
      <select name="country">
        {section name=ux loop=$countries}
        <option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{$countries[ux]}</option>
        {/section}
      </select>
    </td>
  </tr>
{/if}
  <tr>
    <td class="formcolor">{tr}Is valid{/tr}:</td>
    <td class="formcolor"><input name="isValid" type="checkbox" {if $info.isValid eq 'y'}checked="checked"{/if} /></td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" />
  </tr>
</table>
</form>

<h2>{tr}Sites{/tr}</h2>

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_admin_sites.php" method="post">
<div style="text-align:right;"><input type="submit" name="groupdel" value="{tr}Delete selected{/tr}" /></div>
<table class="normal">
  <tr>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}Url{/tr}</a></td>
{if $prefs.directory_country_flag eq 'y'}
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}country{/tr}</a></td>
{/if}
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
    <td class="heading"><a class="tableheading" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isValid_desc'}isValid_asc{else}isValid_desc{/if}">{tr}Valid{/tr}</a></td>
    <td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td class="{cycle advance=false}">{$items[user].name}</td>
<td class="{cycle advance=false}"><a href="{$items[user].url}" target="_new">{$items[user].url}</a></td>
 {if $prefs.directory_country_flag eq 'y'}
<td class="{cycle advance=false}"><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'/></td>
{/if}
<td class="{cycle advance=false}">{$items[user].hits}</td>
<td class="{cycle advance=false}">{$items[user].isValid}</td>
<td  class="{cycle advance=false}">
   <a class="link" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;siteId={$items[user].siteId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].siteId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
	 <input type="checkbox" name="remove[]" value="{$items[user].siteId}" />
</td>
</tr>
<tr>
  <td class="{cycle advance=false}">&nbsp;</td>
  <td class="{cycle}" colspan="5"><i>{tr}Categories{/tr}:{assign var=fsfs value=1}
  {section name=ii loop=$items[user].cats}
  {if $fsfs}{assign var=fsfs value=0}{else}, {/if}
  {$items[user].cats[ii].path}
  {/section}</i>
  </td>
</tr>
{/section}
</table>
</form>

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_admin_sites.php?parent={$parent}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_admin_sites.php?parent={$parent}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-directory_admin_sites.php?parent={$parent}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
