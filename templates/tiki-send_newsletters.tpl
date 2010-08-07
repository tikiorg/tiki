{* $Id$ *}
{$showBoxCheck}

{title help="Newsletters"}{tr}Send Newsletters{/tr} {if $nlId ne '0'}{$nlName}{/if}{/title}

{if $tiki_p_admin_newsletters eq "y"}
	<div class="navbar">
		{button href="tiki-newsletters.php" _text="{tr}List Newsletters{/tr}"}
		{if $nlId}
			{button href="tiki-admin_newsletters.php?nlId=$nlId" _text="{tr}Admin Newsletters{/tr}"}
		{else}
			{button href="tiki-admin_newsletters.php" _text="{tr}Admin Newsletters{/tr}"}
		{/if}
	</div>
{/if}

{if $upload_err_msg neq ''}
	{remarksbox type='warning' title="{tr}Warning{/tr}" icon='error'}
		{$upload_err_msg}
	{/remarksbox}
{/if}

{assign var=area_name value="editwiki"}
{if $emited eq 'y'}
	{remarksbox type="note" title="{tr}Notice{/tr}" icon="lock"}
		{tr}The newsletter was sent to {$sent} email addresses{/tr}
	{/remarksbox}
	
	{if $errors}
		{remarksbox type='warning' title="{tr}Errors{/tr}" icon='error'}
		<table class="normal">
			<tr class="formcolor">
				<th>{tr}User{/tr}</th>
				<th>{tr}Email{/tr}</th>
				<th>{tr}Message{/tr}</th>
			</tr>
			{cycle values="odd,even" print=false}
			{section loop=$errors name=ix}
					<tr class="formcolor">
					<td class="{cycle advance=false}">{$errors[ix].user|escape}</td>
					<td class="{cycle advance=false}">{$errors[ix].email|escape}</td>
					<td class="{cycle}">{$errors[ix].msg|escape}</td>
				</tr>
			{/section}
		</table>
		{/remarksbox}
	{/if}
{/if}

{if $presend eq 'y'}
	<div id="confirmArea">
	{remarksbox type='note' title="{tr}Please Confirm{/tr}"}
		<b>{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}</b>
		<br />
		{tr}Reply to:{/tr} {if empty($replyto)}{$prefs.sender_email|escape} ({tr}default{/tr}){else}{$replyto|escape}{/if}
	{/remarksbox}
	<p>
		<form method="post" action="tiki-send_newsletters.php" target="resultIframe" id='confirmForm'>
			<input type="hidden" name="nlId" value="{$nlId|escape}" />
			<input type="hidden" name="sendingUniqId" value="{$sendingUniqId|escape}" />
			<input type="hidden" name="editionId" value="{$info.editionId}"/>
			<input type="hidden" name="subject" value="{$subject|escape}" />
			<input type="hidden" name="data" value="{$data|escape}" />
			<input type="hidden" name="dataparsed" value="{$dataparsed|escape}" />
			<input type="hidden" name="cookietab" value="3" />
			<input type="hidden" name="datatxt" value="{$datatxt|escape}" />
			<input type="hidden" name="replyto" value="{$replyto|escape}" />
			<input type="hidden" name="wysiwyg" value="{$wysiwyg|escape}" />
			<input type="submit" name="send" value="{tr}Send{/tr}" onclick="document.getElementById('confirmArea').style.display = 'none'; document.getElementById('sendingArea').style.display = 'block';" />
			<input type="submit" name="preview" value="{tr}Cancel{/tr}" />
			{foreach from=$info.files item=newsletterfile key=fileid}
				<input type='hidden' name='newsletterfile[{$fileid}]' value='{$newsletterfile.id}'/>
			{/foreach}
		</form>
	</p>
	{if $subscribers gt 0}
		<h3>{tr}Recipients{/tr} <a id="flipperrecipients" href="javascript:flipWithSign('recipients')">[+]</a></h3>
		<div id="recipients" class="simplebox" style="display:none; max-height: 250px; overflow: auto;">
			<table class="small normal">
				<tr>
					<th>{tr}Email{/tr}</th>
					<th>{tr}Validated{/tr}</th>
					<th>{tr}Is user{/tr}</th>
				</tr>
				{cycle values="even,odd" print=false}
				{foreach from=$subscribers_list item=sub key=ix}
					<tr>
						<td class="{cycle advance=false}">{$sub.email|escape}</td>
						<td class="{cycle advance=false}">{$sub.valid}</td>
						<td class="{cycle}">{$sub.isUser}</td>
					</tr>
				{/foreach}
			</table>
		</div>
	{/if}	
	<h2>{tr}Preview{/tr}</h2>
	<h3>{tr}Subject{/tr}</h3>
	<div class="simplebox wikitext">{$subject|escape}</div>

	<h3>{tr}HTML version{/tr}</h3>
	<div class="simplebox wikitext">{$previewdata}</div>

	{if $allowTxt eq 'y' }
		<h3>{tr}Text version{/tr}</h3>
		{if $info.datatxt}<div class="simplebox wikitext" >{$info.datatxt|escape|nl2br}</div>{/if}
		{if $txt}<div class="simplebox wikitext">{$txt|escape|nl2br}</div>{/if}
	{/if}
	
	<h3>{tr}Files{/tr}</h3>
	<div class="simplebox wikitext">
		{if $info.file|@count gt 0}
			<ul>
				{foreach from=$info.files item=newsletterfile key=fileid}
					<li>
						{$newsletterfile.name|escape} ({$newsletterfile.type|escape}, {$newsletterfile.size|escape} {tr}bytes{/tr})
					</li>
				{/foreach}
			</ul>
		{else}
			{tr}None{/tr}
		{/if}
	</div>


	
	</div>

	<div id="sendingArea" style="display:none">
		<h3>{tr}Sending Newsletter{/tr} ...</h3>
		<iframe id="resultIframe" name="resultIframe" frameborder="0" style="width: 600px; height: 400px"></iframe>
	</div>

