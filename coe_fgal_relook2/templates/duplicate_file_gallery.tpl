{* $Id$ *}
{if $tiki_p_create_file_galleries eq 'y'}
	<h2>{tr}Duplicate File Gallery{/tr}</h2>
	<form action="tiki-list_file_gallery.php{if $filegals_manager neq ''}?filegals_manager={$filegals_manager}{/if}" method="post">
		<table class="formcolor">
			<tr>
				<td>
					<label for="name">{tr}Name{/tr}:</label>
				</td>
				<td>
					<input type="text" size="50" id="name" name="name" value="{$name|escape}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="description">{tr}Description{/tr}:</label>
				</td>
				<td>
					<textarea id="description" name="description" rows="4" cols="40">{$description|escape}</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<label for="galleryId">{tr}File gallery{/tr}:</label>
				</td>
				<td>
					<select id="galleryId" name="galleryId"{if $all_galleries|@count eq '0'} disabled="disabled"{/if}>
						{section name=ix loop=$all_galleries}
							<option value="{$all_galleries[ix].id}">{$all_galleries[ix].label|escape}</option>
						{sectionelse}
							<option value="">{tr}None{/tr}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="dupCateg">{tr}Duplicate categories{/tr}:</label>
				</td>
				<td>
					<input type="checkbox" id="dupCateg" name="dupCateg" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="dupPerms">{tr}Duplicate permissions{/tr}:</label>
				</td>
				<td>
					<input type="checkbox" id="dupPerms" name="dupPerms" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" name="duplicate" value="{tr}Duplicate{/tr}" />
				</td>
			</tr>
		</table>
	</form>
{/if}
