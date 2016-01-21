{* $Id$ *}
{jq notonready=true}
	var baseURI = '{$smarty.server.REQUEST_URI}';
	{literal}
		function refreshCache( entry ) { // {{{
			var datespan = document.getElementById( 'profile-date-' + entry );

			if($('profile-status-' + entry + ' > span.icon-status-pending').is(':visible')) {
				return;
			}

			$('#profile-status-' + entry + ' > span.icon-status-pending').show();
			$('#profile-status-' + entry + ' > span.icon-status-open').hide();
			$('#profile-status-' + entry + ' > span.icon-status-closed').hide();

			var req = getHttpRequest( 'POST', baseURI + '&refresh=' + escape(entry), true );
			req.onreadystatechange = function (aEvt) {
				if (req.readyState == 4) {
					if(req.status == 200) {
						var data = eval( "(" + req.responseText + ")" );
						$.each(['open', 'pending', 'closed'], function (key, value) {
							if (value == data.status) {
								$('#profile-status-' + entry + ' > span.icon-status-' + value).show();
							} else {
								$('#profile-status-' + entry + ' > span.icon-status-' + value).hide();
							}
						});
						datespan.innerHTML = data.lastupdate;
					} else
						alert("Error loading page\n");
				}
			};
			req.send('');
		} // }}}

		function showDetails( id, domain, profile ) { // {{{

			var nid = id + "-sub";
			var infoId = id + "-info";
			var prev = document.getElementById( id );
			var obj = document.getElementById( nid );

			if( obj )
			{
				obj.id = null;
				obj.parentNode.removeChild( obj );
				return;
			}

			var infoOb = document.getElementById( infoId );
			if (!infoOb) {
				infoOb = document.createElement('span');
				infoOb.innerHTML = "";
				infoOb.style.fontStyle = "italic";
				infoOb.id = infoId;
				prev.getElementsByTagName("td")[0].appendChild( infoOb );
			}
			infoOb.innerHTML = " {/literal}{tr}Loading profile{/tr}{literal}...";

			var req = getHttpRequest( 'POST', baseURI + '&getinfo&pd=' + escape(domain) + '&pp=' + escape(profile), true );
			req.onreadystatechange = function (aEvt) {

				if (infoOb) {
					infoOb.innerHTML = " ";

				}
				if (req.readyState == 4) {
					if(req.status == 200) {
						var data = eval( "(" + req.responseText + ")" );

						var row = document.createElement( 'tr' );
						var cell = document.createElement( 'td' );
						var body = document.createElement( 'div' );
						var ul = document.createElement( 'ul' );

						row.appendChild( cell );
						cell.colSpan = 3;

						if( data.installable || data.already ) {

							var pStep = document.createElement('p');
							pStep.style.fontWeight = 'bold';
							if( data.installable ) {
								pStep.innerHTML = "Click on Apply Now to apply Profile";
							} else if ( data.already ) {
								pStep.innerHTML = "A version of this profile is already applied.";
							}

							var form = document.createElement( 'form' );
							var p = document.createElement('p');
							var submit = document.createElement('input');
							var pd = document.createElement('input');
							var pp = document.createElement('input');
							form.method = 'post';
							form.action = document.location.href;

							var iTable = document.createElement('table');
							iTable.className = 'normal';

							var rowNum = 0;
							for( i in data.userInput ) {
								if( typeof(data.userInput[i]) != 'string' )
									continue;

								var iRow = iTable.insertRow( rowNum++ );
								var iLabel = iRow.insertCell( 0 );
								var iField = iRow.insertCell( 1 );

								iRow.className = 'formcolor';

								iLabel.appendChild( document.createTextNode( i ) );
								var iInput = document.createElement( 'input' );
								iInput.type = 'text';
								iInput.name = i;
								iInput.value = data.userInput[i];

								iField.appendChild( iInput );
							}

							if( rowNum > 0 )
								form.appendChild( iTable );

							form.appendChild(p);

							submit.type = 'submit';
							if( data.installable ) {
								submit.name = 'install';
								submit.value = 'Apply Now';
								form.setAttribute ( "onsubmit", 'return confirm(\"{/literal}{tr}Are you sure you want to apply the profile{/tr}{literal} ' + profile + '?\");' );
								submit.setAttribute ( "class", "btn btn-primary");
							} else if ( data.already ) {
								submit.name = 'forget';
								submit.value = 'Forget and Re-apply';
								form.setAttribute ( "onsubmit", 'return confirm(\"{/literal}{tr}Are you sure you want to re-apply the profile{/tr}{literal} ' + profile + '?\");' );
								submit.setAttribute ( "class", "btn btn-primary");
							}

							p.appendChild(submit);
							pd.type = 'hidden';
							pd.name = 'pd';
							pd.value = domain;
							p.appendChild(pd);
							p.appendChild(pStep);
							pp.type = 'hidden';
							pp.name = 'pp';
							pp.value = profile;
							p.appendChild(pp);

							cell.appendChild(form);
						}
						else if( data.error )
						{
							var p = document.createElement('p');
							p.style.fontWeight = 'bold';
							p.innerHTML = "An error occurred during the profile validation. This profile cannot be applied. Message: " + data.error;
							cell.appendChild(p);
						}
						else
						{
							var p = document.createElement('p');
							p.style.fontWeight = 'bold';
							p.innerHTML = "An error occurred during the profile validation. This profile cannot be applied.";
							cell.appendChild(p);
						}

						if( data.dependencies.length > 1 )
						{
							for( k in data.dependencies )
							{
								if( typeof(data.dependencies[k]) != 'string')
									continue;

								var li = document.createElement( 'li' );
								var a = document.createElement( 'a' );
								a.href = data.dependencies[k];
								a.innerHTML = data.dependencies[k];

								li.appendChild( a );
								ul.appendChild( li );
							}

							var p = document.createElement( 'p' );
							p.innerHTML = 'These profiles will be applied:';
							cell.appendChild( p );
							cell.appendChild( ul );
						}

						body.innerHTML = data.content;
						body.style.height = '200px';
						body.style.overflow = 'auto';
						body.style.borderStyle = 'solid';
						body.style.borderWidth = '2px';
						body.style.borderColor = 'black';
						body.style.padding = '8px';

						cell.appendChild( body );

						row.id = nid;
						prev.parentNode.insertBefore( row, prev.nextSibling );

						if (data.feedback.length) {
							alert("Profile issues: \n" + data.feedback);
						}

					}
				} else { // readyState not 4 (complete)

					switch (req.readyState) {
						case 1: {
							infoOb.innerHTML = " {/literal}{tr}Loading profile{/tr}{literal}...";
							break;
						}
						case 2: {
							infoOb.innerHTML = " {/literal}{tr}Sending{/tr}{literal}...";
							break;
						}
						case 3: {
							infoOb.innerHTML = " {/literal}{tr}Waiting{/tr}{literal}...";
							break;
						}
					}

				}
			}
			req.send('');
		} // }}}
	{/literal}
{/jq}

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
			<form method="get" action="tiki-admin.php">
				<input type="hidden" name="ticket" value="{$ticket|escape}">
				<h4>{tr}Find Profiles{/tr} <small>{tr}Search by name, types and repository{/tr}</small></h4>
				<div class="table-responsive">
					<table class="table">
						<tr>
							<td class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="profile">{tr}Profile name{/tr} </label>
									<input type="text" class="form-control" name="profile" placeholder="{tr}Find{/tr}..." id="profile" value="{if isset($profile)}{$profile|escape}{/if}" /></div>
								</div>
								{if isset($category_list) and count($category_list) gt 0}
									<div class="form-group">
										<label class="control-label" for="categories">{tr}Profile Types{/tr}</label>
										<select multiple="multiple" name="categories[]" id="categories" class="form-control" style="min-height: 8em; max-height: 15em">
											{foreach item=cat from=$category_list}
												<option value="{$cat|escape}"{if !empty($categories) and in_array($cat, $categories)} selected="selected"{/if}>{$cat|escape}</option>
											{/foreach}
										</select>
									</div>
								{/if}
								<div class="form-group">
									<label class="control-label" for="repository">{tr}Profile Repository{/tr}</label>
									<select name="repository" id="repository" class="form-control">
										<option value="">{tr}All{/tr}</option>
										{foreach item=source from=$sources}
											<option value="{$source.url|escape}"{if isset($repository) && $repository eq $source.url} selected="selected"{/if}>{$source.short|escape}</option>
										{/foreach}
									</select>
								</div>
								<input type="hidden" name="page" value="profiles"/>
									{jq}
										if ($("#profile-0").length > 0) {
											$(".quickmode_notes").hide();
											$(window).scrollTop($("#step2").offset().top);
										} else {
											$(".quickmode_notes").show();
										}
										$("#repository, #categories").change(function(){
											if ($(this).val()) {
												$(".quickmode_notes").hide(400);
											} else {
												$(".quickmode_notes").show(400);
											}
										});
									{/jq}
								</div>
							<div class="form-group text-center">
								<input type="submit" class="btn btn-primary" name="list" value="{tr}Find{/tr}" />
							</div>
						</td>
						<td class="col-lg-6">
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
						</td>
					</tr>
				</table>
				</div>
			</form>
			<a id="step2"></a>
			{if isset($result) && $result|@count != '0'}
				<h4>{tr}Select and apply profile <small>Click on a Configuration Profile Name below to review it and apply it on your site</small>{/tr}</h4>
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th>{tr}Profile Name{/tr}</th>
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
									<td><a href="javascript:showDetails( 'profile-{$k}', '{$profile.domain|escape}', '{$profile.name|escape}' )">{$profile.name|escape}</a>{if $profile.installed} <em>{tr}applied{/tr}</em>{/if}</td>
								{/if}

								<td>{$profile.domain}</td>
								<td>{$profile.categoriesString}</td>
							</tr>
						{/foreach}
						{if $result|@count eq '0'}
							<tr><td colspan="3" class="odd">{tr}None{/tr}</td></tr>
						{/if}
					</table>
					{if $show_details_for_profile_num != ""}
						{jq}showDetails('profile-{{$show_details_for_profile_num}}', '{{$show_details_for_domain}}', '{{$show_details_for_fullname}}');{/jq}
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
		<h2>{tr}Export{/tr}</h2>
		<form action="tiki-admin.php?page=profiles" method="post" role="form">
			<input type="hidden" name="ticket" value="{$ticket|escape}">
			<fieldset id="export_to_yaml">
				<legend>{tr}Export YAML{/tr}</legend>
				{if !empty($export_yaml)}
					<div class="wikitext">{$export_yaml}</div>
				{/if}
				<div class="form-group">
					<label class="control-label" for="export_type">{tr}Object Type{/tr}</label>
					<select name="export_type" id="export_type" class="form-control">
						<option value="prefs"{if $export_type eq "prefs"} selected="selected"{/if}>
							{tr}Preferences{/tr}
						</option>
						<option value="modules"{if $export_type eq "modules"} selected="selected"{/if}>
							{tr}Modules{/tr}
						</option>
					</select>
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
					<ul id="prefs_to_export_list" class="profile_export_list"{if $export_type neq "prefs"} style=display:none;"{/if}>

						{foreach from=$modified_list key="name" item="data"}
							<li>
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
							<li>
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
					{jq}
						$("#select_all_prefs_to_export").click( function () {
							$("input[name^=prefs_to_export]:visible,input[name^=modules_to_export]:visible").click();
						});
						$("#export_show_added").click( function () {
							$(this)[0].form.submit();
						});
						$("#export_type").change(function(){
							$(".profile_export_list").hide();
							$("#" + $(this).val() + "_to_export_list").show();
						});
					{/jq}
					<div class="text-center submit input_submit_container">
						<input type="submit" class="btn btn-primary" name="export" value="{tr}Export{/tr}" />
					</div>
				</fieldset>
			</fieldset>
		</form>
	{/tab}

	{tab name="{tr}Advanced{/tr}"}
		<h2>{tr}Advanced{/tr}</h2>
		<fieldset>
			<h4>{tr}Repository Status{/tr} <small>{tr}Status of the registered profile repositories{/tr}</small></h4>
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
						<td><span id="profile-date-{$k}">{$entry.formatted}</span> <a href="javascript:refreshCache({$k})" title="{tr}Refresh{/tr}">{icon name="refresh" iclass='tips' ititle=":{tr}Refresh{/tr}"}</a></td>
					</tr>
				{/foreach}
			</table>
			<form action="tiki-admin.php?page=profiles" method="post">
				<input type="hidden" name="ticket" value="{$ticket|escape}">
				{preference name=profile_unapproved}
				{preference name=profile_sources}
				{preference name=profile_channels}
				<div class="text-center submit">
					<input type="submit" class="btn btn-primary" name="config" value="{tr}Save{/tr}" />
				</div>
			</form>
		</fieldset>
		<fieldset><legend>{tr}Profile tester{/tr}</legend>
			<form action="tiki-admin.php?page=profiles" method="post">
				<input type="hidden" name="ticket" value="{$ticket|escape}">
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}Paste or type wiki markup and YAML (with or without the {literal}{CODE}{/literal} tags) into the text area below{/tr}<br>
					<em><strong>{tr}This will run the profile and make potentially unrecoverable changes in your database!{/tr}</strong></em>
				{/remarksbox}
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="profile_tester_name">{tr}Test Profile Name:{/tr} </label>
						<input type="text" name="profile_tester_name" id="profile_tester_name" value="{if isset($profile_tester_name)}{$profile_tester_name}{else}Test{/if}" />
						<select name="empty_cache">
							<option value=""{if isset($empty_cache) and $empty_cache eq ''} checked="checked"{/if}>{tr}None{/tr}</option>
							<option value="all"{if isset($empty_cache) and $empty_cache eq 'all'} checked="checked"{/if}>{tr}All{/tr}</option>
							<option value="templates_c"{if isset($empty_cache) and $empty_cache eq 'templates_c'} checked="checked"{/if}>templates_c</option>
							<option value="temp_cache"{if isset($empty_cache) and $empty_cache eq 'temp_cache'} checked="checked"{/if}>temp_cache</option>
							<option value="temp_public"{if isset($empty_cache) and $empty_cache eq 'temp_public'} checked="checked"{/if}>temp_public</option>
							<option value="modules_cache"{if isset($empty_cache) and $empty_cache eq 'modules_cache'} checked="checked"{/if}>modules_cache</option>
							<option value="prefs"{if isset($empty_cache) and $empty_cache eq 'prefs'} checked="checked"{/if}>prefs</option>
						</select>{$empty_cache}
					</div>
					<div>
						<textarea data-codemirror="true" data-syntax="yaml" id="profile_tester" name="profile_tester" rows="5" cols="40" style="width:95%;">{if isset($test_source)}{$test_source}{/if}</textarea>
					</div>
				</div>
				<div align="center" style="padding:1em;"><input type="submit" class="btn btn-default" name="test" value="{tr}Test{/tr}" /></div>
			</form>
		</fieldset>
	{/tab}

{/tabset}

{jq}
	{{foreach item=k from=$oldSources}
		refreshCache({$k});
	{/foreach}}
{/jq}
