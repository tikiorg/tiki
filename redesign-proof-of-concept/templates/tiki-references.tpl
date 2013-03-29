{* $Id$ *}
{if $prefs.wikiplugin_addreference eq 'y'}

{jq}
jQuery(document).ready(function(){
	jQuery('#main').find('input[type="text"]').each(function(){
		jQuery(this).keydown(function(){
			var id = jQuery(this).parents('form').attr('id');
			var btn = jQuery('#save_'+id);
			btn.removeAttr('disabled');
		});
	});
});
{/jq}


<h2>{tr}Library References{/tr}: <a href="tiki-index.php?page={$page|escape:"url"}">{$page}</a></h2>

<table class="formcolor" id="main">
	{section name=i loop=$references}
		<tr>
			<td>
				<form action="tiki-references.php" method="post" id="{$references[i].ref_id|escape}">
					<input type="hidden" name="referenceId" value="{$references[i].ref_id|escape}">
					<table class="formcolor">
						<tr>
							<td>{tr}Biblio Code{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_biblio_code" id="ref_biblio_code" value="{$references[i].biblio_code|escape}" maxlength="50"></td>
						</tr>
						<tr>
							<td>{tr}Author{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_author" id="ref_author" value="{$references[i].author|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Title{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_title" id="ref_title" value="{$references[i].title|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Year{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_year" id="ref_year" value="{$references[i].year|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Part{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_part" id="ref_part" value="{$references[i].part|escape}"></td>
						</tr>
						<tr>
							<td>{tr}URI{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_uri" id="ref_uri" value="{$references[i].uri|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Code{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_code" id="ref_code" value="{$references[i].code|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Publisher{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_publisher" id="ref_publisher" value="{$references[i].publisher|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Location{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_location" id="ref_location" value="{$references[i].location|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Style{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_style" id="ref_style" value="{$references[i].style|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Template{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_template" id="ref_style" value="{$references[i].template|escape}"></td>
						</tr>
					</table>
				<td align="right">
					<input type="submit" name="editreference" value="{tr}Save{/tr}" style="color:#ffffff!important;" id="save_{$references[i].ref_id|escape}" disabled="disabled">
					<a title="{tr}Delete{/tr}" href="tiki-references.php?action=delete&amp;referenceId={$references[i].ref_id}" >{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
				</form>
			</td>
	</tr>
	<tr>
		<td colspan="2" style="border: medium none;background-color: #FFFFFF;">&nbsp;</td>
	</tr>
	{/section}
		<tr>
			<td>
				<form action="tiki-references.php" method="post">
					<table class="formcolor">
						<tr>
							<td>{tr}Biblio Code{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_biblio_code" id="ref_biblio_code" value=""></td>
						</tr>
						<tr>
							<td>{tr}Author{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_author" id="ref_author" value=""></td>
						</tr>
						<tr>
							<td>{tr}Title{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_title" id="ref_title" value="" /></td>
						</tr>
						<tr>
							<td>{tr}Year{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_year" id="ref_year" value=""></td>
						</tr>
						<tr>
							<td>{tr}Part{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_part" id="ref_part" value=""></td>
						</tr>
						<tr>
							<td>{tr}URI{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_uri" id="ref_uri" value=""></td>
						</tr>
						<tr>
							<td>{tr}Code{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_code" id="ref_code" value=""></td>
						</tr>
						<tr>
							<td>{tr}Publisher{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_publisher" id="ref_publisher" value="{$references[i].publisher|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Location{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_location" id="ref_location" value="{$references[i].location|escape}"></td>
						</tr>
						<tr>
							<td>{tr}Style{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_style" id="ref_style" value=""></td>
						</tr>
						<tr>
							<td>{tr}Template{/tr}:</td>
							<td><input type="text" size="40"  class="wikiedit" name="ref_template" id="ref_template" value=""></td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="addreference" value="{tr}Add{/tr}">
							</td>
						</tr>
					</table>
			</form>
		</td>
	</tr>
</table>
{/if}
