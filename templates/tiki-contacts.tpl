{title}{tr}Contacts{/tr}{/title}

<div class="navbar">
	<div style="float:right;margin:5px;">
		{if $view eq 'list'}
			{button href="?view=group" _text="{tr}Group View{/tr}"}
		{else}
			{button href="?view=list" _text="{tr}List View{/tr}"}
		{/if}
	</div>
	<div style="float:left;margin:5px;">
		{button href="#" _onclick="flip('editform');return false;" _text="{tr}Create/edit contacts{/tr}"}
		{button href="tiki-user_contacts_prefs.php" _text="{tr}Preferences{/tr}"}
	</div>
</div>

<form action="tiki-contacts.php" method="post" id="editform" name='editform_contact' style="clear:both;margin:5px;display:{ if $contactId}block{else}none{/if};">
	<input type="hidden" name="locSection" value="contacts" />
	<input type="hidden" name="contactId" value="{$contactId|escape}" />
	
	<table class="formcolor">
		<tbody id='tbody_editcontact'>
			<tr>
				<td>{tr}First Name:{/tr}</td>
				<td>
					<input type="text" maxlength="80" size="20" name="firstName" value="{$info.firstName|escape}" />
				</td>
				<td rowspan="5">
					{tr}Publish this contact to groups:{/tr}<br />
					<select multiple="multiple" name="groups[]" size="6">
						<option value=""></option>
						{foreach item=group from=$groups}
							<option value="{$group|escape}"{if in_array($group,$info.groups)} selected="selected"{/if}>{$group}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{tr}Last Name:{/tr}</td>
				<td>
					<input type="text" maxlength="80" size="20" name="lastName" value="{$info.lastName|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Email:{/tr}</td>
				<td>
					<input type="text" maxlength="80" size="20" name="email" value="{$info.email|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Nickname:{/tr}</td>
				<td>
					<input type="text" maxlength="80" size="20" name="nickname" value="{$info.nickname|escape}" />
				</td>
			</tr>
			<tr id='tr_exts'>
				<td>
					<select id='select_exts' onchange='ext_select();'>
						<option>{tr}More...{/tr}</option>
					</select>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<input type="submit" name="save" value="{tr}Save{/tr}" />
				</td>
			</tr>
		</tbody>
	</table>
</form>

{include file='find.tpl'}

{initials_filter_links}

<table class="normal">
	<tr>
		{assign var=numbercol value=4}
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a>
		</th>
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a>
		</th>
		{foreach from=$exts item=ext key=k}
			{if $ext.show eq 'y'}
				<th>
					{assign var=numbercol value=`$numbercol+1`}
					<a>{$ext.tra}</a>
				</th>
			{/if}
		{/foreach}
		
		{if $view eq 'list'}
			{assign var=numbercol value=`$numbercol+1`}
			<th>{tr}Groups{/tr}</th>
		{/if}
		
		{assign var=numbercol value=`$numbercol+1`}
		<th>{tr}Action{/tr}</th>
	</tr>
	
	{cycle values="odd,even" print=false}
	{foreach key=k item=channels from=$all}
		{if count($channels)}
			{if $view neq 'list'}
				<tr>
					<td colspan="6" style="font-size:80%;color:#999;">
						{tr}from{/tr} <b>{$k}</b>
					</td>
				</tr>
			{/if}
			{section name=user loop=$channels}
				<tr class="{cycle}">
					<td>
						<a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">
							{$channels[user].firstName}
						</a>
					</td>
					<td>
						<a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">
							{$channels[user].lastName}
						</a>
					</td>
					<td>
						{if $prefs.feature_webmail eq 'y'}
							{self_link _script='tiki-webmail.php' locSection='compose' to=$channels[user].email}{$channels[user].email}{/self_link}
						{else}
							<a class="link" href="mailto:{$channels[user].email}">{$channels[user].email}</a>
						{/if}
					</td>
					<td>
						{$channels[user].nickname}
					</td>
					{foreach from=$exts item=ext key=e}
						{if $ext.show eq 'y'}
							<td>{$channels[user].ext[$e]}</td>
						{/if}
					{/foreach}
					{if $view eq 'list'}
						<td>
							{if isset($channels[user].groups)}
								{foreach item=it name=gr from=$channels[user].groups}
									{$it}
									{if $smarty.foreach.gr.index+1 ne $smarty.foreach.gr.last}, {/if}
								{/foreach}
							{else}
								&nbsp;
							{/if}
						</td>
					{/if}
					
					<td>&nbsp;
						{if $channels[user].user eq $user}
							<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}" title="{tr}Edit{/tr}">
								{icon _id='page_edit'}
							</a>
							<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" style="margin-left:20px;" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
						{elseif $tiki_p_admin eq 'y'}
							<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" style="margin-left:36px;" title="{tr}Delete{/tr}">{icon _id='cross_admin' alt="{tr}Delete{/tr}"}</a>
						{/if}
					</td>
				</tr>
			{/section}
		{else}
			{norecords _colspan="$numbercol"}
		{/if}
	{/foreach}
