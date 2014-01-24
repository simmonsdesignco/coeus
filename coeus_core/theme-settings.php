<?php

function coeus_core_form_system_theme_settings_alter(&$form, $form_state) {
  $rdfa = 'http://www.w3.org/TR/rdfa-in-html/#extensions-to-the-html5-syntax';
  $rdfa_link = l(t('extensions to the HTML5 syntax.'), $rdfa, array(
    'attributes' => array(
      'target' => '_blank',
    )
  ));
  $rdfa_desc =
    'Implements new features in RDFa 1.1 to express structured data in HTML5.
    <br />' . 'See W3C recommendations on ' . $rdfa_link;

  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => 99,
  );
  
  $form['vertical_tabs_html5'] = array(
    '#type' => 'fieldset',
    '#title' => t('HTML5 Compliance'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#group' => 'additional_settings',
    '#attached' => array(
      'js' => array(
        'vertical-tabs' => drupal_get_path('module', 'vertical_tabs_example').
          '/vertical_tabs_example.js',
      ),
    ),
    '#tree' => TRUE,
    '#weight' => -15,
  );

  $form['vertical_tabs_html5']['rdfa_serialization'] = array(
    '#type' => 'checkbox',
    '#title' => t('RDFa Core 1.1 Serialization'),
    '#default_value' => theme_get_setting('rdfa_serialization'),
    '#description'   => $rdfa_desc,
  );
  
  $form['#submit'][] = 'coeus_core_form_system_theme_settings_submit';
}

/*function coeus_core_form_system_theme_settings_submit(&$form, &$form_state) {
  // Form has been submitted, execute your function.
}*/
