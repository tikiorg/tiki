{* $Id$ *}
{jq notonready=true}
var baseURI = '{$smarty.server.REQUEST_URI}';
{literal}
function refreshCache( entry ) { // {{{
	var status = document.getElementById( 'profile-status-' + entry );
	var datespan = document.getElementById( 'profile-date-' + entry );
	var pending = 'img/icons2/status_pending.gif';

	if( status.src == pending )
		return;
	
	status.src = pending;

	var req = getHttpRequest( 'POST', baseURI + '&refresh=' + escape(entry), true );
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200) {
				var data = eval( "(" + req.responseText + ")" );
				status.src = 'img/icons2/status_' + data.status + '.gif';
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
						pStep.innerHTML = "Step 3: Click on Apply Now to apply Profile";
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
					} else if ( data.already ) {
						submit.name = 'forget';
						submit.value = 'Forget and Re-apply';
						form.setAttribute ( "onsubmit", 'return confirm(\"{/literal}{tr}Are you sure you want to re-apply the profile{/tr}{literal} ' + profile + '?\");' );
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
					p.innerHTML = "An error occured during the profile validation. This profile cannot be applied. Message: " + data.error;
					cell.appendChild(p);
				}
				else
				{
					var p = document.createElement('p');
					p.style.fontWeight = 'bold';
					p.innerHTML = "An error occured during the profile validation. This profile cannot be applied.";
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
		} else {		// readyState not 4 (complete)
			
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

{remarksbox type="tip" title="{tr}Tip{/tr}"}<a class="rbox-link" href="http://profiles.tiki.org">{tr}Tiki Profiles{/tr}</a>{/remarksbox}

{if isset($profilefeedback)}
	{remarksbox type="note" title="{tr}Note{/tr}"}
		{cycle values="odd,even" print=false}
		{tr}The following list of changes has been applied:{/tr}
		<ul>
		{section name=n loop=$profilefeedback}
			<li class="{cycle}">
				<p>{$profilefeedback[n]}</p>
			</li>
		{/section}
		</ul>
	{/remarksbox}
{/if}
{tabset name='tabs_admin-profiles'}
	{tab name="{tr}Apply Profiles{/tr}"}
	
		<h3>{tr}Configure Tiki in 3 easy steps using Profiles{/tr}</h3>
		{if $prefs.javascript_enabled eq 'y'}
				{if $openSources == 'some'}
					{remarksbox type="warning" title="{tr}A Friendly Warning{/tr}"}
						{tr}Some of your Profiles Repositories are not connecting. This may prevent you from applying certain profiles{/tr}
					{/remarksbox}
				{/if}
					
				<fieldset><legend>{tr}Profiles{/tr}</legend>
				<form method="get" action="tiki-admin.php">
					<div class="adminoptionbox">
						<b>{tr}Step 1: Use the Quick or Manual Filter option to see a list of Profiles you can apply{/tr}</b>
						<table class="normal">
							<tr>
								<th width="50%" class="quickmode_notes">{tr}Option 1: Quick Filter{/tr}</th>
	
								<th width="50%">{tr}Option 2: Manual Filter{/tr}</th>
							</tr>
							<tr>
								<td class="quickmode_notes">
									<br/>
									{assign var=profilesFilterUrlStart value='tiki-admin.php?profile=&categories%5B%5D='}
									{assign var=profilesFilterUrlMid value='x&categories%5B%5D='}
									{assign var=profilesFilterUrlEnd value='&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2'}
									
									<p>
										{assign var=profilesFilterUrlFeaturedProfiles value='Featured+profiles'}
										<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlFeaturedProfiles}{$profilesFilterUrlEnd}">{tr}Featured Profiles{/tr}</a>
										<br />{tr}Featured Profiles is a list of applications that are maintained by the Tiki community and are a great way to get started.{/tr}
									</p>
									
									<p>
										{assign var=profilesFilterUrlFullProfiles value='Full+profile+(out+of+the+box+%26+ready+to+go)'}
										<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlFullProfiles}{$profilesFilterUrlEnd}">{tr}Full Profiles{/tr}</a>
										<br />{tr}Full Profiles are full featured out of the box solutions.{/tr} 
									</p>
		
									<p>
										{assign var=profilesFilterUrlMiniProfiles value='Mini-profile+(can+be+included+in+other)'}
										<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlMiniProfiles}{$profilesFilterUrlEnd}">{tr}Mini Profiles{/tr}</a>
										<br />{tr}Mini Profiles will configure specific features and are a great way to add more functionality to an existing configuration.{/tr} 
									</p>
		
									<p>
										{assign var=profilesFilterUrlLearningProfiles value='Learning+profile+(just+to+show+off+feature)'}
										<a href="{$profilesFilterUrlStart}{$tikiMajorVersion}{$profilesFilterUrlMid}{$profilesFilterUrlLearningProfiles}{$profilesFilterUrlEnd}">{tr}Learning Profiles{/tr}</a>
										<br />{tr}Learning Profiles will allow you to quickly evaluate specific features in Tiki.{/tr}
									</p>
		
								</td>
								<td>
									<div class="adminoptionboxchild">
										<div class="adminoptionlabel">{tr}Filter the list of profiles:{/tr}</div>
										<div class="adminoptionlabel">
											<label for="profile">{tr}Profile:{/tr} </label>
											<input type="text" name="profile" id="profile" value="{if isset($profile)}{$profile|escape}{/if}" /></div>
											{if isset($category_list) and count($category_list) gt 0}
												<div class="adminoptionlabel"><label for="categories">{tr}Categories:{/tr} </label>
													<select multiple="multiple" name="categories[]" id="categories" style="max-height: 10em">
													{foreach item=cat from=$category_list}
														<option value="{$cat|escape}"{if !empty($categories) and in_array($cat, $categories)} selected="selected"{/if}>{$cat|escape}</option>
													{/foreach}
													</select>
												</div>
											{/if}
		
										<div class="adminoptionlabel"><label for="repository">{tr}Repository:{/tr} </label>
											<select name="repository" id="repository">
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
	$(window).scrollTop($("a[name=step2]").offset().top);
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
								<div align="center"><input type="submit" name="list" value="{tr}List{/tr}" /></div>
							</td>
						</tr>
					</table>
				</div>
		</form>
	        <a name="step2"></a>
	<br />	
		
	       {if isset($result) && $result|@count != '0'}
	
	        <b>Step 2: Click on a Profile to review and see description</b>
			<table class="normal">
				<tr>
					<th>{tr}Profile{/tr}</th>
					<th>{tr}Repository{/tr}</th>
					<th>{tr}Categories{/tr}</th>
				</tr>
				{foreach key=k item=profile from=$result}
					<tr id="profile-{$k}">
						<td><a href="javascript:showDetails( 'profile-{$k}', '{$profile.domain|escape}', '{$profile.name|escape}' )">{$profile.name|escape}</a>{if $profile.installed} <em>{tr}applied{/tr}</em>{/if}</td>
						<td>{$profile.domain}</td>
						<td>{$profile.categoriesString}</td>
					</tr>
				{/foreach}
				{if $result|@count eq '0'}
				<tr><td colspan="3" class="odd">{tr}None{/tr}</td></tr>
				{/if}
			</table>
			{/if}
	
	</fieldset>




