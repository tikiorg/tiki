{title}{tr}Discounts{/tr}{/title}
{if !empty($errors)}
{remarksbox type='errors' title="{tr}Errors{/tr}"}
	{foreach from=$errors item=error}
		{$error|escape}<br/>
	{/foreach}
{/remarksbox}
{/if}
{tabset}
{tab name="{tr}List{/tr}"}
	<table class="normal">
	<tr>
		<th>{tr}Code{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Created{/tr}</th>
		<th>{tr}Max{/tr}</th>
		<th>{tr}Comment{/tr}</th>
		<th>{tr}Actions{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{foreach from=$discounts.data item=discount}
	<tr class="{cycle}">
		<td class="text">{$discount.code|escape}</td>
		<td class="text">{$discount.value|escape}{if !strstr($discount.value, '%')} {$prefs.payment_currency|escape}{/if}</td>
		<td class="date">{$discount.created|tiki_short_date}</td>
		<td class="integer">{$discount.max|escape}</td>
		<td class="text">{$discount.comment|escape}</td>
		<td class="action">
			{self_link id=$discount.id cookietab=2}{icon _id=page_edit class=titletips title="{tr}Edit{/tr}" alt="{tr}Edit{/tr}"}{/self_link}
			{self_link del=$discount.id}{icon _id=cross class=titletips title="{tr}Delete{/tr}" alt="{tr}Delete{/tr}"}{/self_link}
		</td>
	</tr>
	{foreachelse}
		{norecords _colspan=6}
	{/foreach}
	</table>
	{pagination_links cant=$discounts.cant step=$discounts.max offset=$discounts.offset}{/pagination_links}
{/tab}
{capture name=tabtitle}{if empty($info.id)}{tr}Create{/tr}{else}{tr}Edit{/tr}{/if}{/capture}
{tab name="`$smarty.capture.tabtitle`"}
	<form method="post" action="tiki-discount.php">
	{if !empty($info.id)}<input type="hidden" name="id" value="{$info.id}" />{/if}
	<table class="formcolor">
	<tr>
		<td><label for="code">{tr}Code:{/tr}</label></td>
		<td><input type="text" id="code" name="code" {if !empty($info.code)}value="{$info.code|escape}"{/if} /></td>
	</tr>
	<tr>
		<td><label for="value">{tr}Value:{/tr}</label></td>
		<td>
			<input type="text" id="value" name="value" {if empty($info.percent) and !empty($info.value)}value="{$info.value|escape}" {/if} />{$prefs.payment_currency|escape}<br />
			{tr}or{/tr}<input type="text" id="percent" name="percent" {if !empty($info.percent)} value="{$info.percent|escape}"{/if} />%
		</td>
	</tr>
	<tr>
		<td><label for="max">{tr}Max time the discount can be used in the first phase of payment:{/tr}</label></td>
		<td><input type="text" id="max" name="max" {if !empty($info.max)} value="{$info.max|escape}"{/if}/>{tr}-1 for illimited{/tr}</td>
	</tr>
	<tr>
		<td><label for="comment">{tr}Comment:{/tr}</label></td>
		<td><input type="text" id=comment" name="comment" {if !empty($info.comment)} value="{$info.comment|escape}"{/if}/></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
	</tr>
	</table>
	</form>
{/tab}
{/tabset}
