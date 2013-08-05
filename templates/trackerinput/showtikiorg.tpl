<h5 id="showtikiorg{$field.fieldId}" class="showactive{$field.fieldId}" {if $field.status neq 'ACTIV'}style="display: none;"{/if}>This bug has been demonstrated on show.tiki.org</h5>
<h5 class="shownone{$field.fieldId}" {if $field.status neq 'NONE'}style="display: none;"{/if}>Please demonstrate your bug on show.tiki.org</h5>
{if !$field.id}
{remarksbox type="info" title="Bug needs to be created first" close="n"}
<p>You will be able to demonstrate your bug on a show.tiki.org instance once it has been created.</p>
{/remarksbox}
{else}
<div class="showsnapshot{$field.fieldId}" style="display: none;">
{remarksbox type="error" title="Show.tiki.org snapshot creation is in progress" close="n"}
<p>Show.tiki.org snapshot creation is in progress... Please monitor <a href="{$field.snapshoturl|escape}" target="_blank">{$field.snapshoturl|escape}</a> for progress. <strong>Note that if you get a popup asking for a username/password, please just enter "show" and "show".</strong></p> 
{/remarksbox}
</div>
<div class="showdestroy{$field.fieldId}" style="display: none;">
{remarksbox type="error" title="Show.tiki.org instance destruction in progress" close="n"}
<p>Show.tiki.org instance destruction is in progress... Please wait...</p>
{/remarksbox}
</div>
<div class="showdisconnected{$field.fieldId}" {if $field.status neq 'DISCO'}style="display: none;"{/if}>
{remarksbox type="error" title="Show.tiki.org is currently unavailable" close="n"}
<p>Unable to connect to show.tiki.org. Please let us know of the problem so that we can do something about it. Thanks.</p>
{/remarksbox}
</div>
<div class="showfail{$field.fieldId}" {if $field.status neq 'FAIL'}style="display: none;"{/if}>
{remarksbox type="error" title="Unable to get information from show.tiki.org" close="n"}
<p>Unable to get information from show.tiki.org. Please let us know of the problem so that we can do something about it. Thanks.</p>
{/remarksbox}
</div>
<div class="showbuilding{$field.fieldId}" {if $field.status neq 'BUILD'}style="display: none;"{/if}>
{remarksbox type="error" title="Instance is being created" close="n"}
<p>Show.tiki.org is in the progress of creating the new instance. Please continue waiting for a minute or two. If this continues on for more than 10 minutes, please let us know of the problem so that we can do something about it. Thanks.</p>
{/remarksbox}
</div>
<div class="shownone{$field.fieldId}" {if $field.status neq 'NONE'}style="display: none;"{/if}>
{remarksbox type="info" title="About show.tiki.org" close="n"}
<p>To help developers solve the bug, we kindly request that you demonstrate your bug on a show.tiki.org instance. To start, simply select a version and click on "Create show.tiki.org instance". Once the instance is ready (in a minute or two), as indicated in the status window below, you can then access that instance, login (the initial admin username/password is "admin") and configure the Tiki to demonstrate your bug. Priority will be given to bugs that have been demonstrated on show.tiki.org.</p> 
{/remarksbox}
Version: <select name="svntag">
<option selected="selected">trunk</option>
<option>11.0</option>
<option>10.2</option>
<option>10.1</option>
<option>10.0</option>
<option>9.4</option>
<option>9.3</option>
<option>9.2</option>
<option>9.1</option>
<option>9.0</option>
<option>6.10</option>
<option>6.9</option>
<option>6.8</option>
<option>6.7</option>
<option>6.6</option>
<option>6.5</option>
<option>6.4</option>
<option>6.3</option>
<option>6.2</option>
<option>6.1</option>
<option>6.0</option>
</select>
{button href="#showtikiorg{$field.fieldId}" _onclick="showtikiorg_process('create');"  _text="{tr}Create show.tiki.org instance{/tr}"}
</div>
<div class="showactive{$field.fieldId}" {if $field.status neq 'ACTIV'}style="display: none;"{/if}>
{remarksbox type="info" title="Accessing the Tiki instance that demonstrates this bug" close="n"}
<p>The URL for the show.tiki.org instance that demonstrates this bug is at: <a href="{$field.showurl|escape}" target="_blank">{$field.showurl|escape}</a>. <strong>Note that if you get a popup asking for a username/password, please just enter "show" and "show". This is different from the initial login and password for a new Tiki which is "admin" and "admin".</strong></p>
{/remarksbox}
{remarksbox type="info" title="Snapshots" close="n"}
<p>Snapshots are database dumps of the configuration that developers can download for debugging. Once you have reproduced your bug on the show.tiki.org instance, create a snapshot that can then be downloaded by developers for further investigation.</p>
<p>Snapshots can be accessed at: <a href="{$field.snapshoturl|escape}" target="_blank">{$field.snapshoturl|escape}</a>. <strong>Note that if you get a popup asking for a username/password, please just enter "show" and "show".</strong></p>
{button href="#showtikiorg{$field.fieldId}" _onclick="showtikiorg_process('snapshot');"  _text="{tr}Create new snapshot{/tr}"}
{/remarksbox}
{if $field.canDestroy}
{button href="#showtikiorg{$field.fieldId}" _onclick="showtikiorg_process('destroy');"  _text="{tr}Destroy this show.tiki.org instance{/tr}"}
{/if}
</div>

