<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

abstract class Quicktag
{
	protected $wysiwyg;
	protected $icon;
	protected $label;

	abstract function isAccessible();
	abstract function getWikiHtml( $areaName );

	function getWysiwygToken() // {{{
	{
		return $this->wysiwyg;
	} // }}}

	protected function setIcon( $icon ) // {{{
	{
		$this->icon = $icon;

		return $this;
	} // }}}

	protected function setLabel( $label ) // {{{
	{
		$this->label = $label;

		return $this;
	} // }}}

	protected function setWysiwygToken( $token ) // {{{
	{
		$this->wysiwyg = $token;

		return $this;
	} // }}}

	protected function getIconHtml() // {{{
	{
		return '<img src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" class="icon"/>';
	} // }}}
}

class QuicktagSeparator extends Quicktag
{
	function __construct() // {{{
	{
		$this->setWysiwygToken('-');
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return '|';
	} // }}}
}

class QuicktagFckOnly extends Quicktag
{ 
	private function __construct( $token ) // {{{
	{
		$this->setWysiwygToken( $token );
	} // }}}
	
	public static function fromName( $name ) // {{{
	{
		switch( $name ) {
		case 'templates':
			return new self( 'Templates' );
		case 'cut':
			return new self( 'Cut' );
		case 'copy':
			return new self( 'Copy' );
		case 'paste':
			return new self( 'Paste' );
		case 'pastetext':
			return new self( 'PasteText' );
		case 'pasteword':
			return new self( 'PasteWord' );
		case 'print':
			return new self( 'Print' );
		case 'spellcheck':
			return new self( 'SpellCheck' );
		case 'undo':
			return new self( 'Undo' );
		case 'redo':
			return new self( 'Redo' );
		case 'find':
			return new self( 'Find' );
		case 'replace':
			return new self( 'Replace' );
		case 'selectall':
			return new self( 'SelectAll' );
		case 'removeformat':
			return new self( 'RemoveFormat' );
		case 'smiley':
			return new self( 'Smiley' );
		case 'showblocks':
			return new self( 'ShowBlocks' );
		case 'left':
			return new self( 'JustifyLeft' );
		case 'right':
			return new self( 'JustifyRight' );
		case 'full':
			return new self( 'JustifyFull' );
		case 'indent':
			return new self( 'Indent' );
		case 'outdent':
			return new self( 'Outdent' );
		case 'underline':
			return new self( 'Underline' );
		case 'unlink':
			return new self( 'Unlink' );
		case 'style':
			return new self( 'Style' );
		case 'fontname':
			return new self( 'FontName' );
		case 'fontsize':
			return new self( 'FontSize' );
		case 'source':
			return new self( 'Source' );
		}
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return null;
	} // }}}
}

class QuicktagInline extends Quicktag
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'bold':
			$label = tra('Bold');
			$icon = tra('pics/icons/text_bold.png');
			$wysiwyg = 'Bold';
			$syntax = '__text__';
			break;
		case 'italic':
			$label = tra('Italic');
			$icon = tra('pics/icons/text_italic.png');
			$wysiwyg = 'Italic';
			$syntax = "''text''";
			break;
		case 'strike':
			$label = tra('Strikethrough');
			$icon = tra('pics/icons/text_strikethrough.png');
			$wysiwyg = 'StrikeThrough';
			$syntax = '--text--';
			break;
		case 'sub':
			$label = tra('Subscript');
			$icon = tra('pics/icons/text_subscript.png');
			$wysiwyg = 'Subscript';
			$syntax = '{SUB()}text{SUB}';
			break;
		case 'sup':
			$label = tra('Superscript');
			$icon = tra('pics/icons/text_superscript.png');
			$wysiwyg = 'Superscript';
			$syntax = '{SUP()}text{SUP}';
			break;
		case 'tikilink':
			$label = tra('Wiki Link');
			$icon = tra('pics/icons/page_link.png');
			$wysiwyg = 'tikilink';
			$syntax = '((text))';
			break;
		case 'link':
			$label = tra('Link');
			$icon = tra('pics/icons/world_link.png');
			$wysiwyg = 'Link';
			$syntax = '[http://example.com|text]';
			break;
		case 'anchor':
			$label = tra('Anchor');
			$icon = tra('pics/icons/anchor.png');
			$wysiwyg = 'Anchor';
			$syntax = '{ANAME()}text{ANAME}';
			break;
		case 'color':
			$label = tra('Text Color');
			$icon = tra('pics/icons/palette.png');
			$wysiwyg = 'TextColor';
			$syntax = '~~red:text~~';
			break;
		case 'bgcolor':
			$label = tra('Background Color');
			$icon = tra('pics/icons/palette.png');
			$wysiwyg = 'BGColor';
			$syntax = '~~white,black:text~~';
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
			->setIcon( $icon )
			->setSyntax( $syntax );
		
		return $tag;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return '<a href="javascript:insertAt(\'' . $areaName . '\', \'' . htmlentities($this->syntax, ENT_QUOTES, 'UTF-8') . '\')" onclick="needToConfirm=false;" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '">' . $this->getIconHtml() . '</a>';
	} // }}}
}

