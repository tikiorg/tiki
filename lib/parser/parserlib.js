var parserlib = {
	parse: function(val, errors) {
		try {
			function Lexer () {}
			Lexer.prototype = WikiParser.lexer;

			function Parser () {
				this.lexer = new Lexer();
				this.yy = {cmd: parserlib};
			}
			Parser.prototype = WikiParser;
			
			var wikiParser = (new Parser);
			
			return wikiParser.parse(val);
		} catch (e) {
			if (errors) {
				return e;
			}
		}
	},
	plugin: function(plugin) {
		//needs a little ajax magic
		if (plugin) {
			if (plugin.name) {
				var wikiPluginName = "wikiplugin_" + plugin.name.toLowerCase();
				if (window[wikiPluginName]) {
					var thisOutput = window[wikiPluginName](plugin.body);
					if (thisOutput) {
						var newOutput = parserlib.parse(thisOutput);
						
						return (newOutput ? newOutput : thisOutput);
					}
				}
			}
			return (plugin.body ? plugin.body : '');
		}
		return '';
	},
	make_bold: function ($html) {
		return "<strong>" + $html + "</strong>";
	},
	make_box: function ($html) {
		return "<div style='border: solid 1px black;'>" + $html + "</div>";
	},
	make_center: function ($html) {
		return "<center>" + $html + "</center>";
	},
	make_colortext: function ($color, $html) {
		return "<span style='color: #" + $color + ";'>" + $html + "</span>";
	},
	make_italics: function ($html) {
		return "<i>" + $html + "</i>";
	},
	make_header1: function ($html) {
		return "<h1>" + $html + "</h1>";
	},
	make_header2: function ($html) {
		return "<h2>" + $html + "</h2>";
	},
	make_header3: function ($html) {
		return "<h3>" + $html + "</h3>";
	},
	make_header4: function ($html) {
		return "<h4>" + $html + "</h4>";
	},
	make_header5: function ($html) {
		return "<h5>" + $html + "</h5>";
	},
	make_header6: function ($html) {
		return "<h6>" + $html + "</h6>";
	},
	make_hr: function () {
		return "<hr />";
	},
	make_link: function ($href, $html) {
		return "<a href='" + $href + "'>" + $html + "</a>";
	},
	make_smile: function ($smile) { //this needs more tlc too
		return "<img src='img/smiles/icon_" + $smile + ".gif' alt='" + $smile + "' />";
	},
	make_strikethrough: function ($html) {
		return "<span style='text-decoration: line-through;'>" + $html + "</span>";
	},
	make_table: function ($html) {
		return "<table style='width: 100%;'>" + $html + "</table>";
	},
	make_table_tr: function ($html) {
		return "<tr>" + $html + "</tr>";
	},
	make_table_td: function ($html) {
		return "<td>" + $html + "</td>";
	},
	make_titlebar: function ($html) {
		return "<div class='titlebar'>" + $html + "</div>";
	},
	make_underscore: function ($html) {
		return "<u>" + $html + "</u>";
	},
	make_wikilink: function ($href, $html) {
		return "<a href='" + $href + "'>" + $html + "</a>";
	},
	html: function ($html) { //this needs some ajax tlc
		return $html;
	},
	substring: function($val, $left, $right) {
		return $val.substring($left, $val.length + $right);
	},
	match: function($pattern, $subject) {
		return $subject.match($pattern);
	},
	replace: function($search, $replace, $subject) {
		return $subject.replace($search, $replace);
	},
	split: function ($delimiter, $string) {
		return $string.split($delimiter);
	}
};