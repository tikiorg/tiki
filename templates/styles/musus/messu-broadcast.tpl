<a class="pagetitle" href="messu-broadcast.php">{tr}Broadcast message{/tr}</a>
{if $feature_help eq 'y'}
	<a href="http://tikiwiki.org/tiki-index.php?page=UserMessagesDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Message Broadcast{/tr}">{$helpIcon $helpIconDesc}</a>
{/if}

{if $feature_view_tpl eq 'y'}
	<a href="tiki-edit_templates.php?template=templates/messu-broadcast.tpl" target="tikihelp" class="tikihelp" title="{tr}Edit article template{/tr}">{$editIcon $editIconDesc}</a>
{/if}

<br /><br />
{include file="tiki-mytiki_bar.tpl"}
{include file="messu-nav.tpl"}
<br />
{if $sent}
{$message}
{else}
<form action="messu-broadcast.php" method="post">
<table>
	<tr>
		<td><label for="broadcast-group">{tr}Group{/tr}:</label></td>
		<td>
			<select name="group" id="broadcast-group">
				{if $tiki_p_broadcast_all eq 'y'}
					<option value="all" selected="selected">{tr}All users{/tr}</option>
				{/if}
				{section name=ix loop=$groups}
					<option value="{$groups[ix].groupName|escape}">{$groups[ix].groupName}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="broadcast-priority">{tr}Priority{/tr}:</label></td>
		<td>
			<select name="priority" id="broadcast-priority">
				<option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
				<option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
				<option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
				<option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
				<option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
			</select>
			<input type="submit" name="send" value="{tr}send{/tr}" />
		</td>
	</tr>
	<tr>
		<td><label for="broadcast-subject">{tr}Subject{/tr}:</label></td>
		<td><input type="text" name="subject" id="broadcast-subject" value="{$subject|escape}" size="80" maxlength="255"/></td>
	</tr>
</table>
<br />
<table>
	<tr>
<!--td below had style="text-align: center;" -->
		<td><textarea rows="20" cols="80" name="body">{$body|escape}</textarea></td>
	</tr>
</table>
</form>
{/if}
<br /><br />
