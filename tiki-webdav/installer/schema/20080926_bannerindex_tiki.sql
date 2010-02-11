#2008-09-26 sylvieg
ALTER TABLE tiki_banners ADD INDEX ban1 (zone,`useDates`,impressions,`maxImpressions`,`hourFrom`,`hourTo`,`fromDate`,`toDate`,mon,tue,wed,thu,fri,sat,sun);