<fieldset class="admin">
<legend>{tr}Status{/tr}</legend>
<div class="adminoptionbox">
	<table class="normal">
		<tr>
			<th>{tr}Profile repository{/tr}</th>
			<th>{tr}Status{/tr}</th>
			<th>{tr}Last update{/tr}</th>
		</tr>
		{foreach key=k item=entry from=$sources}
			<tr>
				<td>{$entry.short}</td>
				<td><img id="profile-status-{$k}" alt="{tr}Status{/tr}" src="img/icons2/status_{$entry.status}.gif"/></td>
				<td><span id="profile-date-{$k}">{$entry.formatted}</span> <a href="javascript:refreshCache({$k})" class="icon"><img src="pics/icons/arrow_refresh.png" class="icon" alt="{tr}Refresh{/tr}"/></a></td>
			</tr>
		{/foreach}
	</table>
</div>
</fieldset>
{else}
	{remarksbox type="warning" title="{tr}A Friendly Warning{/tr}"}
		{tr}Javascript must be turned <b>ON</b> in order to apply Profiles. Please enable your javascript and try again.{/tr}
	{/remarksbox}
{/if}
{/tab}
{tab name="{tr}Export{/tr}"}
	<form action="tiki-admin.php?page=profiles" method="post">
		<fieldset id="export_to_yaml">
			<legend>{tr}Export YAML{/tr}</legend>
			{if !empty($export_yaml)}
				{$export_yaml}
			{/if}
			<div class="navbar">
				<label for="export_type">{tr}Object type:{/tr}</label>
				<select name="export_type" id="export_type">
					<option value="prefs"{if $export_type eq "prefs"} checked="checked"{/if}>
						{tr}Preferences{/tr}
					</option>
				</select>
			</div>
			<fieldset>
				<legend>{tr}Export modified preferences as YAML{/tr}</legend>
				<div class="navbar">
					{listfilter selectors="#prefs_to_export_list > li"}
					<label for="select_all_prefs_to_export">{tr}Toggle Visible{/tr}</label>
					<input type="checkbox" id="select_all_prefs_to_export" />
					<label for="export_show_added">{tr}Show added preferences{/tr}</label>
					<input type="checkbox" name="export_show_added" id="export_show_added"
							{if !empty($smarty.request.export_show_added)} checked="checked"{/if} />
				</div>
				<ul id="prefs_to_export_list">
					{cycle values="odd,even" print=false}
					{foreach from=$modified_list  key="name" item="data"}
						<li class="{cycle}">
							<input type="checkbox" name="prefs_to_export[{$name}]" value="{$data.cur|escape}"
									 id="checkbox_{$name}"{if isset($prefs_to_export[$name])} checked="checked"{/if} />
							<label for="checkbox_{$name}">
								{$name} = <strong>{$data.cur|truncate:40:"...":true|escape}</strong>
								<em>
									&nbsp;&nbsp;
									{if isset($data.def)}
										{if empty($data.def)}
											('')
										{else}
											({$data.def|truncate:20:"...":true|escape})
										{/if}
									{else}
										({tr}no default{/tr})
									{/if}
								</em>
							</label>
						</li>
					{/foreach}
				</ul>
				{jq}
