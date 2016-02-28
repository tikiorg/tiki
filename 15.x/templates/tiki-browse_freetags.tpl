{* $Id$ *}

{title admpage=freetags}{tr}Browse Tags{/tr}{/title}

{jq}
	$('#tagBox').tiki('autocomplete', 'tag', {multiple: true, multipleSeparator: " "} );
{/jq}
<form action="tiki-browse_freetags.php" method="get" class="freetagsearch form-horizontal" role="form">
	<div class="form-group">
		<div class="col-sm-10">
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon1">{icon name="tags"} {tr}Tags{/tr}</span>
				<input type="text" id="tagBox" class="form-control" name="tag" value="{$tagString|escape}">
				<div class="input-group-btn">
					<input type="submit" class="btn btn-default tips" value="{tr}Go{/tr}">
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			{button _class="btn-link tips" _onclick="clearTags(); return false;" _text="{tr}Clear{/tr}" _title=":{tr}Clear tags{/tr}"}
		</div>
	</div>
	<div class="form-inline margin-bottom-md">
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
		<input type="radio" name="broaden" class="radio" id="stopb1" value="n"{if $broaden eq 'n'} checked="checked"{/if}>
		<label for="stopb1">{tr}With all selected tags{/tr}</label>
		<input type="radio" name="broaden" class="radio" id="stopb2" value="y"{if $broaden eq 'y'} checked="checked"{/if}>
		<label for="stopb2">{tr}With one selected tag{/tr}</label>
		<input type="radio" name="broaden" class="radio" id="stopb3" value="last"{if $broaden eq 'last'} checked="checked"{/if}>
		<label for="stopb3">{tr}With last selected tag{/tr}</label>
	</div>

	{if $prefs.freetags_browse_show_cloud eq 'y'}
		{jq notonready=true}
				function addTag(tag) {
					if (tag.search(/ /) >= 0) tag = '"'+tag+'"';
					document.getElementById('tagBox').value = document.getElementById('tagBox').value + ' ' + tag;
				}
				function clearTags() {
					document.getElementById('tagBox').value = '';
				}
		{/jq}
		<div class="panel panel-default">
			<div class="panel-body freetaglist margin-bottom-md">
				{foreach from=$most_popular_tags item=popular_tag}
					{capture name=tagurl}{if (strstr($popular_tag.tag, ' '))}"{$popular_tag.tag}"{else}{$popular_tag.tag}{/if}{/capture}
					<a class="freetag_{$popular_tag.size}{if $tag eq $popular_tag.tag|escape} selectedtag{/if}" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}" onclick="javascript:addTag('{$popular_tag.tag|escape:'javascript'}');return false;" ondblclick="location.href=this.href;"{if $popular_tag.color} style="color:{$popular_tag.color}"{/if}>{$popular_tag.tag|escape}</a>
				{/foreach}
			</div>
			<div class="freetagsort panel-footer">
				<div class="text-center">
					{if empty($maxPopular)}
						{assign var=maxPopular value=50+$prefs.freetags_browse_amount_tags_in_cloud}
					{/if}
					<a class='more' href="{$smarty.server.PHP_SELF}?{query maxPopular=$maxPopular tagString=$tagString}">{tr}More Popular Tags{/tr}</a>
				</div>
				<div class="text-center">
					<a href="{$smarty.server.PHP_SELF}?{query tsort_mode=tag_asc}">{tr}Alphabetically{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query tsort_mode=count_desc tagString=$tagString}">{tr}By Size{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query mode=c tagString=$tagString}">{tr}Cloud{/tr}</a> | <a href="{$smarty.server.PHP_SELF}?{query mode=l tagString=$tagString}">{tr}List{/tr}</a>
				</div>
			</div>
		</div>
	{/if}
	{assign var=cpt value=0}
	{capture name="browse"}
		{if $type eq $objectType}
			{assign var=thisclass value='highlight'}
		{else}
			{assign var=thisclass value=''}
		{/if}
		{if $broaden eq ''}
			{assign var=thisbroaden value="&amp;broaden=$broaden"}
		{else}
			{assign var=thisbroaden value=''}
		{/if}
		<div class="btn-group margin-bottom-md">
			{button _text="{tr}All{/tr}" _class=$thisclass href="tiki-browse_freetags.php?tag=$tagString$thisbroaden&amp;type="}
			{foreach item=objectType from=$objects_with_freetags}
				{foreach item=sect key=key from=$sections_enabled}
					{if isset($sect.objectType) and $sect.objectType eq $objectType and $objectType neq 'blog post'}
						{assign var=feature_label value=$objectType|ucwords}
						{if $type eq $objectType}
							{assign var=thisclass value='highlight'}
						{else}
							{assign var=thisclass value=''}
						{/if}
						{if $broaden eq ''}
							{assign var=thisbroaden value="&amp;broaden=$broaden"}
						{else}
							{assign var=thisbroaden value=''}
						{/if}
						{assign var=thistype value=$objectType|escape:'url'}
						{capture name="fl"}{tr}{$feature_label}{/tr}{/capture}
						{button _text=$smarty.capture.fl _class=$thisclass href="tiki-browse_freetags.php?tag=$tagString$thisbroaden&amp;type=$thistype&amp;sort_mode=$sort_mode"}
						{assign var=cpt value=$cpt+1}
					{/if}
					{if isset($sect.itemObjectType) and $sect.itemObjectType eq $objectType}
						{if $objectType eq 'tracker %d'}
							{assign var=feature_label value='Tracker Item'}
							{assign var=objectType value='trackerItem'}
						{else}
							{assign var=feature_label value=$objectType|ucwords}
						{/if}
						{if $type eq $objectType}
							{assign var=thisclass value='highlight'}
						{else}
							{assign var=thisclass value=''}
						{/if}
						{if $broaden eq ''}
							{assign var=thisbroaden value="&amp;broaden=$broaden"}
						{else}
							{assign var=thisbroaden value=''}
						{/if}
						{assign var=thistype value=$objectType|escape:'url'}
						{capture name="fl"}{tr}{$feature_label}{/tr}{/capture}
						{button _text=$smarty.capture.fl _class=$thisclass href="tiki-browse_freetags.php?tag=$tagString$thisbroaden&amp;type=$thistype&amp;sort_mode=$sort_mode"}
						{assign var=cpt value=$cpt+1}
					{/if}
				{/foreach}
			{/foreach}
		</div>
		<div class="form-inline margin-bottom-md">
			<div class="input-group col-sm-6">
				<input type="text" name="find" value="{$find|escape}" class="form-control input-sm" placeholder="{tr}Find{/tr}...">
				<div class="input-group-btn">
					<input type="submit" class="btn btn-default btn-sm" value="{tr}Filter{/tr}">
				</div>
			</div>
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
			<input type="hidden" name="old_type" value="{$type|escape}">
			{if !empty($blogs)}
				<div class="form-group">
					<div id="blogs"{if $type ne 'blog post'} style="visibility:hidden"{/if}>
						<select name="objectId" onchange="this.form.submit();" class="form-control">
							<option value="">--{tr}All blogs{/tr}--</option>
							{foreach item=blog from=$blogs}
								<option value="{$blog.blogId|escape}"{if $blog.blogId eq $objectId} selected="selected"{/if}>{$blog.title|escape}</option>
							{/foreach}
						</select>
					</div>
				</div>
			{/if}
		</div>
	{/capture}
	{if $cpt > 1}
		<div class="freetagsbrowse">{$smarty.capture.browse}</div>
	{/if}