</table>
	
<div class="mini">
	{if $prev_offset >= 0}
		[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]
		&nbsp;
	{/if}
	{tr}Page:{/tr} {$actual_page}/{$cant_pages}
	{if $next_offset >= 0}
		&nbsp;
		[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
	{/if}
	{if $prefs.direct_pagination eq 'y'}
		<br />
		{section loop=$cant_pages name=foo}
			{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
			<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>
			&nbsp;
		{/section}
	{/if}
</div>

{literal}
<script type="text/javascript">
	function newelem(type, vals) {
		var elem=document.createElement(type);

		for (key in vals) {
			elem.setAttribute(key, vals[key]);
		}
		return elem;
	}

	function htmlspecialchars(ch) {
		ch = ch.replace(/&/g,"&amp;");
		ch = ch.replace(/\"/g,"&quot;");
		ch = ch.replace(/\'/g,"&#039;");
		ch = ch.replace(/</g,"&lt;");
		ch = ch.replace(/>/g,"&gt;");
		return ch;
	}

	function ext_add(extid, text, defaultvalue, pub) {
		var option=document.getElementById('ext_option_'+extid);
		option.disabled='1';

		var tr,td, input;
		tr=newelem('tr', { 'class':'formcolor', 'id':'tr_ext_'+extid });
		td=newelem('td', { });
		if (pub != 'y' || {/literal}{if $tiki_p_admin_group_webmail eq 'y'}1{else}0{/if}{literal}) {	// add button only if not public
			input=newelem('input', { 'type':'button', 'value':'-', 'onclick':'ext_remove(\''+extid+'\');' });
			td.appendChild(input);
		}
		td.innerHTML += (pub == 'y' ? ' <em>' : ' ') + text + ':' + (pub == 'y' ? '</em>' : '');
		tr.appendChild(td);

		td=newelem('td', { });
		input=newelem('input', { 'maxlength':'80', 'size':'20', 'name':'ext_'+extid, 'value':defaultvalue });
		td.appendChild(input);
		tr.appendChild(td);

		var tbody=document.getElementById('tbody_editcontact');
		tbody.insertBefore(tr, document.getElementById('tr_exts'));	
	}

	function ext_select() {
		var value, text;
		value=document.editform_contact.select_exts.options[document.editform_contact.select_exts.selectedIndex].value;
		text=document.editform_contact.select_exts.options[document.editform_contact.select_exts.selectedIndex].text;
		document.editform_contact.select_exts.selectedIndex=0;

		ext_add(value, htmlspecialchars(text), '');
	}

	function ext_remove(extid) {
		var elem=document.getElementById('tr_ext_'+extid);
		var tbody=document.getElementById('tbody_editcontact');
		tbody.removeChild(elem);
		var option=document.getElementById('ext_option_'+extid);
		option.disabled='';
	}

	function extmenu_add(extid, text, defaultvalue, pub) {
		var selectelem=document.getElementById('select_exts');
		var option=newelem('option', { 'id':'ext_option_'+extid, 'value':extid });
		option.innerHTML=text;
		selectelem.appendChild(option);
		if (defaultvalue != '')
			ext_add(extid, text, defaultvalue, pub);
	}
{/literal}

{foreach from=$exts item=ext key=k}
	extmenu_add('{$k|escape}', '{$ext.tra|escape}', '{$info.ext[$ext.id]|escape:quotes}', '{$ext.public|escape}');
{/foreach}

{literal}
	</script>
{/literal}
