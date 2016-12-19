<form class="form-horizontal" action="tiki-admin.php?page=semantic" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="semantic" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_semantic visible="always"}
		{preference name=feature_backlinks}
	</fieldset>
</form>
<table class="table">
	<col style="width:50%">
	<col style="width:50%">
	<tr>
		<td>
			<h2>{tr}Known Types{/tr}</h2>
			<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
				<input type="hidden" name="ticket" value="{$ticket|escape}">

				<table class="table">
					<tr>
						<th></th>
						<th>{tr}Token{/tr}</th>
						<th>{tr}Label{/tr}</th>
						<th>{tr}Invert{/tr}</th>
					</tr>
					{foreach from=$tokens item=token}
					<tr>
						<td><input type="checkbox" name="select[]" value="{$token.token|escape}"/></td>
						<td><a href="{$smarty.server.PHP_SELF}?page=semantic&token={$token.token|escape}">{$token.token|escape}</a></td>
						<td>{$token.label|escape}</td>
						<td><a href="{$smarty.server.PHP_SELF}?page=semantic&token={$token.invert_token|escape}">{$tokens[$token.invert_token].label|escape}</a></td>
					</tr>
					{/foreach}
				</table>
				<p>
					<input type="submit" class="btn btn-default btn-sm" name="list" value="{tr}Show Usage{/tr}"/>
					<input type="submit" class="btn btn-warning btn-sm" name="remove" value="{tr}Delete{/tr}"/>
					<input type="submit" class="btn btn-warning btn-sm" name="removeclean" value="{tr}Delete &amp; Unreference{/tr}"/>
				</p>
			</form>

			{if $selected_token}
				<form method="post" action="{$smarty.server.REQUEST_URI}">
					<input type="hidden" name="ticket" value="{$ticket|escape}">
					<div>{$save_message|escape}</div>
					<div>
						<label for="token">{tr}Token{/tr} :</label>
						<input id="token" type="text" name="newName" value="{$selected_token|escape}"/>
					</div>
					<div>
						<label for="label">{tr}Label{/tr} :</label>
						<input id="label" type="text" name="label" value="{$selected_detail.label|escape}"/>
					</div>
					<div>
						<label for="invert">{tr}Invert Relation{/tr} :</label>
						<select id="invert" name="invert">
							<option value="">--{tr}Self{/tr}--</option>
							{foreach from=$tokens item=element}
								<option value="{$element.token|escape}"{if $selected_detail.invert_token eq $element.token} selected="selected"{/if}>{$element.label|escape}</option>
							{/foreach}
						</select>
					</div>
					<div>
						<input type="hidden" name="token" value="{$selected_token|escape}"/>
						<input type="submit" class="btn btn-default btn-sm" name="save" value="{tr}Save{/tr}"/>
					</div>
				</form>
			{/if}
		</td>
		<td>
			<h2>{tr}New Types{/tr}</h2>
			<table class="table">
				<tr>
					<th>{tr}Token{/tr}</th>
					<th>{tr}Actions{/tr}</th>
				</tr>
				{foreach from=$new_tokens item=token}
					<tr>
						<td>{$token|escape}</td>
						<td>
							<form method="post" action="{$smarty.server.REQUEST_URI}">
								<div>
									<input type="hidden" name="select[]" value="{$token|escape}"/>
									<input type="hidden" name="token" value="{$token|escape}"/>
									<input type="submit" name="list" value="{tr}Show Usage{/tr}" class="btn btn-default" />
									<input type="submit" name="create" value="{tr}Create{/tr}" class="btn btn-default" />
									<input type="submit" name="rename" value="{tr}Fix{/tr}" class="btn btn-default" />
									<input type="submit" name="clean" value="{tr}Remove{/tr}" class="btn btn-default" />
								</div>
							</form>
						</td>
					</tr>
				{/foreach}
			</table>
			{if $rename}
				<form method="post" action="{$smarty.server.REQUEST_URI}">
					<input type="hidden" name="ticket" value="{$ticket|escape}">
					<div>
						<label for="token">{tr}Token{/tr} :</label>
						<input id="token" type="text" name="token" value="{$rename|escape}"/>
					</div>
					<div>
						<input type="hidden" name="oldName" value="{$rename|escape}"/>
						<input type="submit" class="btn btn-default btn-sm" name="save" value="{tr}Fix{/tr}"/>
					</div>
				</form>
			{/if}
		</td>
	</tr>
</table>

{foreach from=$link_lists item=links key=token}
	<h2>{if $tokens[$token]}{$tokens[$token].label|escape}{else}{$token|escape}{/if}</h2>
	{if $links|@count > 0}
		<ul>
			{foreach from=$links item=t}
				<li><a href="tiki-index.php?page={$t.fromPage|escape}">{$t.fromPage|escape}</a> (link to <a href="tiki-index.php?page={$t.toPage|escape}">{$t.toPage|escape}</a>)</li>
			{/foreach}
		</ul>
	{else}
		<p>{tr}No occurences found.{/tr}</p>
	{/if}
{/foreach}
