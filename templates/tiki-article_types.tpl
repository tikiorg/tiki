{title url="tiki-article_types.php" help=Articles}{tr}Admin Article Types{/tr}{/title}

<div class="navbar">
	{button _onclick="javascript:toggle('wiki-edithelp');return false;" _text="{tr}Show Help{/tr}" _ajax="n"}
</div>


<div class="wiki-edithelp" id="wiki-edithelp" style="display:none;">
	<b>{tr}Article type{/tr}</b> - {tr}Shows up in the drop down list of article types{/tr}<br />
	<b>{tr}Author Rating{/tr}</b> - {tr}Allow ratings by the author{/tr}<br />
	<b>{tr}Show before publish date{/tr}</b> - {tr}non-admins can view before the publish date{/tr}<br />
	<b>{tr}Show after expire date{/tr}</b> - {tr}non-admins can view after the expire date{/tr}<br />
	<b>{tr}Heading only{/tr}</b> - {tr}No article body, heading only{/tr}<br />
	<b>{tr}Comments{/tr}</b> - {tr}Allow comments for this type{/tr}<br />
	<b>{tr}Comment Can Rate Article{/tr}</b> - {tr}Allow comments to include a rating value{/tr}<br />
	<b>{tr}Show image{/tr}</b> - {tr}Show topic or own image{/tr}<br />
	<b>{tr}Show avatar{/tr}</b> - {tr}Show author's avatar{/tr}<br />
	<b>{tr}Show author{/tr}</b> - {tr}Show author name{/tr}<br />
	<b>{tr}Show publish date{/tr}</b> - {tr}Show publish date{/tr}<br />
	<b>{tr}Show expire date{/tr}</b> - {tr}Show expire date{/tr}<br />
	<b>{tr}Show reads{/tr}</b> - {tr}Show the number of times the article was read{/tr}<br />
	<b>{tr}Show size{/tr}</b> - {tr}Show the size of the article{/tr}<br />
	<b>{tr}Show topline{/tr}</b> - {tr}Show a small title over the title{/tr}<br />
	<b>{tr}Show subtitle{/tr}</b> - {tr}Show the subtitle{/tr}<br />
	<b>{tr}Show source{/tr}</b> - {tr}Show link to source after article body{/tr}<br />
	<b>{tr}Show Image Caption{/tr}</b> - {tr}Show a legend under the image{/tr}<br />
	<b>{tr}Show Language{/tr}</b> - {tr}Show the language{/tr}<br />
	<b>{tr}Creator can edit{/tr}</b> - {tr}The person who submits an article of this type can edit it{/tr}<br />
	<b>{tr}Action{/tr}</b> - {tr}Actions on this article type{/tr}<br />
</div>

