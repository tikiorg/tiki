# Command to build: rpmbuild -ba --target noarch tikiwiki.spec
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/tikiwiki.spec,v 1.6 2004-06-23 22:33:55 mose Exp $

%define name tikiwiki
%define version 1.9.RC1
%define release 1

Summary: A PHP-based CMS/Groupware web application with a full Wiki environment
Name: %{name}
Version: %{version}
Release: %{release}
Copyright: LGPL
URL: http://www.tikiwiki.org
Packager: Damian Parker <damian@damosoft.co.uk>
AutoReqProv: no
Requires: php 
Group: Applications/Internet
Source: http://prdownloads.sourceforge.net/%{name}/%{name}-%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-root
Prefix: /var/www/html
Vendor: The TikiWiki Community

%description
TikiWiki is an open source CMS/Groupware web application which provides a full Wiki environment, as well as Articles, Sections/Categories, User/Group Management (including optional LDAP), Polls and Quizzes, File and Image Galleries, Forums, Weblogs, Calendars, Chat and much more.

%prep

%build

%install
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/var/www/html
cd $RPM_BUILD_ROOT/var/www/html
tar xvzf $RPM_SOURCE_DIR/%{name}-%{version}.tar.gz
mv %{name}-%{version} tiki-1.9.RC1
# Change file and directory permissions
cd tiki-1.9.RC1
find . -name "*.php" -exec chmod 644 {} \;
find . -name "*.sql" -exec chmod 644 {} \;
./setup.sh apache apache
# Remove unneeded files
rm -rf templates_c/*
rm -f modules/cache/*.cache
find . -name "CVS" -type d -print|xargs rm -rf
find . -name ".cvsignore" -exec rm -f {} \;

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

%preun
# Remove unneeded files
rm -rf /var/www/html/templates_c/*
rm -f /var/www/html/modules/cache/*.cache

%files
%defattr(-,apache,apache)
%config /var/www/html/tiki-1.9.RC1/db/tiki-db.php
#%doc /var/www/html/tiki-1.9.RC1/README
/var/www/html/tiki-1.9.RC1

%changelog
