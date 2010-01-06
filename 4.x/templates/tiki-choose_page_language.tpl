<H1>{tr}Please choose the language for this page:{/tr}</H1>


<b>Page: {$page}</b>

<p>

<form  enctype="multipart/form-data" method="get" action="tiki-editpage.php?page={$page|escape:'url'}" id='editpageform' name='editpageform'>
	{* Repeat all arguments from the page creation request *}
	{foreach from=$_REQUEST key=request_key item=request_val}
		<input type="hidden" name="{$request_key}" value="{$request_val}"/>
	{/foreach}

	<input type="hidden" name="need_lang" value="n"/>

	<select name="lang">
		{section name=ix loop=$languages}
		<option value="{$languages[ix].value|escape}"{if $languages[ix].value|escape == $default_lang} selected="selected"{/if}>{$languages[ix].name}</option>
		{/section}

	</select>
	<input type="submit" name="select_language" value="{tr}Choose language{/tr}" onclick="needToConfirm=false;" />
</form>
