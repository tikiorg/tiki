{* $Id: tiki-admin-include-ads.tpl 12802 2008-05-12 11:06:16Z sylvieg $ *}
<script type="text/javascript">
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
	var prev = document.getElementById( id );
	var obj = document.getElementById( nid );

	if( obj )
	{
		obj.id = null;
		obj.parentNode.removeChild( obj );
		return;
	}

	var req = getHttpRequest( 'POST', baseURI + '&getinfo&pd=' + escape(domain) + '&pp=' + escape(profile), true );
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200) {
				var data = eval( "(" + req.responseText + ")" );

				var row = document.createElement( 'tr' );
				var cell = document.createElement( 'td' );
				var body = document.createElement( 'div' );
				var ul = document.createElement( 'ul' );

				row.appendChild( cell );
				cell.colSpan = 3;

				if( data.already )
				{
					var p = document.createElement( 'p' );
					p.innerHTML = "A version of this profile is already installed.";
					p.style.fontWeight = 'bold';
					cell.appendChild(p);

					var form = document.createElement( 'form' );
					var p = document.createElement('p');
					var submit = document.createElement('input');
					var pd = document.createElement('input');
					var pp = document.createElement('input');
					form.method = 'post';
					form.action = document.location.href;

					form.appendChild(p);
					submit.type = 'submit';
					submit.name = 'forget';
					submit.value = 'Forget and Reinstall';
					p.appendChild(submit);
					pd.type = 'hidden';
					pd.name = 'pd';
					pd.value = domain;
					p.appendChild(pd);
					pp.type = 'hidden';
					pp.name = 'pp';
					pp.value = profile;
					p.appendChild(pp);
					form.setAttribute ( "onsubmit", 'return confirm(\"{/literal}{tr}Are you sure you want to install the profile{/tr}{literal} ' + profile + '?\");' );

					cell.appendChild(form);
				}
				else if( data.installable )
				{
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
					submit.name = 'install';
					submit.value = 'Install Now';
					p.appendChild(submit);
					pd.type = 'hidden';
					pd.name = 'pd';
					pd.value = domain;
					p.appendChild(pd);
					pp.type = 'hidden';
					pp.name = 'pp';
					pp.value = profile;
					p.appendChild(pp);
					form.setAttribute ( "onsubmit", 'return confirm(\"{/literal}{tr}Are you sure you want to install the profile{/tr}{literal} ' + profile + '?\");' );

					cell.appendChild(form);
				}
				else if( data.error )
				{
					var p = document.createElement('p');
					p.style.fontWeight = 'bold';
					p.innerHTML = "An error occured during the profile validation. This profile cannot be installed. Message: " + data.error;
					cell.appendChild(p);
				}
				else
				{
					var p = document.createElement('p');
					p.style.fontWeight = 'bold';
					p.innerHTML = "An error occured during the profile validation. This profile cannot be installed.";
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
					p.innerHTML = 'These profiles will be installed:';
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
			}
		}
	}
	req.send('');
} // }}}
{/literal}
</script>
{remarksbox type="tip" title="{tr}Tip{/tr}"}<a class="rbox-link" href="http://profiles.tikiwiki.org">{tr}TikiWiki Profiles{/tr}</a>{/remarksbox}




<div class="cbox">
<table class="admin"><tr><td>

<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Status{/tr}:</div>
	<div>
	<table class="normal">
			<tr>
				<th>{tr}Profile repository{/tr}</th>
				<th>{tr}Status{/tr}</th>
				<th>{tr}Last update{/tr}</th>
			</tr>
			{foreach key=k item=entry from=$sources}
				<tr>
					<td>{$entry.short}</td>
					<td><img id="profile-status-{$k}" src="img/icons2/status_{$entry.status}.gif"/></td>
					<td><span id="profile-date-{$k}">{$entry.formatted}</span> <a href="javascript:refreshCache({$k})" class="icon"><img src="pics/icons/arrow_refresh.png" class="icon" alt="{tr}Refresh{/tr}"/></a></td>
				</tr>
			{/foreach}
		</table>
	</div>
</div>

<a name='profile-results'></a>
<fieldset><legend>{tr}Profiles{/tr}</legend>
<form method="get" action="tiki-admin.php#profile-results">
<div class="adminoptionbox">
<div class="adminoptionlabel">{tr}Filter the list of profiles{/tr}:</div>
<div class="adminoptionboxchild">
	<div class="adminoptionlabel"><label for="profile">{tr}Profile{/tr}: </label><input type="text" name="profile" id="profile" value="{$profile|escape}" /></div>
	<div class="adminoptionlabel"><label for="category">{tr}Category{/tr}: </label>
	<select name="category" id="category">
							<option value="">{tr}All{/tr}</option>
							{foreach item=cat from=$category_list}
					 			<option value="{$cat|escape}"{if $cat eq $category} selected="selected"{/if}>{$cat|escape}</option>
							{/foreach}
	</select>
	</div>
	<div class="adminoptionlabel"><label for="repository">{tr}Repository{/tr}: </label>
	<select name="repository" id="repository">
							<option value="">{tr}All{/tr}</option>
							{foreach item=source from=$sources}
								<option value="{$source.url|escape}"{if $repository eq $source.url} selected="selected"{/if}>{$source.short|escape}</option>
							{/foreach}
	</select>
	</div>
	<input type="hidden" name="page" value="profiles"/>
</div>
<div align="center"><input type="submit" name="list" value="{tr}List{/tr}" /></div>
</div>
</form>
<br />		<table class="normal">
			<tr>
				<th>{tr}Profile{/tr}</th>
				<th>{tr}Repository{/tr}</th>
				<th>{tr}Category{/tr}</th>
			</tr>
			{foreach key=k item=profile from=$result}
				<tr id="profile-{$k}">
					<td><a href="javascript:showDetails( 'profile-{$k}', '{$profile.domain|escape}', '{$profile.name|escape}' )">{$profile.name|escape}</a>{if $profile.installed} <em>{tr}installed{/tr}</em>{/if}</td>
					<td>{$profile.domain}</td>
					<td>{$profile.category}</td>
				</tr>
			{/foreach}
			{if $result|@count eq '0'}
			<tr><td colspan="3" class="odd">{tr}None{/tr}</td></tr>
			{/if}
		</table>

</fieldset>

<fieldset><legend>{tr}Repositories{/tr}</legend>
<form action="tiki-admin.php?page=profiles" method="post">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="profile_sources">{tr}Repository URLs{/tr}:</label></div>
	<div><textarea id="profile_sources" name="profile_sources" rows="5" cols="60" style="width:95%;">{$prefs.profile_sources|escape}</textarea>
	<br /><em>{tr}Enter multiple repository URLs, one per line{/tr}.</em>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="profile_channels">{tr}Data Channels{/tr}</label>:</div>
	<div><textarea id="profile_channels" name="profile_channels" rows="5" rows="60" style="width:95%;">{$prefs.profile_channels|escape}</textarea>
	<br /><em>{tr}Data channels create a named pipe to run profiles from user space. One channel per line. Each line is comma delimited and contain <strong>channel name, domain, profile, allowed groups</strong>. {/tr}</em>
	<small><a href="http://profiles.tikiwiki.org/Data+Channels">{tr}More information{/tr}</a></small>
	</div>
</div>

<div align="center" style="padding:1em;"><input type="submit" name="config" value="{tr}Save{/tr}" /></div>
</form>
</fieldset>

</td></tr></table>
</div>

<script type="text/javascript">
{foreach item=k from=$oldSources}
	refreshCache({$k});
{/foreach}
</script>
