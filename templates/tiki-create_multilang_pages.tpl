<title>{tr}Quick Create Multilanguage Pages{/tr}</title>

{if $display_creation_result == 'y'}
	{remarksbox type="feedback"}
		{if count($pages_created) > 0}
			<b>{tr}Following pages were created, as translations of each other:{/tr}</b>
			<p>
			<ul>
				{foreach from=$pages_created key=lang item=page_name}
					<li>{$lang}: {$page_links[$page_name]}</li>
				{/foreach}
			</ul>
		{/if}

		{if count($pages_not_created) > 0}
			<b>{tr}Following pages were not created{/tr} ({tr}page already exists{/tr}):</b>
			<p>
			<ul>
				{foreach from=$pages_not_created key=lang item=page_name}
					<li>{$lang}: {$page_links[$page_name]}</li>
				{/foreach}
			</ul>
		{/if}
	{/remarksbox}
{/if}

<h2>{tr}Enter the names of a new page page in various languages.{/tr}</h2>

<p></p>

<form enctype="multipart/form-data" method="post" action="tiki-create_multilang_pages.php" id='create_multilang_pages_form' name='create_multilang_pages_form'>
	<input type="hidden" name="create_pages" value="">
	<b>{tr}Preferred languages{/tr}</b>
	<p></p>
	<table>
		{foreach from=$user_languages key=index item=lang_id}
			<tr>
				<td>{$lang_id}:</td> <td>&nbsp;</td>
				<td><input type="text" name="page_name_{$lang_id}"></td>
			</tr>
		{/foreach}

	</table>

	<p></p>

	<b>{tr}Other languages{/tr}</b>

	<table>
		{foreach from=$other_languages key=index item=lang_id}
			<tr>
				<td>{$lang_id}:</td> <td>&nbsp;</td>
				<td><input type="text" name="page_name_{$lang_id}"></td>
			</tr>
		{/foreach}

	</table>

	<p></p>

	<input type="submit" class="btn btn-default btn-sm" id="create_multilang_pages_submit_button" value="{tr}Create pages{/tr}">

</form>
