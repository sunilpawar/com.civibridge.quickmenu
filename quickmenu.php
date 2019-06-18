<?php

require_once 'quickmenu.civix.php';
use CRM_Quickmenu_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function quickmenu_civicrm_config(&$config) {
  _quickmenu_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function quickmenu_civicrm_xmlMenu(&$files) {
  _quickmenu_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function quickmenu_civicrm_install() {
  _quickmenu_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function quickmenu_civicrm_postInstall() {
  _quickmenu_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function quickmenu_civicrm_uninstall() {
  _quickmenu_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function quickmenu_civicrm_enable() {
  _quickmenu_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function quickmenu_civicrm_disable() {
  _quickmenu_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function quickmenu_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _quickmenu_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function quickmenu_civicrm_managed(&$entities) {
  _quickmenu_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function quickmenu_civicrm_caseTypes(&$caseTypes) {
  _quickmenu_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function quickmenu_civicrm_angularModules(&$angularModules) {
  _quickmenu_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function quickmenu_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _quickmenu_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function quickmenu_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function quickmenu_civicrm_navigationMenu(&$menu) {
  _quickmenu_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _quickmenu_civix_navigationMenu($menu);
} // */

/**
 * Implementation of hook_civicrm_pageRun
 */
function quickmenu_civicrm_pageRun(&$page) {
  CRM_Quickmenu_Utils::addResource();
}

/**
 * Implementation of hook_civicrm_pageRun
 */
function quickmenu_civicrm_buildForm(&$form) {
  CRM_Quickmenu_Utils::addResource();
}


function _quickmenu_civix_civicrm_js($config) {
  // add js only for normal civicrm url and skip for cron job.
  // Joomla throw error for isUserLoggedIn function because we calling isUserLoggedIn function though config hook that time JFactory class is not initialised.
  if (isset($_SERVER['SCRIPT_URL']) && preg_match('#civicrm/(extern|bin)#', $_SERVER['SCRIPT_URL']) ) {
    return;
  }
  if (CRM_Utils_System::isUserLoggedIn() ) {
    if (array_key_exists('snippet', $_GET)) {
      return;
    }
    CRM_Core_Resources::singleton()->addScript("
      cj( document ).ready(function() {
        cj('ul#civicrm-menu').append('<li id=\"crm-qukckmenu\" class=\"menumain\" tabindex=\"20\"><div id=\"form-quickmenu\"><div style=\"position:relative;\"><input style=\"width:6em;\" type=\"text\" name=\"quickmenu\" placeholder=\"Quick Menu Search\" id=\"quickmenu\" class=\"form-text\" maxlength=\"64\"><input id=\"quickmenu-reset\"  class=\"quickmenu-reset\" type=\"reset\" value=\"X\" /></div></div></li>');
      });
    ");
  }
}

