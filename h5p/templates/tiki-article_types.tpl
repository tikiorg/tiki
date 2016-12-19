{* $Id$ *}
{title admpage="articles" url="tiki-article_types.php" help=Articles}{tr}Article Types{/tr}{/title}
<div class="t_navbar margin-bottom-md">
	{if $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-list_articles.php" _type="link" _icon_name="list" _text="{tr}List Articles{/tr}"}
		{button href="tiki-admin_topics.php" _type="link" _icon_name="flag" _text="{tr}Article Topics{/tr}"}
	{/if}
</div>
<form enctype="multipart/form-data" action="tiki-article_types.php" method="post" role="form" class="form">
	<h2>{tr}Add Type{/tr}</h2>
		<div class="form-group">
			<div class="input-group">
				<input type="text" name="new_type" class="form-control" placeholder="{tr}Add article type{/tr}...">
				<div class="input-group-btn">
					<button type="submit" class="btn btn-primary" name="add_type">{tr}Add{/tr}</button>
				</div>
			</div>
		</div>
	<h2>{tr}Types{/tr}</h2>
	{section name=user loop=$types}
		<h3>{tr}{$types[user].type|escape}{/tr}</h3>
		<a class="link" href="tiki-view_articles.php?type={$types[user].type|escape:url}">{tr}View articles with this type{/tr}</a>
			<div class="table-responsive article-types">
				<table class="table table-striped table-hover">
				<tr>
					<th>{tr}Articles{/tr}</th>
					<th>{tr}Author rating{/tr}</th>
					<th>{tr}Show before publish date{/tr}</th>
					<th>{tr}Show after expire date{/tr}</th>
					<th>{tr}Heading only{/tr}</th>
					<th>{tr}Comments{/tr}</th>
					<th>{tr}Comment can rate article{/tr}</th>
					<th>{tr}Show image{/tr}</th>
					<th>{tr}Show profile picture{/tr}</th>
					<th>{tr}Show author{/tr}</th>
					<th>{tr}Show publish date{/tr}</th>
				</tr>
				<input type="hidden" name="type_array[{$types[user].type|escape}]">
				<tr>
					<td class="integer">{$types[user].article_cnt}</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="use_ratings[{$types[user].type|escape}]" {if $types[user].use_ratings eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_pre_publ[{$types[user].type|escape}]" {if $types[user].show_pre_publ eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_post_expire[{$types[user].type|escape}]" {if $types[user].show_post_expire eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="heading_only[{$types[user].type|escape}]" {if $types[user].heading_only eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="allow_comments[{$types[user].type|escape}]" {if $types[user].allow_comments eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="comment_can_rate_article[{$types[user].type|escape}]" {if $types[user].comment_can_rate_article eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_image[{$types[user].type|escape}]" {if $types[user].show_image eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_avatar[{$types[user].type|escape}]" {if $types[user].show_avatar eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_author[{$types[user].type|escape}]" {if $types[user].show_author eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_pubdate[{$types[user].type|escape}]" {if $types[user].show_pubdate eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<th>{tr}Show expire date{/tr}</th>
					<th>{tr}Show reads{/tr}</th>
					<th>{tr}Show size{/tr}</th>
					<th>{tr}Show topline{/tr}</th>
					<th>{tr}Show subtitle{/tr}</th>
					<th>{tr}Show source{/tr}</th>
					<th>{tr}Show image caption{/tr}</th>
					<th>{tr}Creator can edit{/tr}</th>
					<th colspan="2"></th>
				</tr>
				<tr>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_expdate[{$types[user].type|escape}]" {if $types[user].show_expdate eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_reads[{$types[user].type|escape}]" {if $types[user].show_reads eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_size[{$types[user].type|escape}]" {if $types[user].show_size eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_topline[{$types[user].type|escape}]" {if $types[user].show_topline eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_subtitle[{$types[user].type|escape}]" {if $types[user].show_subtitle eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_linkto[{$types[user].type|escape}]" {if $types[user].show_linkto eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="show_image_caption[{$types[user].type|escape}]" {if $types[user].show_image_caption eq 'y'}checked="checked"{/if}>
					</td>
					<td class="checkbox-cell">
						<input type="checkbox" name="creator_edit[{$types[user].type|escape}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if}>
					</td>
					<td class="action" colspan="2">
						{if $types[user].article_cnt eq 0}
							<a class="tips" title=":{tr}Remove{/tr}" href="tiki-article_types.php?remove_type={$types[user].type|escape:url}">
								{icon name='remove'}
							</a>
						{else}
							&nbsp;
						{/if}
					</td>
				</tr>
			</table>
		</div>
		{if $prefs.article_custom_attributes eq 'y'}
			<div class="table-responsive article-types">
				<table class="table table-striped table-hover">
					<tr>
						<th>{tr}Custom attribute{/tr}</th>
						<th></th>
					</tr>
					{foreach from=$types[user].attributes item=att key=attname}
						<tr>
							<td>{$attname|escape}</td>
							<td class="action">
								<a class="tips" title=":{tr}Remove{/tr}" href="tiki-article_types.php?att_type={$types[user].type|escape:url}&att_remove={$att.relationId|escape:url}">
									{icon name='remove' alt="{tr}Remove{/tr}"}
								</a>
							</td>
						</tr>
					{/foreach}
					<tr>
						<td><input type="text" name="new_attribute[{$types[user].type|escape}]" value="" maxlength="56"></td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		{/if}
		<input type="submit" class="btn btn-primary btn-sm" name="update_type" value="{tr}Save{/tr}"><br>
		<hr>
		<br>
	{/section}
</form>
