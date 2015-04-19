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
	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}
	<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
		<table class="table normaltable-striped table-hover">
			<tr>
				<th>{tr}Code{/tr}</th>
				<th>{tr}Value{/tr}</th>
				<th>{tr}Created{/tr}</th>
				<th>{tr}Max{/tr}</th>
				<th>{tr}Comment{/tr}</th>
				<th></th>
			</tr>

			{foreach from=$discounts.data item=discount}
				<tr>
					<td class="text">{$discount.code|escape}</td>
					<td class="text">{$discount.value|escape}{if !strstr($discount.value, '%')} {$prefs.payment_currency|escape}{/if}</td>
					<td class="date">{$discount.created|tiki_short_date}</td>
					<td class="integer">{$discount.max|escape}</td>
					<td class="text">{$discount.comment|escape}</td>
					<td class="action">
						{capture name=discount_actions}
							{strip}
								{$libeg}{self_link id=$discount.id cookietab=2 _icon_name='edit' _menu_text='y' _menu_icon='y'}
									{tr}Edit{/tr}
								{/self_link}{$liend}
								{$libeg}{self_link del=$discount.id _icon_name='edit' _menu_text='y' _menu_icon='y'}
									{tr}Delete{/tr}
								{/self_link}{$liend}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.discount_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.discount_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{foreachelse}
				{norecords _colspan=6}
			{/foreach}
		</table>
	</div>
	{pagination_links cant=$discounts.cant step=$discounts.max offset=$discounts.offset}{/pagination_links}
{/tab}

{capture name=tabtitle}{if empty($info.id)}{tr}Create{/tr}{else}{tr}Edit{/tr}{/if}{/capture}
{tab name=$smarty.capture.tabtitle}
	<form method="post" action="tiki-discount.php">
		{if !empty($info.id)}<input type="hidden" name="id" value="{$info.id}">{/if}
		<table class="formcolor">
			<tr>
				<td><label for="code">{tr}Code:{/tr}</label></td>
				<td><input type="text" id="code" name="code" {if !empty($info.code)}value="{$info.code|escape}"{/if}></td>
			</tr>
			<tr>
				<td><label for="value">{tr}Value:{/tr}</label></td>
				<td>
					<input type="text" id="value" name="value" {if empty($info.percent) and !empty($info.value)}value="{$info.value|escape}" {/if}>{$prefs.payment_currency|escape}<br>
					{tr}or{/tr}<input type="text" id="percent" name="percent" {if !empty($info.percent)} value="{$info.percent|escape}"{/if}>%
				</td>
			</tr>
			<tr>
				<td><label for="max">{tr}Max time the discount can be used in the first phase of payment:{/tr}</label></td>
				<td><input type="text" id="max" name="max" {if !empty($info.max)} value="{$info.max|escape}"{/if}>{tr}-1 for illimited{/tr}</td>
			</tr>
			<tr>
				<td><label for="comment">{tr}Comment:{/tr}</label></td>
				<td><input type="text" id=comment" name="comment" {if !empty($info.comment)} value="{$info.comment|escape}"{/if}></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}"></td>
			</tr>
		</table>
	</form>
{/tab}

{/tabset}
