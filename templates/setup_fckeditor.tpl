var _TikiPath = '{$tikipath}' ;
var _TikiRoot = '{$tikiroot}' ;
var _FileBrowserLanguage      = 'php' ;
var _QuickUploadLanguage      = 'php' ;

FCKConfig.FontNames = 'sans serif;serif;monospace;Arial;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana' ;

FCKConfig.ToolbarSets["Tiki"] = [
  ['FitWindow','Templates','-','Cut','Copy','Paste','PasteWord','Print','SpellCheck'],
	['Undo','Redo','-','Replace','RemoveFormat','-','Image','Table','Rule','SpecialChar','PageBreak','UniversalKey'],
	'/',
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','OrderedList','UnorderedList','Outdent','Indent'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript','-','Link','Unlink','Anchor','-','tikilink'],
	'/',
	['Style','FontName','FontSize','-','TextColor','BGColor']
] ;

FCKConfig.StylesXmlPath = '../../../lib/fckeditor_tiki/tikistyles.xml';
FCKConfig.TemplatesXmlPath = _TikiRoot + 'lib/fckeditor_tiki/tikitemplates.xml';

FCKConfig.EditorAreaCSS = _TikiRoot + '{$fckstyle}' ;
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;
FCKConfig.DefaultLanguage   = '{$language}' ;
FCKConfig.AutoDetectLanguage   = {if $feature_detect_language eq 'y'}true{else}false{/if} ;
FCKConfig.ContentLangDirection = '{if $feature_bidi eq 'y'}rtl{else}ltr{/if}' ;
FCKConfig.StartupFocus = true ;

FCKConfig.PluginsPath = _TikiRoot + 'lib/fckeditor_tiki/plugins' ;

FCKConfig.Plugins.Add( 'tikilink' ) ;
FCKConfig.tikilinkBtn     = '{tr}Insert/Edit CMS Content{/tr}' ;
FCKConfig.tikilinkDlgTitle    = '{tr}Tiki Link - Insert internal link{/tr}' ;
FCKConfig.tikilinkDlgName   = '{tr}Wiki Link insert{/tr}' ;
FCKConfig.tikilinkDlgSelection    = '{tr}Please make a selection of text in order to create a link{/tr}' ;

