<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#admin-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<ul class="nav navbar-nav">
			<li class="dropdown">	
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="{tr}Settings{/tr}">
					{icon name="filter"} <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li>
						<form method="post" action="" class="form" role="form">
							<span class="dropdown-title">{tr}Preference Filters{/tr}</span>
							{foreach from=$pref_filters key=name item=info}
								<div class="checkbox">
									<label>
										<input type="checkbox" class="preffilter {$info.type|escape}" name="pref_filters[]" value="{$name|escape}" {if $info.selected}checked="checked"{/if}>{$info.label|escape}
									</label>
								</div>
							{/foreach}
							<div class="text-center">
								<input type="submit" value="{tr}Set as my default{/tr}" class="btn btn-primary btn-sm">
							</div>
							{if $prefs.connect_feature eq "y"}
							{capture name=likeicon}{icon name="thumbs-up"}{/capture}
							<label>
									<input type="checkbox" id="connect_feedback_cbx" {if !empty($connect_feedback_showing)}checked="checked"{/if}>
									{tr}Provide Feedback{/tr}
									<a href="http://doc.tiki.org/Connect" target="tikihelp" class="tikihelp" title="{tr}Provide Feedback:{/tr}
										{tr}Once selected, some icon/s will be shown next to all features so that you can provide some on-site feedback about them{/tr}.
										<br/><br/>
										<ul>
											<li>{tr}Icon for 'Like'{/tr} {$smarty.capture.likeicon|escape}</li>
