{* $Id$ *}
<div class="cbox">
	{if !empty($confirmation_text)}
		<div class="cbox-title">{icon _id=information style="vertical-align:middle"} {$confirmation_text}</div>
	{/if}
	{if !empty($confirmation_detail)}
		{$confirm_detail}
	{/if}
	<br />
	<div class="cbox-data">
		<form name='confirm' action="{$confirmaction|escape}" method="post">
			{query _type='form_input' _keepall='y' ticket=$ticket daconfirm='y'}
			{button href="#" _onclick="javascript:document.forms['confirm'].submit();return false;" _text="{tr}Click here to confirm your action{/tr}"}
			{if $prefs.feature_ajax eq 'y' and isset($last_mid_template)}
				{button href=$last_mid_php _template=$last_mid_template _text="{tr}Go back{/tr}"}
			{else}
				{button href="#" _onclick="javascript:history.back(); return false;" _text="{tr}Go back{/tr}"}
			{/if}
			{button href=$prefs.tikiIndex _text="{tr}Return to home page{/tr}"}
		</form>
	</div>
</div>
