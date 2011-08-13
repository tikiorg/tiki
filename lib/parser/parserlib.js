var parserlib = {
	parse: function(val, errors) {
		try {
			function Lexer () {}
			Lexer.prototype = WikiParser.lexer;

			function Parser () {
				this.lexer = new Lexer();
				this.yy = {};
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
	bold: function ($content) {
		return "<strong>" + $content + "</strong>";
	},
	box: function ($content) {
		return "<div style='border: solid 1px black;'>" + $content + "</div>";
	},
	center: function ($content) {
		return "<center>" + $content + "</center>";
	},
	colortext: function ($content) {
		var $text = $content.split(':');
		var $color = $text[0];
		var $html = $text[1];
		return "<span style='color: #" + $color + ";'>" + $html + "</span>";
	},
	italics: function ($content) {
		return "<i>" + $content + "</i>";
	},
	header1: function ($content) {
		return "<h1>" + $content + "</h1>";
	},
	header2: function ($content) {
		return "<h2>" + $content + "</h2>";
	},
	header3: function ($content) {
		return "<h3>" + $content + "</h3>";
	},
	header4: function ($content) {
		return "<h4>" + $content + "</h4>";
	},
	header5: function ($content) {
		return "<h5>" + $content + "</h5>";
	},
	header6: function ($content) {
		return "<h6>" + $content + "</h6>";
	},
	hr: function () {
		return "<hr />";
	},
	link: function ($content) {
		var $link = this.split(':', $content);
		var $href = $content;
		
		if (this.match(/\|/, $content)) {
			$href = $link[0];
			$content = $link[1];
		}
		return "<a href='" + $href + "'>" + $content + "</a>";
	},
	smile: function ($smile) { //this needs more tlc too
		return "<img src='img/smiles/icon_" + $smile + ".gif' alt='" + $smile + "' />";
	},
	strikethrough: function ($content) {
		return "<span style='text-decoration: line-through;'>" + $content + "</span>";
	},
	table: function ($content) {
		var $tableContents = '';
		var $rows = this.split('<br />', $content);
		for(var $i = 0; $i < this.size($rows); $i++) {
			$row = '';
			
			$cells = this.split('|',  $rows[$i]);
			for(var $j = 0; $j < this.size($cells); $j++) {
				$row += this.table_td($cells[$j]);
			}
			$tableContents += this.table_tr($row);
		}
		return "<table style='width: 100%;'>" + $tableContents + "</table>";
	},
	table_tr: function ($content) {
		return "<tr>" + $content + "</tr>";
	},
	table_td: function ($content) {
		return "<td>" + $content + "</td>";
	},
	titlebar: function ($content) {
		return "<div class='titlebar'>" + $content + "</div>";
	},
	underscore: function ($content) {
		return "<u>" + $content + "</u>";
	},
	wikilink: function ($content) {
		var $wikilink = this.split('|', $content);
		var $href = $content;
		
		if (this.match('/\|/', $content)) {
			$href = $wikilink[0];
			$content = $wikilink[1];
		}
		return "<a href='" + $href + "'>" + $content + "</a>";
	},
	html: function ($content) { //this needs some ajax tlc
		return $content;
	},
	formatContent: function ($content) {
		return $content.replace(/\n/g, '<br />');
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
	},
	join: function () {
		var result = '';
		for(var i = 0; i < arguments.length; i++) {
			if (arguments[i]) result += arguments[i];
		}
		return result;
	},
	size: function($array) {
		if (!$array) $array = [];
		return $array.length;
	},
	pop: function($array) {
		if (!$array) $array = [];
		$array.pop();
		return $array;
	},
	push: function ($array, $val) {
		if (!$array) $array = [];
		$array.push($val);
		return $array;
	},
	shift: function($array) {
		if (!$array) $array = [];
		$array.shift();
		return $array;
	},
	// start state handlers
	stackPlugin: function ($yytext, $pluginStack) {
		var $pluginName = this.match(/^\{([A-Z]+)/, $yytext);
		var $pluginArgs =  this.match(/[(].*?[)]/, $yytext);
		
		return this.push($pluginStack, {
			name: $pluginName[1],
			args: $pluginArgs,
			body: ''
		});
	},
	inlinePlugin: function ($yytext) {
		var $pluginName = this.match(/^\{([a-z]+)/, $yytext);
		var $pluginArgs = this.match(/[ ].*?[}]|[/}]/, $yytext);
		
		return {
			name: $pluginName[1],
			args: $pluginArgs,
			body: ''
		};
	},
	npState: function (npState, ifTrue, ifFalse) {
		return (npState ? ifTrue : ifFalse);
	}
	//end state handlers
};