$("#select_all_prefs_to_export").click( function () {
	$("input[name^=prefs_to_export]:visible").click();
});
$("#export_show_added, #export_type").click( function () {
	$(this)[0].form.submit();
});
				{/jq}
				<div class="input_submit_container">
					<input type="submit" name="export" value="{tr}Export{/tr}" />
				</div>
			</fieldset>
		</fieldset>
	</form>
{/tab}
{tab name="{tr}Advanced{/tr}"}

<fieldset><legend>{tr}Repositories{/tr}</legend>
<form action="tiki-admin.php?page=profiles" method="post">
	{preference name=profile_unapproved}
	{preference name=profile_sources}
	{preference name=profile_channels}

	<div align="center" style="padding:1em;"><input type="submit" name="config" value="{tr}Save{/tr}" /></div>
</form>
</fieldset>


	<fieldset><legend>{tr}Profile tester{/tr}</legend>
		<form action="tiki-admin.php?page=profiles" method="post">
		{remarksbox type="warning" title="{tr}Warning{/tr}"}
			{tr}Paste or type wiki markup and YAML (with or without the {literal}{CODE}{/literal} tags) into the text area below{/tr}<br />
			<em><strong>{tr}This will run the profile and make potentially unrecoverable changes in your database!{/tr}</strong></em>
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="profile_tester_name">{tr}Test Profile Name:{/tr} </label>
					<input type="text" name="profile_tester_name" id="profile_tester_name" value="{if isset($profile_tester_name)}{$profile_tester_name}{else}Test{/if}" />
				</div>
				<div>
					<textarea id="profile_tester" name="profile_tester" rows="5" cols="40" style="width:95%;">{if isset($test_source)}{$test_source}{/if}</textarea>
				</div>
			</div>
			<div align="center" style="padding:1em;"><input type="submit" name="test" value="{tr}Test{/tr}" /></div>
		{/remarksbox}
		</form>
	</fieldset>

{/tab}
{/tabset}

{jq}
{{foreach item=k from=$oldSources}
	refreshCache({$k});
{/foreach}}
{/jq}
