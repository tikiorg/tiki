<form class="admin" method="post" action="tiki-admin.php?page=sefurl">
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl" name="feature_sefurl" {if $prefs.feature_sefurl eq 'y'}checked="checked"{/if} />
	</div>
	<div>
		<label for="feature_sefurl">{tr}Search engine friendly url{/tr}</label>
		{if $prefs.feature_help eq 'y'} {help url="Rewrite+Rules" desc="{tr}Search engine friendly url{/tr}"}{/if} <br />
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl_filter" name="feature_sefurl_filter" {if $prefs.feature_sefurl_filter eq 'y'}checked="checked"{/if} />
	</div>
	<div>
		<label for="feature_sefurl_filter">{tr}Search engine friendly url Postfilter{/tr}</label>
		{if $prefs.feature_help eq 'y'} {help url="Rewrite+Rules" desc="{tr}Search engine friendly url{/tr}"}{/if} <br />
	</div>
</div>

<input type="submit" name="save" value="{tr}Change preferences{/tr}" />
</form>
