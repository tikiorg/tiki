#!/usr/bin/perl
# (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

##########################
    print "ATTENTION\n##################\nThis is not a script, it is a concrete poem!\n";
    print "You are allowed to modify it and redistribute it,\n";
    print "as long as you keep it absolutely free.\n";
    print "Execute it at your own risk...\n";
    print "Roberto Winter (cleft) 2005\n";
    print "Feedback and support: rhwinter\@gmail.com\n";
    print "If you've read this poem, and liked it, please email me.\n##################\n\n";
##########################



$dicFile=$ARGV[0].".dic";
$affFile=$ARGV[0].".aff";
$writeTo=$ARGV[1];

if(!$ARGV[0]){
   die "No '.dic/.aff' locale name! Pass it as an argument to this script (without the extension). Examples: en_US or pt_BR\n";
}

if(!$writeTo){
   die "No file to write to specified. Pass it as an argument after the '.dic/.aff' name\n";
}


print"Type the language code to be used (ex. 'en', 'pt', etc). This is used to create the table in the '.sql' file. [$ARGV[0]]: ";
chomp($lang = <STDIN>);
if(!$lang){ $lang=$ARGV[0];}

&parseAff();

open(WRITE,">$writeTo")|| die "Could not open $writeTo: $!\n";

print "Writing header to sql file ($writeTo)... ";
print WRITE <<HEADER;
#
# Table structure for table 'babl_words_$lang'
#

CREATE TABLE babl_words_$lang (
  word varchar(200) binary NOT NULL default '',
  di char(2) binary NOT NULL default '',
  PRIMARY KEY  (word),
  KEY di (di)
) TYPE=MyISAM;

#
# Dumping data for table 'babl_words_$lang'
#

	
HEADER

print" written.\n";
    
print"Opening $dicFile...";
open(READ,$dicFile)||die "Could not open $dicFile: $!\n";
print" ok.\n";

print"Converting (this might take a moment:";
chomp($numberOfLines=<READ>);
print" going through $numberOfLines words in '$dicFile')... \n";
while(<READ>){
    chomp;
    $currentWord=$_;
    if($currentWord=~/\//){
	$num+=1;
	addVariationsOf($currentWord);
	$currentWord =~ s/(.*?)\/.*/$1/;
    }
    writeWord($currentWord)
}
&outputWords;
print"Done!\n\nFile $writeTo written.\nNow just run 'mysql tiki < $writeTo u tiki_user -p' (with appropriate changes).\nSee http://tikiwiki.org/tiki-index.php?page=SpellcheckingDoc for more info.\n";

close(READ);
close(WRITE);
    
    
sub parseAff{
    # this parsing is based on info found on:
    # http://software.newsforge.com/article.pl?sid=06/01/27/2022227&from=rss
    # http://lingucomponent.openoffice.org/dictionary.html
    # http://lingucomponent.openoffice.org/affix.readme
    my ($code,$combine,$readAhead,$garbage,$currentLine,$charToRemove,$charToAdd,$matches);
    print "\nParsing '.aff' file... ";
    open(AFF, $affFile)||die "Unable to open $affFile: $!\n";
    
    while(<AFF>){
	$currentLine=$_;
	if($_=~'SFX'){
	    # we have a suffix set of lines ahead
	    # we get the code letter it uses ($code), if it can be combined with prefixes or not ($combine) and the number of lines to read on ($readAhead)
	    ($garbage,$code,$combine,$readAhead) = split / /,$currentLine;	    
	    for($i=0;$i<$readAhead;$i++){
		chomp($currentLine=<AFF>);
		# now we get each of the lists.
		# here the code is repeated everytime and then the rule is given.
		# the form is: if a word $matches a regexp, then we remove the $charToRemove and add the $charToAdd
		($garbage,$code,$charToRemove,$charToAdd,$matches)= split / /,$currentLine;
		# create a hash to put into the array of infos contained in the suffixes hash
		my %info=("remove"=>$charToRemove,"add"=>$charToAdd,"if"=>$matches);
		# create or add the info to an array (read below to get an idea of this structure)
		if($suffixes{$code}){
		    # that code already has an array, so we just add the new one to it
		    $suffixes{$code}=[@{$suffixes{$code}},\%info];
	        }else{
		    # we have to create the array and put the first info
		    $suffixes{$code}=[\%info];
	        } 
	    }
	    # the structure we have created is of the form:
	    # a hash associating codes to (references to) arrays, to each code we have an array
	    # these arrays contain hashes that tell us:
	    # "remove" -> which characters the word must end with (and must be removed from it)
	    # "add" -> which characters we have to add at the end of the word
	    # "if" -> the string that the end of the word must match so that this rule applies (use /b here!)
	}
	elsif($currentLine=~'PFX'){
	    # we have a prefix line ahead
	    # we don't have a documentation on this!!!
	    print "\nATTENTION: a preffix was found, but they are not supported, please note that your resulting '.sql' dictionary WILL NOT BE COMPLETE\n If you find documentation on this please note the authors of this code.\n"
	}
	else{
	    if($currentLine=~'SET'||$currentLine=~'TRY'){
		# telling us of the charset to be used...
		# line not supported by the dic in tikiwiki... ignore it!
	    }else{
		print "\nATTENTION\nThe following line was found on your $affFile:\n$currentLine We have no idea what to do with it, it might contain useful info or might be garbage, if you are unsure ask for help!\nThe program will continue normally.\n\nParsing resumed... ";
	    }
	}
	
    }
    print" done.\n";
    close(AFF);
}

# given a certain word (with suffix codes in the end) this will get the codes it contains and create the variations accordingly. it will also add it to the '.sql' file.
sub addVariationsOf{
    ($word)=@_;
    my ($code, $newWord, $toStrip, $refToHash);
    # we get the codes
    my ($word, @codes) = split /\//, $word;
    foreach $code (@codes){
	foreach $refToHash (@{$suffixes{$code}}){
	    if($word=~/$refToHash->{"if"}$/){

		# yes we have a match remove last bit:
		$toStrip=$word;
		$toStrip=~s/$refToHash->{"remove"}$//;
		
		# create the variation of the word
		$newWord=$toStrip.$refToHash->{"add"};
		 
		# add to our 'sql' file
		writeWord($newWord);
	    }
	}
    }
}

# this just writes a word to the '.sql' file in a proper way
sub writeWord{
    # we have duplicate entries of each word, so we'll have to  create a hash to ignore them.
    # this is a dirty hack that will have to be replaced with something more elegant.
    my($currentWord)=@_;
    my($first,$second,@rest);
    ($first, $second, @rest) = split //,$currentWord;
    $allWords{$currentWord}=$first.$second;
}

sub outputWords{
    my ($word);
    print "\nPRINTING\n";
    foreach $word (keys %allWords){
	print WRITE "INSERT INTO babl_words_$lang VALUES ('$word','$Word{$word}');\n";
    }
}