{else}
	{if $preview eq 'y'}
		<h2>{tr}Preview{/tr}</h2>
		<h3>{tr}Subject{/tr}</h3>
		<div class="simplebox wikitext">{$info.subject|escape}</div>

		<h3>{tr}HTML version{/tr}</h3>
		<div class="simplebox wikitext">{$previewdata}</div>

		{if $allowTxt eq 'y' }
			<h3>{tr}Text version{/tr}</h3>
			{if $info.datatxt}<div class="simplebox wikitext" >{$info.datatxt|escape|nl2br}</div>{/if}
			{if $txt}<div class="simplebox wikitext">{$txt|escape|nl2br}</div>{/if}
		{/if}

		<h3>{tr}Files{/tr}</h3>
		<ul>
			{foreach from=$info.files item=newsletterfile key=fileid}
				<li>
					{$newsletterfile.name|escape} ({$newsletterfile.type|escape}, {$newsletterfile.size|escape} {tr}bytes{/tr})
				</li>
			{/foreach}
		</ul>
	{/if}

{tabset name='tabs_send_newsletters'}

	{tab name="{tr}Edit{/tr}"}
	{* --- tab with editor --- *}
		<h2>{tr}Prepare a newsletter to be sent{/tr}</h2>
		<form action="tiki-send_newsletters.php" method="post" id='editpageform' enctype='multipart/form-data'>
			<input type="hidden" name="editionId" value="{$info.editionId}"/>
			<table class="normal" id="newstable">
				<tr class="formcolor">
					<td class="formcolor">{tr}Subject:{/tr}</td>
					<td class="formcolor">
						<input type="text" maxlength="250" size="80" name="subject" value="{$info.subject|escape}" />
					</td>
				</tr>
				<tr class="formcolor">
					<td class="formcolor">{tr}Newsletter:{/tr}</td>
					<td class="formcolor">
						<select name="nlId" onchange="checkNewsletterTxtArea(this.selectedIndex);">
							{section loop=$newsletters name=ix}
								<option value="{$newsletters[ix].nlId|escape}" {if $newsletters[ix].nlId eq $nlId}selected="selected"{/if}>
									{$newsletters[ix].name|escape}
								</option>
							{/section}
						</select>
					</td>
				</tr>

				{if $tiki_p_use_content_templates eq 'y'}
					<tr class="formcolor">
						<td class="formcolor">{tr}Apply content template{/tr}</td>
						<td class="formcolor">
							<input type="hidden" name="previousTemplateId" value="{$templateId}" />
							<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();">
								<option value="0">{tr}none{/tr}</option>
								{section name=ix loop=$templates}
									<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>
										{$templates[ix].name|escape}
									</option>
								{/section}
							</select>
						</td>
					</tr>
				{/if}
			
				{if $tpls}
					<tr class="formcolor">
						<td class="formcolor">{tr}Apply template{/tr}</td>
						<td class="formcolor">
							<select name="usedTpl">
								<option value="">{tr}none{/tr}</option>
								{section name=ix loop=$tpls}
									<option value="{$tpls[ix]|escape}" {if $usedTpl eq $tpls[ix]}selected="selected"{/if}>{$tpls[ix]}</option>
								{/section}
							</select>
						</td>
					</tr>
				{/if}

				<tr class="formcolor">
					<td colspan="2" class="formcolor">
						{tr}Data HTML:{/tr}
					</td>
				</tr>
				<tr>
					<td colspan="2" class="formcolor">
						{textarea name='data' id='editwiki'}{$info.data}{/textarea}
						{tr}Must be wiki parsed:{/tr} <input type="checkbox" name="wikiparse" {if empty($info.wikiparse) or $info.wikiparse eq 'y'} checked="checked"{/if} />
					</td>
				</tr>

				<tr class="formcolor">
					<td class="formcolor" id="txtcol1">
						{tr}Data Txt:{/tr}
						<br /><br />
						{include file='textareasize.tpl' area_name='editwikitxt' formId='editpageform'}
					</td>
					<td class="formcolor" id="txtcol2" >
						<textarea id='editwikitxt' name="datatxt" rows="{$rows}" cols="{$cols}">{$datatxt|escape}</textarea>
					</td>
				</tr>

				<tr class="formcolor">
					<td class="formcolor" id="clipcol1">
						{tr}Article Clip (read only):{/tr}
						<input type="submit" name="clipArticles" value="{tr}Clip Now{/tr}" class="wikiaction tips" title="{tr}Clip Articles{/tr}" onclick="needToConfirm=false" />
						<br /><br />
						{include file='textareasize.tpl' area_name='articlecliptxt' formId='editpageform'}
					</td>
					<td class="formcolor" id="clipcol2" >
						{tr}To include the article clipping into your newsletter, cut and paste it into the contents.{/tr}
						<br />{tr}If autoclipping is enabled, you can also enter "~~~articleclip~~~" which will be replaced with the latest	clip when sending.{/tr}
						<textarea id='articlecliptxt' name="articleClip" rows="{$rows}" cols="{$cols}" readonly="readonly">{$articleClip}</textarea>		
					</td>
				</tr>				
				
				<tr class="formcolor">
					<td class="formcolor" id="txtcol1">
						{tr}Attached Files{/tr} :
					</td>
					<td class="formcolor" id="txtcol2" >
						<div style='display: none' id='newsletterfileshack'></div>
						<div id='newsletterfiles'>
							{foreach from=$info.files item=newsletterfile key=fileid}
								<div id='newsletterfileid_{$fileid}'>
									<a href="javascript:remove_newsletter_file('{$fileid}');">[{tr}remove{/tr}]</a>
									{$newsletterfile.name|escape} ({$newsletterfile.type|escape}, {$newsletterfile.size|escape} {tr}octets{/tr})
									<input type='hidden' name='newsletterfile[{$fileid}]' value='{$newsletterfile.id}'/>
								</div>
							{/foreach}
						</div>
						<p><a href="javascript:add_newsletter_file();">{tr}To add a file, click here{/tr}</a></p>
					</td>
				</tr>

				<tr class="formcolor">
					<td class="formcolor" id="txtcol1">{tr}Reply To Email{/tr}</td>
					<td class="formcolor" id="txtcol2" ><input type="text" name="replyto" value="{$replyto|escape}" /> {tr}if not:{/tr} {$prefs.sender_email|escape}</td>
				</tr>

				<tr class="formcolor">
					<td class="formcolor">&nbsp;</td>
					<td class="formcolor">
						<input type="submit" name="preview" value="{tr}Preview{/tr}" class="wikiaction tips" title="{tr}Send Newsletters{/tr}|{tr}Preview your changes.{/tr}" onclick="needToConfirm=false" />
						&nbsp;
						<input type="submit" name="save_only" value="{tr}Save as Draft{/tr}" class="wikiaction tips" title="{tr}Send Newsletters{/tr}|{tr}Save your changes.{/tr}" onclick="needToConfirm=false" />
					</td>
				</tr>

				<tr>
					<td class="formcolor">&nbsp;</td>
					<td class="formcolor">&nbsp;<input type="submit" name="save" value="{tr}Send Newsletter{/tr}" class="wikiaction tips" title="{tr}Send Newsletters{/tr}|{tr}Save any changes and send to all subscribers.{/tr}" onclick="needToConfirm=false" /></td>
				</tr>
			</table>
		</form>
	{/tab}

	{tab name="{tr}Drafts{/tr}&nbsp;(`$cant_drafts`)"}
	{* --- tab with drafts --- *}
		{assign var=channels value=$drafts}
		{assign var=view_editions value='n'}
		{assign var=offset value=$dr_offset}
		{assign var=next_offset value=$dr_next_offset}
		{assign var=prev_offset value=$dr_prev_offset}
		{assign var=actual_page value=$dr_actual_page}
		{assign var=cant_pages value=$dr_cant_pages}
		{assign var=cur value='dr'}
		{assign var=bak value='ed'}
		{assign var=sort_mode value=$dr_sort_mode}
		{assign var=sort_mode_bak value=$ed_sort_mode}
		{assign var=offset value=$dr_offset}
		{assign var=offset_bak value=$ed_offset}
		{assign var=find value=$dr_find}
		{assign var=find_bak value=$ed_find}
		{assign var=tab value=2}
		<h2>{tr}Drafts{/tr}&nbsp;({$cant_drafts})</h2>
		{include file='sent_newsletters.tpl' }
	{/tab}

	{tab name="{tr}Sent editions{/tr}&nbsp;($cant_editions)"}
	{* --- tab with editions --- *}
		{assign var=channels value=$editions}
		{assign var=view_editions value='y'}
		{assign var=offset value=$ed_offset}
		{assign var=next_offset value=$ed_next_offset}
		{assign var=prev_offset value=$ed_prev_offset}
		{assign var=actual_page value=$ed_actual_page}
		{assign var=cant_pages value=$ed_cant_pages}
		{assign var=cur value='ed'}
		{assign var=bak value='dr'}
		{assign var=sort_mode value=$ed_sort_mode}
		{assign var=sort_mode_bak value=$dr_sort_mode}
		{assign var=offset value=$ed_offset}
		{assign var=offset_bak value=$dr_offset}
		{assign var=find value=$ed_find}
		{assign var=find_bak value=$dr_find}
		{assign var=tab value=3}
		<h2>{tr}Sent editions{/tr}&nbsp;({$cant_editions})</h2>
		{include file='sent_newsletters.tpl' }
		{/tab}
	{/tabset}
{/if}

{jq}
{{if $allowTxt eq 'n'}
document.getElementById('txtcol1').style.display='none';
document.getElementById('txtcol2').style.display='none';
{/if}}
{{if $allowArticleClip eq 'n'}
document.getElementById('clipcol1').style.display='none';
document.getElementById('clipcol2').style.display='none';
{/if}}

var newsletterfileid={{$info.files|@count}};
function add_newsletter_file() {
	document.getElementById('newsletterfileshack').innerHTML='<div id="newsletterfileid_'+newsletterfileid+'"><a href="javascript:remove_newsletter_file('+newsletterfileid+');">[{{tr}remove{/tr}}]</a> <input type="file" name="newsletterfile['+newsletterfileid+']"/></div>';
	document.getElementById('newsletterfiles').appendChild(document.getElementById('newsletterfileid_'+newsletterfileid));
	newsletterfileid++;
}
function remove_newsletter_file(id) {
	document.getElementById('newsletterfiles').removeChild(document.getElementById('newsletterfileid_'+id));
}
{/jq}
