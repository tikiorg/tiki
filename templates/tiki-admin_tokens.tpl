{* $Id$ *}
{title help="Token+Access"}{tr}Admin Tokens{/tr}{/title}

{tabset name="tabs_admtokens"}
	{tab name="{tr}List tokens{/tr}"}
		<h2>{tr}List tokens{/tr}</h2>
		<table class="normal">
			<tr>
				<th>{tr}Id{/tr}</th>
				<th>{tr}Entry{/tr}</th>
				<th>{tr}Token{/tr}</th>
				<th>{tr}Creation{/tr}</th>
				<th>{tr}Timeout{/tr}</th>
				<th>{tr}Hits{/tr}</th>
				<th>{tr}Max hits{/tr}</th>
				<th>{tr}E-mail{/tr}</th>
				<th>{tr}Parameters{/tr}</th>
				<th>{tr}Groups{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>
			
			{cycle values="even,odd" print=false}
			{foreach $tokens as $token}
				<tr>
					<td>{$token.tokenId}</td>
					<td>{$token.entry}</td>
					<td>{$token.token}</td>
					<td>{$token.creation}</td>
					<td>{$token.timeout}</td>
					<td>{$token.hits}</td>
					<td>{$token.maxhits}</td>
					<td>{$token.email}</td>
					<td>
						{foreach $token.parameters as $key => $value}
							{$key}={$value}<br>
						{/foreach}
					</td>
					<td>{$token.groups}</td>
					<td>{self_link tokenId=$token.tokenId action='delete' _icon='cross'}{tr}Delete{/tr}{/self_link}</td>
				</tr>
			{foreachelse}
				{norecords _colspan=10}
			{/foreach}
		</table>
	{/tab}
	{tab name="{tr}Add new token{/tr}"}
		<h2>{tr}Add new token{/tr}</h2>
		
		{if $tokenCreated} 
			{remarksbox type="note" title="{tr}Note{/tr}"}
				{tr}Token successfully created.{/tr}
			{/remarksbox}
		{/if}
		
		<form action="tiki-admin_tokens.php" method="post">
			<input type="hidden" name="action" value="add">
			<table class="formcolor">
				<tr>
					<td><label for='entry'>{tr}Full URL{/tr}</label></td>
					<td><input type="text" id='entry' name='entry'></td>
				</tr>
				<tr>
					<td><label for='timeout'>{tr}Timeout in seconds (-1 for unlimited){/tr}</label></td>
					<td><input type="text" id='timeout' name='timeout'></td>
				</tr>			
				<tr>
					<td><label for='maxhits'>{tr}Maximum number of hits (-1 for unlimited){/tr}</label></td>
					<td><input type="text" id='maxhits' name='maxhits'></td>
				</tr>
				<tr>
					<td><label for='groups'>{tr}Groups (separated by comma){/tr}</label></td>
					<td><input type="text" id='groups' name='groups'></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" class="btn btn-default" value="{tr}Add{/tr}"></td>
				</tr>
			</table>
		</form>
	{/tab}
{/tabset}