class QuicktagBlock extends QuicktagInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'center':
			$label = tra('Align Center');
			$icon = tra('pics/icons/text_align_center.png');
			$wysiwyg = 'JustifyCenter';
			$syntax = "::text::";
			break;
		case 'table':
			$label = tra('Table');
			$icon = tra('pics/icons/table.png');
			$wysiwyg = 'Table';
			$syntax = '||r1c1|r1c2\nr2c1|r2c2||';
			break;
		case 'rule':
			$label = tra('Horizontal Bar');
			$icon = tra('pics/icons/page.png');
			$wysiwyg = 'Rule';
			$syntax = '---';
			break;
		case 'pagebreak':
			$label = tra('Page Break');
			$icon = tra('pics/icons/page.png');
			$wysiwyg = 'PageBreak';
			$syntax = '---';
			break;
		case 'blockquote':
			$label = tra('Block Quote');
			$icon = tra('pics/icons/box.png');
			$wysiwyg = 'Blockquote';
			$syntax = '^text^';
			break;
		case 'h1':
		case 'h2':
		case 'h3':
			$label = tra('Heading') . ' ' . $tagName{1};
			$icon = 'pics/icons/text_heading_' . $tagName{1} . '.png';
			$wysiwyg = null;
			$syntax = str_repeat('!', $tagName{1}) . 'text';
			break;
		case 'image':
			$label = tra('Image');
			$icon = tra('pics/icons/picture.png');
			$wysiwyg = 'tikiimage';
			$syntax = '{img src= width= height= link= }';
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
			->setIcon( $icon )
			->setSyntax( $syntax );
		
		return $tag;
	} // }}}
}

class QuicktagLineBased extends QuicktagInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'list':
			$label = tra('Unordered List');
			$icon = tra('pics/icons/text_list_bullets.png');
			$wysiwyg = 'UnorderedList';
			$syntax = '*text';
			break;
		case 'numlist':
			$label = tra('Ordered List');
			$icon = tra('pics/icons/text_list_numbers.png');
			$wysiwyg = 'OrderedList';
			$syntax = '#text';
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
			->setIcon( $icon )
			->setSyntax( $syntax );
		
		return $tag;
	} // }}}
}

