<?php


class CRM_Quickmenu_Utils {

  private static $_resource_loaded = false;

  static function addResource() {
    if (!CRM_Core_Permission::check('allow quick menu')) {
      return;
    }
    if (self::is_public_page() || self::$_resource_loaded || array_key_exists('snippet', $_GET)) {
      return;
    }
    $config = CRM_Core_Config::singleton();
    $menuList = self::buildNavigation();
    $menuList = str_replace('class="menu-separator"', '', $menuList);
    $menuList = substr($menuList, 5);
    $html = '<div class="overlay-quick-menu" id="overlay-quick-menu" style="display:none;overflow:scroll;height:300px !important;"><div id="quick-search-title" style="width:16px;float: right;margin-right: 20px;"><input class="quickmenu-reset" type="reset" value="X" style="display: inline-block;position:fixed;"></div><ul id="civicrm-menu-custom" style="display:none;">' . $menuList . '</ul></div>';

    CRM_Core_Resources::singleton()->addScript("
       cj( document ).ready(function() {
       cj( 'body' ).append( '{$html}' );
       });
    ")
    ;

    CRM_Core_Resources::singleton()->addScript(file_get_contents(dirname(dirname(dirname(__FILE__))) . "/js/quickmenu.js"));
    CRM_Core_Resources::singleton()->addStyleFile('com.civibridge.quickmenu', 'css/quickmenu.css');
    self::$_resource_loaded = TRUE;
  }

  static function is_public_page() {
    // Get the menu items.
    $args = explode('?', $_GET['q']);
    $path = $args[0];
    // Get the menu for above URL.
    $item = CRM_Core_Menu::get($path);

    // Check for public pages
    // If public page and civicrm public theme is set, apply civicrm public theme
    if (CRM_Utils_Array::value('is_public', $item)) {
      return TRUE;
    }
    return FALSE;
  }

  public static function buildNavigation() {
    // Get CiviCRM Menu List
    $navigations = CRM_Core_BAO_Navigation::buildNavigationTree();
    $navigationString = '';

    // run the Navigation  through a hook so users can modify it
    CRM_Utils_Hook::navigationMenu($navigations);
    CRM_Core_BAO_Navigation::fixNavigationMenu($navigations);

    // Do not add any style, class to ul, li.
    $skipMenuItems = array();
    foreach ($navigations as $key => $value) {
      // Home is a special case
      if ($value['attributes']['name'] != 'Home') {
        $name = CRM_Core_BAO_Navigation::getMenuName($value, $skipMenuItems);
        if (!empty(trim($name)) && substr($name, 0, 2) != '<a') {
          // Add anchor tag to Navigation Parent menus. ('Search, Report, etc)
          $name = "<a href=\"#\">{$name}</a>";
        }
        if ($name) {
          $navigationString .= '<li>' . $name;
        }
      }
      self::recurseNavigation($value, $navigationString, $skipMenuItems);
    }

    $navigationString = str_replace('<ul></ul></li>', '', $navigationString);
    return $navigationString;
  }

  public static function recurseNavigation(&$value, &$navigationString, $skipMenuItems) {
    if (!empty($value['child'])) {
      $navigationString .= '<ul>';
    }
    else {
      $navigationString .= '</li>';
    }

    if (!empty($value['child'])) {
      foreach ($value['child'] as $val) {
        $name = CRM_Core_BAO_Navigation::getMenuName($val, $skipMenuItems);
        $name = addcslashes($name, "'");
        if (!empty(trim($name)) && substr($name, 0, 2) != '<a') {
          // Add anchor tag to Navigation Parent menus. ('Search, Report, etc)
          $name = "<a href=\"#\" style=\"pointer-events: none;cursor: default;text-decoration: none;\">{$name}</a>";
        }
        if ($name) {
          $navigationString .= '<li>' . $name;
          self::recurseNavigation($val, $navigationString, $skipMenuItems);
        }
      }
    }
    if (!empty($value['child'])) {
      $navigationString .= '</ul></li>';
    }
    return $navigationString;
  }
}
