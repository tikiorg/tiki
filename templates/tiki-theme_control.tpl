{title help="Theme+Control"}{tr}Theme Control Center: Categories{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To learn more about <a class="rbox-link" target="tikihelp" href="http://themes.tikiwiki.org">themes</a>.{/tr}{/remarksbox}

<div class="simplebox">
<b>{tr}Theme is selected as follows{/tr}:</b><br />
1. {tr}If a theme is assigned to the individual object that theme is used.{/tr}<br />
2. {tr}If not then if a theme is assigned to the object's category that theme is used{/tr}<br />
3. {tr}If not then a theme for the section is used{/tr}<br />
4. {tr}If none of the above was selected the user theme is used{/tr}<br />
5. {tr}Finally if the user didn't select a theme the default theme is used{/tr}<br />
</div>
<br /><br />
<span class="button2"><a href="tiki-theme_control_objects.php">{tr}Control by Objects{/tr}</a></span>
<span class="button2"><a href="tiki-theme_control_sections.php">{tr}Control by Sections{/tr}</a></span>
<h2>{tr}Assign themes to categories{/tr}</h2>
<form action="tiki-theme_control.php" method="post">
<table class="normal">
<tr>
  <td class="formcolor">{tr}Category{/tr}</td>
  <td class="formcolor">{tr}Theme{/tr}</td>
  <td class="formcolor">&nbsp;</td>
</tr>
<tr>
  <td class="formcolor">
    <select name="categId">
      {section name=ix loop=$categories}
      <option value="{$categories[ix].categId|escape}">{$categories[ix].name} ({$categories[ix].categId})</option>
      {/section}
    </select>
  </td>
  <td class="formcolor">
    <select name="theme">
      {section name=ix loop=$styles}
      <option value="{$styles[ix]|escape}">{$styles[ix]}</option>
      {/section}
    </select>
  </td>
  <td class="formcolor">
    <input type="submit" name="assigcat" value="{tr}Assign{/tr}" />
  </td>
</tr>
</table>
</form> 

<h2>{tr}Assigned categories{/tr}</h2>

{include file='find.tpl' _sort_mode='y'}

<form action="tiki-theme_control.php" method="post">
<table class="normal">
<tr>
<td class="heading"><input type="submit" name="delete" value="{tr}Del{/tr}" /></td>
<td class="heading" ><a class="tableheading" href="tiki-theme_control.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Category{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-theme_control.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'theme_desc'}theme_asc{else}theme_desc{/if}">{tr}theme{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="categ[{$channels[user].categId}]" />
</td>
<td class="{cycle advance=false}">{$channels[user].name} ({$channels[user].categId})</td>
<td class="{cycle}">{$channels[user].theme}</td>
</tr>
{/section}
</table>
</form>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-theme_control.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-theme_control.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-theme_control.php?tasks_useDates={$tasks_useDates}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
