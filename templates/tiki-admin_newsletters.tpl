{* $Id$ *}
{title help="Newsletters"}{tr}Admin newsletters{/tr}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-admin_newsletters.php?cookietab=2" _class="btn btn-default" _icon_name="create" _text="{tr}Create{/tr}"}
	{button href="tiki-newsletters.php" _class="btn btn-default" _icon_name="list" _text="{tr}List{/tr}"}
	{button href="tiki-send_newsletters.php" _class="btn btn-default" _icon_name="envelope" _text="{tr}Send{/tr}"}
</div>

{tabset}

	{tab name="{tr}Newsletters{/tr}"}
		<h2>{tr}Newsletters{/tr}</h2>

		{if $channels or ($find ne '')}
			{include file='find.tpl'}
		{/if}

		<div class="table-responsive">
			<table class="table normal">
				<tr>
					<th>{self_link _sort_arg='sort_mode' _sort_field='nlId'}{tr}ID{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Newsletter{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='author'}{tr}Author{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='users'}{tr}Users{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='editions'}{tr}Editions{/tr}{/self_link}</th>
					<th>{tr}Drafts{/tr}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='lastSent'}{tr}Last Sent{/tr}{/self_link}</th>
					<th></th>
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td class="id">{self_link cookietab='2' _anchor='anchor2' nlId=$channels[user].nlId _title="{tr}Edit{/tr}"}{$channels[user].nlId}{/self_link}</td>
						<td class="text">
							{self_link cookietab='2' _anchor='anchor2' nlId=$channels[user].nlId _title="{tr}Edit{/tr}"}{$channels[user].name|escape}{/self_link}
							<div class="subcomment">{$channels[user].description|escape|nl2br}</div>
						</td>
						<td class="username">{$channels[user].author}</td>
						<td class="integer">{$channels[user].users} ({$channels[user].confirmed})</td>
						<td class="integer">{$channels[user].editions}</td>
						<td class="integer">{$channels[user].drafts}</td>
						<td class="date">{$channels[user].lastSent|tiki_short_datetime}</td>
						<td class="action">
							{capture name=newsletters_actions}
								{strip}
									{permission_link mode=text type=newsletter permType=newsletters id=$channels[user].nlId title=$channels[user].name}
									<a href="tiki-admin_newsletter_subscriptions.php?nlId={$channels[user].nlId}">
										{icon name='group' _menu_text='y' _menu_icon='y' alt="{tr}Subscriptions{/tr}"}
									</a>
									<a href="tiki-send_newsletters.php?nlId={$channels[user].nlId}">
										{icon name='envelope' _menu_text='y' _menu_icon='y' alt="{tr}Send newsletter{/tr}"}
									</a>
									<a href="tiki-newsletter_archives.php?nlId={$channels[user].nlId}">
										{icon name='file-archive' _menu_text='y' _menu_icon='y' alt="{tr}Archives{/tr}"}
									</a>
									{self_link _icon_name='edit' _menu_text='y' _menu_icon='y' cookietab='2' _anchor='anchor2' nlId=$channels[user].nlId}
										{tr}Edit{/tr}
									{/self_link}
									{self_link _icon_name='remove' _menu_text='y' _menu_icon='y' remove=$channels[user].nlId}
										{tr}Remove{/tr}
									{/self_link}
								{/strip}
							{/capture}
							<a class="tips"
							   title="{tr}Actions{/tr}"
							   href="#" {popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.newsletters_actions|escape:"javascript"|escape:"html"}
							   style="padding:0; margin:0; border:0"
									>
								{icon name='wrench'}
							</a>
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=8}
				{/section}
			</table>
		</div>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Create/Edit Newsletters{/tr}"}
		<h2>{tr}Create/Edit Newsletters{/tr}</h2>
		{if isset($individual) && $individual eq 'y'}
			{permission_link mode=link type=newsletter permType=newsletters id=$info.nlId title=$info.name label="{tr}There are individual permissions set for this newsletter{/tr}"}
		{/if}

		<form action="tiki-admin_newsletters.php" method="post" class="form-horizontal">
			<input type="hidden" name="nlId" value="{$info.nlId|escape}">
			<input type="hidden" name="author" value="{$user|escape}">
			<div class="form-group">
                <label class="col-md-2 control-label"> {tr}Name:{/tr} </label>
                <div class="col-md-10">
						<input class="form-control" type="text" name="name" value="{$info.name|escape}">
                </div>
			</div>
            <div class="form-group">
                <label class="col-md-2 control-label">{tr}Description:{/tr}</label>
                <div class="col-md-10">
                    <textarea class="form-control" name="description">{$info.description|escape}</textarea>
				</div>
			</div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="allowUserSub" {if $info.allowUserSub eq 'y'}checked="checked"{/if}>
                        {tr}Users can subscribe/unsubscribe to this list{/tr}
                </label>
			</div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="allowAnySub" {if $info.allowAnySub eq 'y'}checked="checked"{/if}>
                    {tr}Users can subscribe any email address{/tr}
                </label>
			</div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="unsubMsg" {if $info.unsubMsg eq 'y'}checked="checked"{/if}>
                    {tr}Add unsubscribe instructions to each newsletter{/tr}
                </label>
            </div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="validateAddr" {if $info.validateAddr eq 'y'}checked="checked"{/if}>
                    {tr}Validate email addresses{/tr}
                </label>
            </div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="allowTxt" {if $info.allowTxt eq 'y'}checked="checked"{/if}>
                    {tr}Allow customized text message to be sent with the HTML version{/tr}
                </label>
            </div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="allowArticleClip" {if $info.allowArticleClip eq 'y'}checked="checked"{/if}>
                    {tr}Allow clipping of articles into newsletter{/tr}
                </label>
            </div>
            <div class="checkbox col-md-offset-2">
                <label>
                    <input type="checkbox" name="autoArticleClip" {if $info.autoArticleClip eq 'y'}checked="checked"{/if}>
                    {tr}Automatically clip articles into newsletter{/tr}
                </label>
            </div>
            <div class="checkbox col-md-offset-2" style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" name="emptyClipBlocksSend" {if $info.emptyClipBlocksSend eq 'y'}checked="checked"{/if}>
                    {tr}Do not send newsletter if clip is empty{/tr}
                </label>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label">
                    {tr}Clip articles published in the past number of days{/tr}</label>
                <div class="col-md-4">
				    <input type="text" class="form-control" name="articleClipRangeDays" value="{$info.articleClipRangeDays|escape}">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label">
				    {tr}Article types to clip{/tr}</label>
                <div class="col-md-4">
					<select name="articleClipTypes[]" class="form-control" multiple="multiple">
						{section name=type loop=$articleTypes}
							<option value="{$articleTypes[type]}" {if in_array($articleTypes[type], $info.articleClipTypes)}selected="selected"{/if}>{$articleTypes[type]|escape}</option>
						{/section}
					</select>
                </div>
            </div>
			<div class="text-center">
						<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
            </div>
		</form>
	{/tab}

{/tabset}
