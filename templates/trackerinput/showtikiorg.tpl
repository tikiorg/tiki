<div id="testingstatus" style="display:none">{$field.status|escape}</div>
<h5 id="showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" class="showactive{$field.fieldId}_{$item.itemId}" {if $field.status neq 'ACTIV'}style="display: none;"{/if}>{tr}This bug has been demonstrated on show.tiki.org{/tr}</h5>
<h5 class="shownone{$field.fieldId}_{$item.itemId}" {if $field.status neq 'NONE'}style="display: none;"{/if}>{tr}Please demonstrate your bug on show.tiki.org{/tr}</h5>
{if !$field.id}
	{remarksbox type="info" title="{tr}Bug needs to be created first{/tr}" close="n"}
		<p>{tr}You will be able to demonstrate your bug on a show.tiki.org instance once it has been created.{/tr}</p>
	{/remarksbox}
{else}
	<div class="showsnapshot{$field.fieldId}_{$item.itemId}" style="display: none;">
		{remarksbox type="error" title="{tr}Show.tiki.org snapshot creation is in progress{/tr}" close="n"}
			<p>{tr _0="<a class=\"snapshoturl{$field.fieldId}_{$item.itemId}\" href=\"http://{$field.snapshoturl|escape}\" target=\"_blank\">http://{$field.snapshoturl|escape}</a>"}Show.tiki.org snapshot creation is in progress... Please monitor %0 for progress.{/tr} <strong>{tr}Note that if you get a popup asking for a username/password, please just enter "show" and "show".{/tr}</strong></p>
		{/remarksbox}
	</div>
	<div class="showresetok{$field.fieldId}_{$item.itemId}" style="display: none;">
		{remarksbox type="info" title="{tr}Password reset{/tr}" close="n"}
			<p>{tr}Password reset was successful{/tr}</p>
		{/remarksbox}
	</div>
	<div class="showresetnok{$field.fieldId}_{$item.itemId}" style="display: none;">
		{remarksbox type="error" title="{tr}Password reset{/tr}" close="n"}
			<p>{tr}Password reset failed{/tr}</p>
		{/remarksbox}
	</div>
	<div class="showdestroy{$field.fieldId}_{$item.itemId}" style="display: none;">
		{remarksbox type="error" title="{tr}Show.tiki.org instance destruction in progress{/tr}" close="n"}
			<p>{tr}Show.tiki.org instance destruction is in progress... Please wait...{/tr}</p>
		{/remarksbox}
	</div>
	<div class="showinvalidkeys{$field.fieldId}_{$item.itemId}" {if $field.status neq 'INVKEYS'}style="display: none;"{/if}>
		{remarksbox type="error" title="{tr}Show.tiki.org is not configured properly{/tr}" close="n"}
			<p>{tr}The public/private keys configured to connect to show.tiki.org were not accepted. Please make sure you are using RSA keys. Thanks.{/tr}</p>
		{/remarksbox}
	</div>
	<div class="showdisconnected{$field.fieldId}_{$item.itemId}" {if $field.status neq 'DISCO'}style="display: none;"{/if}>
		{remarksbox type="error" title="{tr}Show.tiki.org is currently unavailable{/tr}" close="n"}
			<p>{tr}Unable to connect to show.tiki.org. Please let us know of the problem so that we can do something about it. Thanks.{/tr}</p>
		{/remarksbox}
	</div>
	<div class="showmaint{$field.fieldId}_{$item.itemId}" {if $field.status neq 'MAINT'}style="display: none;"{/if}>
		{remarksbox type="error" title="{tr}Show.tiki.org is under maintenance{/tr}" close="n"}
			<p>{tr}Show.tiki.org is currently under maintenance. Sorry for the inconvenience.{/tr} {$field.maintreason|escape}</p>
		{/remarksbox}
	</div>
	<div class="showfail{$field.fieldId}_{$item.itemId}" {if $field.status neq 'FAIL'}style="display: none;"{/if}>
		{remarksbox type="error" title="{tr}Unable to get information from show.tiki.org{/tr}" close="n"}
			<p>{tr}Unable to get information from show.tiki.org. Please let us know of the problem so that we can do something about it. Thanks.{/tr}</p>
		{/remarksbox}
	</div>
	<div class="showbuilding{$field.fieldId}_{$item.itemId}" {if $field.status neq 'BUILD'}style="display: none;"{/if}>
		{remarksbox type="error" title="{tr}Instance is being created{/tr}" close="n"}
			<p>{tr}Show.tiki.org is in the progress of creating the new instance. Please continue waiting for a minute or two. If this continues on for more than 10 minutes, please let us know of the problem so that we can do something about it. Thanks.{/tr}</p>
		{/remarksbox}
	</div>
	<div class="shownone{$field.fieldId}_{$item.itemId}" {if $field.status neq 'NONE'}style="display: none;"{/if}>
		{remarksbox type="info" title="{tr}About show.tiki.org{/tr}" close="n"}
			<p>{tr}To help developers solve the bug, we kindly request that you demonstrate your bug on a show.tiki.org instance. To start, simply select a version and click on "Create show.tiki.org instance". Once the instance is ready (in a minute or two), as indicated in the status window below, you can then access that instance, login (the initial admin username/password is "admin") and configure the Tiki to demonstrate your bug. Priority will be given to bugs that have been demonstrated on show.tiki.org.{/tr}</p>
		{/remarksbox}
		{tr}Version:{/tr} 
		<select name="svntag" class="form-control">
			<option selected="selected">trunk</option>
			<option>18.x</option>
			<option>17.x</option>
			<option>16.x</option>
			<option>15.x</option>
			<option>12.x</option>
		</select>
		{button href="#showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" _onclick="showtikiorg_process{$field.fieldId}_{$item.itemId}('create');" _text="{tr}Create show.tiki.org instance{/tr}"}
	</div>
	<div class="showactive{$field.fieldId}_{$item.itemId}" {if $field.status neq 'ACTIV'}style="display: none;"{/if}>
		{remarksbox type="info" title="{tr}Accessing the Tiki instance that demonstrates this bug{/tr}" close="n"}
			<p>{tr _0="<a class=\"showurl{$field.fieldId}_{$item.itemId}\" href=\"http://{$field.showurl|escape}\" target=\"_blank\">http://{$field.showurl|escape}</a>"}The URL for the show.tiki.org instance that demonstrates this bug is at: %0.{/tr} <strong>{tr}Note that if you get a popup asking for a username/password, please just enter "show" and "show". This is different from the initial login and password for a new Tiki which is "admin" and "admin".{/tr}</strong></p>
			<p>{tr _0="<a class=\"showlogurl{$field.fieldId}_{$item.itemId}\" href=\"http://{$field.showlogurl|escape}\" target=\"_blank\">http://{$field.showlogurl|escape}</a>"}The install log is at %0{/tr}</p>
			<p><strong>{tr}Note that if you see PHP errors or a Tiki claiming to be missing third party software, the instance creation is probably not finished. Please wait 1 minute and reload.{/tr}</strong></p>
		{/remarksbox}
		{remarksbox type="info" title="{tr}Snapshots{/tr}" close="n"}
			<p>{tr}Snapshots are database dumps of the configuration that developers can download for debugging. Once you have reproduced your bug on the show.tiki.org instance, create a snapshot that can then be downloaded by developers for further investigation.{/tr}</p>
			<p>{tr _0="<a class=\"snapshoturl{$field.fieldId}_{$item.itemId}\" href=\"http://{$field.snapshoturl|escape}\" target=\"_blank\">http://{$field.snapshoturl|escape}</a>"}Snapshots can be accessed at: %0.{/tr} <strong>{tr}Note that if you get a popup asking for a username/password, please just enter "show" and "show".{/tr}</strong></p>
			{button href="#showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" _onclick="showtikiorg_process{$field.fieldId}_{$item.itemId}('snapshot');" _text="{tr}Create new snapshot{/tr}"}
		{/remarksbox}
		{if $field.canDestroy}
			{button href="#showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" _onclick="showtikiorg_process{$field.fieldId}_{$item.itemId}('destroy');" _text="{tr}Destroy this show.tiki.org instance{/tr}"}
			{button href="#showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" _onclick="showtikiorg_process{$field.fieldId}_{$item.itemId}('reset');" _text="{tr}Reset password to 12345{/tr}"}
		{/if}
		<span class="buttonupdate{$field.fieldId}_{$item.itemId}" {if $field.version != 'trunk' && $field.version != '18.x' && $field.version != '17.x' && $field.version != '16.x' && $field.version != '15.x' && $field.version != '12.x'}style="display: none;"{/if}>
			{button href="#showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" _onclick="showtikiorg_process{$field.fieldId}_{$item.itemId}('update');" _text="{tr}SVN update{/tr}"}
		</span>
	</div>

	{if $field.debugmode}
		{remarksbox type="info" title="{tr}Debug Mode Information{/tr}" close="n"}
			<div class="showdebugoutput{$field.fieldId}_{$item.itemId}">-{$field.status|escape}- {$field.debugoutput|escape}</div>
			{button href="#showtikiorg{$field.fieldId}_{$item.itemId}{if isset($context.list_mode)}_view{/if}" _onclick="showtikiorg_process{$field.fieldId}_{$item.itemId}('info');" _text="{tr}Get instance information and refresh cache{/tr}"}
		{/remarksbox}
	{/if}
	{jq notonready=true}
	function showtikiorg_process{{$field.fieldId}}_{{$item.itemId}}(action) {
		var request = {
			id: {{$field.id}},
			userid: {{$field.userid}},
			username: '{{$field.username}}',
			fieldId: {{$field.fieldId}},
			command: action,
			svntag: $("select[name='svntag']").val()

		};
		$.ajax({
			url: $.service('showtikiorg', 'process'),
			data: request,
			dataType: 'json',
			type: 'POST',
			success: function(data) {
				var debugoutput = data.debugoutput;
				//$('#testingstatus').html(data.status);
				$('.showdebugoutput{{$field.fieldId}}_{{$item.itemId}}').html(data.debugoutput);
				if (data.version == '12.x' || data.version == '15.x' || data.version == '16.x' || data.version == '17.x' || data.version == '18.x' || data.version == 'trunk') {
					$('.buttonupdate{{$field.fieldId}}_{{$item.itemId}}').show();
				}
				if (data.status == 'DISCO') {
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$.tikiModal();
				} else if (data.status == 'INVKEYS') {
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$.tikiModal();
				} else if (data.status == 'MAINT') {
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$.tikiModal();
				} else if (data.status == 'FAIL') {
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$.tikiModal();
				} else if (data.status == 'BUILD') {
					$('.showbuilding{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.shownone{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showactive{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					setTimeout("showtikiorg_process{{$field.fieldId}}_{{$item.itemId}}('info')",5000);
					$.tikiModal(tr('Instance is being created... Please wait... This might take a minute or two.'));
				} else if (data.status == 'NONE') {
					$('.shownone{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showactive{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showbuilding{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$.tikiModal();
				} else if (data.status == 'ACTIV') {
					$('.showactive{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showbuilding{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.shownone{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showurl{{$field.fieldId}}_{{$item.itemId}}').attr("href", "http://" + data.showurl).html("http://" + data.showurl);
					$('.showlogurl{{$field.fieldId}}_{{$item.itemId}}').attr("href", "http://" + data.showlogurl).html("http://" + data.showlogurl);
					$('.snapshoturl{{$field.fieldId}}_{{$item.itemId}}').attr("href", "http://" + data.snapshoturl).html("http://" + data.snapshoturl);
					$.tikiModal();
				} else if (data.status == 'SNAPS') {
					$('.showactive{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showbuilding{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.shownone{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showresetok{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showresetnok{{$field.fieldId}}_{{$item.itemId}}').hide();
				} else if (data.status == 'RESOK') {
					$('.showresetok{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showresetnok{{$field.fieldId}}_{{$item.itemId}}').hide();
				} else if (data.status == 'RENOK') {
					$('.showresetnok{{$field.fieldId}}_{{$item.itemId}}').show();
					$('.showresetok{{$field.fieldId}}_{{$item.itemId}}').hide();
				} else if (data.status == 'DESTR') {
					$('.showactive{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showbuilding{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.shownone{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showfail{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdisconnected{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showinvalidkeys{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showmaint{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showsnapshot{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showresetnok{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showresetok{{$field.fieldId}}_{{$item.itemId}}').hide();
					$('.showdestroy{{$field.fieldId}}_{{$item.itemId}}').show();
					setTimeout("showtikiorg_process{{$field.fieldId}}_{{$item.itemId}}('info')",5000);
					$.tikiModal(tr('Instance is being destroyed... Please wait...'));
				}
			}
		});
		return false;
	}
	{/jq}
{/if}
