{* $Id$ *}

{tr}You can choose to use by default the 'Compatible' Wiki mode (content is saved in wiki syntax), or the HTML mode{/tr}.
<img class="pull-right" src="img/icons/large/wysiwyg48x48.png" alt="{tr}Set up the Wysiwyg environment{/tr}" />
<div class="media">
    <img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" />
    <div class="media">
        <fieldset>
	        <legend>{tr}Wiki editor setup{/tr}</legend>
	        {tr}Select the Wysiwyg editor mode{/tr}
	        <div class="row">
	            <div class="col-md-4">
	                <input type="radio" name="editorType" value="wiki" {if empty($editorType) || $editorType eq 'wiki'}checked="checked"{/if} /> {tr}Compatible Wiki mode{/tr}
                </div>
                <div class="col-md-8">{tr}Use wiki syntax for saved pages{/tr}.<br>
		            <p>{tr}This is the most compatible with Tiki functionality and the most stable editor mode{/tr}.<br>
		            {tr}Tools and functions in the editor toolbar will be limited{/tr}.</p>

                    <p>{preference name=wysiwyg_default}</p>
                </div>
	        </div>
            <div class="row">
                <div class="col-md-4">
                    <input type="radio" name="editorType" value="html" {if $editorType eq 'html'}checked="checked"{/if} /> {tr}HTML mode{/tr}
                </div>
                <div class="col-md-8">
                    <p>{tr}Use HTML syntax for saved pages{/tr}.<br>
		            {tr}Best compatibility with inline editing{/tr}. {tr}Loses some wiki related features, such as SlideShow, and has some problems with SEFURL{/tr}.<br>
		            {tr}Full editor toolbar{/tr}.</p>

                    <p>{preference name=wysiwyg_optional}</p>
                </div>
            </div>
	    	{preference name=wysiwyg_inline_editing}
	        <br>
	        <em>{tr}See also{/tr} <a href="tiki-admin.php?page=wysiwyg" target="_blank">{tr}Wysiwyg admin panel{/tr}</a></em>
        </fieldset>
    </div>
</div>

