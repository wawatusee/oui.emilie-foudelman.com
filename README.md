# oui.emilie-foudelman.com
Site d'Emilie Foudelman section reportage de mariage

Le domaine "emilie-foudelman.com" est consacré à la diffusion des oeuvres de la photographe Emilie Foudelman

## sous domaine "Oui"

Le sous domaine "oui.emilie-foudelman.com" est le site qui vante et donne accès aux reportages de mariage réalisés par Emilie Foudelman ou à venir.

### Infrastructure et moyens technique

#### Techniques employées

Back-end : Le site est réalisé en PHP il nécessite que Apache soit installé sur le serveur qui le reçoit.
Front-end : HTML, CSS, Java-Script, les navigateurs devront supporter ces techniques pour lire ce site.
Ambiance : du code "Vanilla" sera privilégié. L'import de classes tierces ou techno propriétaires ne sera utilisé que dans des cas où ils sont la seule solutions possibles pour aboutir une fonctionnalité.

#### Arborescence

Listes des répertoires et fichiers présents à la racine du site:

- admin, répertoire
- config, répertoire
- inc, répertoire
- json, répertoire
- public, répertoire
- src, répertoire

- index.php, fichier
- .htaccess, fichier
- readme.md, fichier

##### A la racine

- index.php, fichier : via header redirection vers le dossier "public".
- readme.MD, fichier : descriptif du motif et de la configuration du site.
- .htaccess, fichier : paramètres serveur, exemple réécriture d'url pour https.

###### admin, répertoire : TODO

Le répertoire admin est accessible via une connexion sécurisée.

Dans le répertoire admin, gestion galeries(nomenclature, diffusion), gestion des uploads d'images.

Pour l'instant seuls les brouillons concernant l'upload sont en place.

TODO :

- créer accès sécurisé,
- l'admin des galeries,
- les tests sur les types de fichiers acceptés par l'upload.
- rendre dynamique les parties du site qui ne le sont pas, exemple :

- les contacts, mail, tel, ces données peuvent être stoquées dans un fichier JSON ce qui permettrait de le traiter facilement depuis la partie admin.

- le tableau des éléments du titre, ces données peuvent être stoquées dans un fichier JSON ce qui permettrait de le traiter facilement depuis la partie admin.

###### config, répertoire

Paramètres globaux du site, dans le répertoire config, le fichier config.php :

- $singlePage, boléen écrit directement dans le fichier , quand sur false définit chaque section traitée par le controleur central comme une url indépendante. Sinon ces sections alimenteront une page unique.
- $langues_disponibles, tableau écrit directement dans le fichier, liste les langues du site sous la forme de paire "abbréviation"=>"Nom de la langue" ex : 'fr' => 'Français'.
Une condition axée sur la valeur de la variable globale $_GET['lang'] est présente dans le tableau des langues, si elle ne l'est pas la langue par défaut('fr') est déclarée.
- Répertoires du site, les variables 'ROOT', 'PUBLIC_URL', 'IMG_URL', $repMedias, $repDeco, $repImg et 'JSON', donnent les url à employer.
- Titre du site, un tableau contenant plusieurs string contient et ordonne les mots qui compose le titre du site.

Dans le répertoire config, le fichier menu.php :

Chargement du fichier menus_model.php qui traite le fichier menu.json, ce fichier sert de base pour décrire les intitulés des menus dans chaque langue et construire le lien vers les rubriques principales.

###### src/models, repertoire

Répertoire des fichiers php contenant toutes les classes qui traitent des données.

Fichiers :

- menus_model.php, files. Classes et méthodes pour récupérer les données du fichier menus.json et les met à disposition sous forme de tableau.

###### src/views, répertoire

Répertoire des fichiers php contenant toutes les classes qui traitent les vues exploitées dans le site.
Exemples de views :

- menu

###### inc, répertoire

Répertoire des fichiers récurrents d'une page à l'autre, le menu, l'entète le pied de site sont isolés et stoqués dans ce prépertoire pour pouvoir être inclu sans changement dans toutes les pages où ils sont nécessaires.
Répertoires :

- pages, répertoire contenant les fichiers php qui correspondent à des pages du menu principal, pour l'instant ces pages doivent encore être créées à la main, dans le cadre d'un CMS se pourrait-être automatisé, je ne sais pas encore comment.

Fichiers, les éléments récurents du site qui seront appelés dans différentes pages :

- head.php, les entêtes de la page html, les balises OG de Facebook doivent y être incluses si l'option partage sur les réseaux sociaux est désirée.
- header.php, divers éléments dont la navigation et le titre et le module de changement de langues.
- nav.php, menu principal
- footer.php, pied de site, avec menu annexe et autres compléments d'information et de navigation.
- main.php, controleur central, selon une instruction get "page" incorpore la page concernée, soit dans une section si le site est en singlepage, soit vers une autre page si le site n'est pas en singlepage.

### Composants

#### Services

#### Galerie images

#### Contacts

#### Avis
