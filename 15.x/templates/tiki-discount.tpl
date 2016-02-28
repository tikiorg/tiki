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
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="text-center">{tr}Code{/tr}</th>
					<th class="text-center">{tr}Value{/tr}</th>
					<th class="text-center">{tr}Created{/tr}</th>
					<th class="text-center">{tr}Max{/tr}</th>
					<th class="text-center">{tr}Comment{/tr}</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			{foreach from=$discounts.data item=discount}
				<tr>
					<td class="text text-center">{$discount.code|escape}</td>
					<td class="text text-center">{$discount.value|escape}{if !strstr($discount.value, '%')} {$prefs.payment_currency|escape}{/if}</td>
					<td class="date text-center">{$discount.created|tiki_short_date}</td>
					<td class="text text-center">{$discount.max|escape}</td>
					<td class="text text-center">{$discount.comment|escape}</td>
					<td class="action text-center">
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
	<form method="post" action="tiki-discount.php" class="form-horizontal">
		<br>
		{if !empty($info.id)}<input type="hidden" name="id" value="{$info.id}">{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label">{tr}Code{/tr}</label>
			<div class="col-sm-7">
		      	<input type="text" id="code" name="code" {if !empty($info.code)}value="{$info.code|escape}"{/if} class="form-control">
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Value{/tr}</label>
			<div class="col-sm-7">
		      	<input type="text" id="code" name="code" {if !empty($info.code)}value="{$info.code|escape}"{/if} class="form-control">
		      	<div class="help-block">
		      		{tr}{$prefs.payment_currency|escape}{/tr} {tr} or {/tr}
		      	</div>
		      	<input type="text" id="percent" name="percent" {if !empty($info.percent)} value="{$info.percent|escape}"{/if} class="form-control">
		      	<div class="help-block">
		      		%
		      	</div>
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Maximum time the discount can be used in the first phase of payment{/tr}</label>
			<div class="col-sm-7">
		      	<input type="text" id="max" name="max" {if !empty($info.max)} value="{$info.max|escape}"{/if} class="form-control">
		      	<div class="help-block">
		      		{tr}-1 for unlimited{/tr}
		      	</div>
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Comment{/tr}</label>
			<div class="col-sm-7">
		      	<input type="text" id="comment" name="comment" {if !empty($info.comment)} value="{$info.comment|escape}"{/if} class="form-control">
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-7">
		      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
		    </div>
	    </div>
	</form>
{/tab}

{/tabset}
