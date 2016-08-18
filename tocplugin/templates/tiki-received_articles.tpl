{* $Id$ *}

{title help="Articles"}{tr}Received articles{/tr}{/title}

{if $preview eq 'y'}
	<h2>{tr}Preview{/tr}</h2>
	<header class="articletitle">
		<h2>{$title}</h2>
		<span class="titleb">{tr}By:{/tr} {$authorName} {$publishDate|tiki_short_datetime:'On:'} (0 {tr}Reads{/tr})</span>
	</header>
	<div class="articleheading">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
					{if $useImage eq 'y'}
						<img alt="{tr}Article image{/tr}" src="received_article_image.php?id={$receivedArticleId}">
					{else}
						<img alt="{tr}Topic image{/tr}" src="article_image.php?image_type=topic&amp;id={$topic}">
					{/if}
				</td>
				<td valign="top">
					<span class="articleheading">{$parsed_heading}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="articletrailer">
		(xx {tr}bytes{/tr})
	</div>

	<div class="articlebody">
		{$parsed_body}
	</div>
{/if}


{if $receivedArticleId > 0}
	<h2>{tr}Edit received article{/tr}</h2>
	<form action="tiki-received_articles.php" method="post" class="form-horizontal"><br>
		<input type="hidden" name="receivedArticleId" value="{$receivedArticleId|escape}">
		<input type="hidden" name="created" value="{$created|escape}">
		<input type="hidden" name="image_name" value="{$image_name|escape}">
		<input type="hidden" name="image_size" value="{$image_size|escape}">
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Title:{/tr}</label>
			<div class="col-sm-7">
				<input type="text" name="title" value="{$title|escape}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Author Name:{/tr}</label>
			<div class="col-sm-7">
				<input type="text" name="authorName" value="{$authorName|escape}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Type{/tr}</label>
			<div class="col-sm-7">
				<select id='articletype' name='type' onchange='javascript:chgArtType();' class="form-control">
					{section name=t loop=$types}
						<option value="{$types[t].type|escape}" {if $type eq $types[t].type}selected="selected"{/if}>{$types[t].type}</option>
					{/section}
				</select>
				{if $tiki_p_admin_cms eq 'y'}
					<a href="tiki-article_types.php" class="link">{tr}Admin Types{/tr}</a>
				{/if}
			</div>
		</div>
		<div class="form-group" id='isreview' {if $type ne 'Review'}style="display:none;"{else}style="display:block;"{/if}>
			<label class="control-label col-sm-3">{tr}Rating{/tr}</label>
			<div class="col-sm-7">
				<select name='rating' class="form-control">
					<option value="10" {if $rating eq 10}selected="selected"{/if}>10</option>
					<option value="9.5" {if $rating eq "9.5"}selected="selected"{/if}>9.5</option>
					<option value="9" {if $rating eq 9}selected="selected"{/if}>9</option>
					<option value="8.5" {if $rating eq "8.5"}selected="selected"{/if}>8.5</option>
					<option value="8" {if $rating eq 8}selected="selected"{/if}>8</option>
					<option value="7.5" {if $rating eq "7.5"}selected="selected"{/if}>7.5</option>
					<option value="7" {if $rating eq 7}selected="selected"{/if}>7</option>
					<option value="6.5" {if $rating eq "6.5"}selected="selected"{/if}>6.5</option>
					<option value="6" {if $rating eq 6}selected="selected"{/if}>6</option>
					<option value="5.5" {if $rating eq "5.5"}selected="selected"{/if}>5.5</option>
					<option value="5" {if $rating eq 5}selected="selected"{/if}>5</option>
					<option value="4.5" {if $rating eq "4.5"}selected="selected"{/if}>4.5</option>
					<option value="4" {if $rating eq 4}selected="selected"{/if}>4</option>
					<option value="3.5" {if $rating eq "3.5"}selected="selected"{/if}>3.5</option>
					<option value="3" {if $rating eq 3}selected="selected"{/if}>3</option>
					<option value="2.5" {if $rating eq "2.5"}selected="selected"{/if}>2.5</option>
					<option value="2" {if $rating eq 2}selected="selected"{/if}>2</option>
					<option value="1.5" {if $rating eq "1.5"}selected="selected"{/if}>1.5</option>
					<option value="1" {if $rating eq 1}selected="selected"{/if}>1</option>
					<option value="0.5" {if $rating eq "0.5"}selected="selected"{/if}>0.5</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Use Image:{/tr}</label>
			<div class="col-sm-7">
				<select name="useImage" class="form-control">
					<option value="y" {if $useImage eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
					<option value="n" {if $useImage eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Image x size:{/tr}</label>
			<div class="col-sm-7">
				<input type="text" name="image_x" value="{$image_x|escape}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Image y size:{/tr}</label>
			<div class="col-sm-7">
				<input type="text" name="image_y" value="{$image_y|escape}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Image name:{/tr}</label>
			<div class="col-sm-7 form-static-input">
				{$image_name}
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Image size:{/tr}</label>
			<div class="col-sm-7 form-control-static">
				{$image_size}
			</div>
		</div>
		{if $useImage eq 'y'}
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Image:{/tr}</label>
			<div class="col-sm-7">
				<img alt="article image" width="{$image_x}" height="{$image_y}" src="received_article_image.php?id={$receivedArticleId}">
			</div>
		</div>
		{/if}
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Created:{/tr}</label>
			<div class="col-sm-7">
				{$created|tiki_short_datetime}
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Publishing date:{/tr}</label>
			<div class="col-sm-7">
				{html_select_date time=$publishDate end_year="+1" field_order=$prefs.display_field_order} at
				{html_select_time time=$publishDate display_seconds=false use_24_hours=$use_24hr_clock}
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Heading:{/tr}</label>
			<div class="col-sm-7">
				<textarea rows="5" cols="40" name="heading" class="form-control">{$heading|escape}</textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Body:{/tr}</label>
			<div class="col-sm-7">
				<textarea rows="25" cols="40" name="body" class="form-control">{$body|escape}</textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"></label>
			<div class="col-sm-7">
				<input type="submit" class="btn btn-default btn-sm" name="preview" value="{tr}Preview{/tr}">
				&nbsp;
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Accept Article{/tr}</label>
			<div class="col-sm-7">
				<select name="topic">
					{section name=t loop=$topics}
						<option value="{$topics[t].topicId|escape}" {if $topic eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name}</option>
					{/section}
				</select>
				<input type="submit" class="btn btn-default btn-sm" name="accept" value="{tr}Accept{/tr}">
			</div>
		</div>
	</form>
{/if}

<div align="center">
	{if $channels or $find ne ''}
		{include file='find.tpl'}
	{/if}
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tr>
				<th>
					<a href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedArticleId_desc'}receivedArticleId_asc{else}receivedArticleId_desc{/if}">{tr}ID{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a>
				</th>
				<th></th>
			</tr>

			{section name=user loop=$channels}
				<tr>
					<td class="id">{$channels[user].receivedArticleId}</td>
					<td class="text">{$channels[user].title|escape}
						{if $channels[user].type eq 'Review'}(r){/if}
					</td>
					<td class="date">{$channels[user].receivedDate|tiki_short_datetime}</td>
					<td class="text">{$channels[user].receivedFromSite}</td>
					<td class="text">{$channels[user].receivedFromUser|escape}</td>
					<td class="action">
						<a class="tips" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedArticleId={$channels[user].receivedArticleId}" title=":{tr}Edit{/tr}">
							{icon name='edit'}
						</a>
						&nbsp;
						<a class="tips" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedArticleId}" title=":{tr}Remove{/tr}">
							{icon name='remove'}
						</a>
					</td>
				</tr>
			{sectionelse}
				{norecords _colspan=6}
			{/section}
		</table>
	</div>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>
