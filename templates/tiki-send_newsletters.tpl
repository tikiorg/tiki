{* $Id$ *} 
{$showBoxCheck}

{if $nlId}
	{$titleurl="tiki-send_newsletters.php?nlId=$nlId"}
{else}
	{$titleurl="tiki-send_newsletters.php"}
{/if}
{title help="Newsletters" url=$titleurl}{tr}Send Newsletters{/tr}{/title}

{if $tiki_p_admin_newsletters eq "y"}
	<div class="t_navbar margin-bottom-md">
		{button href="tiki-newsletters.php" class="btn btn-default" _text="{tr}List Newsletters{/tr}"}
		{if $nlId}
			{button href="tiki-admin_newsletters.php?nlId=$nlId" class="btn btn-default" _text="{tr}Admin Newsletters{/tr}"}
		{else}
			{button href="tiki-admin_newsletters.php" class="btn btn-default" _text="{tr}Admin Newsletters{/tr}"}
		{/if}
	</div>
{/if}

{if $upload_err_msg neq ''}
	{remarksbox type='warning' title="{tr}Warning{/tr}" icon='error'}
		{$upload_err_msg}
	{/remarksbox}
{/if}

{if $mailto_link}
	{remarksbox type=info title="External Client"}
		{tr}You can also send newsletters using an external client:{/tr}
		<a href="{$mailto_link|escape}">{tr}Compose{/tr}</a>
	{/remarksbox}
{/if}

{if $emited eq 'y'}
	{remarksbox type="note" title="{tr}Notice{/tr}" icon="lock"}
		{tr}The newsletter was sent to {$sent} email addresses{/tr}
	{/remarksbox}

	{if $errors}
		{remarksbox type='warning' title="{tr}Errors{/tr}" icon='error'}
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>{tr}User{/tr}</th>
						<th>{tr}Email{/tr}</th>
						<th>{tr}Message{/tr}</th>
					</tr>

					{section loop=$errors name=ix}
						<tr>
							<td>{$errors[ix].user|escape}</td>
							<td>{$errors[ix].email|escape}</td>
							<td>{$errors[ix].msg|escape}</td>
						</tr>
					{/section}
				</table>
			</div>
		{/remarksbox}
	{/if}
{/if}

{if $presend eq 'y'}
	<div id="confirmArea">
	{remarksbox type='note' title="{tr}Please Confirm{/tr}"}
		<b>{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}</b>
		<br>
        {if $prefs.newsletter_throttle eq 'y'}{tr}Sending will be throttled with a batch size of {$prefs.newsletter_batch_size} and a pause period of {$prefs.newsletter_pause_length} seconds.{/tr}<br>{/if}
		{tr}Reply to:{/tr} {if empty($replyto)}{$prefs.sender_email|escape} ({tr}default{/tr}){else}{$replyto|escape}{/if}
	{/remarksbox}
	<p>
		<form method="post" action="tiki-send_newsletters.php" target="resultIframe" id='confirmForm'>
			<input type="hidden" name="nlId" value="{$nlId|escape}">
			<input type="hidden" name="sendingUniqId" value="{$sendingUniqId|escape}">
			<input type="hidden" name="editionId" value="{$info.editionId}">
			<input type="hidden" name="subject" value="{$subject|escape}">
			<input type="hidden" name="data" value="{$data|escape}">
			<input type="hidden" name="dataparsed" value="{$dataparsed|escape}">
			<input type="hidden" name="cookietab" value="3">
			<input type="hidden" name="datatxt" value="{$info.datatxt|escape}">
			<input type="hidden" name="replyto" value="{$replyto|escape}">
			<input type="hidden" name="wysiwyg" value="{$info.wysiwyg|escape}">
			<input type="hidden" name="is_html" value="{$info.is_html|escape}">
			<input type="submit" class="btn btn-default btn-sm" name="send" value="{tr}Send{/tr}" onclick="document.getElementById('confirmArea').style.display = 'none'; document.getElementById('sendingArea').style.display = 'block';">
			<input type="submit" class="btn btn-default btn-sm" name="cancel" value="{tr}Cancel{/tr}">
			{foreach from=$info.files item=newsletterfile key=fileid}
				<input type='hidden' name='newsletterfile[{$fileid}]' value='{$newsletterfile.id}'>
			{/foreach}
		</form>
	</p>
	{if $subscribers gt 0}
		<h3>{tr}Recipients{/tr} <a id="flipperrecipients" href="javascript:flipWithSign('recipients')">[+]</a></h3>
		<div id="recipients" class="cbox" style="display:none; max-height: 250px; overflow: auto;">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>{tr}Email{/tr}</th>
						<th>{tr}Validated{/tr}</th>
						<th>{tr}Is user{/tr}</th>
					</tr>

					{foreach from=$subscribers_list item=sub key=ix}
						<tr>
							<td>{$sub.email|escape}</td>
							<td>{$sub.valid}</td>
							<td>{$sub.isUser}</td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
	{/if}
	<h2>{tr}Preview{/tr}</h2>
	<h3>{tr}Subject{/tr}</h3>
	<div class="cbox wikitext">{$subject|escape}</div>

	<h3>{tr}HTML version{/tr}</h3>
	<div class="cbox wikitext">{$previewdata}</div>

	{if $allowTxt eq 'y'}
		<h3>{tr}Text version{/tr}</h3>
		{if $info.datatxt}<div class="cbox wikitext" >{$info.datatxt|escape|nl2br}</div>{/if}
		{if $txt}<div class="cbox wikitext">{$txt|escape|nl2br}</div>{/if}
	{/if}

	<h3>{tr}Files{/tr}</h3>
	<div class="cbox wikitext">
		{if $info.files|@count gt 0}
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
        <h5> ... {tr}sending to {$subscribers} addresses{/tr}</h5>
		<div id="confirmed"></div>
		<iframe id="resultIframe" name="resultIframe" frameborder="0" style="width: 600px; height: 400px"></iframe>
		{jq}
			$('#resultIframe').bind('load', function () {
				var root = this.contentDocument.documentElement, iframe = this;
				$('#confirmed').append($('.confirmation', root));
				$('.throttle', root).each(function () {
					var url = 'tiki-send_newsletters.php?resume=' + $(this).data('edition');
					setTimeout(function () {
						$(iframe).attr('src', url);
					}, parseInt($(this).data('rate'), 10) * 1000);
				});
			});
		{/jq}
	</div>

