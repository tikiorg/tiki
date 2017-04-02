<form class="form-horizontal" action="tiki-admin.php?page=video" method="post">
	{include file='access/include_ticket.tpl'}

	{tabset name="admin_video"}

		{tab name="{tr}Kaltura{/tr}"}
			<br>
			{remarksbox type="info" title="{tr}Kaltura Registration{/tr}"}
				{tr}To get a Kaltura Partner ID:{/tr} {tr}Setup your own instance of Kaltura Community Edition (CE){/tr} or <a href="http://corp.kaltura.com/about/signup">{tr}get an account via Kaltura.com{/tr}</a>
			{/remarksbox}

			{button _text="{tr}List Media{/tr}" href="tiki-list_kaltura_entries.php"}
			{if $kaltura_legacyremix eq 'y'}{button _text="{tr}List Remix Entries{/tr}" href="tiki-list_kaltura_entries.php?list=mix"}{/if}
			{button _text="{tr}Add New Media{/tr}" href="tiki-kaltura_upload.php"}

			<div class="row">
				<div class="form-group col-lg-12 clearfix">
					{include file='admin/include_apply_top.tpl'}
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
				<legend>{tr}Enable related tracker field types{/tr}</legend>
				{preference name=trackerfield_kaltura}
			</fieldset>

			<fieldset>
				<legend>{tr}Kaltura / Tiki config{/tr}</legend>
				{preference name=kaltura_kServiceUrl}
			</fieldset>

			<fieldset>
				<legend>{tr}Kaltura partner settings{/tr}</legend>
				{preference name=kaltura_partnerId}
				{preference name=kaltura_adminSecret}
				{preference name=kaltura_secret}
			</fieldset>

			<br>

			<fieldset>
				<legend>{tr}Kaltura dynamic player{/tr}</legend>
				{preference name=kaltura_kdpUIConf}
				{preference name=kaltura_kdpEditUIConf}
				{$kplayerlist}
			</fieldset>

			<br>

			<fieldset>
				<legend>{tr}Kaltura contribution wizard{/tr}</legend>
				{$kcwText}
				<div class="adminoptionbox">
					{tr}You can manually edit these values in lib/videogals/standardTikiKcw.xml{/tr}<br>
					{tr}Recreate KCW "uiConf"{/tr} {button _class="timeout" _text="{tr}Update{/tr}" kcw_rebuild=1 _keepall='y' _auto_args='*'}
				</div>
			</fieldset>

			<br>

			<fieldset>
				<legend>{tr}Legacy support{/tr}</legend>
				{preference name=kaltura_legacyremix}
			</fieldset>

			<br>
		{/tab}

		{tab name="{tr}Interface{/tr}" key=interface}
			<br>
			<fieldset class="table clearfix featurelist">
				<legend> {tr}jQuery plugins and add-ons{/tr} </legend>
				{preference name=jquery_fitvidjs}
			</fieldset>
		{/tab}

	{/tabset}
	{include file='admin/include_apply_bottom.tpl'}
</form>
