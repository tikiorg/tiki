# $Id: Makefile,v 1.1 2003-08-12 21:24:54 rossta Exp $

# [SQL::Translator] Error: translate: Error with producer 'SQL::Translator::Producer::ClassDBI': 
# Class::DBI isn't going to like that you don't have a primary key field for table galaxia_activities at
# /usr/local/share/perl/5.6.1/SQL/Translator/Producer/ClassDBI.pm line 315.

# [SQL::Translator] Error: translate: Error with producer 'SQL::Translator::Producer::TTSchema':
# No template file! at /usr/local/share/perl/5.6.1/SQL/Translator/Producer/TTSchema.pm line 38.

# Can't load SQL::Translator::Producer::Diagram: 
# Can't locate GD.pm

# Can't load SQL::Translator::Producer::GraphViz: 
# Can't locate Graph/Directed.pm

ROOT=tiki

SCHEMAS= \
	$(ROOT)_oracle.sql	\
	$(ROOT)_postgresql.sql	\
	$(ROOT)_sqlite.sql	\
	$(ROOT)_sybase.sql	\
	$(ROOT)_mysql.sql	\
	$(ROOT).html		\
	$(ROOT).pod		\
	$(ROOT).xml		\
	$(ROOT).classdbi	\
	$(ROOT).ttschema	\
	$(ROOT).diagram		\
	$(ROOT).graphviz

.PHONY:	all clean delete_empty_files

define sql_translator
	sql_translator.pl -d --add-drop-table -f MySQL -t
endef

all:  delete_empty_files $(SCHEMAS)

# if sql_translator crashes it leaves a zero byte file lying around
delete_empty_files:
	for i in $(SCHEMAS) ; \
	do \
		if [ -f $$i -a ! -s $$i ]; \
		then \
			rm $$i ;\
		fi \
	done
	
$(ROOT)_ddl.sql:	$(ROOT).sql
	egrep -i -v "^(INSERT|UPDATE|#)" $< >$@

$(ROOT)_inserts.sql:	$(ROOT).sql
	egrep -i "^(INSERT|UPDATE|#)" $< >$@

$(ROOT).classdbi:	$(ROOT)_ddl.sql
	-${sql_translator} ClassDBI	$< >$@

$(ROOT).diagram:	$(ROOT)_ddl.sql
	${sql_translator} Diagram	$< >$@

$(ROOT).graphviz:	$(ROOT)_ddl.sql
	${sql_translator} GraphViz	$< >$@

$(ROOT).html:		$(ROOT)_ddl.sql
	${sql_translator} HTML		$< >$@

$(ROOT)_mysql.sql:	$(ROOT)_ddl.sql
	${sql_translator} MySQL		$< >$@

$(ROOT)_oracle.sql:	$(ROOT)_ddl.sql
	${sql_translator} Oracle	$< >$@

$(ROOT).pod:		$(ROOT)_ddl.sql
	${sql_translator} POD		$< >$@

$(ROOT)_postgresql.sql:	$(ROOT)_ddl.sql
	${sql_translator} PostgreSQL	$< >$@
	
$(ROOT)_sqlite.sql:	$(ROOT)_ddl.sql
	${sql_translator} SQLite	$< >$@

$(ROOT).xml:		$(ROOT)_ddl.sql
	${sql_translator} SqlfXML	$< >$@

$(ROOT)_sybase.sql:	$(ROOT)_ddl.sql
	${sql_translator} Sybase	$< >$@

$(ROOT).ttschema:	$(ROOT)_ddl.sql
	${sql_translator} TTSchema	$< >$@

clean:
	rm -f $(ROOT)_ddl.sql $(ROOT)_inserts.sql $(SCHEMAS)
