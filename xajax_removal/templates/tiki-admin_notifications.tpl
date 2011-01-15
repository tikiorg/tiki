{* $Id$ *}
{title help="Mail+Notifications"}{tr}Mail notifications{/tr}{/title}

{if empty($prefs.sender_email)}
	<br />
	<div class="highlight simplebox">{icon _id=information style="vertical-align:middle"} {tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a> before creating email notifications.{/tr}</div>
	<br />
{/if}

<h2>{tr}Add notification{/tr}</h2>
{if !empty($tikifeedback)}
	<div class="highlight simplebox">{section name=ix loop=$tikifeedback}{icon _id=delete alt="{tr}Alert{/tr}" style="vertical-align:middle"} {$tikifeedback[ix].mes}.{/section}</div>
	<br />
{/if}
<form action="tiki-admin_notifications.php" method="post">
     <input type="hidden" name="find" value="{$find|escape}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     {if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
	 {if $numrows ne $prefs.maxRecords and $numrows}<input type="hidden" name="numrows" value="{$numrows|escape}" />{/if}
	<table class="formcolor">
		<tr>
			<td><label for="event">{tr}Event:{/tr}</label></td>
			<td>
				<select id="event" name="event">
					{foreach from=$watches key=key item=watch}
						<option value="{$key}">{$watch.label|escape}</option>
					{/foreach}
				</select>
			</td>
		</tr> 
		<tr>
			<td><label for="destination">{tr}Destination:{/tr}</label></td>
			<td>
				<select id="destination" name="destination">
					<option value="login" selected="selected">{tr}User{/tr}</option>
					<option value="email">{tr}Email{/tr}</option>
				</select>
				{jq}
				$("select[name='destination']").change(function () {
					$("#loginrow").hide();
					$("#emailrow").hide();
					$("input[name='login']").attr("disabled","disabled");
					$("input[name='email']").attr("disabled","disabled");
					$("#" + $("select[name='destination']").val() + "row").show();
					$("input[name='" + $("select[name='destination']").val() + "']").focus();
					$("input[name='" + $("select[name='destination']").val() + "']").removeAttr("disabled");
				}
				);
				{/jq}
			</td>
		</tr>
		<tr id="loginrow">
			<td><label for="flogin">{tr}User:{/tr}</label></td>
			<td>
				<input type="text" id="flogin" name="login" />
				{jq}$("#flogin").tiki("autocomplete", "username"){/jq}
				<a href="#" onclick="javascript:document.getElementById('flogin').value='{$user}'" class="link">{tr}Myself{/tr}</a>
			</td>
		</tr>
		<tr id="emailrow" style="display:none">
			<td><label for="femail">{tr}Email:{/tr}</label></td>        
			<td>
				<input type="text" id='femail' name="email" />
			</td>
		</tr> 
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
		</tr>
	</table>
</form>
<br />
<h2>{tr}Mail notifications{/tr}</h2>
{if $channels or ($find ne '')}
  {include file='find.tpl' find_show_num_rows='y'}
{/if}
<form method="get" action="tiki-admin_notifications.php">
	<table class="normal">
		<tr>
			<th>
				{if $channels}
					{select_all checkbox_names='checked[]'}
				{/if}
			</th>
			<th>{self_link _sort_arg="sort_mode" _sort_field="event"}{tr}Event{/tr}{/self_link}</th>
			<th>{self_link _sort_arg="sort_mode" _sort_field="object"}{tr}Object{/tr}{/self_link}</th>
			<th>{self_link _sort_arg="sort_mode" _sort_field="email"}{tr}Email{/tr}{/self_link}</th>
			<th>{self_link _sort_arg="sort_mode" _sort_field="user"}{tr}User / Group{/tr}{/self_link}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle print=false values="even,odd"}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td>
					<input type="checkbox" name="checked[]" value="{$channels[user].watchtype}{$channels[user].watchId|escape}" {if $smarty.request.checked and in_array($channels[user].watchId,$smarty.request.checked)}checked="checked"{/if} />
				</td>
				<td>{$channels[user].event}</td>
				<td>
					{if $channels[user].url}
						<a href="{$channels[user].url}" title="{$channels[user].title|escape}">{$channels[user].object|escape}</a>
					{else}
						{$channels[user].object|escape}
					{/if}
					</td>
				<td>
					{if $channels[user].watchtype eq 'user'}
						{$channels[user].email}
					{else}
						<em>{tr}Multiple{/tr}</em>
					{/if}
				</td>
				<td>
					{if $channels[user].watchtype eq 'group'}
						{icon _id='group'}
					{else}
						{icon _id='user'}
					{/if}
					{$channels[user].user|escape}
				</td>
				<td><a class="link" href="{$smarty.server.PHP_SELF}?{query removeevent=$channels[user].watchId removetype=$channels[user].watchtype}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a></td>
			</tr>
		{sectionelse}
         {norecords _colspan="6"}
		{/section}
	</table>
	{if $channels}
		<br />
		{tr}Perform action with checked:{/tr}
		<input type="image" name="delsel" src='pics/icons/cross.png' alt="{tr}Delete{/tr}" title="{tr}Delete{/tr}" />
	{/if}
</form>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

{if !empty($trackers)}
	<h2>{tr}Trackers Outbound Emails{/tr}</h2>
	<table class="normal">
		{section name=ix loop=$trackers}
			<tr class="{cycle}">
				<td><a href="tiki-admin_trackers.php?trackerId={$trackers[ix].trackerId}">{$trackers[ix].value|escape}</a></td>
			</tr>
		{/section}
	</table>
{/if}

{if !empty($forums)}
	<h2>{tr}Forums Outbound Emails{/tr}</h2>
	<table class="normal">
		{section name=ix loop=$forums}
			<tr class="{cycle}">
				<td><a href="tiki-admin_forums.php?forumId={$forums[ix].forumId}">{$forums[ix].outbound_address|escape}</a><br/></td>
			</tr>
		{/section}
	</table>
{/if}

