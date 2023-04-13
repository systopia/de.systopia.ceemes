<?php

require_once 'ceemes.civix.php';
use CRM_Ceemes_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function ceemes_civicrm_config(&$config) {
  _ceemes_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function ceemes_civicrm_install() {
  _ceemes_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function ceemes_civicrm_enable() {
  _ceemes_civix_civicrm_enable();

  // Make sure our custom fields exist.
  require_once 'CRM/Ceemes/CustomData.php';
  $customData = new CRM_Ceemes_CustomData('de.systopia.ceemes');
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_ceemes.json');
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function ceemes_civicrm_navigationMenu(&$menu) {
  _ceemes_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _ceemes_civix_navigationMenu($menu);
} // */
