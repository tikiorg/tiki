{* $Id$ *}
{popup_init src="lib/overlib.js"}
{title help="comments"}{$title}{/title}

{if $comments or ($find ne '') or count($show_types) gt 0 or isset($smarty.request.findfilter_approved)}
	{include file='find.tpl' types=$show_types find_type=$selected_types types_tag='checkbox' filters=$filters filter_names=$filter_names filter_values=$filter_values}
{/if}

{if $comments}
	<form name="checkboxes_on" method="post" action="tiki-list_comments.php">
	{query _type='form_input'}
{/if}

{assign var='cntcol' value=2}
<table class="normal">
	<tr>
		<th>
			{if $comments}
				{select_all checkbox_names='checked[]'}
			{/if}
		</th>
		<th></th>
	
		{foreach key=headerKey item=headerName from=$headers}
			<th>
				{assign var='cntcol' value=$cntcol+1}
				{self_link _sort_arg="sort_mode" _sort_field=$headerKey}{tr}{$headerName}{/tr}{/self_link}
			</th>
		{/foreach}

		{if $tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y'}
			<th>
				{assign var='cntcol' value=$cntcol+1}
				{self_link _sort_arg="sort_mode" _sort_field='approved'}{tr}Approval{/tr}{/self_link}
			</th>
		{/if}
		<th></th>
	</tr>
	
	{cycle values="even,odd" print=false}
	{section name=ix loop=$comments}{assign var=id value=$comments[ix].threadId}
		{capture name=over_actions}
			{strip}
				<div class='opaque'>
					<div class='box-title'>{tr}Actions{/tr}</div>
					<div class='box-data'>
						<a href="{$comments[ix].href}#threadId{$id}">{icon _id='magnifier' alt="{tr}Display{/tr}"}</a>
						<a href="{$comments[ix].href|cat:"&amp;comments_threadId=`$id`&amp;edit_reply=1#form"}">{icon _id='page_edit' alt="{tr}Edit{/tr}"}</a>
						{self_link remove=1 checked=$id _icon='cross'}{tr}Delete{/tr}{/self_link}
					</div>
				</div>
			{/strip}
		{/capture}

		{capture name=over_more_info}
			{strip}
				<div class='opaque'>
					<div class='box-title'>{tr}More info{/tr}</div>
					<div class='box-data'>
						<div>
							{foreach from=$more_info_headers key=headerKey item=headerName}
								{assign var=val value=$comments[ix].$headerKey}
								<b>{tr}{$headerName}{/tr}</b>: {$val}
								<br />
							{/foreach}
						</div>
					</div>
				</div>
			{/strip}
		{/capture}

		<tr{if $prefs.feature_comments_moderation eq 'y'} class="post-approved-{$comments[ix].approved}"{/if}>
			<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$id}"/></td>
			<td class="{cycle advance=false}">
				<a title="{tr}Actions{/tr}" href="#" {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" center=true text=$smarty.capture.over_actions|escape:"javascript"|escape:"html"} style="padding:0; margin:0; border:0">{icon _id='wrench' alt="{tr}Actions{/tr}"}</a>
			</td>

			{foreach key=headerKey item=headerName from=$headers}{assign var=val value=$comments[ix].$headerKey}
				<td class="{cycle advance=false}" {if $headerKey eq 'data'}{popup caption=$comments[ix].title|escape:"javascript"|escape:"html"	text=$comments[ix].parsed|escape:"javascript"|escape:"html"}{/if}>
					<span> {* span is used for some themes CSS opacity on some cells content *}
						{if $headerKey eq 'title'}
							<a href="{$comments[ix].href}#threadId{$id}" title="{$val}">{$val|truncate:50:"...":true|escape}</a>
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
				<td class="{cycle advance=false} approval">
					{if $comments[ix].approved eq 'n'}
						{self_link approve='y' checked=$id _icon='comment_approve'}{tr}Approve{/tr}{/self_link}
						{self_link approve='r' checked=$id _icon='comment_reject'}{tr}Reject{/tr}{/self_link}
					{elseif $comments[ix].approved eq 'y'}
						&nbsp;{tr}Approved{/tr}&nbsp;
					{elseif $comments[ix].approved eq 'r'}
						<span>&nbsp;{tr}Rejected{/tr}&nbsp;</span>
					{/if}
				</td>
			{/if}

			<td class="{cycle advance=false}">
				<a title="{tr}More info{/tr}" href="#" {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" center=true text=$smarty.capture.over_more_info|escape:"javascript"|escape:"html"} style="padding:0; margin:0; border:0">{icon _id='information' alt="{tr}More info{/tr}"}</a>
			</td>

			{cycle print=false}
		</tr>
	{sectionelse}
		<tr><td class="odd" colspan="{$cntcol}">{tr}No records found.{/tr}</td></tr>
	{/section}
</table>

{if $comments}
	<div class="formcolor">
		{tr}Perform action with checked:{/tr}
		{icon _id='cross' _tag='input_image' name='remove' value='y' alt="{tr}Delete{/tr}"}
		{if $tiki_p_admin_comments eq 'y' and $prefs.feature_comments_moderation eq 'y'}
			{icon _id='comment_approve' _tag='input_image' name='approve' value='y' alt="{tr}Approve{/tr}"}
			{icon _id='comment_reject' _tag='input_image' name='approve' value='r' alt="{tr}Reject{/tr}"}
		{/if}
	</div>
	</form>
{/if}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
