{* $Id$ *}
{if $tiki_p_create_file_galleries eq 'y' and $gal_info.type neq 'user'}
	<h2>{tr}Duplicate File Gallery{/tr}</h2>
	<form class="form-horizontal" role="form" action="tiki-list_file_gallery.php{if isset($filegals_manager) and $filegals_manager neq ''}?filegals_manager={$filegals_manager}{/if}" method="post">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">{tr}Name{/tr}</label>
            <div class="col-sm-10">
                <input type="text" size="50" id="name" name="name" value="">
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">{tr}Description{/tr}</label>
            <div class="col-sm-10">
                <textarea id="description" name="description" rows="4" cols="40">
					{if isset($description)}{$description|escape}{/if}
				</textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="galleryId" class="col-sm-2 control-label">{tr}File gallery{/tr}</label>
            <div class="col-sm-10">
                <select id="galleryId" name="galleryId"{if $all_galleries|@count eq '0'} disabled="disabled"{/if}>
					{section name=ix loop=$all_galleries}
						<option value="{$all_galleries[ix].id}"{if $galleryId eq $all_galleries[ix].id}
							selected="selected"{/if}>{$all_galleries[ix].label|escape}
						</option>
					{sectionelse}
						<option value="">{tr}None{/tr}</option>
					{/section}
				</select>
            </div>
        </div>
        <div class="form-group">
            <label for="dupCateg" class="col-sm-2 control-label">{tr}Duplicate categories{/tr}</label>
            <div class="col-sm-10">
				<input type="checkbox" id="dupCateg" name="dupCateg">
            </div>
        </div>
        <div class="form-group">
            <label for="dupPerms" class="col-sm-2 control-label">{tr}Duplicate permissions{/tr}</label>
            <div class="col-sm-10">
				<input type="checkbox" id="dupPerms" name="dupPerms">
			</div>
        </div>
        <div class="form-group">
            <div class="col-sm-10 col-sm-push-2">
                <input type="submit" class="btn btn-primary btn-sm" name="duplicate" value="{tr}Duplicate{/tr}">
            </div>
        </div>
    </form>
{/if}
