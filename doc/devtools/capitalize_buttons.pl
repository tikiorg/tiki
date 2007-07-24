#!/usr/bin/perl
################################################################################
#    Copyright Council of Europe - Conseil de l'Europe
#Division  :  DIT / STI
#Project   :  Espaces Collaboratifs Open-Source
#Filename  :  capitalize_buttons.pl
#Author    :  Jean-Marc LIBS (Jyhem)
#Company   :  Council of Europe - Conseil de l'Europe
#Date      :  2007-07-17
#Language  :  Perl
#License   :  Licensed under the GNU LESSER GENERAL PUBLIC LICENSE, Version 2.1
#
#Input     :  Mot minuscule, mot majuscule: "word" "Word"
#
#Output    :  Editer tous les fichiers de langages
#
#Abstract  :  Reçoit un mot dont on change la capitalisation.
#             Parcourt tous les fichiers de language. Pour chacun:
#             Si on trouve la ligne avec '"Word" => "Truc"' on laisse
#             Sinon:
#               On cherche la ligne avec '"word" => "truc"' (commenté ou pas)
#                 Si langage "simple", et on la duplique en '"Word" => "Truc"'
#                 Sinon, on garde '"Word" => "truc"'
#               Si on ne trouve pas cette ligne, 
#                 on rajoute '// "Word" => "Word"'
#
#Warning: This was written quickly as a use-once script. Use as inspiration, don't
#         trust blindly
#
#Usage     :  perl capitalize_buttons.pl word Word
#
#Revision history :
#  Date       Author  Description
#  2007-07-17  JML    1: First version
#  2007-07-20  JML    2: words can be expressions
#  2007-07-23  JML    3: renaming, add license, add TODO, all for publication
#
#TODO:
#translate all comments
#
################################################################################

use strict;
use File::Copy;
use Time::CTime;

########################################################################
#   Manage Command Line options
####
use vars qw($opt_help $opt_debug $opt_verbose );
use Getopt::Long;
my $correct_options=GetOptions("help","debug","verbose");

if($opt_debug){
	print "Number of args: ".$#ARGV."\n";
	exit 1;
}

if($opt_help || !$correct_options || ($#ARGV != 1) || ("$ARGV[0]" ne "\L$ARGV[0]") || ( "$ARGV[1]" ne "\u$ARGV[1]" ) ) {
die "Usage: $0 [options] word Word

options:
 -h --help               this message
 -d --debug              debugging info
 -v --verbose            more verbose output
";
}

my $word_lowercase = $ARGV[0];
my $word_correct = $ARGV[1];
if($opt_verbose){print "$word_lowercase --> $word_correct (given) \u$word_lowercase (auto)\n";}

my $day= strftime("%Y%m%d", localtime());
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
	my $lang="";
	my $trycapitalisation=0;
	if($langfile =~ /lang\/(.*)\/language.php/) {
		$lang=$1;
  }else{
		print "ERROR: could not extract language from $langfile \n";
		next;
	}
	print "$langfile ($lang)";
	if( $languages_delicats{$lang} != 1 ){
		print " capitalisation will be attempted";
		$trycapitalisation=1;
	}
	print "\n";
	# Looking for '"Word" => "Truc"'
	my $command="grep '\"$word_correct\"[ 	]*=>[ 	]*\"[^\"]*\"[ 	]*,.*\$' $langfile | wc -l > /tmp/result.txt";
	if($opt_verbose){print "-> $command\n";}
	if(system($command)) {
		print STDERR "Ignore system call failure ($!)\n";
	}
	open(RESULT,"/tmp/result.txt") or die("Failed to open /tmp/result.txt ($!)\n");
	chomp(my $result = <RESULT>);
	close(RESULT);
	if( $result > 0){
		print "Nothing to do: translation of $word_correct is there already (lines found: $result)\n\n";
		next;
	}
	print "Need to do add translation of $word_correct (lines found: $result)\n";
	$command="grep '\"$word_lowercase\"[ 	]*=>[ 	]*\"[^\"]*\"[ 	]*,.*\$' $langfile | wc -l > /tmp/result.txt";
	if($opt_verbose){print "-> $command\n";}
	if(system($command)) {
		print STDERR "Ignore system call failure ($!)\n";
	}
	open(RESULT,"/tmp/result.txt") or die("Failed to open /tmp/result.txt ($!)\n");
	chomp(my $result = <RESULT>);
	close(RESULT);
	if( $result == 0){
		print STDERR "ERROR: $word_lowercase not in translation file (lines found: $result)\n";
		next;
		# last;
	}
	print "Need to edit $langfile (lines found: $result)\n";
	# here we edit the file
	if($trycapitalisation==0){
		$command="perl -pi.bak -e 's/(^(.*)\"($word_lowercase)\"[ 	]*=>[ 	]*\"([^\"]*)\"[ 	]*,.*\$)/\$1\\n\$2\"$word_correct\" => \"\$4\",/' $langfile";
	}else{
		$command="perl -pi.bak -e 's/(^(.*)\"($word_lowercase)\"[ 	]*=>[ 	]*\"([^\"]*)\"[ 	]*,.*\$)/\$1\\n\$2\"$word_correct\" => \"\\u\$4\",/' $langfile";
	}
	if($opt_verbose){print "-> $command\n";}
	if(system($command)) {
		print STDERR "Ignore system call failure ($!)\n";
	}

	if($opt_verbose){print "-- file handled --\n\n";}
	
}

exit 0;