{tabset name='tabs_articletypes'}
	{tab name="{tr}List of article types{/tr}"}
	<form enctype="multipart/form-data" action="tiki-article_types.php" method="post">
		{section name=user loop=$types}
			<h3>{tr}{$types[user].type|escape}{/tr}</h3>
			<a class="link" href="tiki-view_articles.php?type={$types[user].type|escape:url}">{tr}view articles with this type{/tr}</a>
			<table class="normal">
				<tr>
					<th>{tr}Articles{/tr}</th>
					<th>{tr}Author Rating{/tr}</th>
					<th>{tr}Show before publish date{/tr}</th>
					<th>{tr}Show after expire date{/tr}</th>
					<th>{tr}Heading only{/tr}</th>
					<th>{tr}Comments{/tr}</th>
					<th>{tr}Comment Can Rate Article{/tr}</th>
					<th>{tr}Show image{/tr}</th>
					<th>{tr}Show avatar{/tr}</th>
					<th>{tr}Show author{/tr}</th>
					<th>{tr}Show publish date{/tr}</th>
				</tr>
				{cycle print=false values="even,odd"}
				<input type="hidden" name="type_array[{$types[user].type|escape}]" />
				<tr class="{cycle}">
					{*get_strings {tr}Article{/tr}{tr}Review{/tr}{tr}Event{/tr}{tr}Classified{/tr} *}
					<td class="integer">{$types[user].article_cnt}</td>
					<td class="checkbox">
						<input type="checkbox" name="use_ratings[{$types[user].type|escape}]" {if $types[user].use_ratings eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_pre_publ[{$types[user].type|escape}]" {if $types[user].show_pre_publ eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_post_expire[{$types[user].type|escape}]" {if $types[user].show_post_expire eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="heading_only[{$types[user].type|escape}]" {if $types[user].heading_only eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="allow_comments[{$types[user].type|escape}]" {if $types[user].allow_comments eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="comment_can_rate_article[{$types[user].type|escape}]" {if $types[user].comment_can_rate_article eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_image[{$types[user].type|escape}]" {if $types[user].show_image eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_avatar[{$types[user].type|escape}]" {if $types[user].show_avatar eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_author[{$types[user].type|escape}]" {if $types[user].show_author eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_pubdate[{$types[user].type|escape}]" {if $types[user].show_pubdate eq 'y'}checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<th>{tr}Show expire date{/tr}</th>
					<th>{tr}Show reads{/tr}</th>
					<th>{tr}Show size{/tr}</th>
					<th>{tr}Show topline{/tr}</th>
					<th>{tr}Show subtitle{/tr}</th>
					<th>{tr}Show source{/tr}</th>
					<th>{tr}Show Image Caption{/tr}</th>
					<th>{tr}Show lang{/tr}</th>
					<th>{tr}Creator can edit{/tr}</th>
					<th colspan="2">{tr}Action{/tr}</th>
				</tr>
				<tr class="{cycle}">
					<td class="checkbox">
						<input type="checkbox" name="show_expdate[{$types[user].type|escape}]" {if $types[user].show_expdate eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_reads[{$types[user].type|escape}]" {if $types[user].show_reads eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_size[{$types[user].type|escape}]" {if $types[user].show_size eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_topline[{$types[user].type|escape}]" {if $types[user].show_topline eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_subtitle[{$types[user].type|escape}]" {if $types[user].show_subtitle eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_linkto[{$types[user].type|escape}]" {if $types[user].show_linkto eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_image_caption[{$types[user].type|escape}]" {if $types[user].show_image_caption eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="show_lang[{$types[user].type|escape}]" {if $types[user].show_lang eq 'y'}checked="checked"{/if} />
					</td>
					<td class="checkbox">
						<input type="checkbox" name="creator_edit[{$types[user].type|escape}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if} />
					</td>
					<td class="action" colspan="2">
						<center>
							{if $types[user].article_cnt eq 0}
								<a class="link" href="tiki-article_types.php?remove_type={$types[user].type|escape:url}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
							{else}
								&nbsp;
							{/if}
						</center>
					</td>
			</tr>
		</table>
		{if $prefs.article_custom_attributes eq 'y'}
			<table class="normal">
				<tr>
					<th>{tr}Custom attribute{/tr}</th>
					<th>{tr}Action{/tr}</th>
				</tr>
				{cycle print=false values="even,odd"}
				{foreach from=$types[user].attributes item=att key=attname}
					<tr class="{cycle}">
						<td>{$attname|escape}</td>
						<td class="action">
							<a class="link" href="tiki-article_types.php?att_type={$types[user].type|escape:url}&att_remove={$att.relationId|escape:url}">
								{icon _id='cross' alt="{tr}Remove{/tr}"}
							</a>
						</td>
					</tr>
				{/foreach}
				<tr>
					<td><input type="text" name="new_attribute[{$types[user].type|escape}]" value="" maxlength="56" /></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		{/if}
		<input type="submit" name="update_type" value="{tr}Save{/tr}" /><br />
		<hr />
		<br />
		{/section}
	{/tab}
	{tab name="{tr}Create a new type{/tr}"}
		<h3>{tr}Create a new type{/tr}</h3>
		<input type="text" name="new_type" /><input type="submit" name="add_type" value="{tr}Create a new type{/tr}" />
	{/tab}
	</form>
{/tabset}
