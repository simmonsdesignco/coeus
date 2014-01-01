<?php

include_once drupal_get_path('theme', 'coeus') .
  '/includes/browser-detection.inc';

/**
 * Prepares variables for HTML templates.
 *
 * Default template: html.tpl.php.
 *
 * @param array $variables
 *   An associative array containing:
 *   - drupal_add_css: An associative array containing the properties of
 *     css files being added to the HTML head element.
 *     Properties used: #type, #group, #every_page, and #browsers.
 */
function coeus_preprocess_html(&$variables) {
  // Adds the Open Sans Google Font as an external stylesheet.
  drupal_add_css('http://fonts.googleapis.com/css?family=Open+Sans', array(
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE
  ));

  drupal_add_js(drupal_get_path('theme', 'coeus') .
    '/js/modernizr.custom.min.js');
  
  // Sets the HTML class to include the browser name from the browser detection
  // script.
  $browser_name = browser_detection('browser_name');

  // HTML element attributes.
  $variables['html_attributes_array'] = array(
    'class' => array($browser_name),
    'lang' => $variables['language']->language,
    'dir' => $variables['language']->dir,
  );

  // Serialize RDF Namespaces into an RDFa 1.1 prefix attribute.
  if ($variables['rdf_namespaces'] && function_exists('rdf_get_namespaces')) {
    $variables['rdf'] = array('prefix' => '');
    foreach (rdf_get_namespaces() as $prefix => $uri) {
      $variables['rdf']['prefix'] .= $prefix . ': ' . $uri . "\n";
    }
    $variables['rdfa_namespaces'] = drupal_attributes($variables['rdf']);
  }
  
  // Define variable to be called for html lang and dir tags.
  $variables['html_attributes'] = drupal_attributes(
    $variables['html_attributes_array']);
}

/**
 * Prepares variables for HTML templates.
 *
 * Default template: html.tpl.php.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of
 *     unneeded HTML tags.
 *     Properties used: #attributes, #value_prefix, and #value_suffix.
 */
function coeus_preprocess_html_tag(&$variables) {
  $el = &$variables['element'];

  // Remove type="..." attributes from style, script, and link elements.
  if (in_array($el['#tag'], array('script', 'link', 'style'))) {
    unset($el['#attributes']['type']);
  }

  // Remove CDATA prefix comments.
  if (isset($el['#value_prefix'])
    && ($el['#value_prefix'] == "\n<!--//--><![CDATA[//><!--\n"
    || $el['#value_prefix'] == "\n<!--/*--><![CDATA[/*><!--*/\n")) {
      unset($el['#value_prefix']);
  }

  // Remove CDATA suffix comments.
  if (isset($el['#value_suffix'])
    && ($el['#value_suffix'] == "\n//--><!]]>\n"
    || $el['#value_suffix'] == "\n/*]]>*/-->\n")) {
      unset($el['#value_suffix']);
  }

  // Remove media="all" attributes, leaving others alone.
  if (isset($el['#attributes']['media']) &&
    $el['#attributes']['media'] === 'all') {
      unset($el['#attributes']['media']);
  }
}

/**
 * Implements hook_html_head_alter()
 *
 * @todo Detect the which Apple device is being used and select the correct
 *   touch icon and add it to the head. Also verify the head element is
 *   is following the W3C semantic guidelines outlined at
 *   @link http://tinyurl.com/w3c-html5-semantics-head @endlink.
 */
function coeus_html_head_alter(&$head_elements) {
  // Get the browser name and set the head title for use later.
  $browser_name = browser_detection('browser_name');
  $drupal_title = drupal_get_title() . ' | ' . variable_get('site_name');
  $site_title = implode(' - ', array(
    $drupal_title,
    variable_get('site_slogan')
  ));

	// Remove Drupal's 'Generator' and the 'Content-Type' attributes.
  unset($head_elements['system_meta_generator']);
  unset($head_elements['system_meta_content_type']);

  // Add the utf-8 character set to all browsers.
  $head_elements['character_set'] = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'charset' => 'utf-8'
    ),
    '#weight' => -10
  );

  // Create new head title using the custom strings set above.
  $head_elements['head_title'] = array(
    '#type' => 'html_tag',
    '#tag' => 'title',
    '#value' => $site_title,
    '#weight' => -9
  );

  // Uses the latest IE rendering engine and Google Chrome Frame for IE.
  if ($browser_name == 'msie') {
    $head_elements['chrome_frame'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'http-equiv' => 'X-UA-Compatible',
        'content' => 'IE=edge,chrome=1'
      ),
      '#weight' => -8
    );
  }

  // Set the viewport for correct mobile scaling.
  $head_elements['view_port'] = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'viewport',
      'content' => 'width=device-width, initial-scale=1'
    ),
    '#weight' => -7
  );
}

/**
 * Preprocess variables for page.tpl.php
 */
function coeus_preprocess_page(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Set up logo element
  $logo_path = check_url($vars['logo']);
  $logo_alt = variable_get('site_name');
  $logo_title = drupal_get_title() . ' | ' . $logo_alt;
  $logo_array = array($logo_title, variable_get('site_slogan'));
  $logo_title_imploded = implode(' - ', $logo_array);
  $logo_vars = array(
    'path' => $logo_path,
    'alt' => $logo_alt . ' logo',
    'attributes' => array(
      'class' => 'site-logo'
    )
  );
  $vars['logo_img'] = theme('image', $logo_vars);
  $vars['site_logo'] = $vars['logo_img'] ? l($vars['logo_img'],
    '<front>', array(
      'attributes' => array(
        'title' => check_plain($logo_title_imploded)
      ),
      'html' => TRUE
    )
  ) : '';
}
