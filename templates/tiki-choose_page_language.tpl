{extends 'layout_edit.tpl'}

{block name=title}
<h1>{tr}Please choose the language for this page:{/tr}</h1>
{/block}

{block name=content}
<div class="panel-body" style="overflow: visible;">
	<p>
		<strong>{tr _0=$page|escape}Page: "%0"{/tr}</strong>
	</p>
	<form method="post" action="tiki-editpage.php?page={$page|escape:'url'}" id='editpageform' name='editpageform'>
		{* Repeat all arguments from the page creation request *}
		{query _type='form_input' _keepall='y' need_lang='n'}
		<div class="form-group">
			<div class="col-sm-6 input-group">
				<select name="lang" class="form-control">
					{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}"{if $languages[ix].value|escape == $default_lang} selected="selected"{/if}>
							{$languages[ix].name}
						</option>
					{/section}
				</select>
				<span class="input-group-btn">
					<input type="submit" class="btn btn-primary" name="select_language" value="{tr}Choose language{/tr}" onclick="needToConfirm=false;">
				</span>
			</div>
		</div>
	</form>
</div>
{/block}
