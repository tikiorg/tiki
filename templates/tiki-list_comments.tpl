{* $Id$ *}
{title help="comments" admpage="comments"}{$title}{/title}

{if $comments or ($find ne '') or count($show_types) gt 0 or isset($smarty.request.findfilter_approved)}
	<div class="col-md-6">
		{include file='find.tpl' types=$show_types find_type=$selected_types types_tag='checkbox' filters=$filters filter_names=$filter_names filter_values=$filter_values}
	</div>
{/if}

{if $comments}
	<form name="checkboxes_on" method="post" action="tiki-list_comments.php">
	{query _type='form_input'}
{/if}

{assign var=numbercol value=2}

{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
		<div class="{if $js === 'y'}table-responsive{/if} comment-table"> {*the table-responsive class cuts off dropdown menus *}
<table class="table table-striped table-hover">
	<tr>
		{if $comments}
			<th>
				{select_all checkbox_names='checked[]'}
				{assign var=numbercol value=$numbercol+1}
			</th>
		{/if}
		<th></th>

		{foreach key=headerKey item=headerName from=$headers}
			<th>
				{assign var=numbercol value=$numbercol+1}
				{self_link _sort_arg="sort_mode" _sort_field=$headerKey}{tr}{$headerName}{/tr}{/self_link}
			</th>
		{/foreach}

		{if $tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y'}
			<th>
				{assign var=numbercol value=$numbercol+1}
				{self_link _sort_arg="sort_mode" _sort_field='approved'}{tr}Approval{/tr}{/self_link}
			</th>
		{/if}
		<th></th>
	</tr>


	{section name=ix loop=$comments}{assign var=id value=$comments[ix].threadId}
		{capture name=over_actions}
			{strip}
				{$libeg}<a href="{$comments[ix].href}">
					{icon name='view' _menu_text='y' _menu_icon='y'}
				</a>{$liend}
				{$libeg}<a href="{$comments[ix].href|cat:"&amp;comments_threadId=$id&amp;edit_reply=1#form"}">
					{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
				</a>{$liend}
				{if $tiki_p_admin_comments eq 'y' and $prefs.comments_archive eq 'y'}
					{if $comments[ix].archived eq 'y'}
						{$libeg}{self_link action='unarchive' checked=$id _menu_text='y' _menu_icon='y' _icon_name='file-archive-open'}
							{tr}Unarchive{/tr}
						{/self_link}{$liend}
					{else}
						{$libeg}{self_link action='archive' checked=$id _menu_text='y' _menu_icon='y' _icon_name='file-archive'}
							{tr}Archive{/tr}
						{/self_link}{$liend}
					{/if}
				{/if}
				{$libeg}{self_link action='remove' checked=$id _menu_text='y' _menu_icon='y' _icon_name='remove'}
					{tr}Delete{/tr}
				{/self_link}{$liend}
			{/strip}
		{/capture}

		{capture name=over_more_info}
			{strip}
				{foreach from=$more_info_headers key=headerKey item=headerName}
					{if (isset($comments[ix].$headerKey))}
						{assign var=val value=$comments[ix].$headerKey}
						{$libeg}<b>{tr}{$headerName}{/tr}</b>: {$val}{$liend}
					{/if}
				{/foreach}
			{/strip}
		{/capture}

		<tr class="{cycle}{if $prefs.feature_comments_moderation eq 'y'} post-approved-{$comments[ix].approved}{/if}">
			<td class="checkbox-cell"><input type="checkbox" name="checked[]" value="{$id}" {if isset($rejected[$id]) }checked="checked"{/if}></td>
			<td class="action">
				{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a
					class="tips"
					title="{tr}Actions{/tr}"
					href="#"
					{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.over_actions|escape:"javascript"|escape:"html"}{/if}
					style="padding:0; margin:0; border:0"
				>
					{icon name="wrench"}
				</a>
				{if $js === 'n'}
					<ul class="dropdown-menu" role="menu">{$smarty.capture.over_actions}</ul></li></ul>
				{/if}
			</td>

			{foreach key=headerKey item=headerName from=$headers}{assign var=val value=$comments[ix].$headerKey}
				<td {if $headerKey eq 'data'}{popup caption=$comments[ix].title|escape:"javascript"|escape:"html" text=$comments[ix].parsed|escape:"javascript"|escape:"html"}{/if}>
					<span> {* span is used for some themes CSS opacity on some cells content *}
						{if $headerKey eq 'title'}
							<a href="{$comments[ix].href}" title="{$val|escape}">
								{if !empty($val)}
									{$val|truncate:50:"...":true|escape}
								{else}
									{tr}(no title){/tr}
								{/if}
							</a>
						{elseif $headerKey eq 'objectType'}
							{tr}{$val|ucwords}{/tr}
						{elseif $headerKey eq 'object'}
							{$val|truncate:50:"...":true|escape}
						{elseif $headerKey eq 'data'}
							{$val|truncate:90:"...":true|escape}
						{elseif $headerKey eq 'commentDate'}
							{$val|tiki_short_datetime}
						{elseif $headerKey eq 'userName'}
							{$val|userlink}
						{else}
							{$val}
						{/if}
					</span>
				</td>
			{/foreach}

			{if $tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y'}
				<td class="approval">
					{if $comments[ix].approved eq 'n'}
						{self_link action='approve' checked=$id _icon_name='ok' _class='tips' _title=":{tr}Approve{/tr}"}
						{/self_link}
						{self_link action='reject' checked=$id _icon_name='delete' _class='tips' _title=":{tr}Reject{/tr}"}
						{/self_link}
					{elseif $comments[ix].approved eq 'y'}
						&nbsp;{tr}Approved{/tr}&nbsp;
					{elseif $comments[ix].approved eq 'r'}
						<span>&nbsp;{tr}Rejected{/tr}&nbsp;</span>
					{/if}
				</td>
			{/if}

			<td>
				{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a
					class="tips"
					title="{tr}More information{/tr}"
					href="#"
					{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.over_more_info|escape:"javascript"|escape:"html"}{/if}
					style="padding:0; margin:0; border:0"
				>
					{icon name="information"}
				</a>
				{if $js === 'n'}
					<ul class="dropdown-menu" role="menu">{$smarty.capture.over_more_info}</ul></li></ul>
				{/if}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=$numbercol}
	{/section}
</table>
</div>

{if $comments}
	<div class="input-group col-sm-6">
		<select class="form-control" name="action">
			<option value="no_action" selected="selected">
				{tr}Select action to perform with checked{/tr}...
			</option>
			<option value="remove">
				{tr}Delete{/tr}
			</option>
			{if $tiki_p_admin_comments eq 'y' and $prefs.feature_banning eq 'y'}
				<option value="ban">
					{tr}Ban{/tr}
				</option>
				<option value="ban_remove">
					{tr}Delete and ban{/tr}
				</option>
			{/if}
			{if $tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y'}
				<option value="approve">
					{tr}Approve{/tr}
				</option>
				<option value="reject">
					{tr}Reject{/tr}
				</option>
			{/if}
			{if $tiki_p_admin_comments eq 'y' and $prefs.comments_archive eq 'y'}
				<option value="archive">
					{tr}Archive{/tr}
				</option>
				<option value="unarchive">
					{tr}Unarchive{/tr}
				</option>
			{/if}
		</select>
		<span class="input-group-btn">
			<button type="submit" class="btn btn-primary">{tr}OK{/tr}</button>
		</span>
	</div>
	</form>
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
