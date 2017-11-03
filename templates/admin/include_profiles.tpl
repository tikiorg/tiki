{* $Id$ *}
{assign var="baseURI" value="{$smarty.server.REQUEST_URI}&ticket={{$ticket|escape:url}}&daconfirm=y"}
{$headerlib->add_jsfile("lib/jquery_tiki/tiki-profile.js")}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	<a class="alert-link" href="http://profiles.tiki.org">{tr}Tiki Configuration Profiles{/tr}</a>
{/remarksbox}

{if isset($profilefeedback)}
	{remarksbox type="note" title="{tr}Note{/tr}"}

		{tr}The following list of changes has been applied:{/tr}
		<ul>
		{section name=n loop=$profilefeedback}
			<li>
				<p>{$profilefeedback[n]}</p>
			</li>
		{/section}
		</ul>
	{/remarksbox}
{/if}

{tabset name='tabs_admin-profiles'}

	{tab name="{tr}Apply{/tr}"}
		{if $prefs.javascript_enabled eq 'y'}
			{if $openSources == 'some'}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}Some of your Profiles Repositories are not connecting. This may prevent you from applying certain profiles{/tr}
				{/remarksbox}
			{/if}
			<form method="get" action="tiki-admin.php?page=profiles">
				{ticket}
				<h4>{tr}Find profiles{/tr} <small>{tr}Search by name, types and repository{/tr}</small></h4>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="profile">{tr}Profile name{/tr} </label>
							<input type="text" class="form-control" name="profile" placeholder="{tr}Find{/tr}..." id="profile" value="{if isset($profile)}{$profile|escape}{/if}" />
						</div>
						{if isset($category_list) and count($category_list) gt 0}
							<div class="form-group">
								<label class="control-label" for="categories">{tr}Profile types{/tr}</label>
									<select multiple="multiple" name="categories[]" id="categories" class="form-control" style="min-height: 8em; max-height: 15em">
										{foreach item=cat from=$category_list}
											<option value="{$cat|escape}"{if !empty($categories) and in_array($cat, $categories)} selected="selected"{/if}>{$cat|escape}</option>
										{/foreach}
									</select>
							</div>
						{/if}
						<div class="form-group">
							<label class="control-label" for="repository">{tr}Profile repository{/tr}</label>
							<select name="repository" id="repository" class="form-control">
								<option value="">{tr}All{/tr}</option>
								{foreach item=source from=$sources}
									<option value="{$source.url|escape}"{if isset($repository) && $repository eq $source.url} selected="selected"{/if}>{$source.short|escape}</option>
								{/foreach}
							</select>
						</div>
						<input type="hidden" name="page" value="profiles">
						<input type="hidden" name="redirect" value=0>
						<div class="form-group text-center">
							<input type="submit" class="btn btn-primary timeout" name="list" value="{tr}Find{/tr}" />
						</div>
					</div>
					<div class="col-sm-6">
							{remarksbox type="info" title="{tr}Suggested Profiles{/tr}" close="n"}
								{assign var=profilesFilterUrlStart value='tiki-admin.php?profile=&categories%5B%5D='}
								{assign var=profilesFilterUrlMid value='.x&categories%5B%5D='}
								{assign var=profilesFilterUrlEnd value='&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2'}

								<p>
									{assign var=profilesFilterUrlFeaturedProfiles value='Featured+profiles'}
									<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlFeaturedProfiles}{$profilesFilterUrlEnd}" class="alert-link">{tr}Featured Site Profiles{/tr}</a>
									<br>{tr}Featured Site Profiles is a list of applications that are maintained by the Tiki community and are a great way to get started.{/tr}
								</p>

								<p>
									{assign var=profilesFilterUrlFullProfiles value='Full+profile+(out+of+the+box+%26+ready+to+go)'}
									<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlFullProfiles}{$profilesFilterUrlEnd}" class="alert-link">{tr}Full Profiles{/tr}</a>
									<br>{tr}Full Profiles are full featured out of the box solutions.{/tr}
								</p>

								<p>
									{assign var=profilesFilterUrlMiniProfiles value='Mini-profile+(can+be+included+in+other)'}
									<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlMiniProfiles}{$profilesFilterUrlEnd}" class="alert-link">{tr}Mini Profiles{/tr}</a>
									<br>{tr}Mini Profiles will configure specific features and are a great way to add more functionality to an existing configuration.{/tr}
								</p>

								<p>
									{assign var=profilesFilterUrlLearningProfiles value='Learning+profile+(just+to+show+off+feature)'}
									<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlLearningProfiles}{$profilesFilterUrlEnd}" class="alert-link">{tr}Learning Profiles{/tr}</a>
									<br>{tr}Learning Profiles will allow you to quickly evaluate specific features in Tiki.{/tr}
								</p>
							{/remarksbox}
					</div>

				</div>
			</form>
			<a id="step2"></a>
			{if isset($result) && $result|@count != '0'}
				<h4>{tr}Select and apply profile <small>Click on a configuration profile name below to review it and apply it on your site</small>{/tr}</h4>
				<div class="table-responsive">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th>{tr}Profile name{/tr}</th>
							<th>{tr}Repository{/tr}</th>
							<th>{tr}Profile type{/tr}</th>
						</tr>
						{foreach key=k item=profile from=$result}
							<tr id="profile-{$k}">
								{if $profile.name == $show_details_for}
									{assign var="show_details_for_profile_num" value="$k"}
									{assign var="show_details_for_fullname" value=$profile.name|escape}
									{assign var="show_details_for_domain" value=$profile.domain|escape}
									<td>{$profile.name|escape}: {tr}See profile info below (may take a few seconds to load){/tr}.</td>
								{else}
									<td><a href="javascript:$.profilesShowDetails( '{$baseURI}', 'profile-{$k}', '{$profile.domain|escape}', '{$profile.name|escape}' )">{$profile.name|escape}</a>{if $profile.installed} <em>{tr}applied{/tr}</em>{/if}</td>
								{/if}

								<td>{$profile.domain}</td>
								<td>{$profile.categoriesString}</td>
							</tr>
						{/foreach}
						{if $result|@count eq '0'}
							<tr><td colspan="3" class="odd">{tr}None{/tr}</td></tr>
						{/if}
					</table>
					{if isset($show_details_for_profile_num) && $show_details_for_profile_num != ""}
						{jq}$.profilesShowDetails('profile-{{$show_details_for_profile_num}}', '{{$show_details_for_domain}}', '{{$show_details_for_fullname}}');{/jq}
					{/if}
				</div>
			{/if}
		{else}
			{remarksbox type="warning" title="{tr}Warning{/tr}"}
				{tr}JavaScript must be turned <strong>ON</strong> in order to apply Profiles. Please enable your JavaScript and try again.{/tr}
			{/remarksbox}
		{/if}
	{/tab}

	{tab name="{tr}Export{/tr}"}
		<form class="form-horizontal" action="tiki-admin.php?page=profiles" method="post" role="form">
			{ticket}
			<input type="hidden" name="redirect" value=0>
			<fieldset id="export_to_yaml">
				<legend>{tr}Export YAML{/tr}</legend>
				{if !empty($export_yaml)}
					<div class="wikitext">{$export_yaml}</div>
				{/if}
				<div class="form-group">
					<label class="control-label col-sm-2" for="export_type">{tr}Object type{/tr}</label>
					<div class="col-sm-5">
					<select name="export_type" id="export_type" class="form-control">
						<option value="prefs"{if !empty($export_type) && $export_type eq "prefs"} selected="selected"{/if}>
							{tr}Preferences{/tr}
						</option>
						<option value="modules"{if !empty($export_type) && $export_type eq "modules"} selected="selected"{/if}>
							{tr}Modules{/tr}
						</option>
					</select>
					</div>
				</div>
				<fieldset>
					<legend>{tr}Export modified preferences as YAML{/tr}</legend>
					<div class="t_navbar">
						{listfilter selectors=".profile_export_list > li"}
						<label for="select_all_prefs_to_export">{tr}Toggle Visible{/tr}</label>
						<input type="checkbox" id="select_all_prefs_to_export" />
						<label for="export_show_added">{tr}Show added preferences{/tr}</label>
						<input type="checkbox" name="export_show_added" id="export_show_added" {if !empty($smarty.request.export_show_added)} checked="checked"{/if} >
					</div>
					<ul id="prefs_to_export_list" class="profile_export_list"{if not empty($export_type) and $export_type neq "prefs"} style=display:none;"{/if}>

						{foreach from=$modified_list key="name" item="data"}
							<li class="checkbox">
								{if is_array($data.current.expanded)}
									{assign var=current value=$data.current.expanded|implode:", "}
									{assign var=current value="[$current]"}
								{else}
									{assign var=current value=$data.current.expanded}
								{/if}
								<input type="checkbox" name="prefs_to_export[{$name}]" value="{$current|escape}"
									id="checkbox_{$name}"{if isset($prefs_to_export[$name])} checked="checked"{/if}
								>
								<label for="checkbox_{$name}">
									{$name} = '<strong>{$current|truncate:40:"...":true|escape}</strong>'{* FIXME: This one line per preference display format is ugly and doesn't work for multiline values *}
									<em>
										&nbsp;&nbsp;
										{if isset($data.default)}
											{if empty($data.default)}
												('')
											{else}
												{if is_array($data.default)}{assign var=default value=$data.default|implode:", "}{else}{assign var=default value=$data.default}{/if}
												('{$default|truncate:20:"...":true|escape}')
											{/if}
										{else}
											({tr}no default{/tr})
										{/if}
									</em>
								</label>
							</li>
						{/foreach}
					</ul>
					<ul id="modules_to_export_list" class="profile_export_list"{if $export_type neq "modules"} style=display:none;"{/if}>

						{foreach from=$modules_for_export key="name" item="data"}
							<li class="checkbox">
								<input type="checkbox" name="modules_to_export[{$name}]" value="{$data.name|escape}"
									id="modcheckbox_{$name}"{if isset($modules_to_export[$name])} checked="checked"{/if} />
								<label for="modcheckbox_{$name}">
									{$data.data.name|escape} :
									<em>
										&nbsp;&nbsp;
										{$data.data.position}
										{$data.data.order}
									</em>
								</label>
							</li>
						{/foreach}
					</ul>
					<div class="text-center submit input_submit_container">
						<input type="submit" class="btn btn-primary timeout" name="export" value="{tr}Export{/tr}" />
					</div>
				</fieldset>
			</fieldset>
		</form>
	{/tab}

	{tab name="{tr}Advanced{/tr}"}
		<br>
		<fieldset>
			<h4>{tr}Repository status{/tr} <small>{tr}status of the registered profile repositories{/tr}</small></h4>
			<table class="table">
				<tr>
					<th>{tr}Profile repository{/tr}</th>
					<th>{tr}Status{/tr}</th>
					<th>{tr}Last update{/tr}</th>
				</tr>
				{foreach key=k item=entry from=$sources}
					<tr>
						<td>{$entry.short}</td>
						<td id="profile-status-{$k}">
							{if $entry.status == 'open'}
								{icon name='status-open' iclass='tips' ititle="{tr}Status{/tr}:{tr}Open{/tr}"}
								{icon name='status-pending' istyle='display:none' iclass='tips' ititle="{tr}Status{/tr}:{tr}Pending{/tr}"}
								{icon name='status-closed' istyle='display:none' iclass='tips' ititle="{tr}Status{/tr}:{tr}Closed{/tr}"}
							{elseif $entry.status == 'closed'}
								{icon name='status-open' istyle='display:none' iclass='tips' ititle="{tr}Status{/tr}:{tr}Open{/tr}"}
								{icon name='status-pending' istyle='display:none' iclass='tips' ititle="{tr}Status{/tr}:{tr}Pending{/tr}"}
								{icon name='status-closed' iclass='tips' ititle="{tr}Status{/tr}:{tr}Closed{/tr}"}
							{else}
								{icon name='status-open' istyle='display:none' iclass='tips' ititle="{tr}Status{/tr}:{tr}Open{/tr}"}
								{icon name='status-pending' iclass='tips' ititle="{tr}Status{/tr}:{tr}Pending{/tr}"}
								{icon name='status-closed' istyle='display:none' iclass='tips' ititle="{tr}Status{/tr}:{tr}Closed{/tr}"}
							{/if}
						</td>
						<td><span id="profile-date-{$k}">{$entry.formatted}</span> <a href='javascript:$.profilesRefreshCache("{$baseURI}", "{$k}")' title="{tr}Refresh{/tr}">{icon name="refresh" iclass='tips' ititle=":{tr}Refresh{/tr}"}</a></td>
					</tr>
				{/foreach}
			</table>
			<form class="form-horizontal" action="tiki-admin.php?page=profiles" method="post">
				{ticket}
				{preference name=profile_unapproved}
				{preference name=profile_sources}
				{preference name=profile_channels}
				<div class="text-center submit">
					<input type="submit" class="btn btn-primary timeout" name="config" value="{tr}Save{/tr}" />
				</div>
			</form>
		</fieldset>
		<fieldset><legend>{tr}Profile tester{/tr}</legend>
			<form class="form-horizontal" action="tiki-admin.php?page=profiles" method="post">
				{ticket}
				<input type="hidden" name="redirect" value=0>
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}Paste or type wiki markup and YAML (with or without the {literal}{CODE}{/literal} tags) into the text area below{/tr}<br>
					<em><strong>{tr}This will run the profile and make potentially unrecoverable changes in your database!{/tr}</strong></em>
				{/remarksbox}
				<div class="adminoptionbox">
					<div class="adminoptionlabel form-group">
						<label for="profile_tester_name" class="control-label col-sm-4">{tr}Test profile name{/tr} </label>
						<div class="col-sm-4 margin-bottom-sm">
						<input class="form-control" type="text" name="profile_tester_name" id="profile_tester_name" value="{if isset($profile_tester_name)}{$profile_tester_name}{else}Test{/if}" />
						</div>
						<div class="col-sm-4">
							<select class="form-control" name="empty_cache" class="form-control">
							<option value=""{if isset($empty_cache) and $empty_cache eq ''} checked="checked"{/if}>{tr}None{/tr}</option>
							<option value="all"{if isset($empty_cache) and $empty_cache eq 'all'} checked="checked"{/if}>{tr}All{/tr}</option>
							<option value="templates_c"{if isset($empty_cache) and $empty_cache eq 'templates_c'} checked="checked"{/if}>templates_c</option>
							<option value="temp_cache"{if isset($empty_cache) and $empty_cache eq 'temp_cache'} checked="checked"{/if}>temp_cache</option>
							<option value="temp_public"{if isset($empty_cache) and $empty_cache eq 'temp_public'} checked="checked"{/if}>temp_public</option>
							<option value="modules_cache"{if isset($empty_cache) and $empty_cache eq 'modules_cache'} checked="checked"{/if}>modules_cache</option>
							<option value="prefs"{if isset($empty_cache) and $empty_cache eq 'prefs'} checked="checked"{/if}>prefs</option>
						</select>{$empty_cache}
							</div>
					</div>
					<div>
						<textarea data-codemirror="true" data-syntax="yaml" id="profile_tester" name="profile_tester" class="form-control">{if isset($test_source)}{$test_source}{/if}</textarea>
					</div>
				</div>
				<div align="center" style="padding:1em;"><input type="submit" class="btn btn-default timeout" name="test" value="{tr}Test{/tr}"></div>
			</form>
		</fieldset>
	{/tab}

{/tabset}

{jq}
        {{foreach item=k from=$oldSources}
                $.profilesRefreshCache("{$baseURI}", "{$k}");
	{/foreach}}
{/jq}
