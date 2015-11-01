{* $Id$ *}
{title admpage="articles" help="Articles"}{tr}Article Topics{/tr}{/title}
<div class="t_navbar margin-bottom-md">
	{if $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-list_articles.php" _type="link" _icon_name="list" _text="{tr}List Articles{/tr}"}
		{button href="tiki-article_types.php" _type="link" _icon_name="structure" _text="{tr}Article Types{/tr}"}
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
			<input class="form-control" name="userfile1" type="file">
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
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table table-striped table-hover">
		<tr>
			<th>{tr}ID{/tr}</th>
			<th>{tr}Name{/tr}</th>
			<th>{tr}Image{/tr}</th>
			<th>{tr}Active{/tr}</th>
			<th>{tr}Articles{/tr}</th>
			{if $prefs.feature_submissions eq 'y'}<th>{tr}Submissions{/tr}</th>{/if}
			<th></th>
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
				<td class="text">{if $topics[user].active eq 'y'}{icon name="toggle-on"}{else}{icon name="toggle-off"}{/if}</td>
				<td><span class="badge">{$topics[user].arts}</span></td>
				{if $prefs.feature_submissions eq 'y'}<td><span class="badge">{$topics[user].subs}</span></td>{/if}
				<td class="action">
					{capture name=topic_actions}
						{strip}
							{$libeg}{permission_link mode=text type=topic permType=articles id=$topics[user].topicId title=$topics[user].name}{$liend}
							{if $topics[user].active eq 'n'}
								{$libeg}<a href="tiki-admin_topics.php?activate={$topics[user].topicId}">
									{icon name="toggle-on" _menu_text='y' _menu_icon='y' alt="{tr}Activate{/tr}"}
								</a>{$liend}
							{else}
								{$libeg}<a href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">
									{icon name="toggle-off" _menu_text='y' _menu_icon='y' alt="{tr}De-activate{/tr}"}
								</a>{$liend}
							{/if}
							{$libeg}<a href="tiki-edit_topic.php?topicid={$topics[user].topicId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-admin_topics.php?remove={$topics[user].topicId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-admin_topics.php?removeall={$topics[user].topicId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove with articles{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.topic_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.topic_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{if $prefs.feature_submissions eq 'y'}{norecords _colspan=7}{else}{norecords _colspan=6}{/if}
		{/section}
	</table>
</div>
