{* $Id$ *}

{title help="Webmail" admpage="webmail"}{tr}Webmail{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
<table width="100%" border=0>
	<tr>
		<td>
			{self_link _icon='img/webmail/mailbox.gif' locSection='mailbox' _width='48' _height='48'}{tr}Mailbox{/tr}{/self_link}
			<br />
			{self_link locSection='mailbox'}{tr}Mailbox{/tr}{/self_link}
		</td>
		<td>
			{self_link _icon='img/webmail/compose.gif' locSection='compose' _width='48' _height='48'}{tr}Compose{/tr}{/self_link}
			<br />
			{self_link locSection='compose'}{tr}Compose{/tr}{/self_link}
		</td>
		{if $prefs.feature_contacts eq 'y'}
			<td>
				{self_link _icon='img/webmail/contact.gif' _script='tiki-contacts.php' _width='48' _height='48'}{tr}Contacts{/tr}{/self_link}
				<br />
				{self_link  _script='tiki-contacts.php'}{tr}Contacts{/tr}{/self_link}
			</td>
		{/if}
		<td width="50%">
		</td>
		<td>
			{self_link _icon='img/webmail/settings.gif' locSection='settings' _width='48' _height='48'}{tr}Settings{/tr}{/self_link}
			<br />
			{self_link locSection='settings'}{tr}Settings{/tr}{/self_link}
		</td>
	</tr>
</table>

{if !empty($conmsg)}
	{remarksbox type='warning' title="{tr}Error{/tr}"}{$conmsg}{/remarksbox}
{/if}

<hr/>

{if $locSection eq 'settings'}
	{tabset  name='tabs_webmail_settings'}
		{tab name="List"}
			{if count($accounts) != 0}
				<h2>{tr}Personal e-mail accounts{/tr}</h2>
				<table class="normal">
					<tr>
						<th>{tr}Active{/tr}</th>
						<th>{tr}Account{/tr}</th>
						<th>{tr}Server{/tr}</th>
						<th>{tr}Username{/tr}</th>
						<th>{tr}Action{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false}
					{section name=ix loop=$accounts}
						{if $accounts[ix].current eq 'y' and $accounts[ix].user eq $user or $accounts[ix].accountId eq $mailCurrentAccount}{assign var=active value=true}{else}{assign var=active value=false}{/if}
						<tr class="{cycle}">
							<td class="icon">
								{if !$active}
									{self_link _icon='star_grey' current=$accounts[ix].accountId}{tr}Activate{/tr}{/self_link}
								{else}
									{icon _id='star' alt="{tr}This is the active account.{/tr}"}
								{/if}
							</td>
							<td class="username">
								{if !$active}
									{self_link current=$accounts[ix].accountId _title="{tr}Activate{/tr}"}{$accounts[ix].account}{/self_link}
								{else}
									{$accounts[ix].account|escape}
								{/if}
							</td>
							<td class="text">
								{if !empty($accounts[ix].imap)}{tr}IMAP:{/tr} {$accounts[ix].imap} ({$accounts[ix].port})
								{elseif !empty($accounts[ix].mbox)}{tr}Mbox:{/tr} {$accounts[ix].mbox}
								{elseif !empty($accounts[ix].maildir)}{tr}Maildir:{/tr} {$accounts[ix].maildir}
								{elseif !empty($accounts[ix].pop)}{tr}POP3:{/tr} {$accounts[ix].pop} ({$accounts[ix].port}){/if}
							</td>
							<td class="username">
								{$accounts[ix].username}
							</td>
							<td class="action">
								{self_link _icon='page_edit' accountId=$accounts[ix].accountId}{tr}Edit{/tr}{/self_link}
								{self_link _icon='cross' remove=$accounts[ix].accountId}{tr}Delete{/tr}{/self_link}
								{if !$active}
									{self_link _icon='accept' current=$accounts[ix].accountId}{tr}Activate{/tr}{/self_link}
								{/if}
							</td>
						</tr>
					{sectionelse}
						{norecords _colspan=5}
					{/section}
				</table>
			{/if}
			
			{if $tiki_p_use_group_webmail eq 'y'}
				{if count($pubAccounts) != 0}
					<h2>{tr}Group e-mail accounts{/tr}</h2>
					<table class="normal">
						<tr>
							<th>{tr}Active{/tr}</th>
							<th>{tr}Account{/tr}</th>
							<th>{tr}Server{/tr}</th>
							<th>{tr}Username{/tr}</th>
							<th>{tr}Action{/tr}</th>
						</tr>
						{cycle values="odd,even" print=false}
						{section name=ixp loop=$pubAccounts}
							{if $pubAccounts[ixp].current eq 'y' and $pubAccounts[ixp].user eq $user or $pubAccounts[ixp].accountId eq $mailCurrentAccount}{assign var=active value=true}{else}{assign var=active value=false}{/if}
							<tr class="{cycle}">
								<td class="icon">
									{if !$active}
										{self_link _icon='star_grey' current=$pubAccounts[ixp].accountId}{tr}Activate{/tr}{/self_link}
									{else}
										{icon _id='star' alt="{tr}This is the active account.{/tr}"}
									{/if}
								</td>
								<td class="username">
									{if !$active}
										{self_link current=$pubAccounts[ixp].accountId _title="{tr}Activate{/tr}"}{$pubAccounts[ixp].account}{/self_link}
									{else}
										{$pubAccounts[ixp].account|escape}
									{/if}
								</td>
								<td class="text">
									{if !empty($pubAccounts[ixp].imap)}{tr}IMAP:{/tr} {$pubAccounts[ixp].imap} ({$pubAccounts[ixp].port})
									{elseif !empty($pubAccounts[ixp].mbox)}{tr}Mbox:{/tr} {$pubAccounts[ixp].mbox}
									{elseif !empty($pubAccounts[ixp].maildir)}{tr}Maildir:{/tr} {$pubAccounts[ixp].maildir}
									{elseif !empty($pubAccounts[ixp].pop)}{tr}POP3:{/tr} {$pubAccounts[ixp].pop} ({$pubAccounts[ixp].port}){/if}
								</td>
								<td class="username">{$pubAccounts[ixp].username}</td>
								<td class="action">
									{if $tiki_p_admin_group_webmail eq 'y'or $tiki_p_admin eq 'y'}
										{self_link _icon='page_edit' accountId=$pubAccounts[ixp].accountId}{tr}Edit{/tr}{/self_link}
										{self_link _icon='cross' remove=$pubAccounts[ixp].accountId}{tr}Delete{/tr}{/self_link}
									{/if}
									{if !$active}
										{self_link _icon='accept' current=$pubAccounts[ixp].accountId}{tr}Activate{/tr}{/self_link}
									{/if}
								</td>
							</tr>
						{sectionelse}
							{norecords _colspan=5}
						{/section}
					</table>
				{/if}
			{/if}
		{/tab}
		{if $accountId eq 0}{assign var="tablab" value="{tr}Create{/tr}"}{else}{assign var="tablab" value="{tr}Edit{/tr}"}{/if}
		{tab name=$tablab}
			{if $tiki_p_admin_personal_webmail eq 'y' or $tiki_p_admin_group_webmail eq 'y' or !isset($info.user) or $user eq $info.user}
				<div id="settingsFormDiv">
					<form action="tiki-webmail.php" method="post" name="settings">
						<input type="hidden" name="accountId" value="{$accountId|escape}" />
						<input type="hidden" name="locSection" value="settings" />
						<table class="formcolor">
							<tr>
								<td>{tr}Account name{/tr}</td>
								<td>
									<input type="text" name="account" value="{$info.account|escape}" />
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr><td colspan="4">
								<hr />
								<h3>{tr}Incoming servers (used in this order){/tr}</h3>
							</td></tr>
							<tr>
								<td>{tr}IMAP server{/tr}</td>
								<td>
									<input type="text" name="imap" value="{$info.imap|escape}" />
								</td>
								<td rowspan="2" valign="middle">{tr}Port{/tr}</td>
								<td rowspan="2" valign="middle">
									<input type="text" name="port" size="7" value="{$info.port}" />
								</td>
							</tr>
							<tr>
								<td>{tr}Mbox filepath{/tr}</td>
								<td>
									<input type="text" name="mbox" value="{$info.mbox|escape}" />
								</td>
							</tr>
							<tr>
								<td>{tr}Maildir mail directory{/tr}</td>
								<td>
									<input type="text" name="maildir" value="{$info.maildir|escape}" />
								</td>
								<td rowspan="2" valign="middle">{tr}Use SSL{/tr}</td>
								<td rowspan="2" valign="middle">
									<input type="checkbox" name="useSSL" value="y" {if $info.useSSL eq 'y'}checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td>{tr}POP server{/tr}</td>
								<td>
									<input type="text" name="pop" value="{$info.pop|escape}" />
								</td>
							</tr>
							<tr><td colspan="4">
								<hr />
								<h3>{tr}Outgoing server{/tr}</h3>
							</td></tr>
							<tr>
								<td>{tr}SMTP server{/tr}</td>
								<td>
									<input type="text" name="smtp" value="{$info.smtp|escape}" />
								</td>
								<td>{tr}Port{/tr}</td>
								<td>
									<input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" />
								</td>
							</tr>
							<tr>
								<td>{tr}SMTP requires authentication{/tr}</td>
								<td colspan="3">
									{tr}Yes{/tr}<input type="radio" name="useAuth" value="y" {if $info.useAuth eq 'y'}checked="checked"{/if} />
									{tr}No{/tr}<input type="radio" name="useAuth" value="n" {if $info.useAuth eq 'n'}checked="checked"{/if} />
								</td>
							</tr>
							<tr>
								<td>{tr}From email{/tr}</td>
								<td colspan="2">
									<input type="text" name="fromEmail" value="{$info.fromEmail}" />
								</td>
								<td>
									<em>{tr}Uses current in preferences if empty{/tr} ({if !empty($userEmail)}{$userEmail}{else}<strong>{tr}No email set:{/tr}</strong> {icon _id="arrow_right" href="tiki-user_preferences.php?cookietab=2"}{/if})</em>
								</td>
							</tr>
							<tr><td colspan="4">
								<hr />
								<h3>{tr}Account details{/tr}</h3>
							</td></tr>
							<tr>
								<td>{tr}Username{/tr}</td>
								<td colspan="3">
									<input type="text" name="username" value="{$info.username|escape}" />
								</td>
							</tr>
							<tr>
								<td>{tr}Password{/tr}</td>
								<td colspan="3">
									<input type="password" name="pass" value="{$info.pass|escape}" />
								</td>
							</tr>
							<tr>
								<td>{tr}Messages per page{/tr}</td>
								<td colspan="3">
									<input type="text" name="msgs" size="4" value="{$info.msgs|escape}" />
								</td>
							</tr>
		
							{if ($tiki_p_admin_group_webmail eq 'y' and $tiki_p_admin_personal_webmail eq 'y') or $tiki_p_admin eq 'y'}
								<tr>
									<td>{tr}Group (shared mail inbox) or private{/tr}</td>
									<td colspan="3">
										{tr}Group{/tr}<input type="radio" name="flagsPublic" value="y" {if $info.flagsPublic eq 'y'}checked="checked"{/if} /> {tr}Private{/tr}<input type="radio" name="flagsPublic" value="n" {if $info.flagsPublic eq 'n'}checked="checked"{/if} />
									</td>
								</tr>
							{else}
								<tr>
									<td></td>
									<td>
										<input type="hidden" name="flagsPublic" {if $tiki_p_admin_group_webmail eq 'y'}value="y"{else} value="n"{/if}>
										{if $tiki_p_admin_group_webmail eq 'y'}
											{tr}This will be a group mail account.{/tr}{else}{tr}This will be a personal mail account.{/tr}
										{/if}
									</td>
								</tr>
							{/if}
		
							<tr>
								<td>{tr}Auto-refresh page time{/tr}</td>
								<td colspan="3">
									<input type="text" name="autoRefresh" size="4" value="{$info.autoRefresh|escape}" /> seconds (0 = no auto refresh)
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="3">
									<input type="submit" name="new_acc" value="{if $accountId eq ''}{tr}Add{/tr}{else}{tr}Update{/tr}{/if}" />
									<input type="submit" name="cancel_acc" value="{tr}Cancel{/tr}" />
								</td>
							</tr>
						</table>
					</form>
				</div>
			{else}
				{remarksbox type="info" title="{tr}Permissions{/tr}"}
					{tr}You do not have the correct permissions to Add or Edit a webmail account. <BR />Please contact your administrator and ask for "admin_personal_webmail" or "admin_group_webmail" permission.{/tr}
				{/remarksbox}
			{/if}
		{/tab}
	{/tabset}
{/if}


{if $locSection eq 'mailbox'}
	<table width="100%">
		<tr>
			<td>
				{if empty($filter)}<strong>{tr}Show All{/tr}</strong>{else}{self_link filter=''}{tr}Show All{/tr}{/self_link}{/if} |
				{if $filter eq 'unread'}<strong>{tr}Show Unread{/tr}</strong>{else}{self_link filter='unread'}{tr}Show Unread{/tr}{/self_link}{/if} |
				{if $filter eq 'flagged'}<strong>{tr}Show Flagged{/tr}</strong>{else}{self_link filter='flagged'}{tr}Show Flagged{/tr}{/self_link}{/if} |
				{if $autoRefresh != 0}
					{assign var=tip value="{tr}Auto refresh set for every $autoRefresh seconds.{/tr}"}
					{self_link refresh_mail=1 _title=$tip}{tr}Refresh now{/tr}{/self_link}
					<em></em>
				{else}
					{self_link refresh_mail=1}{tr}Refresh{/tr}{/self_link}
				{/if}
			</td>
			<td align="right" style="text-align:right">
				{if $flagsPublic eq 'y'}
					{tr}Group messages{/tr}
				{else}
					{tr}Messages{/tr}
				{/if}
				{$showstart} {tr}to{/tr} {$showend} {tr}of{/tr} {$total}
				&nbsp;
				| {if $first}{self_link start=$first}{tr}First{/tr}{/self_link}{else}{tr}First{/tr}{/if}
				| {if $prevstart}{self_link start=$prevstart}{tr}Prev{/tr}{/self_link}{else}{tr}Prev{/tr}{/if}
				| {if $nextstart}{self_link start=$nextstart}{tr}Next{/tr}{/self_link}{else}{tr}Next{/tr}{/if}
				| {if $last}{self_link start=$last}{tr}Last{/tr}{/self_link}{else}{tr}Last{/tr}{/if}
			</td>
		</tr>
	</table>
	<br />
	<form action="tiki-webmail.php" method="post" name="mailb">
		<input type="hidden" name="quickFlag" value="" />
		<input type="hidden" name="quickFlagMsg" value="" />
		<input type="hidden" name="locSection" value="mailbox" />
		<input type="submit" name="delete" value="{tr}Delete{/tr}" />
		<input type="hidden" name="start" value="{$start|escape}" />
		<select name="action">
			<option value="flag">{tr}Mark as flagged{/tr}</option>
			<option value="unflag">{tr}Mark as unflagged{/tr}</option>
			<option value="read">{tr}Mark as read{/tr}</option>
			<option value="unread">{tr}Mark as unread{/tr}</option>
		</select>
		<input type="submit" name="operate" value="{tr}Mark{/tr}" />
		<br />
		<br />
		<table class="normal webmail_list">
			<tr>
				<th>{select_all checkbox_names='msg[]'}</th>
				<th>&nbsp;</th>
				<th>{tr}Sender{/tr}</th>
				<th>{tr}Subject{/tr}</th>
				<th>{tr}Date{/tr}</th>
				<th>{tr}Size{/tr}</th>
			</tr>
			{section name=ix loop=$list}
				{if $list[ix].isRead eq 'y'}
					{assign var=class value="webmail_read"}
				{else}
					{assign var=class value=""}
				{/if}
				<tr class="{$class}">
					<td class="checkbox">
						<input type="checkbox" name="msg[]" value="{$list[ix].msgid}" />
						<input type="hidden" name="realmsg[{$list[ix].msgid}]" value="{$list[ix].realmsgid|escape}" />
					</td>
					<td class="icon">
						{if $list[ix].isFlagged eq 'y'}
							<a href="javascript: submit_form('{$list[ix].realmsgid|escape}','n')"><img src="img/webmail/flagged.gif" alt="{tr}Flagged{/tr}"></a>
						{else}
							{if $prefs.webmail_quick_flags eq 'y'}
								<a href="javascript: submit_form('{$list[ix].realmsgid|escape}','y')"><img src="img/webmail/unflagged.gif" alt="{tr}unFlagged{/tr}"></a>
							{/if}
						{/if}
						{if $list[ix].isReplied eq 'y'}
							<img src="img/webmail/replied.gif" alt="{tr}Replied{/tr}"/>
						{/if}
					</td>
					<td class="email">{$list[ix].sender.name}</td>
					<td class="text">
						{self_link msgid=$list[ix].msgid locSection='read'}{$list[ix].subject}{/self_link}
						{if $list[ix].has_attachment}<img src="img/webmail/clip.gif" alt="{tr}Clip{/tr}"/>{/if}
					</td>
					<td class="date">{$list[ix].timestamp|tiki_short_datetime}</td>
					<td class="integer">{$list[ix].size|kbsize}</td>
				</tr>
			{/section}
		</table>
	</form>
{/if}


{if $locSection eq 'read'}
	{if $prev}{self_link msgid=$prev}{tr}Prev{/tr}{/self_link} |{/if}
	{if $next}{self_link msgid=$next}{tr}Next{/tr}{/self_link} |{/if}
	{self_link locSection=mailbox}{tr}Back To Mailbox{/tr}{/self_link} |
	{if $fullheaders eq 'n'}
		{self_link msgid=$msgid fullheaders='1' msgid=$next}{tr}Full Headers{/tr}{/self_link}
	{else}
		{self_link msgid=$msgid msgid=$next}{tr}Normal Headers{/tr}{/self_link}
	{/if}
	<table>
		<tr>
			<td>
				<form method="post" action="tiki-webmail.php">
					<input type="submit" name="delete_one" value="{tr}Delete{/tr}" />
					{if $next}
						<input type="hidden" name="locSection" value="read" />
						<input type="hidden" name="msgid" value="{$next|escape}" />
					{else}
						<input type="hidden" name="locSection" value="mailbox" />
					{/if}
					<input type="hidden" name="msgdel" value="{$msgid|escape}" />
				</form>
			</td>
			<td>
				<form method="post" action="tiki-webmail.php">
					<input type="hidden" name="locSection" value="compose" />
					<input type="submit" name="reply" value="{tr}Reply{/tr}" />
					<input type="hidden" name="realmsgid" value="{$realmsgid|escape}" />
					<input type="hidden" name="to" value="{$headers.replyto|escape}" />
					<input type="hidden" name="subject" value="Re:{$headers.subject}" />
					<input type="hidden" name="body" value="{$plainbody|escape}" />
				</form>
			</td>
			<td>
				<form method="post" action="tiki-webmail.php">
					<input type="hidden" name="locSection" value="compose" />
					<input type="submit" name="replyall" value="{tr}Reply To All{/tr}" />
					<input type="hidden" name="to" value="{$headers.replyto|escape}" />
					<input type="hidden" name="realmsgid" value="{$realmsgid|escape}" />
					<input type="hidden" name="cc" value="{$headers.replycc|escape}" />
					<input type="hidden" name="subject" value="Re:{$headers.subject}" />
					<input type="hidden" name="body" value="{$plainbody|escape}" />
				</form>
			</td>
			<td>
				<form method="post" action="tiki-webmail.php">
					<input type="submit" name="reply" value="{tr}Forward{/tr}" />
					<input type="hidden" name="locSection" value="compose" />
					<input type="hidden" name="to" value="" />
					<input type="hidden" name="cc" value="" />
					<input type="hidden" name="subject" value="Fw:{$headers.subject}" />
					<input type="hidden" name="body" value="{$plainbody|escape}" />
				</form>
			</td>
		</tr>
	</table>

	<table class="webmail_message_headers">
		{if $fullheaders eq 'n'}
			<tr>
				<th><strong>{tr}Subject{/tr}</strong></td>
				<td><strong>{$headers.subject|escape}</strong></td>
			</tr>
			<tr>
				<th>{tr}From{/tr}</td>
				<td>{$headers.from|escape}</td>
			</tr>
			<tr>
				<th>{tr}To{/tr}</td>
				<td>{$headers.to|escape}</td>
			</tr>
			{if $headers.cc}
				<tr>
					<td>{tr}Cc{/tr}</td>
					<td>{$headers.cc|escape}</td>
				</tr>
			{/if}
			<tr>
				<th>{tr}Date{/tr}</td>
				<td>{$headers.timestamp|tiki_short_datetime}</td>
			</tr>
		{/if}
		{if $fullheaders eq 'y'}
			{foreach key=key item=item from=$headers}
				<tr>
					<th>{$key}</td>
					<td>
						{if is_array($item)}
							{foreach from=$item item=part}
								{$part}
								<br />
							{/foreach}
						{else}
							{$item}
						{/if}
					</td>
				</tr>
			{/foreach}
		{/if}
	</table>

	{section name=ix loop=$bodies}
		{assign var='wmid' value='webmail_message_'|cat:$msgid|cat:'_'|cat:$smarty.section.ix.index}
		{assign var='wmopen' value='y'}
		{if $bodies[ix].contentType eq 'text/plain'}
			{if count($bodies) gt 1}
				{assign var='wmopen' value='n'}
			{/if}
			{assign var='wmclass' value='webmail_message webmail_mono'}
		{else}
			{if $bodies[ix].contentType neq 'text/html'}
				{assign var='wmopen' value='n'}
			{/if}
			{assign var='wmclass' value='webmail_message'}
		{/if}
		<div>
			{button _flip_id=$wmid _text="{tr}Part:{/tr} "|cat:$bodies[ix].contentType _flip_default_open=$wmopen}
		</div>
		<div id="{$wmid}" class="{$wmclass}" {if $wmopen eq 'n'}style="display:none"{/if}>
{$bodies[ix].body}
		</div>
	{/section}
	<div>
		{button _flip_id='webmail_message_source_'|cat:$msgid _text="{tr}Source:{/tr} " _flip_default_open='n'}
	</div>
	<div id="webmail_message_source_{$msgid}" class="webmail_message webmail_mono" style="display:none">
{$allbodies|nl2br}
	</div>

	{section name=ix loop=$attachs}
		<div class="simplebox">
			<a class="link" href="tiki-webmail_download_attachment.php?locSection=read&amp;msgid={$msgid}&amp;getpart={$attachs[ix].part}">{$attachs[ix].name|iconify}{$attachs[ix].name}</a>
		</div>
	{/section}
{/if}
{if $locSection eq 'compose'}
	{if $attaching eq 'n'}
		{if $sent eq 'n'}
			<form action="tiki-webmail.php" method="post">
				<input type="hidden" name="locSection" value="compose" />
				<input type="hidden" name="attach1" value="{$attach1|escape}" />
				<input type="hidden" name="attach2" value="{$attach2|escape}" />
				<input type="hidden" name="attach3" value="{$attach3|escape}" />
				<input type="hidden" name="attach1file" value="{$attach1file|escape}" />
				<input type="hidden" name="attach2file" value="{$attach2file|escape}" />
				<input type="hidden" name="attach3file" value="{$attach3file|escape}" />
				<input type="hidden" name="attach1type" value="{$attach1type|escape}" />
				<input type="hidden" name="attach2type" value="{$attach2type|escape}" />
				<input type="hidden" name="attach3type" value="{$attach3type|escape}" />
				<input type="submit" name="send" value="{tr}Send{/tr}" />
				<table class="formcolor">
					<tr>
						<td>
							<a title="{tr}Select from address book{/tr}" class="link" href="#" onclick="javascript:window.open('tiki-webmail_contacts.php?element=to','','menubar=no,width=452,height=550');">{tr}To{/tr}</a>:
						</td>
						<td colspan="3">
							<input size="69" type="text" id="to" name="to" value="{$to|escape}" />
						</td>
					</tr>
					<tr>
						<td>
							<a title="{tr}Select from address book{/tr}" class="link" href="#" onclick="javascript:window.open('tiki-webmail_contacts.php?element=cc','','menubar=no,width=452,height=550');">{tr}CC{/tr}</a>:
						</td>
						<td>
							<input id="cc" type="text" name="cc" value="{$cc|escape}" /></td>
						<td>
							<a title="{tr}Select from address book{/tr}" class="link" href="#" onclick="javascript:window.open('tiki-webmail_contacts.php?element=bcc','','menubar=no,width=452,height=550');">{tr}BCC{/tr}</a>:
						</td>
						<td>
							<input type="text" name="bcc" value="{$bcc}" id="bcc" />
						</td>
					</tr>
					<tr>
						<td>{tr}Subject{/tr}</td>
						<td colspan="3">
							<input size="69" type="text" name="subject" value="{$subject|escape}" />
						</td>
					</tr>
					<tr>
						<td>{tr}Attachments{/tr}</td>
						<td colspan="3">
							{if $attach1}
								({$attach1})
							{/if}
							{if $attach2}
								({$attach2})
							{/if}
							{if $attach3}
								({$attach3})
							{/if}
							<input type="submit" name="attach" value="{tr}Add{/tr}" />
						</td>
					</tr>
					<tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="3">
							<!--textarea name="body" cols="60" rows="30">{$body}</textarea-->
							{textarea name='body'}{$body}{/textarea}
						</td>
					</tr>
					<tr>
						<td>{tr}Use HTML mail{/tr}</td>
						<td colspan="3">
							<input type="checkbox" name="useHTML"{if $useHTML eq "y" || $smarty.session.wysiwyg eq "y"} checked="checked"{/if} />
						</td>
					</tr>
				</table>
			</form>
		{else}
			{$msg}
			<br /><br />
			{if $notcon eq 'y'}
				{tr}The following addresses are not in your address book{/tr}
				<br /><br />
				<form action="tiki-webmail.php" method="post">
					<table class="normal">
						<tr>
							<th>&nbsp;</th>
							<th>{tr}Email{/tr}</th>
							<th>{tr}First Name{/tr}</th>
							<th>{tr}Last Name{/tr}</th>
							<th>{tr}Nickname{/tr}</th>
						</tr>
						{section name=ix loop=$not_contacts}
							<tr>
								<td class="checkbox">
									<input type="checkbox" name="add[{$smarty.section.ix.index}]" />
									<input type="hidden" name="addemail[{$smarty.section.ix.index}]" value="{$not_contacts[ix]|escape}" />
								</td>
								<td class="email">{$not_contacts[ix]}</td>
								<td class="text">
									<input type="text" name="addFirstName[{$smarty.section.ix.index}]" />
								</td>
								<td class="text">
									<input type="text" name="addLastName[{$smarty.section.ix.index}]" />
								</td>
								<td class="text">
									<input type="text" name="addNickname[{$smarty.section.ix.index}]" />
								</td>
							</tr>
						{/section}
						<tr>
							<td>&nbsp;</td>
							<td>
								<input type="submit" name="add_contacts" value="{tr}Add Contacts{/tr}" />
							</td>
						</tr>
					</table>
				</form>
			{/if}
		{/if}
	{else}
		<form enctype="multipart/form-data" action="tiki-webmail.php" method="post">
			<input type="hidden" name="locSection" value="compose" />
			<input type="hidden" name="to" value="{$to|escape}" />
			<input type="hidden" name="cc" value="{$cc|escape}" />
			<input type="hidden" name="bcc" value="{$bcc|escape}" />
			<input type="hidden" name="subject" value="{$subject|escape}" />
			<input type="hidden" name="body" value="{$body|escape}" />
			<input type="hidden" name="attach1" value="{$attach1|escape}" />
			<input type="hidden" name="attach2" value="{$attach2|escape}" />
			<input type="hidden" name="attach3" value="{$attach3|escape}" />
			<input type="hidden" name="attach1file" value="{$attach1file|escape}" />
			<input type="hidden" name="attach2file" value="{$attach2file|escape}" />
			<input type="hidden" name="attach3file" value="{$attach3file|escape}" />
			<input type="hidden" name="attach1type" value="{$attach1type|escape}" />
			<input type="hidden" name="attach2type" value="{$attach2type|escape}" />
			<input type="hidden" name="attach3type" value="{$attach3type|escape}" />
			<table class="formcolor">
				{if $attach1}
					<tr>
						<td>{tr}Attachment 1{/tr}</td>
						<td>{$attach1} <input type="submit" name="remove_attach1" value="{tr}Remove{/tr}" /></td>
					</tr>
				{else}
					<tr>
						<td>{tr}Attachment 1{/tr}</td>
						<td>
							<input type="hidden" name="MAX_FILE_SIZE" value="1500000" />
							<input name="userfile1" type="file" />
						</td>
					</tr>
				{/if}
				{if $attach2}
					<tr>
						<td>{tr}Attachment 2{/tr}</td>
						<td>
							{$attach2} <input type="submit" name="remove_attach2" value="{tr}Remove{/tr}" />
						</td>
					</tr>
				{else}
					<tr>
						<td>
							{tr}Attachment 2{/tr}
						</td>
						<td>
							<input type="hidden" name="MAX_FILE_SIZE" value="1500000" /><input name="userfile2" type="file" />
						</td>
					</tr>
				{/if}
				{if $attach3}
					<tr>
						<td>{tr}Attachment 3{/tr}</td>
						<td>
							{$attach3} <input type="submit" name="remove_attach3" value="{tr}Remove{/tr}" />
						</td>
					</tr>
				{else}
					<tr>
						<td>{tr}Attachment 3{/tr}</td>
						<td>
							<input type="hidden" name="MAX_FILE_SIZE" value="1500000" /><input name="userfile3" type="file" />
						</td>
					</tr>
				{/if}
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="attached" value="{tr}Done{/tr}" />
					</td>
				</tr>
			</table>
		</form>
	{/if}
{/if}
