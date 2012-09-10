var WikiHandler = {
	blockLoc: {},
	blockLast: '',
	plugin: function(plugin) {
		//needs a little ajax magic
		if (plugin) {
			if (plugin.name) {
				var wikiPluginName = "wikiplugin_" + plugin.name.toLowerCase();
				if (window[wikiPluginName]) {
					var thisOutput = window[wikiPluginName](plugin.body);
					if (thisOutput) {
						//var newOutput = this.Parse(thisOutput);
						
						//return (newOutput ? newOutput : thisOutput);
					}
				}
			}
		}
		return (plugin.inline ? plugin.static : plugin.static + plugin.body + '{' + plugin.name + '}');
	},
	beginBlock: function (condition)
	{
		if (condition != this.blockLast)
			this.blockLoc[condition]++;

		this.blockLast = condition;

		return this.lexer.begin(condition);
	},
	newLine: function() {
		return '<br />';
	},
    np: function(content) {
        return content;
    },
	bold: function (convent) {
		return "<strong>" + convent + "</strong>";
	},
	box: function (convent) {
		return "<div style='border: solid 1px black;'>" + convent + "</div>";
	},
	center: function (convent) {
		return "<center>" + convent + "</center>";
	},
	colortext: function (convent) {
		var text = convent.split(':');
		var color = text[0];
		var html = text[1];
		return "<span style='color: #" + color + ";'>" + html + "</span>";
	},
	italics: function (convent) {
		return "<i>" + convent + "</i>";
	},
	header: function (convent) {
		return "<h1>" + convent + "</h1>";
	},
    stackList: function(content) {
        return content;
    },
	hr: function () {
		return "<hr />";
	},
	link: function (convent) {
		var link = this.split(':', convent);
		var href = convent;
		
		if (this.match(/\|/, convent)) {
			href = link[0];
			convent = link[1];
		}
		return "<a href='" + href + "'>" + convent + "</a>";
	},
	smile: function (smile) { //this needs more tlc too
		return "<img src='img/smiles/icon_" + smile + ".gif' alt='" + smile + "' />";
	},
	strikethrough: function (convent) {
		return "<span style='text-decoration: line-through;'>" + convent + "</span>";
	},
	tableParser: function (convent) {
		var tableContents = '';
		var rows = this.split('<br />', convent);
		for(var i = 0; i < this.size(rows); i++) {
			row = '';
			
			cells = this.split('|',  rows[i]);
			for(var j = 0; j < this.size(cells); j++) {
				row += this.table_td(cells[j]);
			}
			tableContents += this.table_tr(row);
		}
		return "<table style='width: 100%;'>" + tableContents + "</table>";
	},
	table_tr: function (convent) {
		return "<tr>" + convent + "</tr>";
	},
	table_td: function (convent) {
		return "<td>" + convent + "</td>";
	},
	titlebar: function (convent) {
		return "<div class='titlebar'>" + convent + "</div>";
	},
	underscore: function (convent) {
		return "<u>" + convent + "</u>";
	},
	wikilink: function (convent) {
		var wikilink = this.split('|', convent);
		var href = convent;
		
		if (this.match('/\|/', convent)) {
			href = wikilink[0];
			convent = wikilink[1];
		}
		return "<a href='" + href + "'>" + convent + "</a>";
	},
	olist: function(convent) {
		return '<ol><li>' + convent + '</li></ol>';
	},
	ulist: function(convent) {
		return '<ul><li>' + convent + '</li></ul>';
	},
	html: function (convent) { //this needs some ajax tlc
		return convent;
	},
	formatContent: function (convent) {
		return convent.replace(/\n/g, '<br />');
	},
    isContent: function () {
        return (this.pluginStackCount > 0 || this.npStack == true ? true : null);
    },
	substring: function(val, left, right) {
		return val.substring(left, val.length + right);
	},
	match: function(pattern, subject) {
		return subject.match(pattern);
	},
	replace: function(search, replace, subject) {
		return subject.replace(search, replace);
	},
	split: function (delimiter, string) {
		return string.split(delimiter);
	},
	join: function () {
		var result = '';
		for(var i = 0; i < arguments.length; i++) {
			if (arguments[i]) result += arguments[i];
		}
		return result;
	},
	size: function(array) {
		if (!array) array = [];
		return array.length;
	},
	pop: function(array) {
		if (!array) array = [];
		array.pop();
		return array;
	},
	push: function (array, val) {
		if (!array) array = [];
		array.push(val);
		return array;
	},
	shift: function(array) {
		if (!array) array = [];
		array.shift();
		return array;
	},
	stackPlugin: function (yytext, pluginStack) {
		var pluginName = this.match(/^\{([A-Z]+)/, yytext);
		var pluginArgs =  this.match(/[(].*?[)]/, yytext);
		
		return this.push(pluginStack, {
			name: pluginName[1],
			args: pluginArgs,
			body: '',
			inline: false,
			static: yytext
		});
	},
	inlinePlugin: function (yytext) {
		var pluginName = this.match(/^\{([a-z]+)/, yytext);
		var pluginArgs = this.match(/[ ].*?[}]|[/}]/, yytext);
		
		return {
			name: pluginName[1],
			args: pluginArgs,
			body: '',
			inline: true,
			static: yytext
		};
	},
	isPlugin: function() {
		if (!this.yy.pluginStack) this.yy.pluginStack = [];
		return (this.yy.pluginStack.length > 0);
	},
	preParse: function (val) {
		return '\n'  + val + '\n'; //here we will strip things like np and plugins, and parse them at the end and process
	},
	postParse: function (val) {
		return val; //here we will restore things like np, and plugins and parse them if needed
	},
	Parse: function(val, errors) {
		try {
			if (this.inUse) {
				var wiki = new Wiki.Parser();
				wiki.extend.parser(WikiHandler);
				wiki.inUser = false;
				return wiki.parse(val);
			}

			this.inUse = true;
			val = this.preParse(val);
			val = this.parse(val);
			val = this.postParse(val);
			this.inUse = false;
			return val;
		} catch (e) {
			if (errors) {
				console.log(e);
			}
		}
	},
	parseError: function() {
		return "";
	}
};

Wiki.extend.parser(WikiHandler);