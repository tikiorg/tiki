%define name tikiwiki
%define version 1.8.RC1
%define release 1

Summary: A PHP-based CMS/Groupware web application with a full Wiki environment
Name: %{name}
Version: %{version}
Release: %{release}
Copyright: LGPL
URL: http://www.tikiwiki.org
Packager: Dennis Heltzel <tiki-rpm@fishroom.net>
AutoReqProv: no
Requires: php-mysql 
Group: Applications/Internet
Source: http://prdownloads.sourceforge.net/%{name}/%{name}-%{version}.tar.gz
#Source: http://prdownloads.sourceforge.net/%{name}/%{name}-%{version}%{release}.tar.gz
BuildRoot: %{_tmppath}/%{name}-root
Prefix: /var/www/html
Vendor: The TikiWiki Team

# Command to build: rpmbuild -ba --target noarch tikiwiki.spec

%description
TikiWiki is an open source CMS/Groupware web application which provides a full Wiki environment, as well as Articles, Sections/Categories, User/Group Management (including optional LDAP), Polls and Quizzes, File and Image Galleries, Forums, Weblogs, Calendars, Chat and much more.

%prep

%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/var/www/html
cd $RPM_BUILD_ROOT/var/www/html
tar xvzf $RPM_SOURCE_DIR/%{name}-%{version}.tar.gz
#tar xvzf $RPM_SOURCE_DIR/%{name}-%{version}%{release}.tar.gz
mv %{name}-%{version} tiki-1.8
#mv %{name}-%{version}%{release} tiki-1.8
# Change ownership and permissions
chown -R apache:apache *
cd tiki-1.8
find . -name "*.php" -exec chmod 644 {} \;
find . -name "*.sql" -exec chmod 644 {} \;
./setup.sh apache apache
# Remove unneeded files
rm -rf templates_c/*
rm -f modules/cache/*.cache
find . -name "CVS" -type d -print|xargs rm -rf
find . -name ".cvsignore" -exec rm -f {} \;

%clean
rm -rf $RPM_BUILD_ROOT

%preun
# Remove unneeded files
rm -rf templates_c/*
rm -f modules/cache/*.cache

%files
%defattr(-,apache,apache)
%config /var/www/html/tiki-1.8/db/tiki-db.php
#%doc /var/www/html/tiki-1.8/README
/*

%changelog
