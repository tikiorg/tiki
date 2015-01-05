{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $message}
		{remarksbox type="info" title="{tr}Upload Successful{/tr}"}
			{$message|escape}
		{/remarksbox}
	{else}
		<div id="kcwFlashObject" class="kcwFlashObject kcwUIConf"></div>

		<form name="kcw_{$identifier|escape}" id="kcw_{$identifier|escape}" action="{service controller=kaltura action=upload}" method="post" enctype="multipart/form-data" style="margin:0px; padding:0px">
			<input type="hidden" name="kcw" value="true">
		</form>

		{jq notonready=true}
			function afterAddEntry_{{$identifier|escape}} (entries) {

				var $f = $("#kcw_{{$identifier|escape}}");
				$.each(entries, function (k, item) {
					$f.append($('<input type="hidden" name="entryId[]">').val(item.entryId));
				});
				if ($("input[name=from]", $f).val() === "picker") {
					if (entries.length) {
						$("#" + $("input[name=area]", $f).val()).val(entries[0].entryId).focus();
					}
					$f.parents(".ui-dialog").empty().dialog("close").dialog("destroy");	// doesn't seem to want to close ?
					return true;
				}

				$f.submit();
			}

			var params = {
				allowScriptAccess: "always",
				allowNetworking: "all",
				wmode: "opaque"
			};

			var flashVars = {{$flashVars}};
			swfobject.embedSWF("{{$prefs.kaltura_kServiceUrl}}kcw/ui_conf_id/{{$prefs.kaltura_kcwUIConf|escape}}", "kcwFlashObject", "680", "360", "9.0.0", "expressInstall.swf", flashVars, params);

		{/jq}

	{/if}
{/block}
