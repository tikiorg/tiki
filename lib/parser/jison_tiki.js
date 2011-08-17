var fs = require('fs');
var sys = require('sys')
var exec = require('child_process').exec;

function puts(error, stdout, stderr) {
	sys.puts(stdout)
}

exec("jison WikiParser.jison", function() {
	var WikiParser = require('./WikiParser.js');

	var symbols = JSON.stringify(WikiParser.parser.symbols_);
	var terminals = JSON.stringify(WikiParser.parser.terminals_);
	var productions = JSON.stringify(WikiParser.parser.productions_);

	var table = JSON.stringify(WikiParser.parser.table);
	var defaultActions = JSON.stringify(WikiParser.parser.defaultActions);

	//turn regex into string
	var rules = [];
	for(var i = 0; i < WikiParser.parser.lexer.rules.length; i++) {
		rules.push(WikiParser.parser.lexer.rules[i].toString());
	}
	rules = JSON.stringify(rules);
	rules = rules.substring(1, rules.length - 1);
	
	var conditions = JSON.stringify(WikiParser.parser.lexer.conditions);
	var parserPerformAction = WikiParser.parser.performAction.toString();
	var lexerPerformAction = WikiParser.parser.lexer.performAction.toString();

	function jsToPhpGen(str, stripKey) {
		str = str.replace(new RegExp('[\[]', 'g'), "array(");
		str = str.replace(new RegExp('\]', 'g'), ")");
		str = str.replace(new RegExp('[\{]', 'g'), "array(");
		str = str.replace(new RegExp('[\}]', 'g'), ")");
		str = str.replace(new RegExp('[:]', 'g'), "=>");
		str = str.replace('$accept', 'accept');
		str = str.replace('$end', 'end');

		if (stripKey) {
			str = str.replace(new RegExp(',"', 'g'), ',');
			str = str.replace(new RegExp('"=>', 'g'), '=>');
			str = str.replace(new RegExp('[\(]"', 'g'), '(');
		}

		return str;
	}

	function jsFnToPhpGen(str) {
		str = str.split('{');
		str.shift();
		str = str.join('{');

		str = str.split('}');
		str.pop();
		str = str.join('}');

		return str;
	}

	function jsPerformActionToPhp(str) {
		str = jsFnToPhpGen(str);
		str = str.replace("var $0 = $$.length - 1;", '');
		str = str.replace("var YYSTATE=YY_START", '');
		str = str.replace(new RegExp('[$]0', 'g'), '$O');
		str = str.replace(new RegExp('[$][$]', 'g'), '$S');
		str = str.replace(new RegExp('parserlib[.]', 'g'), 'ParserLib::');
		str = str.replace(new RegExp('this[.][$]', 'g'), '$thisS');
		str = str.replace(new RegExp('yystate', 'g'), '$yystate');
		str = str.replace(new RegExp('this[.]yy[.]', 'g'), '$this->yy->');
		str = str.replace(new RegExp('this[.]', 'g'), '$this->');
		str = str.replace(new RegExp('yy[_][.]yytext', 'g'), '$yy_->yytext');
		str = str.replace(new RegExp('yy[.]', 'g'), '$yy->');
		str = str.replace(new RegExp('\][.]', 'g'), ']->');
		str = str.replace(new RegExp('\[\]', 'g'), 'array()');
		str = str.replace(new RegExp('default[:][;]', 'g'), '');
		
		str = str.replace(/(\d)\n/g, function(){
			return arguments[1] + ';\n';
		});
		
		return str;
	}

	var parserRaw = fs.readFileSync("./parser_template.php", "utf8");

	parserRaw = parserRaw.replace('"<@@SYMBOLS@@>"', jsToPhpGen(symbols));
	parserRaw = parserRaw.replace('"<@@TERMINALS@@>"', jsToPhpGen(terminals, true));
	parserRaw = parserRaw.replace('"<@@PRODUCTIONS@@>"', jsToPhpGen(productions));

	parserRaw = parserRaw.replace('"<@@TABLE@@>"', jsToPhpGen(table));
	parserRaw = parserRaw.replace('"<@@DEFAULT_ACTIONS@@>"', jsToPhpGen(defaultActions));

	parserRaw = parserRaw.replace('"<@@RULES@@>"', 'array(' + rules + ')');
	parserRaw = parserRaw.replace('"<@@CONDITIONS@@>"', jsToPhpGen(conditions));

	parserRaw = parserRaw.replace('"<@@PARSER_PERFORM_ACTION@@>"', jsPerformActionToPhp(parserPerformAction));
	parserRaw = parserRaw.replace('"<@@LEXER_PERFORM_ACTION@@>"', jsPerformActionToPhp(lexerPerformAction));

	fs.writeFile("WikiParser.php", parserRaw, function(err) {
		if (err) {
			console.log("Something went bad");
		} else {
			console.log("Success writing new parser files WikiParser.js & WikiParser.php.");
			console.log("Please Note: The php version of the jison parser is only an ATTEMPTED conversion, that being said:");
			console.log("PLEASE TEST FILES BEFORE COMMITTING!");
		}
	});
});