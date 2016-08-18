#!/usr/bin/perl -w
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

#
# This script helps you recover your wiki data from google cache if
# you somehow lose it.
# The tipical situation is that you have an old backup, so this script will
# crawl google cache for source code of wiki pages and put them in database.
# Don't forget to backup first.

use strict;
use LWP::UserAgent;
use CGI::Simple;
use DBI;

# results per page in google search
our $increment = 100;

# directory to dump all cached pages
our $dumpdir = "wiki_dump";

# url of your site
our $siteurl = "mydomain.com";

# list of important pages you remember by name, helps finding them
our @pages = qw(HomePage User+List Some+Page);

# configure database connection here
our $dbh = DBI->connect("dbi:mysql:database_name:localhost","root");

# tiki file patterns to be searched, you don't have to touch this
our @phpfiles = ('source "tiki pagehistory php "',
		 'source=0 "tiki pagehistory php "'
#		 ,'tiki-index.php'
		 );

our $cgi = new CGI::Simple;
our $ua = LWP::UserAgent->new;

chdir($dumpdir);

# this will fetch all results and dump each one to a file in dump dir
fetch();

# this will parse all pages and import best version to database, and put
# earlier version of page in history.
importDump();

sub importDump {
    my %pages = %{getBestVersions()};

    my $getPage = $dbh->prepare("select * from tiki_pages where pageName=?");
    my $insertHistory = $dbh->prepare("insert into tiki_history values (?,?,?,?,?,?,?,?,?)");
    my $updatePage = $dbh->prepare("update tiki_pages set ".
				   " data=?, ".
				   " lastModif=?, ".
				   " comment=?, ".
				   " version=?, ".
				   " user=? ".
				   " where pageName=?");

    foreach my $page (keys %pages) {
	my $version;

	if (pageExists($page)) {

	    $getPage->execute($page);
	    my $info = $getPage->fetchrow_hashref;

	    $version = $info->{version} + 1;

	    $insertHistory->execute($page,
				    $info->{version}, 
				    0, 
				    $info->{lastModif},
				    $info->{description},
				    $info->{user},
				    $info->{ip},
				    $info->{comment},
				    $info->{data});
	} else {
	    $dbh->do("insert into tiki_pages (pageName) values (".$dbh->quote($page).")");
	    $version = 1;
	}

	#			   " data=?, ".
	#			   " description=?, ".
	#			   " lastModif=?, ".
	#			   " comment=?, ".
	#			   " version=?, ".
	#			   " user=? ".
	#			   " where pageName=?");#

	my $info = $pages{$page};
	$updatePage->execute($info->{content},
			     time(),
			     'recuperacao do cache do google',
			     $version,
			     'admin',
			     $page);
    }
       		 
}

sub pageExists {
    my $page = shift;

    my ($id) = $dbh->selectrow_array("select page_id from tiki_pages where pageName=".$dbh->quote($page));

    return $id ? 1 : 0;
}


sub getBestVersions {

    my %data;
    foreach my $file (<*>) {
	my $info = getInfoFromFile($file);
	
	if (!defined $data{$info->{page}} ||
	    $info->{version} == 0 ||
	    $data{$info->{page}}{version} < $info->{version}) {
	    
	    $data{$info->{page}} = $info;
	}
    }

    return \%data;
}

1;

sub getInfoFromFile {
    my $file = shift;

    my ($page, $source) = $cgi->url_decode($file) =~ /page=(.+?)\&.*?source=(\d+)/;

    $page && defined $source
	or return undef;

    $page =~ s/\%([0-9A-Fa-f]{2})/chr(hex($1))/ge;

    undef $/;
    open ARQ, $file
	or die $!;
    my $content = <ARQ>;
    close ARQ;

    $content =~ s|^.+?<div[^>]+class="wikitext">(.+?)</div>.+$|$1|s;

    $content =~ s/\r//gs;
    $content =~ s|<br />||gs;

    return {
	'page' => $page,
	'content' => $content,
	'version' => $source
    };    
}

sub fetch {
    
    $ua->timeout(30);
    $ua->cookie_jar({});
    $ua->agent("Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1");
    my $response = $ua->get('http://www.google.com/');
    
    sleep 2;

    my @queries;
    
    foreach my $page (@pages) {
	foreach my $phpfile (@phpfiles) {
	    push @queries, "$page $phpfile";
	}
    }
    

    foreach my $query (@queries) {
	
	my $offset = 0;
	
	while (my @links = getList($query, $offset)) {
	    foreach my $link (@links) {
		retrieveLink($link);
	    }
	    $offset += $increment;
	}
    }
}

sub retrieveLink {
    my $link = shift;

    my ($file) = $link =~ m|$siteurl/(.+)$|;

    if (-f $file) {
	open ARQ, ">>$file.repeated";
	print ARQ ".";
	close ARQ;
	return 1;
    }

    my $response = $ua->get($link);
    $response->is_success
	or return undef;

    open ARQ, ">$file";
    print ARQ $response->content;
    close ARQ;
}

sub getList {
    my $query = shift;
    my $start = shift;

    $query = $cgi->url_encode($query);

    my $url = 'http://www.google.com.br/search?q='.$query.'+site:'.$siteurl.'&num=100&hl=pt-BR&lr=&as_qdr=all&filter=0';
    if ($start) {
	$url .= '&sa=N&start='.$start;
    }

    my $response = $ua->get($url);

    my $content;
    if ($response->is_success) {
	$content = $response->content;  # or whatever
    } else {
	return 0;
    }

    my @links;
    while (my ($siteUrl) = $content =~ m|href=\"(http://[0-9.]+/search\?q=cache[^\"]+)\"|) {
	push @links, $siteUrl;
	$content =~ s|href=\"(http://[0-9.]+/search\?q=cache[^\"]+)\"||;
    }
    
    return @links;
}

