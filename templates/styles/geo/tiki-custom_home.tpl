<div style="margin:20px;">

<div style="margin-bottom:10px"><img src="img/freeenduser2.png" width="156" height="30" border="0" alt="Free End User"></div>

<div>Après 5 ans de silence laborieux nous ouvrons a nouveau cet espace de publication, que nous nommions r1 et moi le Free End User, et
qui a donné lieu a dix publications entre mai et decembre 98. Consultez les archives sur <a href="http://old.feu.org"
class="link">http://old.feu.org</a>.</div>

<div>Maintenant que le temps a passé, l'outillage a suivi. TikiWiki est un outil très complet qui satisfera les web-users les plus
exigeants, il est supporté par une communauté d'utilisateurs en ce moment actifs, et le niveau de francisation est acceptable
(encore qu'incomplet).</div>
<br/>
<div>
<a href="mailto:m0@feu.org" class="link">m0</a>
&
<a href="mailto:r1@feu.org" class="link">r1</a>
</div>
</div>

<div class="cbox">
<div class="cbox-title">
Testez l'espace de travail du FEU
</div>
<div class="cbox-data">

<table border="0" cellpadding="0" cellspacing="0">

<tr><td nowrap style="padding-right:10px;" width="180">
<div class="separator"><a href="tiki-index.php" class="separator">[-] Wiki [+]</a></div>
- <a href="tiki-lastchanges.php" class="linkmenu">dernières modifications</a><br/>
- <a href="tiki-wiki_rankings.php" class="linkmenu">classements par visites</a><br/>
- <a href="tiki-listpages.php" class="linkmenu">liste des pages</a><br/>
</td>
<td>
<div class="simplebox">
<b>Une friche d'auto-édition</b> :
C'est un système d'édition ouvert. Chacun peut y modifier le contenu sans même s'identifier, toutes les versions de chaque page sont
conservées en cas de bourde. Un truc marrant sous mozilla, il suffit de double-cliquer sur un fond de page pour l'editer. Il est possible
d'y inclure des images, des liens, ou n'importe quel contenu du TikiWiki.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="160">
<div class="mini">Pages récentes</div>
<div class="mini">
{section name=changes loop=$lastchanges}
<a href="{$lastchanges[changes].href}" class="link" style="font-size:7px;padding-left:10px;"
title="{$lastchanges[changes].name}">{$lastchanges[changes].name|truncate:20:"(...)":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-view_aticles.php" class="separator">[-] Articles [+]</a></div>
- <a href="tiki-list_articles.php" class="linkmenu">Liste des articles</a><br/>
- <a href="tiki-cms_rankings.php" class="linkmenu">Classements par visites</a><br/>
- <a href="tiki-edit_submission.php" class="linkmenu">Soumettre un article</a><br/>
</td>
<td>
<div class="simplebox">
<b>Un système de publication</b> :
Ce système contributif de publication permet à un visiteur anonyme de proposer un article, qui sera ensuite (ou non) approuvé par un des
éditeurs de feu.org. Le droit à l'édition est soumis à l'acceptation de la charte de publication de feu.org (très bientôt mise en ligne).
Le collège initial est composé de <a href="/tiki-user_information.php?view_user=mose" class="linkmenu">mose</a>, <a
href="/tiki-user_information.php?view_user=phil" class="linkmenu">phil</a>, <a href="/tiki-user_information.php?view_user=tom"
class="linkmenu">tom</a>,
et <a href="/tiki-user_information.php?view_user=djamel" class="linkmenu">djamel</a>.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="160">
<div class="mini">Articles récents</div>
<div class="mini">
{section name=pages loop=$lastpages}
<a href="tiki-read_article.php?articleId={$lastpages[pages].articleId}" class="link" style="font-size:7px;padding-left:10px;"
title="{$lastpages[pages].title}">{$lastpages[pages].title|truncate:22:"..":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-view_aticles.php" class="separator">[-] Blogues [+]</a></div>
- <a href="tiki-list_blogs.php" class="linkmenu">Lister les blogues</a><br/>
- <a href="tiki-blog_rankings.php" class="linkmenu">Classements par visites</a><br/>
- <a href="tiki-blog_post.php" class="linkmenu">Contribuer</a><br/>
</td>
<td>
<div class="simplebox">
<b>Un recueil de Blogues</b> : Un Blogue c'est un peu comme un journal de bord, ou juste une accumulation de notes
personnelles collectives ou thématiques. Vous pouvez ici faire une demande d'ouverture de Blogue si vous avez déjà un
compte uniquement par messagerie au <a href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+blogue"
class="linkmenu">concierge</a>.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Blogues actifs</div>
<div class="mini">
{section name=blogs loop=$activeblogs}
<a href="{$activeblogs[blogs].href}" class="link" style="font-size:7px;padding-left:10px;"
title="{$activeblogs[blogs].name}">{$activeblogs[blogs].name|truncate:22:"..":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-forums.php" class="separator">[-] Forums [+]</a></div>
- <a href="tiki-forums.php" class="linkmenu">Liste des forums</a><br/>
- <a href="tiki-forum_rankings.php" class="linkmenu">Classements</a><br/>
</td>
<td>
<div class="simplebox">
<b>Un espace de Forums</b> : Sur un certain nombre de thèmes définis, chacun des forums permet d'interagir, communiquer, se
concerter ou partager. Si vous pensez qu'un nouveau thème de forum devrait être ouvert, adressez-vous au <a
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+forum" class="linkmenu">concierge</a>.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Forums actifs</div>
<div class="mini">
{section name=forums loop=$lastforumposts}
<a href="{$lastforumposts[forums].href}" class="link" style="font-size:7px;padding-left:10px;"
title="{$lastforumposts[forums].name}">{$lastforumposts[forums].name|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-forums.php" class="separator">[-] Sites [+]</a></div>
- <a href="tiki-directory_browse.php" class="linkmenu">Liste des sites</a><br/>
- <a href="tiki-directory_add_site.php" class="linkmenu">Proposer un site</a><br/>
</td>
<td>
<div class="simplebox">
<b>Un référencement de Sites</b> : Sous forme de courtes fiches descriptive les sites intéressants sont classés par
catégories. Tout le monde peut proposer un nouveau site, dont les détails sont vérifiés avant publication par les Editeurs.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Liens récents</div>
<div class="mini">
{section name=dir loop=$newdirectory}
<a href="tiki-directory_redirect.php?siteId={$newdirectory[dir].siteId}" class="link" target="_new" style="font-size:7px;padding-left:10px;"
title="{$newdirectory[dir].name}">{$newdirectory[dir].name|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-galleries.php" class="separator">[-] Images [+]</a></div>
- <a href="tiki-galleries_rankings.php" class="linkmenu">Classement par visites</a><br/>
- <a href="tiki-upload_image.php" class="linkmenu">Télécharger une image</a><br/>
</td>
<td>
<div class="simplebox">
<b>Une banque d'Images</b> : Les banques regroupent des images par catégorie. Certaine sont publiques et tout le monde peut
y contribuer, d'autres ne sont visibles qu'en consultation. Si vous souhaitez disposer d'une banque d'image, il faut vous
adresser au <a href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+banque+Images" class="linkmenu">concierge</a> (bawé, encore).
</div> 
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Nouvelles Images</div>
<div class="mini">
{section name=images loop=$newimages}
<a href="{$newimages[images].href}" class="link" target="_new" style="font-size:7px;padding-left:10px;"
title="{$newimages[images].name}">{$newimages[images].name|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-file_galleries.php" class="separator">[-] Fichiers [+]</a></div>
- <a href="tiki-file_galleries_rankings.php" class="linkmenu">Classements par visites</a><br/>
- <a href="tiki-upload_file.php" class="linkmenu">Télécharger un fichier</a><br/>
</td>
<td>
<div class="simplebox">
<b>Un hangar à Fichiers</b> : Comme pour les images, mais pour des contenus plus hétérogènes, c'est un espace de stockage
pour formats divers. Pour disposer d'un hangar personnel, voyez donc avec le <a
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+hangar+Fichiers" class="linkmenu">concierge</a>
(vous le trouverez dans l'escalier).
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Nouveaux Fichiers</div>
<div class="mini">
{section name=files loop=$newfiles}
<a href="{$newfiles[files].href}" class="link" target="_new" style="font-size:7px;padding-left:10px;"
title="{$newfiles[files].name}">{$newfiles[files].name|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-list_faqs.php" class="separator">[-] FAQs [+]</a></div>
</td>
<td>
<div class="simplebox">
<b>Une usine à FAQs</b> : C'et outil s'est avéré très efficace pour communiquer rapidement des informations pragmatiques.
Cette usine permet de générer des FAQ en donnant la possibilité à tous de poser des questions soumises ensuite à la verve
de l'auteur. Si vous avez envie de jouer avec ça, devinez qui c'est qu'il faut aller voir. Le <a
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+FAQ" class="linkmenu">concierge</a>,
evidemment !
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Dernières FAQ</div>
<div class="mini">
{section name=faqs loop=$newfaqs}
<a href="tiki-view_faq.php?faqId={$newfaqs[faqs].faqId}" class="link" target="_new" style="font-size:7px;padding-left:10px;"
title="{$newfaqs[faqs].title}">{$newfaqs[faqs].title|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-list_faqs.php" class="separator">[-] QCM [+]</a></div>
</td>
<td>
<div class="simplebox">
<b>Une machine à QCM</b> : Les questionnaires à réponse multiples sont utiles pour beaucoup d'usages. Une interface
complète permet de créér des QCM mais sans scénarisation, juste une liste de questions. Pour utiliser la machine et créér
son QCM, et bien, dame, c'est le <a
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+QCM" class="linkmenu">concierge</a>, voyez donc sa
loge. Soyez indulgent ce module semble encore un peu sauvage.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Derniers QCM</div>
<div class="mini">
{section name=qcms loop=$newqcms}
<a href="tiki-take_quiz.php?quizId={$newqcms[qcms].quizId}" class="link" style="font-size:7px;padding-left:10px;"
title="{$newqcms[qcms].name}">{$newqcms[qcms].name|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-list_faqs.php" class="separator">[-] Taches [+]</a></div>
</td>
<td>
<div class="simplebox">
<b>Un gestionnaire de taches</b> : techniquement on pourrait parler d'outil de ticketing. Chaque jeu de fiches est
personnalisable, outre l'etat ouvert/fermé qui caractérise un ticket. Mais ce systeme de personnalisation des fiches peut
laisser envisager des usages beaucoup plus proches de ceux d'une base de données par exemple. Pour utiliser ce machin,
voyez avec <a
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+Tracker" class="linkmenu">l'autre</a>, la.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Tickets publics</div>
<div class="mini">
<a href="tiki-view_tracker.php?trackerId=2" class="link" style="font-size:7px;padding-left:10px;" title="Bug Reports">Bug Reports</a><br/>
<a href="tiki-view_tracker.php?trackerId=3" class="link" style="font-size:7px;padding-left:10px;" title="Coquilles">Coquilles</a><br/>
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-list_faqs.php" class="separator">[-] Sondages [+]</a></div>
</td>
<td>
<div class="simplebox">
<b>Une unité de Sondages</b> : C'est un outil ludique, qu'il faudra eviter de prendre au sérieux. Mais si vous souhaitez
vous amuser, vous pouvez demander au <a
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+Sondage" class="linkmenu">concierge</a> qu'il vous file une clé.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Sondages en cours</div>
<div class="mini">
{section name=sondage loop=$newsondage}
<a href="tiki-take_survey.php?surveyId={$newsondage[sondage].surveyId}" class="link" style="font-size:7px;padding-left:10px;"
title="{$newsondage[sondage].name}">{$newsondage[sondage].name|truncate:22:"...":true}</a><br/>
{/section}
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-list_faqs.php" class="separator">[-] Bulletins [+]</a></div>
</td>
<td>
<div class=simplebox>
<b>Une centrale de publication</b> : Pour publier des bulletins par mail à une liste d'inscrits. C'est un usage devenu courant et chacun de vous peut en disposer du moment
qu'il a convaincu le <a 
href="http://feu.org/messu-compose.php?to=concierge&subject=demande+de+Sondage" class="linkmenu">concierge</a> de son aptitude à connaitre les responsabilités que cet
outil propose.
</div>
</td>
<td nowrap style="padding-left:3px;padding-top:5px;" width="120">
<div class="mini">Abonnements</div>
<div class="mini">
<a href="tiki-newsletters.php?nlId=1&info=1" class="link" style="font-size:7px;padding-left:10px;" title="FeU">FeU</a><br/>
</div>
</td>
</tr>

<tr><td nowrap style="padding-right:20px;">
<div class="separator"><a href="tiki-eph.php" class="separator">[-] Calendrier [+]</a></div>
</td>
<td>
<div class=simplebox>
<b>Un Calendrier</b> : Je crois bien que c'est une sorte d'outil pour voyager dans le temps, mais j'avoue que je dois mieux relire la doc a ce sujet, c'est pas tres clair
....
</div>
</td>
</tr>


</table>

</div>
</div>
