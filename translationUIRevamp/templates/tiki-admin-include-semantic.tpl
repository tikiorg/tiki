<table width="100%">
	<col width="50%"/>
	<col width="50%"/>
	<tr>
		<td>
			<h2>{tr}Known Types{/tr}</h2>
			<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
			<table width="100%">
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
				<input type="submit" name="list" value="{tr}Show Usage{/tr}"/>
				<input type="submit" name="remove" value="{tr}Delete{/tr}"/>
				<input type="submit" name="removeclean" value="{tr}Delete &amp; Unreference{/tr}"/>
			</p>
			</form>

			{if $selected_token}
			<form method="post" action="{$smarty.server.REQUEST_URI}">
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
					<select id="invert" name="invert"/>
						<option value="">--{tr}Self{/tr}--</option>
						{foreach from=$tokens item=element}
						<option value="{$element.token|escape}"{if $selected_detail.invert_token eq $element.token} selected="selected"{/if}>{$element.label|escape}</option>
						{/foreach}
					</select>
				</div>
				<div>
					<input type="hidden" name="token" value="{$selected_token|escape}"/>
					<input type="submit" name="save" value="{tr}Save{/tr}"/>
				</div>
			</form>
			{/if}
		</td>
		<td>
			<h2>{tr}New Types{/tr}</h2>
			<table width="100%">
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
								<input type="submit" name="list" value="{tr}Show Usage{/tr}"/>
								<input type="submit" name="create" value="{tr}Create{/tr}"/>
								<input type="submit" name="rename" value="{tr}Fix{/tr}"/>
								<input type="submit" name="clean" value="{tr}Remove{/tr}"/>
							</div>
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
			{if $rename}
			<form method="post" action="{$smarty.server.REQUEST_URI}">
				<div>
					<label for="token">{tr}Token{/tr} :</label>
					<input id="token" type="text" name="token" value="{$rename|escape}"/>
				</div>
				<div>
					<input type="hidden" name="oldName" value="{$rename|escape}"/>
					<input type="submit" name="save" value="{tr}Fix{/tr}"/>
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
