var PythonParser = Editor.Parser = (function() {
    function wordRegexp(words) {
        return new RegExp("^(?:" + words.join("|") + ")$");
    }
    var DELIMITERCLASS = 'rsplus-delimiter';
    var LITERALCLASS = 'rsplus-literal';
    var ERRORCLASS = 'rsplus-error';
    var OPERATORCLASS = 'rsplus-operator';
    var IDENTIFIERCLASS = 'rsplus-identifier';
    var STRINGCLASS = 'rsplus-string';
    var BYTESCLASS = 'rsplus-bytes';
    var UNICODECLASS = 'rsplus-unicode';
    var RAWCLASS = 'rsplus-raw';
    var NORMALCONTEXT = 'normal';
    var STRINGCONTEXT = 'string';
    var singleOperators = '+-*/%&|^~<>';
    var doubleOperators = wordRegexp(['==', '!=', '\\<=', '\\>=', '\\<\\-', '\\-\\>', '\\<\\>',
                                      '\\<\\<', '\\>\\>', '\\/\\/', '\\*\\*']);
    var singleDelimiters = '()[]{}@,:`=;';
    var doubleDelimiters = ['\\+=', '\\-=', '\\*=', '/=', '%=', '&=', '\\|=',
                            '\\^='];
    var tripleDelimiters = wordRegexp(['//=','\\>\\>=','\\<\\<=','\\*\\*=']);
    var singleStarters = singleOperators + singleDelimiters + '=!';
    var doubleStarters = '=<>*/';
    var identifierStarters = /[_A-Za-z]/;

    var wordOperators = wordRegexp(['and', 'or', 'not', 'is', 'in']);
    var commonkeywords = ['as', 'assert', 'break', 'class', 'continue',
                          'def', 'del', 'elif', 'else', 'except', 'finally',
                          'for', 'from', 'global', 'if', 'import',
                          'lambda', 'otherwise', 'pass', 'persistent', 'raise', 'return',
                          'try', 'while', 'with', 'yield'];
    var commontypes = ['bool', 'classmethod', 'complex', 'dict', 'enumerate',
                       'float', 'frozenset', 'int', 'list', 'object',
                       'property', 'reversed', 'set', 'slice', 'staticmethod',
                       'str', 'super', 'tuple', 'type'];
    var basepackage = ['$.package_version', '$<-', '$<-.data.frame', 'abbreviate', 'abs', 'acos', 'acosh', 'addNA', 'addTaskCallback',
                       'agrep', 'alist', 'all', 'all.equal', 'all.equal.character', 'all.equal.default', 'all.equal.factor',
                       'all.equal.formula', 'all.equal.language', 'all.equal.list', 'all.equal.numeric', 'all.equal.POSIXct',
                       'all.equal.raw', 'all.names', 'all.vars', 'any', 'aperm', 'append', 'apply', 'Arg', 'args', 'array', 'as.array',
                       'as.array.default', 'as.call', 'as.character', 'as.character.condition', 'as.character.Date', 'as.character.default',
                       'as.character.error', 'as.character.factor', 'as.character.hexmode', 'as.character.numeric_version', 'as.character.octmode',
                       'as.character.POSIXt', 'as.character.srcref', 'as.complex', 'as.data.frame', 'as.data.frame.array', 'as.data.frame.AsIs',
                       'as.data.frame.character', 'as.data.frame.complex', 'as.data.frame.data.frame', 'as.data.frame.Date', 'as.data.frame.default',
                       'as.data.frame.difftime', 'as.data.frame.factor', 'as.data.frame.integer', 'as.data.frame.list', 'as.data.frame.logical',
                       'as.data.frame.matrix', 'as.data.frame.model.matrix', 'as.data.frame.numeric', 'as.data.frame.numeric_version',
                       'as.data.frame.ordered', 'as.data.frame.POSIXct', 'as.data.frame.POSIXlt', 'as.data.frame.raw', 'as.data.frame.table',
                       'as.data.frame.ts', 'as.data.frame.vector', 'as.Date', 'as.Date.character', 'as.Date.date', 'as.Date.dates',
                       'as.Date.default', 'as.Date.factor', 'as.Date.numeric', 'as.Date.POSIXct', 'as.Date.POSIXlt', 'as.difftime', 'as.double',
                       'as.double.difftime', 'as.double.POSIXlt', 'as.environment', 'as.expression', 'as.expression.default', 'as.factor',
                       'as.function', 'as.function.default', 'as.hexmode', 'as.integer', 'as.list', 'as.list.data.frame', 'as.list.default',
                       'as.list.environment', 'as.list.factor', 'as.list.function', 'as.list.numeric_version', 'as.logical', 'as.matrix',
                       'as.matrix.data.frame', 'as.matrix.default', 'as.matrix.noquote', 'as.matrix.POSIXlt', 'as.name', 'as.null', 'as.null.default',
                       'as.numeric', 'as.numeric_version', 'as.octmode', 'as.ordered', 'as.package_version', 'as.pairlist', 'as.POSIXct',
                       'as.POSIXct.date', 'as.POSIXct.Date', 'as.POSIXct.dates', 'as.POSIXct.default', 'as.POSIXct.numeric', 'as.POSIXct.POSIXlt',
                       'as.POSIXlt', 'as.POSIXlt.character', 'as.POSIXlt.date', 'as.POSIXlt.Date', 'as.POSIXlt.dates', 'as.POSIXlt.default',
                       'as.POSIXlt.factor', 'as.POSIXlt.numeric', 'as.POSIXlt.POSIXct', 'as.qr', 'as.raw', 'as.real', 'as.single',
                       'as.single.default', 'as.symbol', 'as.table', 'as.table.default', 'as.vector', 'as.vector.factor', 'asin', 'asinh',
                       'asNamespace', 'asS4', 'assign', 'atan', 'atan2', 'atanh', 'attach', 'attachNamespace', 'attr', 'attr.all.equal',
                       'attr<-', 'attributes', 'attributes<-', 'autoload', 'autoloader', 'backsolve', 'baseenv', 'basename', 'besselI',
                       'besselJ', 'besselK', 'besselY', 'beta', 'bindingIsActive', 'bindingIsLocked', 'bindtextdomain', 'body', 'body<-',
                       'bquote', 'break', 'browser', 'builtins', 'by', 'by.data.frame', 'by.default', 'bzfile', 'c', 'c.Date', 'c.noquote',
                       'c.numeric_version', 'c.POSIXct', 'c.POSIXlt', 'call', 'callCC', 'capabilities', 'casefold', 'cat', 'category',
                       'cbind', 'cbind.data.frame', 'ceiling', 'char.expand', 'character', 'charmatch', 'charToRaw', 'chartr', 'check_tzones',
                       'chol', 'chol.default', 'chol2inv', 'choose', 'class', 'class<-', 'close', 'close.connection', 'close.srcfile',
                       'closeAllConnections', 'codes', 'codes.factor', 'codes.ordered', 'codes<-', 'col', 'colMeans', 'colnames',
                       'colnames<-', 'colSums', 'commandArgs', 'comment', 'comment<-', 'complex', 'computeRestarts', 'conditionCall',
                       'conditionCall.condition', 'conditionMessage', 'conditionMessage.condition', 'conflicts', 'Conj', 'contributors',
                       'cos', 'cosh', 'crossprod', 'Cstack_info', 'cummax', 'cummin', 'cumprod', 'cumsum', 'cut', 'cut.Date', 'cut.default',
                       'cut.POSIXt', 'data.class', 'data.frame', 'data.matrix', 'date', 'debug', 'default.stringsAsFactors', 'delay',
                       'delayedAssign', 'deparse', 'det', 'detach', 'determinant', 'determinant.matrix', 'dget', 'diag', 'diag<-', 'diff',
                       'diff.Date', 'diff.default', 'diff.POSIXt', 'difftime', 'digamma', 'dim', 'dim.data.frame', 'dim<-', 'dimnames',
                       'dimnames.data.frame', 'dimnames<-', 'dimnames<-.data.frame', 'dir', 'dir.create', 'dirname', 'do.call', 'double',
                       'dput', 'dQuote', 'drop', 'dump', 'duplicated', 'duplicated.array', 'duplicated.data.frame', 'duplicated.default',
                       'duplicated.matrix', 'duplicated.numeric_version', 'duplicated.POSIXlt', 'dyn.load', 'dyn.unload', 'eapply', 'eigen',
                       'emptyenv', 'encodeString', 'Encoding', 'Encoding<-', 'env.profile', 'environment', 'environment<-', 'environmentIsLocked',
                       'environmentName', 'eval', 'eval.parent', 'evalq', 'exists', 'exp', 'expand.grid', 'expm1', 'expression', 'F', 'factor',
                       'factorial', 'fifo', 'file', 'file.access', 'file.append', 'file.choose', 'file.copy', 'file.create', 'file.exists',
                       'file.info', 'file.path', 'file.remove', 'file.rename', 'file.show', 'file.symlink', 'Filter', 'Find', 'findInterval',
                       'findPackageEnv', 'findRestart', 'floor', 'flush', 'flush.connection', 'for', 'force', 'formals', 'formals<-',
                       'format', 'format.AsIs', 'format.char', 'format.data.frame', 'format.Date', 'format.default', 'format.difftime',
                       'format.factor', 'format.hexmode', 'format.info', 'format.octmode', 'format.POSIXct', 'format.POSIXlt',
                       'format.pval', 'formatC', 'formatDL', 'forwardsolve', 'function', 'gamma', 'gammaCody', 'gc', 'gc.time',
                       'gcinfo', 'gctorture', 'get', 'getAllConnections', 'getCallingDLL', 'getCallingDLLe', 'getCConverterDescriptions',
                       'getCConverterStatus', 'getConnection', 'getDLLRegisteredRoutines', 'getDLLRegisteredRoutines.character',
                       'getDLLRegisteredRoutines.DLLInfo', 'getenv', 'geterrmessage', 'getExportedValue', 'getHook', 'getLoadedDLLs',
                       'getNamespace', 'getNamespaceExports', 'getNamespaceImports', 'getNamespaceInfo', 'getNamespaceName',
                       'getNamespaceUsers', 'getNamespaceVersion', 'getNativeSymbolInfo', 'getNumCConverters', 'getOption', 'getRversion',
                       'getSrcLines', 'getTaskCallbackNames', 'gettext', 'gettextf', 'getwd', 'gl', 'globalenv', 'gregexpr', 'grep',
                       'grepl', 'gsub', 'gzcon', 'gzfile', 'httpclient', 'I', 'iconv', 'iconvlist', 'icuSetCollate', 'identical', 'identity',
                       'if', 'ifelse', 'Im', 'importIntoEnv', 'inherits', 'integer', 'interaction', 'interactive', 'intersect', 'intToBits',
                       'intToUtf8', 'inverse.rle', 'invisible', 'invokeRestart', 'invokeRestartInteractively', 'is.array', 'is.atomic',
                       'is.call', 'is.character', 'is.complex', 'is.data.frame', 'is.double', 'is.element', 'is.environment',
                       'is.expression', 'is.factor', 'is.finite', 'is.function', 'is.infinite', 'is.integer', 'is.language',
                       'is.list', 'is.loaded', 'is.logical', 'is.matrix', 'is.na', 'is.na.data.frame', 'is.na.POSIXlt', 'is.na<-',
                       'is.na<-.default', 'is.na<-.factor', 'is.name', 'is.nan', 'is.null', 'is.numeric', 'is.numeric_version',
                       'is.numeric.Date', 'is.numeric.POSIXt', 'is.object', 'is.ordered', 'is.package_version', 'is.pairlist', 'is.primitive',
                       'is.qr', 'is.R', 'is.raw', 'is.real', 'is.recursive', 'is.single', 'is.symbol', 'is.table', 'is.unsorted', 'is.vector',
                       'isBaseNamespace', 'isdebugged', 'isIncomplete', 'isNamespace', 'ISOdate', 'ISOdatetime', 'isOpen', 'isRestart', 'isS4',
                       'isSeekable', 'isSymmetric', 'isSymmetric.matrix', 'isTRUE', 'jitter', 'julian', 'julian.Date', 'julian.POSIXt', 'kappa',
                       'kappa.default', 'kappa.lm', 'kappa.qr', 'kappa.tri', 'kronecker', 'l10n_info', 'La.chol', 'La.chol2inv', 'La.eigen',
                       'La.svd', 'labels', 'labels.default', 'lapply', 'lazyLoad', 'lazyLoadDBfetch', 'lbeta', 'lchoose', 'length', 'length<-',
                       'length<-.factor', 'letters', 'LETTERS', 'levels', 'levels.default', 'levels<-', 'levels<-.factor', 'lfactorial', 'lgamma',
                       'library', 'library.dynam', 'library.dynam.unload', 'licence', 'license', 'list', 'list.files', 'load', 'loadedNamespaces',
                       'loadingNamespaceInfo', 'loadNamespace', 'loadURL', 'local', 'lockBinding', 'lockEnvironment', 'log', 'log10', 'log1p', 'log2',
                       'logb', 'logical', 'lower.tri', 'ls', 'machine', 'Machine', 'make.names', 'make.unique', 'makeActiveBinding', 'manglePackageName',
                       'Map', 'mapply', 'margin.table', 'mat.or.vec', 'match', 'match.arg', 'match.call', 'match.fun', 'Math.data.frame', 'Math.Date',
                       'Math.difftime', 'Math.factor', 'Math.POSIXt', 'matrix', 'max', 'max.col', 'mean', 'mean.data.frame', 'mean.Date', 'mean.default',
                       'mean.difftime', 'mean.POSIXct', 'mean.POSIXlt', 'mem.limits', 'memory.profile', 'merge', 'merge.data.frame', 'merge.default',
                       'message', 'mget', 'min', 'missing', 'Mod', 'mode', 'mode<-', 'month.abb', 'month.name', 'months', 'months.Date',
                       'months.POSIXt', 'mostattributes<-', 'names', 'names<-', 'namespaceExport', 'namespaceImport', 'namespaceImportClasses',
                       'namespaceImportFrom', 'namespaceImportMethods', 'nargs', 'nchar', 'ncol', 'NCOL', 'Negate', 'new.env', 'next', 'NextMethod',
                       'ngettext', 'nlevels', 'noquote', 'nrow', 'NROW', 'numeric', 'numeric_version', 'nzchar', 'objects', 'oldClass',
                       'oldClass<-', 'on.exit', 'open', 'open.connection', 'open.srcfile', 'open.srcfilecopy', 'Ops.data.frame', 'Ops.Date',
                       'Ops.difftime', 'Ops.factor', 'Ops.numeric_version', 'Ops.ordered', 'Ops.POSIXt', 'options', 'order', 'ordered',
                       'outer', 'package_version', 'package.description', 'packageEvent', 'packageHasNamespace', 'packageStartupMessage',
                       'packBits', 'pairlist', 'parent.env', 'parent.env<-', 'parent.frame', 'parse', 'parse.dcf', 'parseNamespaceFile',
                       'paste', 'path.expand', 'pentagamma', 'pi', 'pipe', 'Platform', 'pmatch', 'pmax', 'pmax.int', 'pmin', 'pmin.int',
                       'polyroot', 'pos.to.env', 'Position', 'pretty', 'prettyNum', 'print', 'print.AsIs', 'print.atomic', 'print.by',
                       'print.condition', 'print.connection', 'print.data.frame', 'print.Date', 'print.default', 'print.difftime',
                       'print.DLLInfo', 'print.DLLInfoList', 'print.DLLRegisteredRoutines', 'print.factor', 'print.hexmode', 'print.libraryIQR',
                       'print.listof', 'print.NativeRoutineList', 'print.noquote', 'print.numeric_version', 'print.octmode', 'print.packageInfo',
                       'print.POSIXct', 'print.POSIXlt', 'print.proc_time', 'print.restart', 'print.rle', 'print.simple.list',
                       'print.srcfile', 'print.srcref', 'print.summary.table', 'print.table', 'print.warnings', 'printNoClass',
                       'prmatrix', 'proc.time', 'prod', 'prop.table', 'provide', 'psigamma', 'pushBack', 'pushBackLength', 'q', 'qr',
                       'qr.coef', 'qr.default', 'qr.fitted', 'qr.Q', 'qr.qty', 'qr.qy', 'qr.R', 'qr.resid', 'qr.solve', 'qr.X', 'quarters',
                       'quarters.Date', 'quarters.POSIXt', 'quit', 'quote', 'R_system_version', 'R.home', 'R.version', 'R.Version',
                       'R.version.string', 'range', 'range.default', 'rank', 'rapply', 'raw', 'rawConnection', 'rawConnectionValue',
                       'rawShift', 'rawToBits', 'rawToChar', 'rbind', 'rbind.data.frame', 'rcond', 'Re', 'read.dcf', 'read.table.url',
                       'readBin', 'readChar', 'readline', 'readLines', 'real', 'Recall', 'Reduce', 'reg.finalizer', 'regexpr',
                       'registerS3method', 'registerS3methods', 'remove', 'removeCConverter', 'removeTaskCallback', 'rep', 'rep.Date',
                       'rep.factor', 'rep.int', 'rep.numeric_version', 'rep.POSIXct', 'rep.POSIXlt', 'repeat', 'replace', 'replicate',
                       'require', 'restart', 'restartDescription', 'restartFormals', 'retracemem', 'return', 'rev', 'rev.default', 'rle',
                       'rm', 'RNGkind', 'RNGversion', 'round', 'round.Date', 'round.difftime', 'round.POSIXt', 'row', 'row.names',
                       'row.names.data.frame', 'row.names.default', 'row.names<-', 'row.names<-.data.frame', 'row.names<-.default',
                       'rowMeans', 'rownames', 'rownames<-', 'rowsum', 'rowsum.data.frame', 'rowsum.default', 'rowSums', 'sample',
                       'sample.int', 'sapply', 'save', 'save.image', 'saveNamespaceImage', 'scale', 'scale.default', 'scan', 'scan.url',
                       'search', 'searchpaths', 'seek', 'seek.connection', 'seq', 'seq_along', 'seq_len', 'seq.Date', 'seq.default',
                       'seq.int', 'seq.POSIXt', 'sequence', 'serialize', 'set.seed', 'setCConverterStatus', 'setdiff', 'setequal',
                       'setHook', 'setNamespaceInfo', 'setSessionTimeLimit', 'setTimeLimit', 'setwd', 'showConnections', 'shQuote',
                       'sign', 'signalCondition', 'signif', 'simpleCondition', 'simpleError', 'simpleMessage', 'simpleWarning', 'sin',
                       'single', 'sinh', 'sink', 'sink.number', 'slice.index', 'socketConnection', 'socketSelect', 'solve', 'solve.default',
                       'solve.qr', 'sort', 'sort.default', 'sort.int', 'sort.list', 'sort.POSIXlt', 'source', 'source.url', 'split',
                       'split.data.frame', 'split.Date', 'split.default', 'split.POSIXct', 'split<-', 'split<-.data.frame', 'split<-.default',
                       'sprintf', 'sqrt', 'sQuote', 'srcfile', 'srcfilecopy', 'srcref', 'standardGeneric', 'stderr', 'stdin', 'stdout',
                       'stop', 'stopifnot', 'storage.mode', 'storage.mode<-', 'strftime', 'strptime', 'strsplit', 'strtrim', 'structure',
                       'strwrap', 'sub', 'subset', 'subset.data.frame', 'subset.default', 'subset.matrix', 'substitute', 'substr',
                       'substr<-', 'substring', 'substring<-', 'sum', 'summary', 'summary.connection', 'summary.data.frame',
                       'Summary.data.frame', 'summary.Date', 'Summary.Date', 'summary.default', 'Summary.difftime',
                       'summary.factor', 'Summary.factor', 'summary.matrix', 'Summary.numeric_version', 'summary.POSIXct',
                       'Summary.POSIXct', 'summary.POSIXlt', 'Summary.POSIXlt', 'summary.table', 'suppressMessages',
                       'suppressPackageStartupMessages', 'suppressWarnings', 'svd', 'sweep', 'switch', 'symbol.C',
                       'symbol.For', 'sys.call', 'sys.calls', 'Sys.chmod', 'Sys.Date', 'sys.frame', 'sys.frames',
                       'sys.function', 'Sys.getenv', 'Sys.getlocale', 'Sys.getpid', 'Sys.glob', 'Sys.info', 'sys.load.image',
                       'Sys.localeconv', 'sys.nframe', 'sys.on.exit', 'sys.parent', 'sys.parents', 'Sys.putenv',
                       'sys.save.image', 'Sys.setenv', 'Sys.setlocale', 'Sys.sleep', 'sys.source', 'sys.status',
                       'Sys.time', 'Sys.timezone', 'Sys.umask', 'Sys.unsetenv', 'Sys.which', 'system', 'system.file',
                       'system.time', 't', 'T', 't.data.frame', 't.default', 'table', 'tabulate', 'tan', 'tanh', 'tapply',
                       'taskCallbackManager', 'tcrossprod', 'tempdir', 'tempfile', 'testPlatformEquivalence', 'tetragamma',
                       'textConnection', 'textConnectionValue', 'tolower', 'topenv', 'toString', 'toString.default', 'toupper',
                       'trace', 'traceback', 'tracemem', 'tracingState', 'transform', 'transform.data.frame', 'transform.default',
                       'trigamma', 'trunc', 'trunc.Date', 'trunc.POSIXt', 'truncate', 'truncate.connection', 'try', 'tryCatch',
                       'typeof', 'unclass', 'undebug', 'union', 'unique', 'unique.array', 'unique.data.frame', 'unique.default',
                       'unique.matrix', 'unique.numeric_version', 'unique.POSIXlt', 'units', 'units.difftime', 'units<-',
                       'units<-.difftime', 'unix', 'unix.time', 'unlink', 'unlist', 'unloadNamespace', 'unlockBinding',
                       'unname', 'unserialize', 'unsplit', 'untrace', 'untracemem', 'unz', 'upper.tri', 'url', 'UseMethod',
                       'utf8ToInt', 'vector', 'Vectorize', 'version', 'Version', 'warning', 'warnings', 'weekdays',
                       'weekdays.Date', 'weekdays.POSIXt', 'which', 'which.max', 'which.min', 'while', 'with',
                       'with.default', 'withCallingHandlers', 'within', 'within.data.frame', 'within.list', 'withRestarts',
                       'withVisible', 'write', 'write.dcf', 'write.table0', 'writeBin', 'writeChar', 'writeLines', 'xor',
                       'xpdrows.data.frame', 'xtfrm', 'xtfrm.Date', 'xtfrm.default', 'xtfrm.factor', 'xtfrm.numeric_version',
                       'xtfrm.POSIXct', 'xtfrm.POSIXlt', 'xtfrm.Surv', 'zapsmall'];
    var datasets = ['ability.cov', 'airmiles', 'AirPassengers', 'airquality',
                    'anscombe', 'attenu', 'attitude', 'austres', 'beaver1',
                    'beaver2', 'BJsales', 'BJsales.lead', 'BOD', 'cars',
                    'ChickWeight', 'chickwts', 'co2', 'crimtab',
                    'discoveries', 'DNase', 'esoph', 'euro', 'euro.cross',
                    'eurodist', 'EuStockMarkets', 'faithful', 'fdeaths',
                    'Formaldehyde', 'freeny', 'freeny.x', 'freeny.y',
                    'HairEyeColor', 'Harman23.cor', 'Harman74.cor', 'Indometh',
                    'infert', 'InsectSprays', 'iris', 'iris3', 'islands',
                    'JohnsonJohnson', 'LakeHuron', 'ldeaths', 'lh', 'LifeCycleSavings',
                    'Loblolly', 'longley', 'lynx', 'mdeaths', 'morley', 'mtcars',
                    'nhtemp', 'Nile', 'nottem', 'occupationalStatus', 'Orange',
                    'OrchardSprays', 'PlantGrowth', 'precip', 'presidents',
                    'pressure', 'Puromycin', 'quakes', 'randu', 'rivers', 'rock',
                    'Seatbelts', 'sleep', 'stack.loss', 'stack.x', 'stackloss',
                    'state.abb', 'state.area', 'state.center', 'state.division',
                    'state.name', 'state.region', 'state.x77', 'sunspot.month',
                    'sunspot.year', 'sunspots', 'swiss', 'Theoph', 'Titanic', 'ToothGrowth',
                    'treering', 'trees', 'UCBAdmissions', 'UKDriverDeaths', 'UKgas',
                    'USAccDeaths', 'USArrests', 'USJudgeRatings', 'USPersonalExpenditure',
                    'uspop', 'VADeaths', 'volcano', 'warpbreaks', 'women', 'WorldPhones',
                    'WWWusage'];
    var graphicspackage = ['abline', 'arrows', 'assocplot', 'axis', 'Axis', 'axis.Date', 'axis.POSIXct',
                           'axTicks', 'barplot', 'barplot.default', 'box', 'boxplot', 'boxplot.default',
                           'boxplot.matrix', 'bxp', 'cdplot', 'clip', 'close.screen', 'co.intervals',
                           'contour', 'contour.default', 'coplot', 'curve', 'dotchart', 'erase.screen',
                           'filled.contour', 'fourfoldplot', 'frame', 'grconvertX', 'grconvertY', 'grid',
                           'hist', 'hist.default', 'identify', 'image', 'image.default', 'layout',
                           'layout.show', 'lcm', 'legend', 'lines', 'lines.default', 'locator', 'matlines',
                           'matplot', 'matpoints', 'mosaicplot', 'mtext', 'pairs', 'pairs.default',
                           'panel.smooth', 'par', 'persp', 'pie', 'piechart', 'plot', 'plot.default',
                           'plot.design', 'plot.new', 'plot.window', 'plot.xy', 'points', 'points.default',
                           'polygon', 'rect', 'rug', 'screen', 'segments', 'smoothScatter', 'spineplot',
                           'split.screen', 'stars', 'stem', 'strheight', 'stripchart', 'strwidth', 'sunflowerplot',
                           'symbols', 'text', 'text.default', 'title', 'xinch', 'xspline', 'xyinch', 'yinch'];
    var grdevicespackage = ['as.graphicsAnnot', 'bitmap', 'blues9', 'bmp', 'boxplot.stats', 'cairo_pdf', 'cairo_ps', 'check.options',
                            'chull', 'CIDFont', 'cm', 'cm.colors', 'col2rgb', 'colorConverter', 'colorRamp', 'colorRampPalette',
                            'colors', 'colorspaces', 'colours', 'contourLines', 'convertColor', 'densCols', 'dev.control', 'dev.copy',
                            'dev.copy2eps', 'dev.copy2pdf', 'dev.cur', 'dev.interactive', 'dev.list', 'dev.new', 'dev.next', 'dev.off',
                            'dev.prev', 'dev.print', 'dev.set', 'dev.size', 'dev2bitmap', 'devAskNewPage', 'deviceIsInteractive',
                            'embedFonts', 'extendrange', 'getGraphicsEvent', 'graphics.off', 'gray', 'gray.colors', 'grey', 'grey.colors',
                            'hcl', 'heat.colors', 'Hershey', 'hsv', 'jpeg', 'make.rgb', 'n2mfrow', 'nclass.FD', 'nclass.scott',
                            'nclass.Sturges', 'palette', 'pdf', 'pdf.options', 'pdfFonts', 'pictex', 'png', 'postscript', 'postscriptFont',
                            'postscriptFonts', 'ps.options', 'quartz', 'quartz.options', 'quartzFont', 'quartzFonts', 'rainbow',
                            'recordGraphics', 'recordPlot', 'replayPlot', 'rgb', 'rgb2hsv', 'savePlot', 'setEPS', 'setPS', 'svg',
                            'terrain.colors', 'tiff', 'topo.colors', 'trans3d', 'Type1Font', 'x11', 'X11', 'X11.options', 'X11Font',
                            'X11Fonts', 'xfig', 'xy.coords', 'xyTable', 'xyz.coords'];
    var methodspackage = ['addNextMethod', 'allGenerics', 'allNames', 'Arith', 'as', 'as<-',
                          'asMethodDefinition', 'assignClassDef', 'assignMethodsMetaData', 'balanceMethodsList',
                          'cacheGenericsMetaData', 'cacheMetaData', 'cacheMethod', 'callGeneric',
                          'callNextMethod', 'canCoerce', 'cbind2', 'checkSlotAssignment', 'classesToAM',
                          'classMetaName', 'coerce', 'coerce<-', 'Compare', 'completeClassDefinition',
                          'completeExtends', 'completeSubclasses', 'Complex', 'conformMethod', 'defaultDumpName',
                          'defaultPrototype', 'doPrimitiveMethod', 'dumpMethod', 'dumpMethods', 'el', 'el<-',
                          'elNamed', 'elNamed<-', 'empty.dump', 'emptyMethodsList', 'existsFunction', 'existsMethod',
                          'extends', 'finalDefaultMethod', 'findClass', 'findFunction', 'findMethod', 'findMethods',
                          'findMethodSignatures', 'findUnique', 'fixPre1.8', 'formalArgs', 'functionBody',
                          'functionBody<-', 'generic.skeleton', 'getAccess', 'getAllMethods', 'getAllSuperClasses',
                          'getClass', 'getClassDef', 'getClasses', 'getClassName', 'getClassPackage', 'getDataPart',
                          'getExtends', 'getFunction', 'getGeneric', 'getGenerics', 'getGroup', 'getGroupMembers',
                          'getMethod', 'getMethods', 'getMethodsForDispatch', 'getMethodsMetaData', 'getPackageName',
                          'getProperties', 'getPrototype', 'getSlots', 'getSubclasses', 'getValidity', 'getVirtual',
                          'hasArg', 'hasMethod', 'hasMethods', 'implicitGeneric', 'initialize', 'insertMethod', 'is',
                          'isClass', 'isClassDef', 'isClassUnion', 'isGeneric', 'isGrammarSymbol', 'isGroup',
                          'isSealedClass', 'isSealedMethod', 'isVirtualClass', 'isXS3Class', 'languageEl', 'languageEl<-',
                          'linearizeMlist', 'listFromMethods', 'listFromMlist', 'loadMethod', 'Logic',
                          'makeClassRepresentation', 'makeExtends', 'makeGeneric', 'makeMethodsList',
                          'makePrototypeFromClassDef', 'makeStandardGeneric', 'matchSignature', 'Math', 'Math2', 'mergeMethods',
                          'metaNameUndo', 'method.skeleton', 'MethodAddCoerce', 'methodSignatureMatrix', 'MethodsList',
                          'MethodsListSelect', 'methodsPackageMetaName', 'missingArg', 'mlistMetaName', 'new', 'newBasic',
                          'newClassRepresentation', 'newEmptyObject', 'Ops', 'packageSlot', 'packageSlot<-', 'possibleExtends',
                          'prohibitGeneric', 'promptClass', 'promptMethods', 'prototype', 'Quote', 'rbind2',
                          'reconcilePropertiesAndPrototype', 'registerImplicitGenerics', 'rematchDefinition',
                          'removeClass', 'removeGeneric', 'removeMethod', 'removeMethods', 'removeMethodsObject', 'representation',
                          'requireMethods', 'resetClass', 'resetGeneric', 'S3Class', 'S3Class<-', 'S3Part', 'S3Part<-', 'sealClass',
                          'seemsS4Object', 'selectMethod', 'selectSuperClasses', 'sessionData', 'setAs', 'setClass', 'setClassUnion',
                          'setDataPart', 'setGeneric', 'setGenericImplicit', 'setGroupGeneric', 'setIs', 'setMethod', 'setOldClass',
                          'setPackageName', 'setPrimitiveMethods', 'setReplaceMethod', 'setValidity', 'show', 'showClass', 'showDefault',
                          'showExtends', 'showMethods', 'showMlist', 'signature', 'SignatureMethod', 'sigToEnv', 'slot', 'slot<-',
                          'slotNames', 'slotsFromS3', 'substituteDirect', 'substituteFunctionArgs', 'Summary', 'superClassDepth',
                          'testInheritedMethods', 'testVirtual', 'traceOff', 'traceOn', 'tryNew', 'trySilent', 'unRematchDefinition',
                          'validObject', 'validSlotNames'];
    var statspackage = ['acf', 'acf2AR', 'add.scope', 'add1', 'addmargins', 'aggregate',
                        'aggregate.data.frame', 'aggregate.default', 'aggregate.ts', 'AIC',
                        'alias', 'anova', 'anova.glm', 'anova.glmlist', 'anova.lm', 'anova.lmlist',
                        'anova.mlm', 'anovalist.lm', 'ansari.test', 'aov', 'approx', 'approxfun',
                        'ar', 'ar.burg', 'ar.mle', 'ar.ols', 'ar.yw', 'arima', 'arima.sim',
                        'arima0', 'arima0.diag', 'ARMAacf', 'ARMAtoMA', 'as.dendrogram', 'as.dist',
                        'as.formula', 'as.hclust', 'as.stepfun', 'as.ts', 'asOneSidedFormula', 'ave',
                        'bandwidth.kernel', 'bartlett.test', 'binom.test', 'binomial', 'biplot',
                        'Box.test', 'bw.bcv', 'bw.nrd', 'bw.nrd0', 'bw.SJ', 'bw.ucv', 'C', 'cancor',
                        'case.names', 'ccf', 'chisq.test', 'clearNames', 'cmdscale', 'coef', 'coefficients',
                        'complete.cases', 'confint', 'confint.default', 'constrOptim', 'contr.helmert',
                        'contr.poly', 'contr.SAS', 'contr.sum', 'contr.treatment', 'contrasts', 'contrasts<-',
                        'convolve', 'cooks.distance', 'cophenetic', 'cor', 'cor.test', 'cov', 'cov.wt',
                        'cov2cor', 'covratio', 'cpgram', 'cutree', 'cycle', 'D', 'dbeta', 'dbinom', 'dcauchy',
                        'dchisq', 'decompose', 'delete.response', 'deltat', 'dendrapply', 'density', 'density.default',
                        'deriv', 'deriv.default', 'deriv.formula', 'deriv3', 'deriv3.default', 'deriv3.formula',
                        'deviance', 'dexp', 'df', 'df.kernel', 'df.residual', 'dfbeta', 'dfbetas', 'dffits',
                        'dgamma', 'dgeom', 'dhyper', 'diff.ts', 'diffinv', 'dist', 'dlnorm', 'dlogis',
                        'dmultinom', 'dnbinom', 'dnorm', 'dpois', 'drop.scope', 'drop.terms', 'drop1',
                        'dsignrank', 'dt', 'dummy.coef', 'dunif', 'dweibull', 'dwilcox', 'ecdf', 'eff.aovlist',
                        'effects', 'embed', 'end', 'estVar', 'expand.model.frame', 'extractAIC', 'factanal',
                        'factor.scope', 'family', 'fft', 'filter', 'fisher.test', 'fitted', 'fitted.values',
                        'fivenum', 'fligner.test', 'formula', 'frequency', 'friedman.test', 'ftable', 'Gamma',
                        'gaussian', 'get_all_vars', 'getInitial', 'glm', 'glm.control', 'glm.fit', 'glm.fit.null',
                        'hasTsp', 'hat', 'hatvalues', 'hatvalues.lm', 'hclust', 'heatmap', 'HoltWinters', 'influence',
                        'influence.measures', 'integrate', 'interaction.plot', 'inverse.gaussian', 'IQR',
                        'is.empty.model', 'is.leaf', 'is.mts', 'is.stepfun', 'is.ts', 'is.tskernel', 'isoreg',
                        'KalmanForecast', 'KalmanLike', 'KalmanRun', 'KalmanSmooth', 'kernapply', 'kernel', 'kmeans',
                        'knots', 'kruskal.test', 'ks.test', 'ksmooth', 'lag', 'lag.plot', 'line', 'lines.ts', 'lm',
                        'lm.fit', 'lm.fit.null', 'lm.influence', 'lm.wfit', 'lm.wfit.null', 'loadings', 'loess',
                        'loess.control', 'loess.smooth', 'logLik', 'loglin', 'lowess', 'ls.diag', 'ls.print', 'lsfit',
                        'mad', 'mahalanobis', 'make.link', 'makeARIMA', 'makepredictcall', 'manova', 'mantelhaen.test',
                        'mauchley.test', 'mauchly.test', 'mcnemar.test', 'median', 'median.default', 'medpolish',
                        'model.extract', 'model.frame', 'model.frame.aovlist', 'model.frame.default', 'model.frame.glm',
                        'model.frame.lm', 'model.matrix', 'model.matrix.default', 'model.matrix.lm', 'model.offset',
                        'model.response', 'model.tables', 'model.weights', 'monthplot', 'mood.test', 'mvfft', 'na.action',
                        'na.contiguous', 'na.exclude', 'na.fail', 'na.omit', 'na.pass', 'napredict', 'naprint', 'naresid',
                        'nextn', 'nlm', 'nlminb', 'nls', 'nls.control', 'NLSstAsymptotic', 'NLSstClosestX', 'NLSstLfAsymptote',
                        'NLSstRtAsymptote', 'numericDeriv', 'offset', 'oneway.test', 'optim', 'optimise', 'optimize',
                        'order.dendrogram', 'p.adjust', 'p.adjust.methods', 'pacf', 'pairwise.prop.test', 'pairwise.t.test',
                        'pairwise.table', 'pairwise.wilcox.test', 'pbeta', 'pbinom', 'pbirthday', 'pcauchy', 'pchisq', 'pexp',
                        'pf', 'pgamma', 'pgeom', 'phyper', 'plclust', 'plnorm', 'plogis', 'plot.density', 'plot.ecdf', 'plot.lm',
                        'plot.mlm', 'plot.spec', 'plot.spec.coherency', 'plot.spec.phase', 'plot.stepfun', 'plot.ts', 'plot.TukeyHSD',
                        'pnbinom', 'pnorm', 'poisson', 'poisson.test', 'poly', 'polym', 'power', 'power.anova.test', 'power.prop.test',
                        'power.t.test', 'PP.test', 'ppoints', 'ppois', 'ppr', 'prcomp', 'predict', 'predict.glm', 'predict.lm',
                        'predict.mlm', 'predict.poly', 'preplot', 'princomp', 'print.anova', 'print.coefmat', 'print.density',
                        'print.family', 'print.formula', 'print.ftable', 'print.glm', 'print.infl', 'print.integrate', 'print.lm',
                        'print.logLik', 'print.terms', 'print.ts', 'printCoefmat', 'profile', 'proj', 'promax', 'prop.test',
                        'prop.trend.test', 'psignrank', 'pt', 'ptukey', 'punif', 'pweibull', 'pwilcox', 'qbeta', 'qbinom',
                        'qbirthday', 'qcauchy', 'qchisq', 'qexp', 'qf', 'qgamma', 'qgeom', 'qhyper', 'qlnorm', 'qlogis',
                        'qnbinom', 'qnorm', 'qpois', 'qqline', 'qqnorm', 'qqnorm.default', 'qqplot', 'qsignrank', 'qt',
                        'qtukey', 'quade.test', 'quantile', 'quantile.default', 'quasi', 'quasibinomial', 'quasipoisson',
                        'qunif', 'qweibull', 'qwilcox', 'r2dtable', 'rbeta', 'rbinom', 'rcauchy', 'rchisq', 'read.ftable',
                        'rect.hclust', 'reformulate', 'relevel', 'reorder', 'replications', 'reshape', 'reshapeLong', 'reshapeWide',
                        'resid', 'residuals', 'residuals.default', 'residuals.glm', 'residuals.lm', 'rexp', 'rf', 'rgamma', 'rgeom',
                        'rhyper', 'rlnorm', 'rlogis', 'rmultinom', 'rnbinom', 'rnorm', 'rpois', 'rsignrank', 'rstandard', 'rstandard.glm',
                        'rstandard.lm', 'rstudent', 'rstudent.glm', 'rstudent.lm', 'rt', 'runif', 'runmed', 'rweibull', 'rwilcox',
                        'scatter.smooth', 'screeplot', 'sd', 'se.contrast', 'selfStart', 'setNames', 'shapiro.test', 'simulate',
                        'smooth', 'smooth.spline', 'smoothEnds', 'sortedXyData', 'spec.ar', 'spec.pgram', 'spec.taper', 'spectrum',
                        'spline', 'splinefun', 'splinefunH', 'SSasymp', 'SSasympOff', 'SSasympOrig', 'SSbiexp', 'SSD', 'SSfol',
                        'SSfpl', 'SSgompertz', 'SSlogis', 'SSmicmen', 'SSweibull', 'start', 'stat.anova', 'step', 'stepfun', 'stl',
                        'StructTS', 'summary.aov', 'summary.aovlist', 'summary.glm', 'summary.infl', 'summary.lm', 'summary.manova',
                        'summary.mlm', 'summary.stepfun', 'supsmu', 'symnum', 't.test', 'termplot', 'terms', 'terms.aovlist',
                        'terms.default', 'terms.formula', 'terms.terms', 'time', 'toeplitz', 'ts', 'ts.intersect', 'ts.plot',
                        'ts.union', 'tsdiag', 'tsp', 'tsp<-', 'tsSmooth', 'TukeyHSD', 'TukeyHSD.aov', 'uniroot', 'update',
                        'update.default', 'update.formula', 'var', 'var.test', 'variable.names', 'varimax', 'vcov', 'weighted.mean',
                        'weighted.residuals', 'weights', 'wilcox.test', 'window', 'window<-', 'write.ftable', 'xtabs'];
    var utilspackage = ['alarm', 'apropos', 'argsAnywhere', 'as.person', 'as.personList', 'as.relistable', 'as.roman',
                        'assignInNamespace', 'available.packages', 'browseEnv', 'browseURL', 'browseVignettes', 'bug.report',
                        'capture.output', 'checkCRAN', 'chooseCRANmirror', 'citation', 'citEntry', 'citFooter', 'citHeader',
                        'close.socket', 'combn', 'compareVersion', 'contrib.url', 'count.fields', 'CRAN.packages', 'data',
                        'data.entry', 'dataentry', 'de', 'de.ncols', 'de.restore', 'de.setup', 'debugger', 'demo', 'download.file',
                        'download.packages', 'dump.frames', 'edit', 'emacs', 'example', 'file_test', 'file.edit', 'find', 'fix',
                        'fixInNamespace', 'flush.console', 'formatOL', 'formatUL', 'getAnywhere', 'getCRANmirrors', 'getFromNamespace',
                        'getS3method', 'getTxtProgressBar', 'glob2rx', 'head', 'head.matrix', 'help', 'help.request', 'help.search',
                        'help.start', 'history', 'index.search', 'install.packages', 'installed.packages', 'is.relistable',
                        'limitedLabels', 'loadhistory', 'localeToCharset', 'ls.str', 'lsf.str', 'make.packages.html', 'make.socket',
                        'makeRweaveLatexCodeRunner', 'memory.limit', 'memory.size', 'menu', 'methods', 'mirror2html', 'modifyList',
                        'new.packages', 'normalizePath', 'nsl', 'object.size', 'old.packages', 'package.contents', 'package.skeleton',
                        'packageDescription', 'packageStatus', 'page', 'person', 'personList', 'pico', 'prompt', 'promptData',
                        'promptPackage', 'rc.getOption', 'rc.options', 'rc.settings', 'rc.status', 'read.csv', 'read.csv2', 'read.delim',
                        'read.delim2', 'read.DIF', 'read.fortran', 'read.fwf', 'read.socket', 'read.table', 'readCitationFile', 'recover',
                        'relist', 'remove.packages', 'Rprof', 'Rprofmem', 'RShowDoc', 'RSiteSearch', 'rtags', 'Rtangle', 'RtangleSetup',
                        'RtangleWritedoc', 'RweaveChunkPrefix', 'RweaveEvalWithOpt', 'RweaveLatex', 'RweaveLatexFinish', 'RweaveLatexOptions',
                        'RweaveLatexSetup', 'RweaveLatexWritedoc', 'RweaveTryStop', 'savehistory', 'select.list', 'sessionInfo',
                        'setRepositories', 'setTxtProgressBar', 'stack', 'Stangle', 'str', 'strOptions', 'summaryRprof', 'Sweave',
                        'SweaveHooks', 'SweaveSyntaxLatex', 'SweaveSyntaxNoweb', 'SweaveSyntConv', 'tail', 'tail.matrix', 'timestamp',
                        'toBibtex', 'toLatex', 'txtProgressBar', 'type.convert', 'unstack', 'unzip', 'update.packages', 'update.packageStatus',
                        'upgrade', 'url.show', 'URLdecode', 'URLencode', 'vi', 'View', 'vignette', 'write.csv', 'write.csv2', 'write.socket',
                        'write.table', 'wsbrowser', 'xedit', 'xemacs', 'zip.file.extract'];
    var rsplus2 = {'types': ['basestring', 'buffer', 'file', 'long', 'unicode',
                         'xrange'],
               'keywords': ['exec', 'print'],
               'version': 2 };
    var rsplus3 = {'types': ['bytearray', 'bytes', 'filter', 'map', 'memoryview',
                         'open', 'range', 'zip'],
               'keywords': ['nonlocal'],
               'version': 3};

    var rsplus, keywords, types, stringStarters, stringTypes, config;

    function configure(conf) {
        if (!conf.hasOwnProperty('rsplusVersion')) {
            conf.rsplusVersion = 2;
        }
        if (!conf.hasOwnProperty('strictErrors')) {
            conf.strictErrors = true;
        }
        if (conf.rsplusVersion != 2 && conf.rsplusVersion != 3) {
            alert('CodeMirror: Unknown RSplus Version "' +
                  conf.rsplusVersion +
                  '", defaulting to RSplus 2.x.');
            conf.rsplusVersion = 2;
        }
        if (conf.rsplusVersion == 3) {
            rsplus = rsplus3;
            stringStarters = /[\'\"rbRB]/;
            stringTypes = /[rb]/;
            doubleDelimiters.push('\\-\\>');
        } else {
            rsplus = rsplus2;
            stringStarters = /[\'\"RUru]/;
            stringTypes = /[ru]/;
        }
        config = conf;
        keywords = wordRegexp(commonkeywords.concat(rsplus.keywords));
        types = wordRegexp(commontypes.concat(rsplus.types));
        doubleDelimiters = wordRegexp(doubleDelimiters);
    }

    var tokenizeRSplus = (function() {
        function normal(source, setState) {
            var stringDelim, threeStr, temp, type, word, possible = {};
            var ch = source.next();
            
            function filterPossible(token, styleIfPossible) {
                if (!possible.style && !possible.content) {
                    return token;
                } else if (typeof(token) == STRINGCONTEXT) {
                    token = {content: source.get(), style: token};
                }
                if (possible.style || styleIfPossible) {
                    token.style = styleIfPossible ? styleIfPossible : possible.style;
                }
                if (possible.content) {
                    token.content = possible.content + token.content;
                }
                possible = {};
                return token;
            }

            // Handle comments
            if (ch == '#') {
                while (!source.endOfLine()) {
                    source.next();
                }
                return 'rsplus-comment';
            }
            // Handle special chars
            if (ch == '\\') {
                if (!source.endOfLine()) {
                    var whitespace = true;
                    while (!source.endOfLine()) {
                        if(!(/[\s\u00a0]/.test(source.next()))) {
                            whitespace = false;
                        }
                    }
                    if (!whitespace) {
                        return ERRORCLASS;
                    }
                }
                return 'rsplus-special';
            }
            // Handle operators and delimiters
            if (singleStarters.indexOf(ch) != -1 || (ch == "." && !source.matches(/\d/))) {
                if (doubleStarters.indexOf(source.peek()) != -1) {
                    temp = ch + source.peek();
                    // It must be a double delimiter or operator or triple delimiter
                    if (doubleOperators.test(temp)) {
                        source.next();
                        var nextChar = source.peek();
                        if (nextChar && tripleDelimiters.test(temp + nextChar)) {
                            source.next();
                            return DELIMITERCLASS;
                        } else {
                            return OPERATORCLASS;
                        }
                    } else if (doubleDelimiters.test(temp)) {
                        source.next();
                        return DELIMITERCLASS;
                    }
                }
                // It must be a single delimiter or operator
                if (singleOperators.indexOf(ch) != -1 || ch == ".") {
                    return OPERATORCLASS;
                } else if (singleDelimiters.indexOf(ch) != -1) {
                    if (ch == '@' && source.matches(/\w/)) {
                        source.nextWhileMatches(/[\w\d_]/);
                        return {style:'rsplus-decorator',
                                content: source.get()};
                    } else {
                        return DELIMITERCLASS;
                    }
                } else {
                    return ERRORCLASS;
                }
            }
            // Handle number literals
            if (/\d/.test(ch) || (ch == "." && source.matches(/\d/))) {
                if (ch === '0' && !source.endOfLine()) {
                    switch (source.peek()) {
                        case 'o':
                        case 'O':
                            source.next();
                            source.nextWhileMatches(/[0-7]/);
                            return filterPossible(LITERALCLASS, ERRORCLASS);
                        case 'x':
                        case 'X':
                            source.next();
                            source.nextWhileMatches(/[0-9A-Fa-f]/);
                            return filterPossible(LITERALCLASS, ERRORCLASS);
                        case 'b':
                        case 'B':
                            source.next();
                            source.nextWhileMatches(/[01]/);
                            return filterPossible(LITERALCLASS, ERRORCLASS);
                    }
                }
                source.nextWhileMatches(/\d/);
                if (ch != '.' && source.peek() == '.') {
                    source.next();
                    source.nextWhileMatches(/\d/);
                }
                // Grab an exponent
                if (source.matches(/e/i)) {
                    source.next();
                    if (source.peek() == '+' || source.peek() == '-') {
                        source.next();
                    }
                    if (source.matches(/\d/)) {
                        source.nextWhileMatches(/\d/);
                    } else {
                        return filterPossible(ERRORCLASS);
                    }
                }
                // Grab a complex number
                if (source.matches(/j/i)) {
                    source.next();
                }

                return filterPossible(LITERALCLASS);
            }
            // Handle strings
            if (stringStarters.test(ch)) {
                var peek = source.peek();
                var stringType = STRINGCLASS;
                if ((stringTypes.test(ch)) && (peek == '"' || peek == "'")) {
                    switch (ch.toLowerCase()) {
                        case 'b':
                            stringType = BYTESCLASS;
                            break;
                        case 'r':
                            stringType = RAWCLASS;
                            break;
                        case 'u':
                            stringType = UNICODECLASS;
                            break;
                    }
                    ch = source.next();
                    stringDelim = ch;
                    if (source.peek() != stringDelim) {
                        setState(inString(stringType, stringDelim));
                        return null;
                    } else {
                        source.next();
                        if (source.peek() == stringDelim) {
                            source.next();
                            threeStr = stringDelim + stringDelim + stringDelim;
                            setState(inString(stringType, threeStr));
                            return null;
                        } else {
                            return stringType;
                        }
                    }
                } else if (ch == "'" || ch == '"') {
                    stringDelim = ch;
                    if (source.peek() != stringDelim) {
                        setState(inString(stringType, stringDelim));
                        return null;
                    } else {
                        source.next();
                        if (source.peek() == stringDelim) {
                            source.next();
                            threeStr = stringDelim + stringDelim + stringDelim;
                            setState(inString(stringType, threeStr));
                            return null;
                        } else {
                            return stringType;
                        }
                    }
                }
            }
            // Handle Identifier
            if (identifierStarters.test(ch)) {
                source.nextWhileMatches(/[\w\d_]/);
                word = source.get();
                if (wordOperators.test(word)) {
                    type = OPERATORCLASS;
                } else if (keywords.test(word)) {
                    type = 'rsplus-keyword';
                } else if (types.test(word)) {
                    type = 'rsplus-type';
                } else {
                    type = IDENTIFIERCLASS;
                    while (source.peek() == '.') {
                        source.next();
                        if (source.matches(identifierStarters)) {
                            source.nextWhileMatches(/[\w\d]/);
                        } else {
                            type = ERRORCLASS;
                            break;
                        }
                    }
                    word = word + source.get();
                }
                return filterPossible({style: type, content: word});
            }

            // Register Dollar sign and Question mark as errors. Always!
            if (/\$\?/.test(ch)) {
                return filterPossible(ERRORCLASS);
            }

            return filterPossible(ERRORCLASS);
        }

        function inString(style, terminator) {
            return function(source, setState) {
                var matches = [];
                var found = false;
                while (!found && !source.endOfLine()) {
                    var ch = source.next(), newMatches = [];
                    // Skip escaped characters
                    if (ch == '\\') {
                        if (source.peek() == '\n') {
                            break;
                        }
                        ch = source.next();
                    }
                    if (ch == terminator.charAt(0)) {
                        matches.push(terminator);
                    }
                    for (var i = 0; i < matches.length; i++) {
                        var match = matches[i];
                        if (match.charAt(0) == ch) {
                            if (match.length == 1) {
                                setState(normal);
                                found = true;
                                break;
                            } else {
                                newMatches.push(match.slice(1));
                            }
                        }
                    }
                    matches = newMatches;
                }
                return style;
            };
        }

        return function(source, startState) {
            return tokenizer(source, startState || normal);
        };
    })();

    function parseRSplus(source, basecolumn) {
        if (!keywords) {
            configure({});
        }
        basecolumn = basecolumn || 0;

        var tokens = tokenizeRSplus(source);
        var lastToken = null;
        var column = basecolumn;
        var context = {prev: null,
                       endOfScope: false,
                       startNewScope: false,
                       level: basecolumn,
                       next: null,
                       type: NORMALCONTEXT
                       };

        function pushContext(level, type) {
            type = type ? type : NORMALCONTEXT;
            context = {prev: context,
                       endOfScope: false,
                       startNewScope: false,
                       level: level,
                       next: null,
                       type: type
                       };
        }

        function popContext(remove) {
            remove = remove ? remove : false;
            if (context.prev) {
                if (remove) {
                    context = context.prev;
                    context.next = null;
                } else {
                    context.prev.next = context;
                    context = context.prev;
                }
            }
        }

        function indentRSplus(context) {
            var temp;
            return function(nextChars, currentLevel, direction) {
                if (direction === null || direction === undefined) {
                    if (nextChars) {
                        while (context.next) {
                            context = context.next;
                        }
                    }
                    return context.level;
                }
                else if (direction === true) {
                    if (currentLevel == context.level) {
                        if (context.next) {
                            return context.next.level;
                        } else {
                            return context.level;
                        }
                    } else {
                        temp = context;
                        while (temp.prev && temp.prev.level > currentLevel) {
                            temp = temp.prev;
                        }
                        return temp.level;
                    }
                } else if (direction === false) {
                    if (currentLevel > context.level) {
                        return context.level;
                    } else if (context.prev) {
                        temp = context;
                        while (temp.prev && temp.prev.level >= currentLevel) {
                            temp = temp.prev;
                        }
                        if (temp.prev) {
                            return temp.prev.level;
                        } else {
                            return temp.level;
                        }
                    }
                }
                return context.level;
            };
        }

        var iter = {
            next: function() {
                var token = tokens.next();
                var type = token.style;
                var content = token.content;

                if (lastToken) {
                    if (lastToken.content == 'def' && type == IDENTIFIERCLASS) {
                        token.style = 'rsplus-func';
                    }
                    if (lastToken.content == '\n') {
                        var tempCtx = context;
                        // Check for a different scope
                        if (type == 'whitespace' && context.type == NORMALCONTEXT) {
                            if (token.value.length < context.level) {
                                while (token.value.length < context.level) {
                                    popContext();
                                }

                                if (token.value.length != context.level) {
                                    context = tempCtx;
                                    if (config.strictErrors) {
                                        token.style = ERRORCLASS;
                                    }
                                } else {
                                    context.next = null;
                                }
                            }
                        } else if (context.level !== basecolumn &&
                                   context.type == NORMALCONTEXT) {
                            while (basecolumn !== context.level) {
                                popContext();
                            }

                            if (context.level !== basecolumn) {
                                context = tempCtx;
                                if (config.strictErrors) {
                                    token.style = ERRORCLASS;
                                }
                            }
                        }
                    }
                }

                // Handle Scope Changes
                switch(type) {
                    case STRINGCLASS:
                    case BYTESCLASS:
                    case RAWCLASS:
                    case UNICODECLASS:
                        if (context.type !== STRINGCONTEXT) {
                            pushContext(context.level + 1, STRINGCONTEXT);
                        }
                        break;
                    default:
                        if (context.type === STRINGCONTEXT) {
                            popContext(true);
                        }
                        break;
                }
                switch(content) {
                    case '.':
                    case '@':
                        // These delimiters don't appear by themselves
                        if (content !== token.value) {
                            token.style = ERRORCLASS;
                        }
                        break;
                    case ':':
                        // Colons only delimit scope inside a normal scope
                        if (context.type === NORMALCONTEXT) {
                            context.startNewScope = context.level+indentUnit;
                        }
                        break;
                    case '(':
                    case '[':
                    case '{':
                        // These start a sequence scope
                        pushContext(column + content.length, 'sequence');
                        break;
                    case ')':
                    case ']':
                    case '}':
                        // These end a sequence scope
                        popContext(true);
                        break;
                    case 'pass':
                    case 'return':
                        // These end a normal scope
                        if (context.type === NORMALCONTEXT) {
                            context.endOfScope = true;
                        }
                        break;
                    case '\n':
                        // Reset our column
                        column = basecolumn;
                        // Make any scope changes
                        if (context.endOfScope) {
                            context.endOfScope = false;
                            popContext();
                        } else if (context.startNewScope !== false) {
                            var temp = context.startNewScope;
                            context.startNewScope = false;
                            pushContext(temp, NORMALCONTEXT);
                        }
                        // Newlines require an indentation function wrapped in a closure for proper context.
                        token.indentation = indentRSplus(context);
                        break;
                }

                // Keep track of current column for certain scopes.
                if (content != '\n') {
                    column += token.value.length;
                }

                lastToken = token;
                return token;
            },

            copy: function() {
                var _context = context, _tokenState = tokens.state;
                return function(source) {
                    tokens = tokenizeRSplus(source, _tokenState);
                    context = _context;
                    return iter;
                };
            }
        };
        return iter;
    }

    return {make: parseRSplus,
            electricChars: "",
            configure: configure};
})();