class QuicktagPicker extends Quicktag
{
	private $list;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'specialchar':
			$wysiwyg = 'SpecialChar';
			$label = tra('Special Characters');
			$icon = tra('pics/img/world_edit.png');
			// Line taken from DokuWiki
            $list = explode(' ','À à Á á Â â Ã ã Ä ä Ǎ ǎ Ă ă Å å Ā ā Ą ą Æ æ Ć ć Ç ç Č č Ĉ ĉ Ċ ċ Ð đ ð Ď ď È è É é Ê ê Ë ë Ě ě Ē ē Ė ė Ę ę Ģ ģ Ĝ ĝ Ğ ğ Ġ ġ Ĥ ĥ Ì ì Í í Î î Ï ï Ǐ ǐ Ī ī İ ı Į į Ĵ ĵ Ķ ķ Ĺ ĺ Ļ ļ Ľ ľ Ł ł Ŀ ŀ Ń ń Ñ ñ Ņ ņ Ň ň Ò ò Ó ó Ô ô Õ õ Ö ö Ǒ ǒ Ō ō Ő ő Œ œ Ø ø Ŕ ŕ Ŗ ŗ Ř ř Ś ś Ş ş Š š Ŝ ŝ Ţ ţ Ť ť Ù ù Ú ú Û û Ü ü Ǔ ǔ Ŭ ŭ Ū ū Ů ů ǖ ǘ ǚ ǜ Ų ų Ű ű Ŵ ŵ Ý ý Ÿ ÿ Ŷ ŷ Ź ź Ž ž Ż ż Þ þ ß Ħ ħ ¿ ¡ ¢ £ ¤ ¥ € ¦ § ª ¬ ¯ ° ± ÷ ‰ ¼ ½ ¾ ¹ ² ³ µ ¶ † ‡ · • º ∀ ∂ ∃ Ə ə ∅ ∇ ∈ ∉ ∋ ∏ ∑ ‾ − ∗ √ ∝ ∞ ∠ ∧ ∨ ∩ ∪ ∫ ∴ ∼ ≅ ≈ ≠ ≡ ≤ ≥ ⊂ ⊃ ⊄ ⊆ ⊇ ⊕ ⊗ ⊥ ⋅ ◊ ℘ ℑ ℜ ℵ ♠ ♣ ♥ ♦ 𝛼 𝛽 𝛤 𝛾 𝛥 𝛿 𝜀 𝜁 𝛨 𝜂 𝛩 𝜃 𝜄 𝜅 𝛬 𝜆 𝜇 𝜈 𝛯 𝜉 𝛱 𝜋 𝛳 𝜍 𝛴 𝜎 𝜏 𝜐 𝛷 𝜑 𝜒 𝛹 𝜓 𝛺 𝜔 𝛻 𝜕 ★ ☆ ☎ ☚ ☛ ☜ ☝ ☞ ☟ ☹ ☺ ✔ ✘ × „ “ ” ‚ ‘ ’ « » ‹ › — – … ← ↑ → ↓ ↔ ⇐ ⇑ ⇒ ⇓ ⇔ © ™ ® ′ ″');
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setWysiwygToken( $wysiwyg )
			->setLabel( $label )
			->setIcon( $icon )
			->setList( $list );

		return $tag;
	} // }}}

	function setList( $list ) // {{{
	{
		$this->list = $list;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		static $pickerAdded = false;
		static $index = -1;
		global $headerlib;

		if( ! $pickerAdded ) {
			$headerlib->add_js( <<<JS
var pickerData = [];

function displayPicker( closeTo, list, areaname ) {
	var div = document.createElement('div');
	document.body.appendChild( div );

	var coord = closeTo.getCoordinates();

	div.className = 'quicktags-picker';
	div.style.left = coord.left + 'px';
	div.style.top = coord.bottom + 'px';

	var prepareLink = function( link, char ) {
		link.innerHTML = char;
		link.href = 'javascript:void(0)';
		link.onclick = function() {
			insertAt( areaname, char );
			div.dispose();
		}
	};

	for( i = 0; pickerData[list].length > i; ++i ) {
		var char = pickerData[list][i];
		var link = document.createElement( 'a' );

		div.appendChild( link );
		div.appendChild( document.createTextNode(' ') );
		prepareLink( link, char );
	}
}

JS
, 0 );
		}

		++$index;
		$headerlib->add_js( "pickerData.push( " . json_encode($this->list) . " );", 1 );

		return '<a href="javascript:void(0)" onclick="displayPicker( this, ' . $index . ', \'' . $areaName . '\'); needToConfirm=false;" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '">' . $this->getIconHtml() . '</a>';
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}
}

class QuicktagFullscreen extends Quicktag
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Full Screen Edit') )
			->setIcon( 'pics/icons/application_get.png' )
			->setWysiwygToken( 'FitWindow' );
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		$name = 'zoom';
		if( isset($_REQUEST['zoom']) )
			$name = 'preview';
		return '<input type="image" name="'.$name.'" alt="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" value="wiki_edit" onclick="needToConfirm=false;" title="" class="icon" src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '"/>';
	} // }}}
}

class QuicktagWikiplugin extends Quicktag
{
	private $pluginName;

