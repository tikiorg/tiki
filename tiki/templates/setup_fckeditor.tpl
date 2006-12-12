var _TikiPath = '{$tikipath}' ;
var _TikiRoot = '{$tikiroot}' ;
var _FileBrowserLanguage      = 'php' ;
var _QuickUploadLanguage      = 'php' ;

FCKConfig.FontNames = 'sans serif;serif;monospace;Arial;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana' ;

FCKConfig.ToolbarSets["Tiki"] = [
  ['FitWindow','Templates','-','Cut','Copy','Paste','PasteWord','Print','SpellCheck'],
	['Undo','Redo','-','Replace','RemoveFormat','-','Image','Table','Rule','SpecialChar','PageBreak','UniversalKey'],
	'/',
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent','Link','Unlink','Anchor'],
	'/',
	['Style','FontFormat','FontName','FontSize','-','TextColor','BGColor']
] ;

// FCKConfig.StylesXmlPath = _TikiPath + '/lib/fckeditor_tiki/tikistyles.xml';
// FCKConfig.TemplatesXmlPath = _TikiPath + '/lib/fckeditor_tiki/tikitemplates.xml';
FCKConfig.AutoDetectLanguage  = true ;
FCKConfig.EditorAreaCSS = _TikiRoot + '/styles/{$style}' ;
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;
FCKConfig.DefaultLanguage   = '{$language}' ;
FCKConfig.AutoDetectLanguage   = {if $feature_detect_language eq 'y'}true{else}false{/if} ;
FCKConfig.ContentLangDirection = '{if $feature_bidi eq 'y'}rtl{else}ltr{/if}' ;
FCKConfig.StartupFocus = true ;