<!--											<li>{tr}Icon for 'Fix me'{/tr} <img src=img/icons/connect_fix.png></li> -->
<!--											<li>{tr}Icon for 'What is this for?'{/tr} <img src=img/icons/connect_wtf.png></li> -->
										</ul>
										<br/>
										{tr}Your votes will be sent when you connect with mother.tiki.org (currently only by clicking the 'Connect > <strong>Send Info</strong>' button){/tr}
										<br/><br/>
										{tr}Click to read more{/tr}
									">
										{icon name="help"}
									</a>
								</label>
								{$headerlib->add_jsfile("lib/jquery_tiki/tiki-connect.js")}
							{/if}
						</form>

						{jq}
							var updateVisible = function() {
								var show = function (selector) {
									selector.show();
									selector.parents('fieldset:not(.tabcontent)').show();
									selector.closest('fieldset.tabcontent').addClass('filled');
								};
								var hide = function (selector) {
									selector.hide();
									/*selector.parents('fieldset:not(.tabcontent)').hide();*/
								};

								var filters = [];
								var prefs = $('.adminoptionbox.preference, .admbox').hide();
								prefs.parents('fieldset:not(.tabcontent)').hide();
								prefs.closest('fieldset.tabcontent').removeClass('filled');
								$('.preffilter').each(function () {
									var targets = $('.adminoptionbox.preference.' + $(this).val() + ',.admbox.' + $(this).val());
									if ($(this).is(':checked')) {
										filters.push($(this).val());
										show(targets);
									} else if ($(this).is('.negative:not(:checked)')) {
										hide(targets);
									}
								});

								show($('.adminoptionbox.preference.modified'))

								$('input[name="filters"]').val(filters.join(' '));
								$('.tabset .tabmark a').each(function () {
									var selector = 'fieldset.tabcontent.' + $(this).attr('href').substring(1);
									var content = $(this).closest('.tabset').find(selector);

									$(this).parent().toggle(content.is('.filled') || content.find('.preference').length === 0);
								});
							};

							updateVisible();
							$('.preffilter').change(updateVisible);
						{/jq}
					</li>
					<li class="divider"></li>
					<li>
						<a href="tiki-admin.php?prefrebuild">
							{tr}Rebuild Admin Index{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-admin.php">
							{tr}Control Panels{/tr}
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	<div class="collapse navbar-collapse" id="admin-navbar-collapse-1">	
		{include file="admin/admin_navbar_menu.tpl"}
		<ul class="nav navbar-nav navbar-right">
			<li>
				<form method="post" action="" class="navbar-form" role="search">
					{*remarksbox type="note" title="{tr}Development Notice{/tr}"}
						{tr}This search feature and the <a class="alert-link" href="tiki-edit_perspective.php">perspectives GUI</a> need <a class="alert-link" href="http://dev.tiki.org/Dynamic+Preferences">dev.tiki.org/Dynamic+Preferences</a>. If you search for something and it's not appearing, please help improve keywords/descriptions.{/tr}
					{/remarksbox*}
					<div class="form-group">
						<input type="hidden" name="filters">
						<input type="text" name="lm_criteria" value="{$lm_criteria|escape}" class="form-control" placeholder="{tr}Search preferences{/tr}..." autofocus>
					</div>
					<button type="submit" class="btn btn-default" {if $indexNeedsRebuilding} class="tips" title="{tr}Configuration search{/tr}|{tr}Note: The search index needs rebuilding, this will take a few minutes.{/tr}"{/if}>{icon name="search"}</button>
				</form>
			</li>
		</ul>
	</div>
	{if $include != "list_sections"}
		<div class="adminanchors panel-body clearfix"><ul class="nav navbar-nav">{include file='admin/include_anchors.tpl'}</ul></div>
	{/if}
</nav>

{if $tikifeedback}
	{remarksbox type="feedback" title="{tr}The following list of changes has been applied{/tr}"}
		<ul class="list-unstyled">
		{section name=n loop=$tikifeedback}
			<li>
			{if $tikifeedback[n].st eq 0}
				{icon name="disable"}
			{elseif $tikifeedback[n].st eq 1}
				{icon name="ok"}
			{elseif $tikifeedback[n].st eq 2}
				{icon name="edit"}
			{elseif $tikifeedback[n].st eq 4}
				{icon name="undo"}
			{else}
				{icon name="information"}
			{/if}
				{if $tikifeedback[n].st ne 3}{tr}Preference{/tr} {/if}<strong>{tr}{$tikifeedback[n].mes|stringfix}{/tr}</strong>
				{if $tikifeedback[n].st ne 3}(<small>{tr}Preference name:{/tr} {$tikifeedback[n].name})</small>{/if}
			</li>
		{/section}
		</ul>
	{/remarksbox}
{/if}
{if isset($csrferror)}
	{remarksbox type="error" title="{tr}Potential Cross-Site Request Forgery{/tr}"}
		{$csrferror}
	{/remarksbox}
{/if}
{* show an alert if the CSRF ticket has timed out - hard coded to 15 minutes in lib/tikiticketlib.php->key_check *}
{jq}setTimeout(function () {
		$("form", "#col1").mouseup(function() {
			var target = $('.modal.fade:not(.in)').first();
			$.post($.service('utilities', 'alert_button'),
				{modal: '1',
				ajaxheading: "{tr}Security ticket timed out{/tr}",
				ajaxmsg: "{tr}The security ticket for this page has timed out. Please reload the page to make any changes.{/tr}",
				ajaxbuttonname: "{tr}Reload{/tr}",
				ajaxtype: 'warning',
				ajaxhref: location.href,
				ajaxdismissible: 'n'},
				function (data) {
					$('.modal-content', target).html(data);
					target.modal();
			});
		});
	}, 1000 * 60 * 15);
{/jq}
{if $lm_error}
	{remarksbox type="warning" title="{tr}Search error{/tr}"}
		{$lm_error}
	{/remarksbox}
{elseif $lm_searchresults}
	<div class="panel panel-default" id="pref_searchresults">
		<div class="panel-heading">
			<h3 class="panel-title">{tr}Preference Search Results{/tr}<button type="button" id="pref_searchresults-close" class="close" aria-hidden="true">&times;</button></h3>
		</div>
		<form method="post" action="" href="tiki-admin.php" class="table" role="form">
			<div class="pref_search_results panel-body">
				{foreach from=$lm_searchresults item=prefName}
					{preference name=$prefName get_pages="y"}
				{/foreach}
			</div>
			<div class="panel-footer text-center">
				<input class="btn btn-primary" type="submit" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
			<input type="hidden" name="lm_criteria" value="{$lm_criteria|escape}">
			<input type="hidden" name="daconfirm" value="y">
			<input type="hidden" name="ticket" value="{$ticket|escape}">
		</form>
	</div>
	{jq}
		$( "#pref_searchresults-close" ).click(function() {
			$( "#pref_searchresults" ).hide();
		});
	{/jq}
{elseif $lm_criteria}
	{remarksbox type="note" title="{tr}No results{/tr}" icon="magnifier"}
		{tr}No preferences were found for your search query with your current choice of Preference Filters ({icon name="filter"}).{/tr}{if $prefs.unified_engine eq 'lucene'}{tr} Not what you expected? Try {/tr}<a class="alert-link" href="tiki-admin.php?prefrebuild">{tr}rebuild{/tr}</a> {tr}the preferences search index.{/tr}{/if}
	{/remarksbox}
{/if}
