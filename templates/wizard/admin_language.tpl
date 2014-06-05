{* $Id$ *}

<div class="media">
    <img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}"/>
    <div class="media-content">
        <img class="pull-right" src="img/icons/large/i18n48x48.png" alt="{tr}Set up the language{/tr}" />
        {tr}Select the site language{/tr}.
        <fieldset>
	        <legend>{tr}Language{/tr}</legend>

	        {preference name=language}
	        <br>
	        {preference name=feature_multilingual visible="always"}
	        {preference name=lang_use_db}
        </fieldset>
    </div>
</div>
