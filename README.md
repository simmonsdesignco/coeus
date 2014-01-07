Coeus
================================
*Version: 7.x-1.x*

Current Maintainer: Richard Buchanan <richard_buchanan@buchanandesigngroup.com>


CONTENTS OF THIS FILE
---------------------

 * Introduction
 * HTML Template File
 * Page Template File


INTRODUCTION
------------
Coeus is planned as a lightweight, flexible, 12-column, responsive, HTML5 /
RDFa 1.1 standards compliant Drupal 7 base theme.

The name Coeus is derived from a Titan in Greek mythology. Like most Titans, he
played no active part in Greek religion, but was primarily important for his
descendants. Which is fitting for this theme, since it is meant as a base theme
for full HTML5 support on Drupal 7 sites. I am actively maintaining this sandbox
project, although any help or advice would be greatly appreciated!

Included is a sub-theme for fine-grained customization. Use this to test out the
base theme and test it against validators such as the W3C Nu Markup Validation
Service (validator.w3.org/nu/).


PAGE TEMPLATE FILE
------------------
Coeus uses a 960 pixel-wide fluid grid system. The page.tpl.php template file
is the main template file used in setting up the grid used throughout the site.

The following are the page elements set by the page template file, identified by
the element ID and element type.

*page-container*
The page container div wraps the page's content. It fills the same area as the
body element, which is 100% of the page's width and height.

*top-nav*
The top navigation section spans the full-width of the page container div
and includes the site logo and main navigational menus.

* 1.1 branding
The branding div wraps the site logo and is 4-columns wide.

* 1.1a logo-figure
The logo figure contains the site logo and is a responsive width based on
the width of the branding div.
      
* 1.2 nav-bar
The navigational bar div wraps the site's main navigational menus.

* 1.2a main-nav
The main navigation nav contains the site's main menu. By default this
is the main menu defined by Drupal, including a "Home" link.

* 1.2b secondary-nav
The secondary navigation nav contains the site's secondary menu. By
default this is the user menu.

*page-header*
* 2.1 page-title

* 2.2 breadcrumb

*highlights*
*3.1 site-messages

*3.2 highlighted

*3.3 help

*content-section*
* 4.1 main-content

* 4.2 content-header

* 4.3 tabs

* 4.4 action-links

* 4.5 content

* 4.5a sidebar-first

* 4.5b page-content

*footer*
* 5.1 footer-section

* 5.1a content