</form>
<div class="freetagresult">
	{if $tagString}
		<h4>{tr}Results{/tr} <span class="badge">{$cantobjects}</span></h4>
	{/if}
	{if $cantobjects > 0}
		<table class="table table-hover">
			<tbody>
				{section name=ix loop=$objects}
					<tr class="{cycle} freetagitemlist" >
						<td>
							<span class="label label-info">
								{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}
								{if !empty($objects[ix].parent_object_id)} {tr}in{/tr} {object_link type=$objects[ix].parent_object_type id=$objects[ix].parent_object_id}{/if}
							</span>
						</td>
						<td>
							<a href="{$objects[ix].href}">
								{$objects[ix].name|strip_tags|escape}
							</a>
							<span class="help-block">
								{$objects[ix].description|strip_tags|escape}
							</span>
						</td>
						{if $tiki_p_unassign_freetags eq 'y' or $tiki_p_admin eq 'y'}
							<td>
								<a href="tiki-browse_freetags.php?del=1&amp;tag={$tag}{if $type}&amp;type={$type|escape:'url'}{/if}&amp;typeit={$objects[ix].type|escape:'url'}&amp;itemit={$objects[ix].name|escape:'url'}" title=":{tr}Delete Tag{/tr}" class="tips">
									{icon name="delete"}
								</a>
							</td>
						{/if}
					</tr>
				{/section}
			</tbody>
		</table>
		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/if}
</div>
