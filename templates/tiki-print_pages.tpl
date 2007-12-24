<h1><a class="pagetitle" href="tiki-print_pages.php">{tr}Print multiple pages{/tr}</a></h1>

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
<td class="findtable">
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
<input type="hidden" name="printstructures" value="{$form_printstructures|escape}" />
<input type="text" name="find" value="{$find|escape}" /><input type="submit" name="filter" value="{tr}Find{/tr}" /><br />
</form>
</td>
</tr>
</table>

{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3" print=false advance=false reset=true}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Structures{/tr}</a></span>
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Pages{/tr}</a></span>
</div>
{/if}

{cycle name=content values="1,2,3" print=false advance=false reset=true}
{* --- tab with structures -- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

{if $printstructures}
<h2>{tr}Selected Structures{/tr}</h2>
<form method="post" action="tiki-print_multi_pages.php">
<input type="hidden" name="printstructures" value="{$form_printstructures|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<ul>
{section name=ix loop=$printnamestructures}
 <li>{$printnamestructures[ix]}</li>
{/section}
</ul>
<input type="submit" name="print" value="{tr}Print{/tr}" />
</form>
<form action="tiki-print_pages.php" method="post">
<input type="submit" name="clearstructures" value="{tr}Clear{/tr}" />
</form>
{/if}

<h2>{tr}Add Structure{/tr}</h2>
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="addstructure" value="y" />
<select name="structureId" size="5" onchange="this.form.submit()">
{section name=ix loop=$structures}
<option value="{$structures[ix].page_ref_id|escape}">{$structures[ix].pageName}</option>
{sectionelse}
<option value="" disabled="disabled">{tr}No structures{/tr}</option>
{/section}
</select>
</form>
</div>

{* --- tab with pages -- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

{if $printpages}
<form method="post" action="tiki-print_multi_pages.php">
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<h2>{tr}Selected Pages{/tr}:</h2>
<ul>
{section name=ix loop=$printpages}
 <li>{$printpages[ix]}</li>
{/section}
</ul>
<br />
<input type="submit" name="print" value="{tr}Print{/tr}" />
</form>
<form action="tiki-print_pages.php" method="post">
<input type="submit" name="clearpages" value="{tr}Clear{/tr}" />
</form>
{/if}

<h2>{tr}Add Pages{/tr}</h2>
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="printstructures" value="{$form_printstructures|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<select name="pageName" size="5">
{section name=ix loop=$pages}
{if !in_array($pages[ix].pageName,$printpages)}{* don't show the page as available,if it is already selected *}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|escape}</option>
{/if}
{sectionelse}
<option value="" disabled="disabled">{tr}No pages{/tr}</option>
{/section}
</select>
<br />
<input type="submit" name="addpage" value="{tr}add page{/tr}" />

<h2>{tr}Add Pages from Structures{/tr}</h2>
<select name="structureId" size="5">
{section name=ix loop=$structures}
{if !in_array($structures[ix].page_ref_id,$printstructures)}
<option value="{$structures[ix].page_ref_id|escape}">{$structures[ix].pageName}</option>
{/if}
{sectionelse}
<option value="" disabled="disabled">{tr}No structures{/tr}</option>
{/section}
</select>
<br /><input type="submit" name="addstructurepages" value="{tr}add structure pages{/tr}"/>
</form>
</div>

