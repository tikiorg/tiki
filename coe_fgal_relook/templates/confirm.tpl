{* $Id$ *}
<div class="cbox">
	{if !empty($confirmation_text)}
		<div class="cbox-title">{icon _id=information style="vertical-align:middle"} {$confirmation_text}</div>
	{/if}
	{if !empty($confirm_detail)}
		{$confirm_detail)}
	{/if}
	<br />
	<div class="cbox-data">
		<form name='confirm' id="formconfirm" action="{$confirmaction|escape}" method="post">
			{query _type='form_input' _keepall='y' ticket=$ticket daconfirm='y'}
			{if $popup}
				{button href="#" _onclick="FileGallery.open(document.forms.confirm.action, 'formconfirm');FileGallery.closeDialog();return false;" _text="{tr}Delete{/tr}"}
				{button href="#" _onclick="FileGallery.closeDialog();return false;" _text="{tr}Cancel{/tr}"}
			{else}
				{button href="#" _onclick="javascript:document.forms['confirm'].submit();return false;" _text="{tr}Click here to confirm your action{/tr}"}
				{if $prefs.feature_ajax eq 'y' and isset($last_mid_template)}
					{button href=$last_mid_php _template=$last_mid_template _text="{tr}Go back{/tr}"}
				{else}
					{button href="#" _onclick="javascript:history.back(); return false;" _text="{tr}Go back{/tr}"}
				{/if}
				{button href=$prefs.tikiIndex _text="{tr}Return to home page{/tr}"}
			{/if}
		</form>
	</div>
</div>
