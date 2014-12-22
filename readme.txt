=== aCategory Dropdown List ===
Contributors: alekart
Tags: category, post, admin, dropdown, select, list, select, menu
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 1.2.7
License: GPLv2

Replace category checkboxes by a dropdown menu on post's edit page.

== Description ==
Limit possible choice to one catégory by post or organize your posts by several categories and subcategories and avoid checking wrong extra category.

= Features =
* Select category from a dropdown list
* One category per post
* Multi choice, choose a category and its subcategory (Cars -> Honda -> Civic)
* Order the list by `id`, `name`, `slug`, `menu_order`
* Descendant/ascendant order (DESC/ASC)
* Hide/Show `None` option`*`

*`*` The default taxonomy (Categories) has not "None" option. If user choose no catégory, the default category "Uncategorized" is selected automatically even without this plugin.*

**NOTE:** the plugin works only for **hierarchical taxonomies** and will not work with tags.

= Installation =

1. Upload `acategory-dropdown-list` folder in `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. **Configure the plugin** through `Settings > a´Category` menu in WordPress.

== Installation ==

1. Upload `acategory-dropdown-list` folder in `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. **Configure the plugin** through `Settings > a´Category` menu in WordPress.

== Frequently Asked Questions ==


== Screenshots ==

1. Default single choice dropdown list
2. Multi choice dropdown list
3. Plugin settings

== Changelog ==

= 1.2.7 =
* Added support for taxonomies shared for multiple post types.
* Fixed a non defined default option for new installations.

= 1.2.6 =
* Fixed critical bugs that broke the plugin in 1.2.0.

= 1.2.0 =
* Added Multi choice. Select a category and its subcategory.
* Better "Reset" button. Need to be pressed two times to reset settings.

= 1.1.0 =
* Added support for WordPress Multisite.
* Added Persian translate (Thanks to Sina Saeedi admin@sinawebhost.ir).

= 1.0 =
* Improved taxonomy identification: now displaying taxonomy's label and slug.
* Posibility to show/hide «None» option in select menu.
* Order categories in the dropdown menu by term ID, name, slug or menu_order. (DESC or ASC).
* Better interface for the settings page.
* Added localization support (.mo) and French language.

= 0.9 =
* Public initial release.

== Upgrade Notice ==

**Important**
If you have installed the 1.2.0 version it's highly recommended to update to 1.2.6.
The **1.2.0 version is broken**.

*Note: After the update you could need to reconfigure the plugin through `Settings > a´Category` menu in WordPress.*

== Notes ==

Check out my portfolio: [alek'](http://alek.be/),
my projects: [alek'labs](http://labs.alek.be/)

<?php code(); // goes in backticks ?>