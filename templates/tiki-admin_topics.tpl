{* $Id$ *}
{title admpage="articles" help="Articles"}{tr}Article Topics{/tr}{/title}
<div class="t_navbar margin-bottom-md">
	{if $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-list_articles.php" class="btn btn-default btn-sm" _text="{tr}List Articles{/tr}"}
		{button href="tiki-article_types.php" class="btn btn-default btn-sm" _text="{tr}Article Types{/tr}"}
	{/if}
</div>
<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post" class="form-horizontal" role="form">
	<h2>{tr}Add topic{/tr}</h2>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="name">{tr}Name{/tr}</label>
		<div class="col-sm-10">
			<input type="text" name="name" id="name" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="image">{tr}Image{/tr}</label>
		<div class="col-sm-10">
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
			<input name="userfile1" type="file">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="notificationemail">{tr}Notification Email{/tr}</label>
		<div class="col-sm-10">
			<div class="well well-sm">
				{tr}You will be able to add a notification email per article topic when you edit the topic after its creation{/tr}
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
			<input type="submit" class="btn btn-primary btn-sm" name="addtopic" value="{tr}Add{/tr}">
		</div>
	</div>
</form>
<h2>{tr}Topics{/tr}</h2>
<div class="table-responsive">
	<table class="table normal">
		<tr>
			<th>{tr}ID{/tr}</th>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Image{/tr}</th>
			<th>{tr}Active{/tr}</th>
			<th>{tr}Articles{/tr}</th>
			{if $prefs.feature_submissions eq 'y'}<th>{tr}Submissions{/tr}</th>{/if}
			<th>{tr}Actions{/tr}</th>
		</tr>
		{section name=user loop=$topics}
			<tr>
				<td class="integer">{$topics[user].topicId}</td>
				<td class="text">
					<a class="link" href="tiki-view_articles.php?topic={$topics[user].topicId}">{$topics[user].name|escape}</a>
				</td>
				<td class="text">
					{if $topics[user].image_size}
						<img alt="{tr}topic image{/tr}" src="article_image.php?image_type=topic&amp;id={$topics[user].topicId}&amp;reload=1">
					{else}
						&nbsp;
					{/if}
				</td>
				<td class="text">{$topics[user].active}</td>
				<td class="integer">{$topics[user].arts}</td>
				{if $prefs.feature_submissions eq 'y'}<td>{$topics[user].subs}</td>{/if}
				<td class="action">
					{icon name="edit" href="tiki-edit_topic.php?topicid={$topics[user].topicId}" title="{tr}Edit{/tr}"}
					{permission_link mode=icon type=topic permType=articles id=$topics[user].topicId title=$topics[user].name}
					{if $topics[user].active eq 'n'}
						{icon name="toggle-off" href="tiki-admin_topics.php?activate={$topics[user].topicId}" title="{tr}Inactive - Click to Activate{/tr}"}
					{else}
						{icon name="toggle-on" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}" title="{tr}Active - Click to Deactivate{/tr}"}
					{/if}
					{icon name="delete" href="tiki-admin_topics.php?remove={$topics[user].topicId}" class="btn btn-warning btn-sm" title="{tr}Delete{/tr}"}
					{icon name="delete" href="tiki-admin_topics.php?removeall={$topics[user].topicId}" class="btn btn-danger btn-sm" title="{tr}Delete with articles{/tr}"}
				</td>
			</tr>
		{sectionelse}
			{if $prefs.feature_submissions eq 'y'}{norecords _colspan=7}{else}{norecords _colspan=6}{/if}
		{/section}
	</table>
</div>
