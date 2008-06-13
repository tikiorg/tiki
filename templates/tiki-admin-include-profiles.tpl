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
	req.send(null);
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
				}
				else if( data.installable )
				{
					var form = document.createElement( 'form' );
					var p = document.createElement('p');
					var submit = document.createElement('input');
					var hidden = document.createElement('input');
					form.method = 'post';
					form.action = document.location.href;

					form.appendChild(p);
					submit.type = 'submit';
					submit.name = 'install';
					submit.value = 'Install Now';
					p.appendChild(submit);
					hidden.type = 'hidden';
					hidden.name = 'url';
					hidden.value = data.url;
					p.appendChild(hidden);

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

				cell.appendChild( body );

				row.id = nid;
				prev.parentNode.insertBefore( row, prev.nextSibling );
			}
		}
	}
	req.send(null);
} // }}}
{/literal}
</script>
<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip"><a class="rbox-link" href="http://profiles.tikiwiki.org">{tr}Tikiwiki Profiles{/tr}</a></div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}Configuration{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=profiles" method="post">
        <table class="admin"><tr>
			<col width="30%"/>
			<col width="70%"/>
		
			<tr>
			<td class="form">{tr}Profile repositories{/tr}:
			<div>
			<small>{tr}Profiles can be installed from multiple repositories. Enter one repository URL per line.{/tr}</small>
			</div>
			</td>
			<td>
				<textarea name="profile_sources" rows="5">{$prefs.profile_sources|escape}</textarea>
			</td>
		</tr><tr>		
          <td colspan="2" class="button"><input type="submit" name="config" value="{tr}Save{/tr}" /></td>
		  </tr>
		</table>
	</form>
  </div>
</div>
<div class="cbox">
  <div class="cbox-title">{tr}Profile repository status{/tr}</div>
  <div class="cbox-data">
  	<table class="normal">
		<tr>
			<th>Profile repository</th>
			<th>Status</th>
			<th>Last update</th>
		</tr>
		{foreach key=k item=entry from=$sources}
		<tr>
			<td>{$entry.short}</td>
			<td><img id="profile-status-{$k}" src="img/icons2/status_{$entry.status}.gif"/></td>
			<td><span id="profile-date-{$k}">{$entry.formatted}</span> <a href="javascript:refreshCache({$k})"><img src="pics/icons/arrow_refresh.png" class="icon" alt="{tr}Refresh{/tr}"/></a></td>
		</tr>
		{/foreach}
	</table>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Profile list{/tr}</div>
  <div class="cbox-data">
  	<form method="get" action="tiki-admin.php">
        <table class="admin"><tr>
			<col width="30%"/>
			<col width="70%"/>
		
			<tr>
			<td class="form">{tr}Repository{/tr}:</td>
			<td>
				<select name="repository">
					<option value="">All</option>
				{foreach item=source from=$sources}
					<option value="{$source.url|escape}"{if $repository eq $source.url} selected="selected"{/if}>{$source.short|escape}</option>
				{/foreach}
				</select><input type="hidden" name="page" value="profiles"/>
			</td>
		</tr><tr>
			<td class="form">{tr}Category{/tr}:</td>
			<td><input type="text" name="category" value="{$category|escape}"/></td>
		</tr><tr>
			<td class="form">{tr}Profile{/tr}:</td>
			<td><input type="text" name="profile" value="{$profile|escape}"/></td>
		</tr><tr>
          <td colspan="2" class="button"><input type="submit" name="list" value="{tr}List{/tr}" /></td>
		  </tr>
		</table>
	</form>
	<table class="normal">
		<tr>
			<th>{tr}Profile{/tr}</th>
			<th>{tr}Repository{/tr}</th>
			<th>{tr}Category{/tr}</th>
		</tr>
		{foreach key=k item=profile from=$result}
		<tr id="profile-{$k}">
			<td><a href="javascript:showDetails( 'profile-{$k}', '{$profile.domain|escape}', '{$profile.name|escape}' )">{$profile.name|escape}</a></td>
			<td>{$profile.domain}</td>
			<td>{$profile.category}</td>
		</tr>
		{/foreach}
	</table>
  </div>
</div>


<script type="text/javascript">
{foreach item=k from=$oldSources}
	refreshCache({$k});
{/foreach}
</script>
