<?php

include_once drupal_get_path('theme', 'coeus_core') .
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
function coeus_core_preprocess_html(&$variables) {
  $browser_name = browser_detection('browser_name');
  // Adds the Droid Google Fonts as external stylesheets. We want Drupal to keep
	// track of our stylesheets for us.
  drupal_add_css('http://fonts.googleapis.com/css?family=Droid+Sans', array(
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE
  ));
  drupal_add_css('http://fonts.googleapis.com/css?family=Droid+Serif', array(
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE
  ));
  drupal_add_css('http://fonts.googleapis.com/css?family=Droid+Sans+Mono', array(
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE
  ));

	// Adds our custom minified version of modernizr script. We only selected
	// HTML5 elements for our script, which will add classes to the HTML tag to
	// target HTML5 compatible browsers.
  drupal_add_js(path_to_theme() . '/js/modernizr.custom.min.js');

  // HTML element attributes.
  $variables['html_attributes_array'] = array(
    'class' => array($browser_name),
    'lang' => $variables['language']->language,
    'dir' => $variables['language']->dir,
  );

  $rdfa_settings = theme_get_setting('rdfa_serialization');
  if ($rdfa_settings == 1) {
    // Serialize RDF Namespaces into an RDFa 1.1 prefix attribute.
    if ($variables['rdf_namespaces'] && function_exists('rdf_get_namespaces')) {
      $variables['rdf'] = array('prefix' => '');
      foreach (rdf_get_namespaces() as $prefix => $uri) {
        $variables['rdf']['prefix'] .= $prefix . ': ' . $uri;
      }
      $variables['rdfa_namespaces'] = drupal_attributes($variables['rdf']);
    }
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
function coeus_core_preprocess_html_tag(&$variables) {
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
function coeus_core_html_head_alter(&$head_elements) {
  // Create browser name variable from the browser_detection script.
  $browser_name = browser_detection('browser_name');

  // Set the default head title using the page title and site name.
  $coeus_get_title = implode(' | ', array(
    drupal_get_title(),
    variable_get('site_name')
  ));

	// If site slogan is given, add this to our head title.
  if (variable_get('site_slogan') != NULL) {
    $coeus_get_title = implode(' - ', array(
      $coeus_get_title,
      variable_get('site_slogan')
    ));
  }
	// Strip all tags from our new head title. We do this so " " is removed from
	// the head title.
  $coeus_title = strip_tags($coeus_get_title);

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

  // Set head title using the custom strings above.
  $head_elements['head_title'] = array(
    '#type' => 'html_tag',
    '#tag' => 'title',
    '#value' => $coeus_title,
    '#weight' => -9
  );

  // Uses the latest IE rendering engine and Google Chrome Frame for IE.
	// Setting this here results in no <!-- --> comments in our HTML.
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

  // Ensure proper rendering and touch zooming.
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
function coeus_core_preprocess_page(&$variables) {
  // Create 'Page Title | Site Name' variable.
  $coeus_get_title = implode(' | ', array(
    drupal_get_title(),
    variable_get('site_name')
  ));

  // If using a slogan, create 'Page Title | Site Name - Site Slogan' variable.
  if (variable_get('site_slogan') != NULL) {
    $coeus_get_title = implode(' - ', array(
      $coeus_get_title,
      variable_get('site_slogan')
    ));
  }

  // Create variable of complete <title> variable from above variables.
  $coeus_title = $coeus_get_title;

  // Set up logo element with the logo path, alt tag, and attributes.
  $logo_path = check_url($variables['logo']);
  $logo_alt = variable_get('site_name');
  $logo_vars = array(
    'path' => $logo_path,
    'alt' => $logo_alt . ' logo',
    'attributes' => array(
      'class' => 'site-logo'
    )
  );

  // Override the default logo element with custom variables set above.
  $variables['logo_img'] = theme('image', $logo_vars);
  $variables['site_logo'] = $variables['logo_img'] ? l($variables['logo_img'],
    '<front>', array(
      'attributes' => array(
        'title' => check_plain($coeus_title)
      ),
      'html' => TRUE
    )
  ) : '';
}

/**
* Changes the search form to use the "search" input element of HTML5.
*/
function coeus_core_preprocess_search_block_form(&$vars) {
  $vars['search_form'] = str_replace(
    'type="text"',
    'type="search"',
    $vars['search_form']
  );
}

/* Put Breadcrumbs in a ul li structure */
function coeus_core_breadcrumb($variables) {
  $bc = $variables['breadcrumb'];
  if (!empty($bc)) {
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with 
    // .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $bc_separator = '<span class="breadcrumb-separator"> / </span>';
    $cpage = '<span class="breadcrumb-separator"> / </span>'.drupal_get_title();
    $output .= '<div class="breadcrumb">'.implode
      ($bc_separator, $bc).$cpage.'</div>';
    return $output;
  }
}

function coeus_core_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    // Define size of the textfield 
    $form['search_block_form']['#size'] = 20;
    // Alternative (HTML5) placeholder attribute instead of using the javascript
    $form['search_block_form']['#attributes']['placeholder'] = t('Search');
  }
}
