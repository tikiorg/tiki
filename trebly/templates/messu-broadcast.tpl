{title help='Inter-User Messages' url='messu-broadcast.php'}{tr}Broadcast message{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
{include file='messu-nav.tpl'}
<br /><br />

{if $message}
	<div class="simplebox highlight">
		{if $preview eq '1'}
			{icon _id=exclamation style="vertical-align:middle" alt="{tr}Confirmation{/tr}"}
		{elseif $sent eq '1'}
			{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle;"}
		{else}
		 	 {icon _id=exclamation style="vertical-align:middle" alt="{tr}Error{/tr}"}
		{/if}
		{$message}
		{if $preview eq '1'}
			<br />
			<form method="post">
				<input type="hidden" name="groupbr" value="{$groupbr|escape}" />
				<input type="hidden" name="priority" value="{$priority|escape}" />
				<input type="hidden" name="replyto_hash" value="{$replyto_hash|escape}" />
				<input type="hidden" name="subject" value="{$subject|escape}" />
				<input type="hidden" name="body" value="{$body|escape}" />
				<input type="submit" name="send" value="{tr}Please Confirm{/tr}" />
			</form>
		{/if}
	</div>
	<br />
	<br />
{/if}

{if $sent ne '1' and $preview ne '1'}
	<form action="messu-broadcast.php" method="post">
		<table class="formcolor" >
			<tr>
				<td>
					<label for="broadcast-group">{tr}Group:{/tr}</label>
				</td>
				<td>
					<select name="groupbr" id="broadcast-group">
						<option value=""{if $groupbr eq ''} selected="selected"{/if} />
						{if $tiki_p_broadcast_all eq 'y'}
							<option value="all"{if $groupbr eq 'All'} selected="selected"{/if}>{tr}All users{/tr}</option>
						{/if}
						{section name=ix loop=$groups}
							{if $groups[ix] ne "Anonymous"}
								<option value="{$groups[ix]|escape}"{if $groupbr eq $groups[ix]} selected="selected"{/if}>{$groups[ix]}</option>
							{/if}
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="broadcast-priority">{tr}Priority:{/tr}</label>
				</td>
				<td>
					<select name="priority" id="broadcast-priority">
						<option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
						<option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
						<option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
						<option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
						<option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
					</select>
					<input type="hidden" name="replyto_hash" value="{$replyto_hash}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="broadcast-subject">{tr}Subject:{/tr}</label>
				</td>
				<td>
					<input type="text" name="subject" id="broadcast-subject" value="{$subject|escape}" size="80" maxlength="255"/>
				</td>
			</tr>
		</table>
		<br />
		<table class="normal" >
			<tr>
				<td style="text-align: center;">
					<textarea rows="20" cols="80" name="body">{$body|escape}</textarea><br /><input type="submit" name="preview" value="{tr}Send{/tr}" />
				</td>
			</tr>
		</table>
	</form>
{/if}
