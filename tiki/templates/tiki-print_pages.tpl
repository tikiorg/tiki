<h1><a class="pagetitle" href="tiki-print_pages.php">{tr}Print multiple pages{/tr}</a></h1>

<div class="cbox">
<div class="cbox-title">
{tr}Filter{/tr}
</div>
<div class="cbox-data">
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
{tr}Filter{/tr}:<input type="text" name="find" value="{$find|escape}" /><input type="submit" name="filter" value="{tr}Filter{/tr}" /><br />
</form>
</div>
</div>
<br />


<div class="cbox">
<div class="cbox-title">
{tr}Print Wiki Pages{/tr}
</div>
<div class="cbox-data">
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<table class="normal" cellpadding="5">
 <tr valign="middle">
  <td width="50%"><strong>{tr}Add Pages{/tr}:</strong><br />
   <select name="pageName" size="5">
{section name=ix loop=$pages}
{if in_array($pages[ix].pageName,$printpages)}{* don't show the page as available,if it is already selected *}
{else}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName}</option>
{/if}
{sectionelse}
    <option value="" disabled="disabled">{tr}No pages{/tr}</option>
{/section}
   </select>
   <br /><input type="submit" name="addpage" value="{tr}add page{/tr}" />
  </td>
  <td width="50%"><strong>{tr}Add Pages from Structures{/tr}:</strong><br />
   <select name="structureId" size="5">
{section name=ix loop=$structures}
<option value="{$structures[ix].page_ref_id|escape}">{$structures[ix].pageName}</option>
{sectionelse}
    <option value="" disabled="disabled">{tr}No structures{/tr}</option>
{/section}
   </select>
  <br /><input type="submit" name="addstructure" value="{tr}add structure{/tr}"/></td>
 </tr>
</table>
{if $printpages}{* only show if pages have been selected *}
<p><strong>{tr}Selected Pages{/tr}:</strong></p>
<ul>
{section name=ix loop=$printpages}
 <li>{$printpages[ix]}&nbsp;</li>
{/section}
</ul>
<br />
<input type="submit" name="clearpages" value="{tr}clear{/tr}" />
{/if}
</form>
{if $printpages}{* only show print button if there is something to print *}
<form method="post" action="tiki-print_multi_pages.php">
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
<input type="submit" name="print" value="{tr}Print{/tr}" />
</form>
{/if}
</div>
</div>
<br />

