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

	<div id="main">
		{section name=i loop=$references}
			<form action="tiki-references.php" method="post" id="{$references[i].ref_id|escape}" class="form-horizontal">
				<input type="hidden" name="referenceId" value="{$references[i].ref_id|escape}">
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Biblio Code{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_biblio_code" id="ref_biblio_code" value="{$references[i].biblio_code|escape}" maxlength="50">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Author{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_author" id="ref_author" value="{$references[i].author|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Title{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_title" id="ref_title" value="{$references[i].title|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Year{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_year" id="ref_year" value="{$references[i].year|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Part{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_part" id="ref_part" value="{$references[i].part|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}URI{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_uri" id="ref_uri" value="{$references[i].uri|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Code{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_code" id="ref_code" value="{$references[i].code|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Publisher{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_publisher" id="ref_publisher" value="{$references[i].publisher|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Location{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_style" id="ref_location" value="{$references[i].location|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Style{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_style" id="ref_style" value="{$references[i].style|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Template{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_template" id="ref_template" value="{$references[i].template|escape}">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3"></label>
					<div class="col-sm-7">
						<input type="submit" class="btn btn-default btn-sm" name="editreference" value="{tr}Save{/tr}" id="save_{$references[i].ref_id|escape}" disabled="disabled">
						<a title=":{tr}Delete{/tr}" class="btn btn-default btn-sm" href="tiki-references.php?action=delete&amp;referenceId={$references[i].ref_id}" >
							{icon name='remove'}
						</a>
					</div>
				</div>
			</form>
		{/section}
		<div>
			<form action="tiki-references.php" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Biblio Code{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_biblio_code" id="ref_biblio_code" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Author{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_author" id="ref_author" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Title{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_title" id="ref_title" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Year{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_year" id="ref_year" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Part{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_part" id="ref_part" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}URI{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_uri" id="ref_uri" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Code{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_code" id="ref_code" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Publisher{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_publisher" id="ref_publisher" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Location{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_style" id="ref_location" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Style{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_style" id="ref_style" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{tr}Template{/tr}:</label>
					<div class="col-sm-7">
						<input type="text" size="40" class="form-control" name="ref_template" id="ref_template" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3"></label>
					<div class="col-sm-7">
						<input type="submit" class="btn btn-default btn-sm" name="addreference" value="{tr}Add{/tr}">
					</div>
				</div>
			</form>
		</div>
	</div>
{/if}
