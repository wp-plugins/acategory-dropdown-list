=== aCategory Dropdown List ===
Contributors: alekart
Tags: category, post, admin, dropdown, select, list, select, menu
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.0r8
License: GPLv2

Replaces the category checkboxes by a dropdown menu on post�s edit page.

== Description ==

Replaces the category checkboxes by a dropdown menu on post�s edit page. Your posts will only show in the category you select and you will be able to select only one term (category) per post.
The plugin works with the defaut and custom hierarchical taxonomies.

("r" in the version indicator means "revision", minor corrections like descriptions, translations or spelling.)

If you like this plugin and find it useful, consider donating to support my work by clicking on "[Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TQ47KSHNBX7HJ)" link.
**[DONATE](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TQ47KSHNBX7HJ)**

--

Remplace les cases � cocher de cat�gories par un menu d�roulant sur la page d'�dition d'articles. Vos articles s'afficheront uniquement dans la cat�gorie que vous choisissez et vous ne pouvez selectionner qu'une cat�gorie par article.
Le plugin fonctionne avec les taxonomies hi�rarchiques d'origine et personnalis�es.

("r" dans l'indicateur de version signifie "r�vision", des changement mineurs comme les d�scriptions, traductions ou l'orthographe.)

Si ce plugin vous plait et que vous le trouvez utile, envisagez de faire un don pour soutenir mon travail en cliquant sur le lien "[Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TQ47KSHNBX7HJ)".
**[DONATE](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TQ47KSHNBX7HJ)**

== Installation ==

1. Upload `acategory-dropdown-list` folder in `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin through 'Settings' > 'a�Category' menu in WordPress.

== Frequently Asked Questions ==


== Screenshots ==

1. Default taxonomy (category) replacement
2. Custom taxonomy replacement
3. Plugin settings

== Changelog ==

= 1.0 =
* Improved taxonomy identification: now displaying taxonomy's label and slug.
* Posibility to show/hide �None� option in select menu.
* Order categories in the dropdown menu by term ID, name, slug or menu_order. (DESC or ASC).
* Better interface for the settings page.
* Added localization support (.mo) and French language.

= 0.9 =
* Public release

= 0.8 =
* Some minor changes in Settings.

= 0.7 =
* Adds Settings page to manage options, possibility to choose which taxonomye should be replaced

= 0.6 =
* Adds options, taxonomies to replace

= 0.5 =
* Custom taxonomies are replaced automatically (no need editing code)

= 0.4 =
* Adds support for custom taxonomies and replaces it's metaboxes (manual definition of taxonomy one by one)

= 0.3 =
* Remove the default categories metabox
* Removed javascript manipulation

= 0.1 =
* Adds a new metabox to the admin screen with dropdown list for default categories (checkboxes select by javascript on list change)

== Upgrade Notice ==
After the update you have to reconfigure the plugin through "Settings"> "a�Category" menu in WordPress or the taxonomies will display as checkboxes.

* Improved taxonomy identification: now displaying taxonomy's label and slug.
* [NEW SETTINGS] Posibility to show/hide �None� option in select menu.
* [NEW SETTINGS] Order categories in the dropdown menu by term ID, name, slug or menu_order. (DESC or ASC).
* Better interface for the settings page.
* Added localization support (.mo) and French language.

---

Apr�s la mise � jours vous devez reconfigurer le plugin via le menu "R�glages"> "a�Category" sinon les taxonomies seront � nouveau affich�es sous formes de cases � cocher.

* Identification de taxonomie am�lior�e: d�sormais le nom (label) et le slug sont affich�s.
* [NOUVEAU REGLAGE] Posibilit� d'afficher/cacher l'option �Aucun� dans le menu de d�roulant.
* [NOUVEAU REGLAGE] Trier les cat�gories dans le menu d�roulant par ID, name, slug ou menu_order. (DESC ou ASC).
* Interface am�lior�es pour la page des param�tres.
* Ajout du support des fichiers de langues (.mo) et de la langue fran�aise.

== Notes ==
("r" in the version indicator means "revision", minor corrections like descriptions, translations or spelling.)

Check out my portfolio: [alek'](http://alek.be/),
my projects: [alek'labs](http://labs.alek.be/)

<?php code(); // goes in backticks ?>