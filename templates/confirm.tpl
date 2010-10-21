{* $Id$ *}
<div class="cbox">
	{if !empty($confirmation_text)}
		<div class="cbox-title">{icon _id=information style="vertical-align:middle"} {$confirmation_text}</div>
	{/if}
	{if !empty($confirm_detail)}
		{$confirm_detail}
	{/if}
	<br />
	<div class="cbox-data">
		<form id='confirm' action="{$confirmaction|escape}" method="post">
			<div>
				{query _type='form_input' _keepall='y' ticket=$ticket daconfirm='y'}
				{button href="#" _onclick="javascript:document.forms['confirm'].submit();return false;" _text="{tr}Click here to confirm your action{/tr}" _ajax="n"}
				{if $prefs.ajax_xajax eq 'y' and isset($last_mid_template)}
					{button href=$last_mid_php _template=$last_mid_template _text="{tr}Go back{/tr}"}
				{else}
					{button href="#" _onclick="javascript:history.back(); return false;" _text="{tr}Go back{/tr}" _ajax="n"}
				{/if}
				{button href=$prefs.tikiIndex _text="{tr}Return to home page{/tr}"}
			</div>
		</form>
	</div>
</div>
