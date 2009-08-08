{title help="Theme+Control"}{tr}Theme Control Center: Sections{/tr}{/title}

{remarksbox type="notice" title="{tr}Notice{/tr}"}
<b>{tr}Theme is selected as follows{/tr}:</b><br />
1. {tr}If a theme is assigned to the individual object that theme is used.{/tr}<br />
2. {tr}If not then if a theme is assigned to the object's category that theme is used{/tr}<br />
3. {tr}If not then a theme for the section is used{/tr}<br />
4. {tr}If none of the above was selected the user theme is used{/tr}<br />
5. {tr}Finally if the user didn't select a theme the default theme is used{/tr}
{/remarksbox}

<div class="navbar">
	{button href="tiki-theme_control.php" _text="{tr}Control by Categories{/tr}"}
	{button href="tiki-theme_control_objects.php" _text="{tr}Control by Objects{/tr}"}
</div>

<h2>{tr}Assign themes to sections{/tr}</h2>
<form action="tiki-theme_control_sections.php" method="post">
<table class="normal">
<tr>
  <td class="formcolor">{tr}Section{/tr}</td>
  <td class="formcolor">{tr}Theme{/tr}</td>
  <td class="formcolor">&nbsp;</td>
</tr>
<tr>
  <td class="formcolor">
    <select name="section">
      {foreach key=sec item=ix from=$sections}
      <option value="{$sec|escape}">{$sec}</option>
      {/foreach}
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
    <input type="submit" name="assign" value="{tr}Assign{/tr}" />
  </td>
</tr>
</table>
</form> 

<h2>{tr}Assigned sections{/tr}</h2>
<form action="tiki-theme_control_sections.php" method="post">
<table class="normal">
<tr>
<th><input type="submit" name="delete" value="{tr}Del{/tr}" /></th>
<th><a href="tiki-theme_control_sections.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'section_desc'}section_asc{else}section_desc{/if}">{tr}Section{/tr}</a></th>
<th><a href="tiki-theme_control_sections.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'theme_desc'}theme_asc{else}theme_desc{/if}">{tr}Theme{/tr}</a></th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="sec[{$channels[user].section}]" />
</td>
<td class="{cycle advance=false}">{$channels[user].section}</td>
<td class="{cycle}">{$channels[user].theme}</td>
</tr>
{/section}
</table>
</form>
<br /> <br /> <br /> 
