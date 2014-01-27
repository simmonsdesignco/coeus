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
  // Adds the Open Sans Google Font as external stylesheets. We want Drupal to 
  // keep track of our stylesheets for us.
  drupal_add_css('http://fonts.googleapis.com/css?family=Open+Sans', array(
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
  // Set some variables for page title, site name, and site slogan.
  $page_title = drupal_get_title();
  $site_name = variable_get('site_name');
  $site_slogan = variable_get('site_slogan');

  // Check if the page title is empty.
  if ($page_title != $site_name) {
    // If true, set a default title using site name.
    $page_title = implode(' | ', array(
      $page_title,
      $site_name
    ));
  }
  
  else {
    $page_title = $site_name;
  }

	// If site slogan is given, add this to our head title.
  if ($site_slogan != NULL) {
    $page_title = implode(' - ', array(
      $page_title,
      $site_slogan
    ));
  }
	// Strip all tags from our new head title. We do this so " " is removed from
	// the head title.
  $coeus_title = strip_tags($page_title);

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
  // Set some variables for page title, site name, and site slogan.
  $page_title = drupal_get_title();
  $site_name = variable_get('site_name');
  $site_slogan = variable_get('site_slogan');
  
  // Change common page titles.
  global $user;
  // Set annonymous user page titles.
  if (!$user->uid) {
    if (((arg(0) == 'user') && (arg(1) == '')) || (arg(1) == 'login')) {
      // Set login page title.
      drupal_set_title('Login');
    }
    if (arg(1) == 'password') {
      // Set password reset page title.
      drupal_set_title('Request New Password');
    }
    if (arg(1) == 'register') {
      // Set password reset page title.
      drupal_set_title('Register');
    }
  }
  
  // Set global page titles.
  if (arg(0) == 'contact') {
    drupal_set_title('Contact Us');
  }

  // Check if the page title is empty.
  if (empty($page_title)) {
    // If true, set a default title using site name.
    drupal_set_title($site_name);
    $page_title = $site_name;
  }

  // Else set the default logo title using the page title and site name.
  else {
    $page_title = implode(' | ', array(
      $page_title,
      $site_name
    ));
  }

	// If site slogan is given, add this to our logo title.
  if ($site_slogan != NULL) {
    $page_title = implode(' - ', array(
      $page_title,
      $site_slogan
    ));
  }

  // Create variable of complete <title> variable from above variables.
  $coeus_title = $page_title;

  // Set up logo element with the logo path, alt tag, and attributes.
  global $base_root;
  $logo_path = str_replace($base_root."/", "", $variables['logo']);
  $logo_details = image_get_info($logo_path);
  $logo_alt = $site_name;
  $logo_vars = array(
    'path' => $logo_path,
    'alt' => $logo_alt . ' logo',
    'attributes' => array(
      'class' => 'site-logo'
    ),
    'width'  => $logo_details['width'],
    'height' => $logo_details['height'],
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
