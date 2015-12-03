{if "$userwatch" eq "$user" }
	{include file='tiki-mytiki_bar.tpl'}
{/if}
{if $infoPublic eq 'y' || true}
	<div class="clearfix">
		<div class="pull-right">
			{if $tiki_p_admin eq 'y' or $userinfo.object_id eq $user}
				{if $tiki_p_admin eq 'y'}
					<a class="link tips" href="tiki-assignuser.php?assign_user={$userwatch|escape:"url"}" title=":{tr}Assign group{/tr}">
						{icon name='group' align="right" alt="{tr}Assign Group{/tr}"}
					</a>
				{/if}
				<a class="link tips" href="tiki-user_preferences.php?user={$userwatch|escape:url}" title=":{tr}Change user preferences{/tr}">
					{icon name='wrench' align="right" alt="{tr}Change user preferences{/tr}"}
				</a>
			{/if}
		</div>
	</div>
	<div class="row">
		<div class="col-md-9">
			<div class="row">
				<div class="col-md-4 profile-left-bar">
					<div class="profile-avatar text-center">
						{if $user eq $userwatch}<a href="tiki-pick_avatar.php">{/if}
						{if false and isset($user_picture_id)}
							{wikiplugin _name="img" fileId="$user_picture_id"}{/wikiplugin}
						{else}
							{$userwatch|avatarize:"":"":false}
						{/if}
						{if $user eq $userwatch}</a>{/if}
						<p class="profile-username">{$userwatch|username}</p>
					</div>
					{if $prefs['feature_display_my_to_others'] eq 'y'}
						<ul class="list-group profile-navigation">
							<a id="tab_show_user" data-target="#tab_user" href="#" class="tab_option list-group-item active">{tr}User Information{/tr}</a>
							{if $prefs['feature_wiki'] eq 'y'}<a id="tab_show_wikis" data-target="#tab_wikis" href="#" class="tab_option list-group-item">{tr}Wikis{/tr}</a>{/if}
							{if $prefs['feature_blogs'] eq 'y'}<a id="tab_show_blogs" data-target="#tab_blogs" href="#" class="tab_option list-group-item">{tr}Blogs{/tr}</a>{/if}
							{if $prefs['feature_galleries'] eq 'y'}<a id="tab_show_galleries" data-target="#tab_galleries" href="#" class="tab_option list-group-item">{tr}Galleries{/tr}</a>{/if}
							{if $prefs['feature_trackers'] eq 'y'}<a id="tab_show_trackers" data-target="#tab_trackers" href="#" class="tab_option list-group-item">{tr}Trackers{/tr}</a>{/if}
							{if $prefs['feature_articles'] eq 'y'}<a id="tab_show_articles" data-target="#tab_articles" href="#" class="tab_option list-group-item">{tr}Articles{/tr}</a>{/if}
							{if $prefs['feature_forums'] eq 'y'}<a id="tab_show_forums" data-target="#tab_forums" href="#" class="tab_option list-group-item">{tr}Forum Threads{/tr}</a>{/if}
						</ul>
					{/if}
				</div>
				{* Content *}
				<div class="col-md-8">
					{* User Information Tab *}
					<div id="tab_user" class="profile-tab-content">
						<h2>User Information</h2>
						<table class="table table-borderless profile-info-table">
						{if $userinfo['user_realName'] && $prefs['user_show_realnames'] == 'n'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Real Name{/tr}</td>
								<td class="profile-info-value">{$userinfo['user_realName']|escape}</td>
							</tr>
						{/if}
						{if $userinfo['user_realName'] && $prefs['user_show_realnames'] == 'y'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Username:{/tr}</td>
								<td class="profile-info-value">{$userwatch|escape}</td>
							</tr>
						{/if}
						{if not empty($userinfo['user_country']) and $userinfo['user_country'] != 'Other'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Country:{/tr}</td>
								<td class="profile-info-value">{$userwatch|countryflag} {tr}{$userinfo['user_country']|stringfix}{/tr}</td>
							</tr>
						{/if}
						{if $prefs.feature_community_gender eq 'y' and $gender neq 'Hidden' and $gender}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Gender:{/tr}</td>
								<td class="profile-info-value">{tr}{$userinfo['user_gender']}{/tr}</td>
							</tr>
						{/if}
						{if $email_isPublic neq 'n' and $userinfo.email neq ''}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Email:{/tr}</td>
								<td class="profile-info-value">{mailto address=$userinfo.email text=$scrambledEmail encode="javascript"}</td>
							</tr>
						{elseif $email_isPublic eq 'n' and $userinfo.email neq '' and $tiki_p_admin eq 'y'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Email:{/tr}</td>
								<td class="profile-info-value">{mailto address=$userinfo.email encode="javascript"} <i>{tr}(non public){/tr}</i></td>
							</tr>
						{/if}
						{if $prefs.change_theme ne 'n'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Theme:{/tr}</td>
								<td class="profile-info-value">{$userinfo['user_style']}</td>
							</tr>
						{/if}
						{if $prefs.change_language eq 'y'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Language:{/tr}</td>
								<td class="profile-info-value">{', '|implode:$userinfo['user_language']}</td>

							</tr>
						{/if}
						{if $userinfo['user_homepage']}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Homepage:{/tr}</td>
								<td class="profile-info-value">
									<a href="{$userinfo['user_homepage']|escape}" class="link" title="{tr}User's homepage{/tr}">
										{$userinfo['user_homepage']|escape}
									</a>
								</td>
							</tr>
						{/if}
						{if $prefs.feature_score eq 'y'}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Score:{/tr}</td>
								<td class="profile-info-value">{$userinfo.score|star}{$userinfo.score}</td>
							</tr>
						{/if}
						{if $prefs.feature_wiki eq 'y' && $prefs.feature_wiki_userpage eq 'y' && (not empty($userinfo['user_page']) or $user == $userinfo.object_id)}
							<tr class="profile-info">
								<td class="profile-info-label">{tr}Personal Wiki Page:{/tr}</td>
								<td class="profile-info-value">
									{if not empty($userinfo['user_page'])}
										<a class="link" href="tiki-index.php?page={$prefs.feature_wiki_userpage_prefix|escape:'url'}{$userinfo.object_id|escape:'url'}">
											{$prefs.feature_wiki_userpage_prefix}{$userinfo.object_id}
										</a>
									{elseif $user == $userinfo.object_id}
										{$prefs.feature_wiki_userpage_prefix}{$userinfo.object_id}
										<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix|escape:'url'}{$userinfo.object_id|escape:'url'}"
										   title="{tr}Create Page{/tr}">?</a>
									{else}&nbsp;{/if}
								</td>
							</tr>
						{/if}
						<tr class="profile-info">
							<td class="profile-info-label">{tr}Last login:{/tr}</td>
							<td class="profile-info-value">{$userinfo.lastLogin|tiki_short_datetime}</td>
						</tr>

						{foreach $template_fields as $field}
							<tr class="profile-info">
								<td class="profile-info-label">{$field.label}:</td>
								<td class="profile-info-value">{$userinfo["tracker_field_`$field.permName`"]}</td>
							</tr>
						{/foreach}
					</table>
					</div>

					{* Wikis Tab *}
					<div id="tab_wikis" class="profile-tab-content collapse">
						<h2>{tr}Wiki Pages{/tr}</h2>
						<p>{tr}This user has contributed to the following wiki pages{/tr}:</p>
						<div class="panel panel-default">
							<div class="panel-body">
								{wikiplugin _name="list"}
								{literal}
									{filter type="wiki page"}
									{filter exact="{/literal}{$userwatch}{literal}" field="contributors"}
									{ALTERNATE()}{tr}This user has not contributed to any wiki pages yet{/tr}.{ALTERNATE}
								{/literal}
								{/wikiplugin}
							</div>
						</div>
					</div>

					{* Blogs Tab *}
					<div id="tab_blogs" class="profile-tab-content collapse">
						<h2>{tr}Wiki Pages{/tr}</h2>
						<p>{tr}This user has contributed to the following wiki pages{/tr}:</p>
						<div class="panel panel-default">
							<div class="panel-body">
								{wikiplugin _name="list"}
								{literal}
									{filter type="blog post"}
									{filter exact="{/literal}{$userwatch}{literal}" field="contributors"}
									{ALTERNATE()}{tr}This user has not contributed any blog posts yet{/tr}.{ALTERNATE}
								{/literal}
								{/wikiplugin}
							</div>
						</div>
					</div>

					{* Galleries Tab *}
					<div id="tab_galleries" class="profile-tab-content collapse">
						<h2>{tr}Wiki Pages{/tr}</h2>
						<p>{tr}This user has contributed to the following wiki pages{/tr}:</p>
						<div class="panel panel-default">
							<div class="panel-body">
								{wikiplugin _name="list"}
								{literal}
									{filter type="file"}
									{filter exact="{/literal}{$userwatch}{literal}" field="contributors"}
									{ALTERNATE()}{tr}This user has not contributed any blog posts yet{/tr}.{ALTERNATE}
								{/literal}
								{/wikiplugin}
							</div>
						</div>
					</div>

					{* Trackers Tab *}
					<div id="tab_trackers" class="profile-tab-content collapse">
						<h2>Tracker Items</h2>
						<p>{tr}This user has contributed to the following tracker items{/tr}:</p>
						<div class="panel panel-default">
							<div class="panel-body">
								{wikiplugin _name="list"}
								{literal}
									{filter type="trackeritem"}
									{filter exact="{/literal}{$userwatch}{literal}" field="contributors"}
									{ALTERNATE()}{tr}This user has not contributed to any tracker items yet{/tr}.{ALTERNATE}
								{/literal}
								{/wikiplugin}
							</div>
						</div>
					</div>

					{* Articles Tab *}
					<div id="tab_articles" class="profile-tab-content collapse">
						<h2>{tr}Articles{/tr}</h2>
						<p>{tr}This user has contributed to the following articles{/tr}:</p>
						<div class="panel panel-default">
							<div class="panel-body">
								{wikiplugin _name="list"}
								{literal}
									{filter type="article"}
									{filter exact="{/literal}{$userwatch}{literal}" field="contributors"}
									{ALTERNATE()}{tr}This user has not contributed to any articles yet{/tr}.{ALTERNATE}
								{/literal}
								{/wikiplugin}
							</div>
						</div>
					</div>

					{* Forum Posts Tab *}
					<div id="tab_forums" class="profile-tab-content collapse">
						<h2>{tr}Forum Threads{/tr}</h2>
						<p>{tr}This user has started to the following forum threads{/tr}:</p>
						<div class="panel panel-default">
							<div class="panel-body">
								{wikiplugin _name="list"}
								{literal}
									{filter type="forum post"}
									{filter field="parent_thread_id" exact="0"}
									{filter exact="{/literal}{$userwatch}{literal}" field="contributors"}
									{ALTERNATE()}{tr}This user has not contributed to any forums yet{/tr}.{ALTERNATE}
								{/literal}
								{/wikiplugin}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{* Friends *}
		<div class="col-md-3 profile-right-bar">
			{if $user and $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y' and $userinfo['user_allowmsgs'] eq 'y'}
				<div class="profile-buttons" xmlns="http://www.w3.org/1999/html">
					<a href="{bootstrap_modal controller=user action=send_message userwatch=$userwatch}" class="btn btn-default">
						<i class="fa fa-envelope-o"></i>
						{tr}Send me a message{/tr}
					</a>
					{if $prefs.feature_friends eq 'y'}
						{if $user neq $userinfo.object_id}
							{wikiplugin _name="friend" other_user="{$userinfo.object_id}"
							add_button_text="{tr}Add to Network{/tr}"
							remove_button_text="{tr}Remove from Network{/tr}"}
							{/wikiplugin}
						{/if}
					{/if}
				</div>
			{/if}

			{if $prefs.feature_friends eq 'y'}
				{if $user eq $userinfo.object_id}
				<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{tr}Friendship Network{/tr}</h3>
				</div>
				<div class="panel-body">
					{module module=friend_list nobox=y}
				</div>
				{/if}
			</div>
			{/if}
		</div>
	</div>
{else}{* infoPublic eq 'n' *}
	<div>
		{remarksbox type="info" title="Private"}{tr}The user has chosen to make his information private{/tr}{/remarksbox}
	</div>
{/if}
{jq}
	$('.tab_option').click(function(e){
		e.preventDefault();
		$('.tab_option').removeClass('active');
		$('.profile-tab-content').hide();

		$(this).addClass('active');
		$($(this).data('target')).show();
	});
{/jq}