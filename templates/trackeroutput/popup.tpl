{capture name=popup}
	<div class="panel panel-default">
		<table class="table table-bordered item">
			{foreach from=$popupFields|default:null item=field}
				 <tr><th>{$field.name|escape}</th><td>{trackeroutput field=$field item=$popupItem showpopup=n showlinks=n}</td></tr>
			{/foreach}
		</table>
	</div>
{/capture}
{popup text=$smarty.capture.popup|escape:"javascript"|escape:"html" fullhtml="1" hauto=true vauto=true trigger="hover"}
