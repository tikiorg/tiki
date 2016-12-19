{tikimodule title=$tpl_module_title name="terminology" flip=$module_params.flip decorations=$module_params.decorations}

	<form class="form" method="post" action="tiki-listpages.php" role="form">
		<div class="form-group">
			<label class="col-sm-2 control-label">{tr}Find term:{/tr}</label>
			{if $term_root_category_id != ''}
				<input type="hidden" name="categId" value="{$term_root_category_id}"/>
				<input type="hidden" name="create_page_with_search_category" value="y"/>
			{/if}
			<div class="col-sm-5">
				<input name="find" class="form-control" type="text" accesskey="s" value=""/>
				<input type="hidden" name="exact_match" value="On"/>
				<input type="hidden" name="hits_link_to_all_languages" value="On"/>
				<input type="hidden" name="create_new_pages_using_template_name" value="{$create_new_pages_using_template_name}"/>
				<input type="hidden" name="term_srch" value="y"/>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-5">
				<select name="lang" class="in form-control">
					<option value=''{if $search_terms_in_lang eq ''} selected="selected"{/if}>{tr}any language{/tr}</option>
					{section name=ix loop=$user_languages}
						<option value="{$user_languages[ix].value}"{if $user_languages[ix].value eq $search_terms_in_lang} selected="selected"{/if}>{tr}{$user_languages[ix].name}{/tr}</option>
					{/section}
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-2 col-sm-offset-2">
				<input type="submit" class="wikiaction btn btn-default btn-sm" name="search" value="{tr}Go{/tr}"/>
			</div>
			<div class="col-sm-8 checkbox-inline">
				<div class="help-block">{tr}If not found, you will be given a chance to create it.{/tr}</div>
			</div>
		</div>
	</form>
	<div class="text-center">
		<a href="tiki-index.php?page=User Guide - Collaborative Terminology Profile">{tr}Help{/tr}</a>
		&nbsp; &nbsp; <a href="tiki-index.php?page=Admin Guide - Collaborative Terminology Profile">{tr}Admin{/tr}</a>
	</div>

{/tikimodule}