	public static function fromName( $name ) // {{{
	{
		global $tikilib;
		if( substr( $name, 0, 11 ) == 'wikiplugin_'  ) {
			$name = substr( $name, 11 );
			if( $info = $tikilib->plugin_info( $name ) ) {
				if (isset($info['icon']) and $info['icon'] != '') {
					$icon = $info['icon'];
				} else {
					$icon = 'pics/icons/plugin_default.png';
				}

				$tag = new self;
				$tag->setLabel( $info['name'] )
					->setIcon( $icon )
					->setWysiwygToken( self::getToken( $name ) )
					->setPluginName( $name );

				return $tag;
			}
		}
	} // }}}

	function setPluginName( $name ) // {{{
	{
		$this->pluginName = $name;

		return $this;
	} // }}}

	function isAccessible() // {{{
	{
		global $tikilib;
		return $tikilib->plugin_enabled( $this->pluginName );
	} // }}}

	private static function getIcon( $name ) // {{{
	{
		// This property could be added to the plugin definition
		switch($name) {
		default:
			return 'pics/icons/plugin_default.png';
		}
	} // }}}

	private static function getToken( $name ) // {{{
	{
		switch($name) {
		case 'flash': return 'Flash';
		}
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return '<a href="javascript:popup_plugin_form(\'' . $this->pluginName . '\')" onclick="needToConfirm=false;" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '">' . $this->getIconHtml() . '</a>';
	} // }}}
}

class QuicktagsList
{
	private $lines = array();

	private function __construct() {}
	
	public static function fromPreference( $section ) // {{{
	{
		global $tikilib;

		$global = $tikilib->get_preference( 'toolbar_global' );
		$local = $tikilib->get_preference( 'toolbar_'.$section, $global );

		return self::fromPreferenceString( $local );
	} // }}}

	public static function fromPreferenceString( $string ) // {{{
	{
		$list = new self;

		$string = preg_replace( '/\s+/', '', $string );

		foreach( explode( '/', $string ) as $line ) {
			$list->addLine( explode( ',', $line ) );
		}

		return $list;
	} // }}}

	private function addLine( array $tags ) // {{{
	{
		$elements = array();
		$group = array();

		foreach( $tags as $tagName ) {
			if( $tagName == '-' ) {
				if( count($group) ) {
					$elements[] = $group;
					$group = array();
				}
			} else {
				if( ( $tag = $this->getTag( $tagName ) ) 
					&& $tag->isAccessible() ) {

					$group[] = $tag;
				}
			}
		}

		if( count($group) )
			$elements[] = $group;

		if( count( $elements ) )
			$this->lines[] = $elements;
	} // }}}

	private function getTag( $tagName ) // {{{
	{
		if( $tag = QuicktagInline::fromName( $tagName ) )
			return $tag;
		elseif( $tag = QuicktagBlock::fromName( $tagName ) )
			return $tag;
		elseif( $tag = QuicktagLineBased::fromName( $tagName ) )
			return $tag;
		elseif( $tag = QuicktagFckOnly::fromName( $tagName ) )
			return $tag;
		elseif( $tag = QuicktagWikiplugin::fromName( $tagName ) )
			return $tag;
		elseif( $tag = QuicktagPicker::fromName( $tagName ) )
			return $tag;
		elseif( $tagName == 'fullscreen' )
			return new QuicktagFullscreen;
		elseif( $tagName == '-' )
			return new QuicktagSeparator;
	} // }}}

	function getWysiwygArray() // {{{
	{
		$lines = array();
		foreach( $this->lines as $line ) {
			$lineOut = array();

			foreach( $line as $group ) {
				foreach( $group as $tag ) {

					if( $token = $tag->getWysiwygToken() )
						$lineOut[] = $token;
				}

				$lineOut[] = '-';
			}

			$lineOut = array_slice( $lineOut, 0, -1 );

			if( count($lineOut) )
				$lines[] = array($lineOut);
		}

		return $lines;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		$html = '';

		foreach( $this->lines as $line ) {
			$lineHtml = '';

			foreach( $line as $group ) {
				$groupHtml = '';
				foreach( $group as $tag ) {
					$groupHtml .= $tag->getWikiHtml( $areaName );
				}

				if( ! empty($groupHtml) ) {
					$param = empty($lineHtml) ? '' : ' style="border-left: double gray; height: 20px;"';
					$lineHtml .= "<span$param>$groupHtml</span>";
				}
			}

			if( ! empty($lineHtml) ) {
				$html .= "<div>$lineHtml</div>";
			}
		}

		return $html;
	} // }}}
}

?>
