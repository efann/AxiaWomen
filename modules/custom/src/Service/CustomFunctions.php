<?php

namespace Drupal\custom\Service;

use Drupal\Core\Cache\Cache;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
class CustomFunctions
{
  //-------------------------------------------------------------------------------------------------
  public function clearContentCaches()
  {
    $llOkay = true;
    $lcResults = '';

    try
    {
      $this->flushSpecificCaches();
    }
    catch (\Exception $loErr)
    {
      $llOkay = false;
      $lcResults = 'An error happened: ' . $loErr->getMessage();
    }

    if ($llOkay)
    {
      $lcResults = 'The content was successfully refreshed.';
    }

    return ($lcResults);
  }

  //-------------------------------------------------------------------------------------------------
  // Tweaked from
  // https://api.drupal.org/api/drupal/core%21includes%21common.inc/function/drupal_flush_all_caches/8.2.x
  private function flushSpecificCaches()
  {
    $module_handler = \Drupal::moduleHandler();

    // Flush all persistent caches.
    // This is executed based on old/previously known information, which is
    // sufficient, since new extensions cannot have any primed caches yet.
    $module_handler->invokeAll('cache_flush');
    foreach (Cache::getBins() as $service_id => $cache_backend)
    {
      $cache_backend->deleteAll();
    }

    /** @var \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler */
    $theme_handler = \Drupal::service('theme_handler');
    $theme_handler->refreshInfo();

    // Re-initialize the maintenance theme, if the current request attempted to
    // use it. Unlike regular usages of this function, the installer and update
    // scripts need to flush all caches during GET requests/page building.
    if (function_exists('_drupal_maintenance_theme'))
    {
      \Drupal::theme()->resetActiveTheme();
      drupal_maintenance_theme();
    }
  }

}
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