{if $field.debugmode}
{remarksbox type="info" title="Debug Mode Information" close="n"}
<div id="showdebugoutput{$field.fieldId}">-{$field.status|escape}- {$field.debugoutput|escape}</div>
{button href="#showtikiorg{$field.fieldId}" _onclick="showtikiorg_process('info');"  _text="{tr}Get instance information and refresh cache{/tr}"}
{/remarksbox}
{/if}
{jq notonready=true}
function showtikiorg_process(action) {
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
		success:  function(data) {
			var debugoutput = data.debugoutput;
			$('#showdebugoutput{{$field.fieldId}}').html(data.debugoutput);
			if (data.status == 'DISCO') {
				$('.showdisconnected{{$field.fieldId}}').show();
				$('.showfail{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').hide();
				$('.showdestroy{{$field.fieldId}}').hide();	
				$.modal();
			} else if (data.status == 'FAIL') {
				$('.showfail{{$field.fieldId}}').show();
				$('.showdisconnected{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').hide();
				$('.showdestroy{{$field.fieldId}}').hide();
				$.modal();
			} else if (data.status == 'BUILD') {
				$('.showbuilding{{$field.fieldId}}').show();
				$('.shownone{{$field.fieldId}}').hide();
				$('.showactive{{$field.fieldId}}').hide();
				$('.showfail{{$field.fieldId}}').hide();
				$('.showdisconnected{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').hide();
				$('.showdestroy{{$field.fieldId}}').hide();
				setTimeout("showtikiorg_process('info')",5000);
				$.modal(tr('Instance is being created... Please wait... This might take a minute or two.'));
			} else if (data.status == 'NONE') {
				$('.shownone{{$field.fieldId}}').show();
				$('.showactive{{$field.fieldId}}').hide();
				$('.showbuilding{{$field.fieldId}}').hide();
				$('.showfail{{$field.fieldId}}').hide();
				$('.showdisconnected{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').hide();
				$('.showdestroy{{$field.fieldId}}').hide();
				$.modal();
			} else if (data.status == 'ACTIV') {
				$('.showactive{{$field.fieldId}}').show();
				$('.showbuilding{{$field.fieldId}}').hide();
				$('.shownone{{$field.fieldId}}').hide();
				$('.showfail{{$field.fieldId}}').hide();
				$('.showdisconnected{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').hide();
				$('.showdestroy{{$field.fieldId}}').hide();
				$.modal();
			} else if (data.status == 'SNAPS') {
				$('.showactive{{$field.fieldId}}').show();
				$('.showbuilding{{$field.fieldId}}').hide();
				$('.shownone{{$field.fieldId}}').hide();
				$('.showfail{{$field.fieldId}}').hide();
				$('.showdisconnected{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').show();
				$('.showdestroy{{$field.fieldId}}').hide();
			} else if (data.status == 'DESTR') {
				$('.showactive{{$field.fieldId}}').hide();
				$('.showbuilding{{$field.fieldId}}').hide();
				$('.shownone{{$field.fieldId}}').hide();
				$('.showfail{{$field.fieldId}}').hide();
				$('.showdisconnected{{$field.fieldId}}').hide();
				$('.showsnapshot{{$field.fieldId}}').hide();
				$('.showdestroy{{$field.fieldId}}').show();
				setTimeout("showtikiorg_process('info')",5000);
				$.modal(tr('Instance is being destroyed... Please wait...'));
			}
		}
	});
	return false;
}
{/jq}
{/if}