{else}
	{if $preview eq 'y'}
		<h2>{tr}Preview{/tr}</h2>
		<h3>{tr}Subject{/tr}</h3>
		<div class="cbox wikitext">{$info.subject|escape}</div>

		<h3>{tr}HTML version{/tr}</h3>
		<div class="cbox wikitext">{$previewdata}</div>

		{if $allowTxt eq 'y'}
			<h3>{tr}Text version{/tr}</h3>
			{if $info.datatxt}<div class="cbox wikitext" >{$info.datatxt|escape|nl2br}</div>{/if}
			{if $txt}<div class="cbox wikitext">{$txt|escape|nl2br}</div>{/if}
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
			<form class="form-horizontal" action="tiki-send_newsletters.php" method="post" id='editpageform' enctype='multipart/form-data'>
				<input type="hidden" name="editionId" value="{$info.editionId}">
				<div class="form-group">
					<label class="control-label col-sm-2">{tr}Subject:{/tr}</label>
					<div class="col-sm-10">
						<input type="text" maxlength="250" id="subject" name="subject" value="{$info.subject|escape}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{tr}Newsletter:{/tr}</label>
					<div class="col-sm-10">
						<select name="nlId" id="nlId" onchange="checkNewsletterTxtArea(this.selectedIndex);" class="form-control">
							{section loop=$newsletters name=ix}
								<option value="{$newsletters[ix].nlId|escape}" {if $newsletters[ix].nlId eq $nlId}selected="selected"{/if}>
									{$newsletters[ix].name|escape}
								</option>
							{/section}
						</select>
					</div>
				</div>
				{if $tiki_p_use_content_templates eq 'y'}
				<div class="form-group form-inline">
					<label class="control-label col-sm-2">{tr}Apply content template{/tr}</label>
					<div class="col-sm-10">
						<select name="templateId" onchange="javascript:needToConfirm=false;document.getElementById('editpageform').submit();" class="form-control">
							<option value="0">{tr}none{/tr}</option>
							{section name=ix loop=$templates}
								<option value="{$templates[ix].templateId|escape}" {if $templateId eq $templates[ix].templateId}selected="selected"{/if}>
									{$templates[ix].name|escape}
								</option>
							{/section}
						</select>
						{button _text="{tr}Admin templates{/tr}" href='tiki-admin_content_templates.php' _target='blank'}
					</div>
				</div>
				{/if}
				{if $tpls}
				<div class="form-group">
					<label class="control-label col-sm-2">{tr}Apply template{/tr}</label>
					<div class="col-sm-10">
						<select id="usedTpl"name="usedTpl" class="form-control">
							<option value="">{tr}none{/tr}</option>
							{section name=ix loop=$tpls}
								<option value="{$tpls[ix]|escape}" {if $usedTpl eq $tpls[ix]}selected="selected"{/if}>{$tpls[ix]}</option>
							{/section}
						</select>
					</div>
				</div>
				{/if}
				<div class="form-group">
					<label class="control-label col-sm-2">{tr}Data HTML:{/tr}</label>
				</div>
                <div class="form-group">
					<div class="col-sm-12">
						{textarea class="form-control" name='data' id='editwiki'}{$info.data}{/textarea}
					</div>
				</div>
				<div class="form-group form-inline">
					<label class="control-label col-sm-2">{tr}Must be wiki parsed:{/tr}</label>
					<div class="col-sm-10 form-control-static">
						<label><input type="checkbox" name="wikiparse" {if empty($info.wikiparse) or $info.wikiparse eq 'y'} checked="checked"{/if}> {tr}Wiki Parsed{/tr}&nbsp;</label>
						<label><input type="checkbox" name="is_html" {if $info.is_html} checked="checked"{/if}> {tr}Is HTML{/tr}</label>
					</div>
				</div>
				<div class="form-group" id="txtcol1"{if $allowTxt eq 'n'} style="display:none;"{/if}>
					<label class="control-label col-sm-2">{tr}Data Txt:{/tr}</label>
					<div class="col-sm-10">
						<textarea id='editwikitxt' name="datatxt" rows="20" cols="80" class="form-control">{$info.datatxt|escape}</textarea>
					</div>
				</div>
				<div class="form-group" id="clipcol1"{if $allowArticleClip eq 'n'} style="display:none;"{/if}>
					<label class="control-label col-sm-2">{tr}Article Clip (read only):{/tr}</label>
					<div class="col-sm-10">
						<input type="submit" name="clipArticles" value="{tr}Clip Now{/tr}" class="wikiaction tips btn btn-default" title="{tr}Clip Articles{/tr}" onclick="needToConfirm=false">
					</div>
				</div>
				<div class="form-group" id="clipcol2"{if $allowArticleClip eq 'n'} style="display:none;"{/if}>
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						{tr}To include the article clipping into your newsletter, cut and paste it into the contents.{/tr}
						<br>{tr}If autoclipping is enabled, you can also enter "~~~articleclip~~~" which will be replaced with the latest	clip when sending.{/tr}
						{if !empty($articleClip)}
						{remarksbox type="warning" title="{tr}Notice{/tr}"}
							{tr}Be careful not to paste articles that must not be seen by the recipients{/tr}
						{/remarksbox}
						{/if}
						<textarea id='articlecliptxt' class="form-control" name="articleClip" rows="20" cols="80" readonly="readonly">{$articleClip}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{tr}Attached Files{/tr}:</label>
					<div class="col-sm-10">
						<div style='display: none' id='newsletterfileshack'></div>
						<div id="newsletterfiles">
						{foreach from=$info.files item=newsletterfile key=fileid}
							<div id='newsletterfileid_{$fileid}'>
								<a href="javascript:remove_newsletter_file('{$fileid}');" class="btn btn-default">{tr}remove{/tr}</a>
								{$newsletterfile.name|escape} ({$newsletterfile.type|escape}, {$newsletterfile.size|escape} {tr}octets{/tr})
								<input type='hidden' name='newsletterfile[{$fileid}]' value='{$newsletterfile.id}'>
							</div>
						{/foreach}
						</div>
						<p><a class="btn btn-default" href="javascript:add_newsletter_file();">{tr}To add a file, click here{/tr}</a></p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{tr}Reply To Email{/tr}</label>
					<div class="col-sm-10">
						<input type="text" name="replyto" id="replyto" value="{$replyto|escape}" class="form-control">
						<div class="help-block">
							{tr}if not:{/tr} {$prefs.sender_email|escape|default:"<em>{tr}Sender email not set{/tr}</em>"}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						<input type="submit" name="preview" value="{tr}Preview{/tr}" class="wikiaction tips btn btn-default" title="{tr}Send Newsletters{/tr}|{tr}Preview your changes.{/tr}" onclick="needToConfirm=false">
						&nbsp;
						<input type="submit" name="save_only" value="{tr}Save as Draft{/tr}" class="wikiaction tips btn btn-default" title="{tr}Send Newsletters{/tr}|{tr}Save your changes.{/tr}" onclick="needToConfirm=false">
						&nbsp;
						<input type="submit" name="save" value="{tr}Send Newsletter{/tr}" class="wikiaction tips btn btn-primary" title="{tr}Send Newsletters{/tr}|{tr}Save any changes and send to all subscribers.{/tr}" onclick="needToConfirm=false"></td>
					</div>
				</div>
			</form>
		{/tab}

		{assign var=name value="{tr _0=$cant_drafts}Drafts (%0){/tr}"}
		{tab name=$name}
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
			<h2>{$name}</h2>
			{include file='sent_newsletters.tpl'}
		{/tab}

		{assign var=name value="{tr _0=$cant_editions}Sent Editions (%0){/tr}"}
		{tab name=$name}
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
			<h2>{$name}</h2>
			{include file='sent_newsletters.tpl'}
		{/tab}
	{/tabset}
{/if}

{jq notonready=true}
var newsletterfileid={{$info.files|@count}};
function add_newsletter_file() {
	document.getElementById('newsletterfileshack').innerHTML='<div id="newsletterfileid_'+newsletterfileid+'" class="form-group form-inline"><input type="file" name="newsletterfile['+newsletterfileid+']"><a href="javascript:remove_newsletter_file('+newsletterfileid+');" class="btn btn-default">{tr}remove{/tr}</a></div>';
	document.getElementById('newsletterfiles').appendChild(document.getElementById('newsletterfileid_'+newsletterfileid));
	newsletterfileid++;
}
function remove_newsletter_file(id) {
	document.getElementById('newsletterfiles').removeChild(document.getElementById('newsletterfileid_'+id));
}
{/jq}
