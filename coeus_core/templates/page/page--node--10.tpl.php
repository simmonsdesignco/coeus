<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in the ./templates directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $is_front: TRUE if the current page is the front page.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 *
 * Regions:
 * - $page['header']: Main content header of the current page.
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar']: Items for the sidebar.
 * - $page['footer']: Items for the footer region.
 *
 * @see html.tpl.php
 */

// Sets up some variables to be used later.
global $base_url;
$error_css = '/sites/all/themes/coeus/coeus_core/templates/page/styles/404.css';
$error_style = $base_url.$error_css;
?>

<link href="<?php print $error_style; ?>" rel="stylesheet">
<div id="page-container">
  <header id="top-nav" class="clearfix">
    <div id="top-nav-wrapper" class="container_12">
      <div id="branding" class="grid_4 alpha">
        <?php if ($logo): ?>
        <figure id="logo-figure"><?php print $site_logo; ?></figure>
        <?php endif; ?>
        <?php if (!$logo): ?>
        <?php print $site_name; ?>
        <?php endif; ?>
      </div>
      <?php if ($main_menu || $secondary_menu): ?>
      <div id="navbar" class="grid_8 omega">
        <nav id="main-nav" class="alpha"><?php print theme('links__system_main_menu', array(
          'links' => $main_menu,
          'attributes' => array(
            'id' => 'main-menu',
            'class' => array(
              'site-nav'
            )
          )
        )); ?></nav>
        <nav id="secondary-nav" class="omega"><?php print theme('links__system_secondary_menu', array(
          'links' => $secondary_menu,
          'attributes' => array(
            'id' => 'secondary-menu',
            'class' => array(
              'site-nav'
            )
          )
        )); ?></nav>
      </div>
      <?php endif ?>
    </div>
  </header>
  <section id="page-header" class="container_12 clearfix">
    <div id="page-title-search" class="grid_12 clearfix">
      <div id="page-title-wrapper" class="grid_9 alpha"><?php print render($title_prefix); ?>
        <h1 id="page-title"><?php // print $title; ?></h1>
      <?php print render($title_suffix); ?></div>
      <div id="search-block-wrapper" class="grid_3 omega">
        <?php $search_block = module_invoke('search', 'block_view', 'search'); print render($search_block); ?>
      </div>
    </div>
  </section>
  <section id="content-section" class="container_12 clearfix">
    <a id="main-content"></a>
    <div id="content" class="container_12">
      <article id="page-content" class="grid_12">
        <div class="wrapper row2">
          <div id="container">
            <section id="fof">
              <div class="fl_left">
                <h1>Sorry, You Do Not Have Access!</h1>
              </div>
              <div class="fl_right">
                <h2>What Next ?</h2>
                <p>Uh oh! You are forbidden from viewing this page!</p>
                <p>Go back to the <a href="javascript:history.go(-1)">previous page</a> or login <a href="<?php print $base_url.'/user'; ?>">homepage</a>.</p>
              </div>
            </section>
          </div>
        </div>
      </article>
    </div>
  </section>
  <footer id="footer" class="clearfix">
    <div id="footer-section" class="container_12">
      <div class="content">
        <ul class="grid_4 alpha">
          <li>Powered by <a href="https://drupal.org" target="_blank">Drupal</a> </li>
        </ul>
        <ul class="grid_4">
          <li></li>
        </ul>
        <ul class="grid_4 omega">
          <li>Theme by <a href="http://www.buchanandesigngroup.com" target="_blank">Buchanan Design Group</a> </li>
        </ul>
      </div>
    </div>
  </footer>
</div>
