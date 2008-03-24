{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-admin_graph.php">{tr}Process Graph{/tr}</a>


  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Workflow"  target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Galaxia Admin Graph{/tr}"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>{/if}



      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-g-admin_graph.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Galaxia Admin Graph tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}



</h1>
{include file=tiki-g-monitor_bar.tpl}
{if $pid > 0}
  {include file=tiki-g-proc_bar.tpl}
  {if count($errors)}
    <div class="wikitext">
      {tr}This process is invalid{/tr}:<br />
      {section name=ix loop=$errors}
        <small>{$errors[ix]}</small><br />
      {/section}
    </div>
  {/if}
{/if}
  <h2>{tr}Process Graph for{/tr} {$info.name}</h2>
  {if $info.graph neq ''}
    <table class="normal">
      <tr>
        <td>
          <center>
            {if $info.map neq ''}
              <img src="{$info.graph}" alt="{$info.name}" border="0" usemap="#procmap" />
              <map name="procmap">
                {$info.map}
              </map>
            {else}
              <img src="{$info.graph}" alt="{$info.name}" border="0" />
            {/if}
          </center>
        </td>
      </tr>
    </table>
  {else}
    {tr}No process graph is available. Either the process still contains errors, the graph is not generated yet, or <a href="http://www.research.att.com/sw/tools/graphviz/">GraphViz</a> is not properly installed.{/tr}
  {/if}
  <br /><br />
<h2>{tr}List of processes{/tr} ({$cant})</h2>
<form action="tiki-g-admin_graph.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find|escape}" />
{tr}Process{/tr}:
<select name="filter_name">
<option value="">{tr}All{/tr}</option>
{section loop=$all_procs name=ix}
<option  value="{$all_procs[ix].name|escape}">{$all_procs[ix].name}</option>
{/section}
</select>

{tr}Status{/tr}:
<select name="filter_active">
<option value="">{tr}All{/tr}</option>
<option value="y">{tr}Active{/tr}</option>
<option value="n">{tr}Inactive{/tr}</option>
</select>

<input type="submit" name="filter" value="{tr}Filter{/tr}" />
</form>
<form action="tiki-g-admin_graph.php" method="post">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="filter_name" value="{$filter_name|escape}" />
<input type="hidden" name="filter_active" value="{$filter_active|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td style="text-align:center;"  class="heading"><input type="submit" name="delete" value="x " /></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_processes.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_processes.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'version_desc'}version_asc{else}version_desc{/if}">{tr}Version{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_processes.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isActive_desc'}isActive_asc{else}isActive_desc{/if}">{tr}act{/tr}</a></td>
<td  class="heading" ><a class="tableheading" href="tiki-g-admin_processes.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isValid_desc'}isValid_asc{else}isActive_desc{/if}">{tr}val{/tr}</a></td>
<td  class="heading" >{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:center;" class="{cycle advance=false}">
		<input type="checkbox" name="process[{$items[ix].pId}]" />
	</td>
	<td class="{cycle advance=false}">
	  <a class="link" href="tiki-g-admin_graph.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;pid={$items[ix].pId}">{$items[ix].name}</a>
	</td>
	<td style="text-align:right;" class="{cycle advance=false}">
	  {$items[ix].version}
	</td>
		<td class="{cycle advance=false}" style="text-align:center;">
	  {if $items[ix].isActive eq 'y'}
	  <img src='lib/Galaxia/img/icons/refresh2.gif' alt=' ({tr}Active{/tr}) ' title='{tr}Active Process{/tr}' />
	  {else}
	  &nbsp;
	  {/if}
	</td>
	<td class="{cycle advance=false}" style="text-align:center;">
	  {if $items[ix].isValid eq 'n'}
	  <img src='lib/Galaxia/img/icons/red_dot.gif' alt=' ({tr}Invalid{/tr}) ' title='{tr}Invalid Process{/tr}' />
	  {else}
	  <img src='lib/Galaxia/img/icons/green_dot.gif' alt=' ({tr}Valid{/tr}) ' title='{tr}Valid Process{/tr}' />
	  {/if}

	</td>

	<td class="{cycle}">
	  <a class="link" href="tiki-g-admin_activities.php?pid={$items[ix].pId}">{tr}Activities{/tr}</a><br />
	  <a class="link" href="tiki-g-admin_shared_source.php?pid={$items[ix].pId}">{tr}Code{/tr}</a><br />
	  <a class="link" href="tiki-g-admin_graph.php?pid={$items[ix].pId}">{tr}Graph{/tr}</a><br />
	  <a class="link" href="tiki-g-admin_roles.php?pid={$items[ix].pId}">{tr}Roles{/tr}</a><br />
	  <a class="link" href="tiki-g-save_process.php?pid={$items[ix].pId}">{tr}Export{/tr}</a><br />
	  <a class="link" href="tiki-g-admin_processes.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;newminor={$items[ix].pId}">{tr}New Minor{/tr}</a><br />
	  <a class="link" href="tiki-g-admin_processes.php?find={$find}&amp;filter_name={$filter_name}&amp;filter_active={$filter_active}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;newmajor={$items[ix].pId}">{tr}New Major{/tr}</a><br />
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="15">
	{tr}No processes defined yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="{sameurl offset=$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{sameurl offset=$next_offset}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
