<a class="pagetitle" href="tiki-print_pages.php">{tr}Print multiple pages{/tr}</a><br /><br />

<div class="cbox">
<div class="cbox-title">
{tr}Filter{/tr}
</div>
<div class="cbox-data">
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
{tr}filter{/tr}:<input type="text" name="find" value="{$find|escape}" /><input type="submit" name="filter" value="{tr}filter{/tr}" /><br />
</form>
</div>
</div>
<br />


<div class="cbox">
<div class="cbox-title">
{tr}Print Wiki Pages{/tr}
</div>
<div class="cbox-data">
<div class="simplebox">
<b>{tr}Pages{/tr}</b>: 
{section name=ix loop=$printpages}
{$printpages[ix]}&nbsp;
{/section}
</div>
<form action="tiki-print_pages.php" method="post">
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<select name="pageName">
{section name=ix loop=$pages}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName}</option>
{/section}
</select>
<input type="submit" name="addpage" value="{tr}add page{/tr}" />
<input type="submit" name="clearpages" value="{tr}clear{/tr}" />
</form>
<form method="post" action="tiki-print_multi_pages.php">
<input type="hidden" name="printpages" value="{$form_printpages|escape}" />
<input type="submit" name="print" value="{tr}print{/tr}" />
</form>
</div>
</div>
<br />

