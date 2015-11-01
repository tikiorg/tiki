#!/usr/bin/perl
# (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

################################################################################
# Initial version contributed by Council of Europe - Conseil de l'Europe to the 
# Tiki community under the GNU LESSER GENERAL PUBLIC LICENSE, Version 2.1
# Initial Author    :  Jean-Marc LIBS
#
#Input     :  previous string, new string "oldstring" "Newstring"
#
#Output    :  Edit all language files
#
#Abstract  :  Gets a lowercase "oldstring" and the uppercase (correct) version of the
#             word: "Newstring"
#             In each language file:
#               If the line with '"Newstring" => "Foo"' is found. Leave alone
#               else:
#                 look for line with '"oldstring" => "foo"' (commented or not)
#                   if language is "simple", duplicate line into 
#                     '"Newstring" => "Foo"'
#                   if language looks complicated, duplicate into 
#                     '"Newstring" => "foo"'
#                   if no line found at all, add line
#                     '// "Newstring" => "Newstring"'
#
#Warning: This was written quickly as a use-once script. Use with care
#         don't trust blindly
#
#Usage     :  perl mass_wording_corrections.pl oldstring Newstring
#
#
# Also see: doc/devtools/update_english_strings.php
#
################################################################################

use strict;

########################################################################
#   Manage Command Line options
####
use vars qw($opt_help $opt_debug $opt_verbose $opt_ignorecase $opt_caseisenforced);
use Getopt::Long;
my $correct_options=GetOptions("help","debug","verbose","ignorecase","caseisenforced");

if($opt_debug){
	print "Number of args: ".$#ARGV."\n";
}

if($opt_help || !$correct_options || ($#ARGV != 1) || ($opt_caseisenforced && (("$ARGV[0]" ne "\L$ARGV[0]") || ( "$ARGV[1]" ne "\u$ARGV[1]" ))) ) {
die "Usage: $0 [options] oldstring Newstring

options:
 -h --help               this message
 -d --debug              debugging info
 -c --caseisenforced     enforces lowercase on first argument and uppercase for second argument
 -i --ignorecase         deprecated (does not enforce lowercase on first argument and uppercase for second argument)
 -v --verbose            more verbose output
";
}

my $word_lowercase = $ARGV[0];
my $word_correct = $ARGV[1];
my $word_lowercase_escaped = $word_lowercase;
my $word_lowercase_escaped_perl = $word_lowercase;
my $word_correct_escaped = $word_correct;
$word_lowercase_escaped =~ s/\//\\\//g;
$word_lowercase_escaped =~ s/'/'\\''/g;
$word_lowercase_escaped =~ s/\#/\\#/g;
$word_lowercase_escaped_perl = $word_lowercase_escaped;
$word_lowercase_escaped_perl =~ s/\(/\\(/g;
$word_lowercase_escaped_perl =~ s/\)/\\)/g;
$word_lowercase_escaped_perl =~ s/\|/\\|/g;
$word_correct_escaped =~ s/\//\\\//g;
$word_correct_escaped =~ s/'/'\\''/g;
#if($opt_verbose) {print "escaped lowercase: '$word_lowercase_escaped'\n";}
if($opt_debug){print "'$word_lowercase' --> '$word_correct' (given) '\u$word_lowercase' (auto)\n";}

# This is the list of "complicated" looking languages
my %languages_delicats=qw( 
ar 1
cn 1
el 1
fa 1
he 1
ja 1
ko 1
ru 1
tw 1
uk 1
);

# We loop on all language files
while ( my $langfile = <lang/*/language.php> ){
	print "-- ";
	my $lang="";
	my $trycapitalisation=0;
	if($langfile =~ /lang\/(.*)\/language.php/) {
		$lang=$1;
	}else{
		print "ERROR: could not extract language from $langfile \n";
		next;
	}
	if($opt_verbose){print "$langfile ";}
	print " ($lang)";
	if( ($opt_caseisenforced) and ($languages_delicats{$lang} != 1 )){
		print " capitalisation of translation will be attempted";
		$trycapitalisation=1;
	}
	print "\n";
	# Looking for '"Word" => "Translation"'
	my $command="grep '\"$word_correct_escaped\"[ 	]*=>[ 	]*\"[^\"]*\"[ 	]*,.*\$' $langfile | wc -l > /tmp/result.txt";
	if($opt_debug){print "-> $command\n";}
	if(system($command)) {
		print STDERR "Ignore system call failure ($!)\n";
	}
	open(RESULT,"/tmp/result.txt") or die("Failed to open /tmp/result.txt ($!)\nRemove file /tmp/result.txt and try again.\n");
	chomp(my $result = <RESULT>);
	close(RESULT);
	if( $result > 0){
		if($opt_verbose) {print "Nothing to do: translation of '$word_correct' is there already (lines found: $result). ";}
		print "Nothing done (already translated).\n";
		next;
	}
	if($opt_verbose) {print "Need to add translation of '$word_correct' (lines found: $result)\n";}
	$command="grep '\"$word_lowercase_escaped\"[ 	]*=>[ 	]*\"[^\"]*\"[ 	]*,.*\$' $langfile | wc -l > /tmp/result.txt";
	if($opt_debug){print "-> $command\n";}
	if(system($command)) {
		print STDERR "Ignore system call failure ($!)\n";
	}
	open(RESULT,"/tmp/result.txt") or die("Failed to open /tmp/result.txt ($!)\n");
	chomp(my $result = <RESULT>);
	close(RESULT);
	if( $result == 0){
		if($opt_verbose) {print STDERR "ERROR: '$word_lowercase' not in translation file (lines found: $result)\n";}
		print "Nothing done (no previous translation).\n";
		next;
		# last;
	}
	if($opt_verbose) {print "Need to edit $langfile (lines found: $result)\n";}
	# here we edit the file
	my $msg_success = "";
	if($trycapitalisation==0){
		$command="perl -pi.bak -e 's/(^(.*)\"($word_lowercase_escaped_perl)\"[ 	]*=>[ 	]*\"([^\"]*)\"[ 	]*,.*\$)/\$1\\n\$2\"$word_correct_escaped\" => \"\$4\",/' $langfile";
		$msg_success = "Translation added.";
	}else{
		if(!$opt_caseisenforced) { # then we assume that no attempt at capitalisation is expected
			$command="perl -pi.bak -e 's/(^(.*)\"($word_lowercase_escaped_perl)\"[ 	]*=>[ 	]*\"([^\"]*)\"[ 	]*,.*\$)/\$1\\n\$2\"$word_correct_escaped\" => \"\$4\",/' $langfile";
			$msg_success = "Translation added.";
		}else{
			$command="perl -pi.bak -e 's/(^(.*)\"($word_lowercase_escaped_perl)\"[ 	]*=>[ 	]*\"([^\"]*)\"[ 	]*,.*\$)/\$1\\n\$2\"$word_correct_escaped\" => \"\\u\$4\",/' $langfile";
			$msg_success = "Translation added and capitalized.";
		}
	}
	if($opt_debug){print "-> $command\n";}
	if(system($command)) {
		print STDERR "Ignore system call failure ($!)\n";
	}else{
		print "$msg_success \n"
	}

	if($opt_verbose){print "-- file handled --\n\n";}
	
}

exit 0;

