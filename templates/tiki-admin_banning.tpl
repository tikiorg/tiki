<a class="pagetitle" href="tiki-admin_banning.php">{tr}Banning system{/tr}</a>
<br /><br />
<h3>{tr}Add or edit a rule{/tr}</h3>
<form action="tiki-admin_banning.php" method="post">
<input type="hidden" name="banId" value="{$banId|escape}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}Rule title{/tr}</td>
	<td class="formcolor">
		<input type="text" name="title" value="{$info.title|escape}" maxlength="200" />
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Username regex matching{/tr}:</td>
	<td class="formcolor">
		<input type="radio" name="mode" value="user" {if $info.mode eq 'user'}checked="checked"{/if} />
		<input type="text" name="user" value="{$info.user|escape}" />
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}IP regex matching{/tr}:</td>
	<td class="formcolor">
		<input type="radio" name="mode" value="ip" {if $info.mode eq 'ip'}checked="checked"{/if} />
		<input type="text" name="ip1" value="{$info.ip1|escape}" size="3" />.
		<input type="text" name="ip2" value="{$info.ip2|escape}" size="3" />.
		<input type="text" name="ip3" value="{$info.ip3|escape}" size="3" />.
		<input type="text" name="ip4" value="{$info.ip4|escape}" size="3" />
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Banned from sections{/tr}:</td>
	<td class="formcolor">
				
		
		<table><tr>
		{section name=ix loop=$sections}
        <td class="formcolor">
			<input type="checkbox" name="section[{$sections[ix]}]" {if in_array($sections[ix],$info.sections)}checked="checked"{/if} /> {$sections[ix]}
        </td>
        {* see if we should go to the next row *}
        {if not ($smarty.section.ix.rownum mod 3)}
                {if not $smarty.section.ix.last}
                        </tr><tr>
                {/if}
        {/if}
        {if $smarty.section.ix.last}
                {* pad the cells not yet created *}
                {math equation = "n - a % n" n=3 a=$data|@count assign="cells"}
                {if $cells ne $cols}
                {section name=pad loop=$cells}
                        <td>&nbsp;</td>
                {/section}
                {/if}
                </tr>
        {/if}
    	{/section}
		</table>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Rule activated by dates{/tr}</td>
	<td class="formcolor">
		<input type="checkbox" name="use_dates" {if $info.use_dates eq 'y'}checked="checked"{/if} />
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Rule active from{/tr}</td>
	<td class="formcolor">
		{html_select_date prefix="date_from" time="$info.date_from"}
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Rule active until{/tr}</td>
	<td class="formcolor">
		{html_select_date prefix="date_to" time="$info.date_to"}
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Custom message to the user{/tr}</td>
	<td class="formcolor">
		<textarea rows="4" cols="40" name="message">{$info.message|escape}</textarea>
	</td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor">
		<input type="submit" name="save" value="{tr}save{/tr}" />
	</td>
</tr>
</table>
</form>


<form method="post" action="tiki-admin_banning.php">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input type="text" name="find" value="{$find|escape}" />
</form>
<h3>{tr}Rules{/tr}:</h3>
<form method="post" action="tiki-admin_banning.php">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
<td width="2%" class="heading"><input type="submit" name="del" value="{tr}x{/tr} " /></td>
<td class="heading">{tr}Title{/tr}</td>
<td width="" class="heading">{tr}User/IP{/tr}</a></td>
<td width="" class="heading">{tr}Sections{/tr}</a></td>
<td width="12%" class="heading">{tr}Action{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="delsec[{$items[user].banId}]" />
</td>
<td class="{cycle advance=false}">
<a href="tiki-admin_banning.php?banId={$items[user].banId}" class="link">
{$items[user].title}</a>
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{if $items[user].mode eq 'user'}
	{$items[user].user}
{else}
	{$items[user].ip1}.{$items[user].ip2}.{$items[user].ip3}.{$items[user].ip4}
{/if}
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{section name=ix loop=$items[user].sections}
	{$items[user].sections[ix].section}{if not $smarty.section.ix.last},{/if}
{/section}
</td>
<td class="{cycle}">
<a href="tiki-admin_banning.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$items[user].banId}" class="link"><img src='img/icons2/delete.gif' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' border='0' /></a>
</td>
</tr>
{sectionelse}
<tr><td colspan="5" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</form>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_banning.php?offset={$prev_offset}&amp;find={$find}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_banning.php?offset={$next_offset}&amp;find={$find}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_banning.php offset=$selector_offset">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
