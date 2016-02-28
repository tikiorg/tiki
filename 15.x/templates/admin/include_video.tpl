<form class="form-horizontal" action="tiki-admin.php?page=video" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	{tabset name="admin_video"}

		{tab name="{tr}Kaltura{/tr}"}
			<h2>{tr}Kaltura{/tr}</h2>
			{remarksbox type="info" title="{tr}Kaltura Registration{/tr}"}
				{tr}To get a Kaltura Partner ID:{/tr} {tr}Setup your own instance of Kaltura Community Edition (CE){/tr} or <a href="http://corp.kaltura.com/about/signup">{tr}get an account via Kaltura.com{/tr}</a>
			{/remarksbox}

			{button _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
			{if $kaltura_legacyremix eq 'y'}{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix"}{/if}
			{button _text="{tr}Add New Media{/tr}" href="tiki-kaltura_upload.php"}

			<div class="row">
				<div class="form-group col-lg-12 clearfix">
					<div class="pull-right">
						<input type="submit" class="btn btn-default btn-sm" name="video" value="{tr}Change preferences{/tr}">
					</div>
				</div>
			</div>

			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_kaltura visible="always"}
			</fieldset>

			<fieldset>
				<legend>{tr}Plugin to embed in pages{/tr}</legend>
				{preference name=wikiplugin_kaltura}
			</fieldset>

			<fieldset>
				<legend>{tr}Enable related Tracker field types{/tr}</legend>
				{preference name=trackerfield_kaltura}
			</fieldset>

			<fieldset>
				<legend>{tr}Kaltura / Tiki config{/tr}</legend>
				{preference name=kaltura_kServiceUrl}
			</fieldset>

			<fieldset>
				<legend>{tr}Kaltura Partner Settings{/tr}</legend>
				{preference name=kaltura_partnerId}
				{preference name=kaltura_adminSecret}
				{preference name=kaltura_secret}
			</fieldset>

			<br>

			<fieldset>
				<legend>{tr}Kaltura Dynamic Player{/tr}</legend>
				{preference name=kaltura_kdpUIConf}
				{preference name=kaltura_kdpEditUIConf}
				{$kplayerlist}
			</fieldset>

			<br>

			<fieldset>
				<legend>{tr}Kaltura Contribution Wizard{/tr}</legend>
				{$kcwText}
				<div class="adminoptionbox">
					{tr}You can manually edit these values in lib/videogals/standardTikiKcw.xml{/tr}<br>
					{tr}Recreate KCW "uiConf"{/tr} {button _text="{tr}Update{/tr}" kcw_rebuild=1 _keepall='y' _auto_args='*'}
				</div>
			</fieldset>

			<br>

			<fieldset>
				<legend>{tr}Legacy support{/tr}</legend>
				{preference name=kaltura_legacyremix}
			</fieldset>

			<br>

			<div align="center" style="padding:1em;">
				<input type="submit" class="btn btn-default btn-sm" name="video" value="{tr}Change preferences{/tr}" />
			</div>
		{/tab}

	{/tabset}

</form>
