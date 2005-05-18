// ** -*-coding:utf-8 -*-
Calendar._DN = new Array
("Dimanche",
 "Lundi",
 "Mardi",
 "Mercredi",
 "Jeudi",
 "Vendredi",
 "Samedi",
 "Dimanche");
Calendar._MN = new Array
("Janvier",
 "Février",
 "Mars",
 "Avril",
 "Mai",
 "Juin",
 "Juillet",
 "Août",
 "Septembre",
 "Octobre",
 "Novembre",
 "Décembre");
Calendar._SDN = new Array
("Dim",
 "Lun",
 "Mar",
 "Mer",
 "Jeu",
 "Ven",
 "Sam");
Calendar._SMN = new Array
("Jan",
 "Fev",
 "Mar",
 "Avr",
 "Mai",
 "Juin",
 "Juil",
 "Aout",
 "Sep",
 "Oct",
 "Nov",
 "Dec");

// tooltips
Calendar._TT = {};

Calendar._TT["INFO"] = "A propos du calendrier";

Calendar._TT["ABOUT"] =
"DHTML Date/Heure Selecteur\n" +
"(c) dynarch.com 2002-2003\n" + // don't translate this this ;-)
"Pour la dernière version visitez: http://dynarch.com/mishoo/calendar.epl\n" +
"Distribué par GNU LGPL.  Voir http://gnu.org/licenses/lgpl.html pour les détails." +
"\n\n" +
"Sélection de la date :\n" +
"- Utiliser les bouttons \xab, \xbb  pour sélectionner l\'année\n" +
"- Utiliser les bouttons " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " pour sélectionner les mois\n" +
"- Garder la souris sur n'importe quels boutons pour un sélection plus rapide";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Sélection de l\'heure:\n" +
"- Cliquer sur heures ou minutes pour incrementer\n" +
"- ou Maj-clic pour décrementer\n" +
"- ou clic et glisser déplacer pour une sélection plus rapide";

Calendar._TT["TOGGLE"] = "Changer le premier jour de la semaine";
Calendar._TT["PREV_YEAR"] = "Année préc. (maintenir pour menu)";
Calendar._TT["PREV_MONTH"] = "Mois préc. (maintenir pour menu)";
Calendar._TT["GO_TODAY"] = "Atteindre date du jour";
Calendar._TT["NEXT_MONTH"] = "Mois suiv. (maintenir pour menu)";
Calendar._TT["NEXT_YEAR"] = "Année suiv. (maintenir pour menu)";
Calendar._TT["SEL_DATE"] = "Choisir une date";
Calendar._TT["DRAG_TO_MOVE"] = "Déplacer";
Calendar._TT["PART_TODAY"] = " (Aujourd'hui)";
Calendar._TT["DAY_FIRST"] = "Commencer par %s";
Calendar._TT["WEEKEND"] = "6,0";
Calendar._TT["CLOSE"] = "Fermer";
Calendar._TT["TODAY"] = "Aujourd'hui";
Calendar._TT["TIME_PART"] = "Cliquez avec majuscule ou glissez pour changer de valeur.";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d-%m-%y";
Calendar._TT["TT_DATE_FORMAT"] = " %A %e %B %Y";

Calendar._TT["WK"] = "sem";
Calendar._TT["TIME"] = "Heure:";
