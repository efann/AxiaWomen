<?php

namespace Drupal\custom\Service;

use Drupal;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\views\Views;
use Exception;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
class CustomFunctions
{
  // Not really the exact url. However, the 404 error will be thrown.
  const NO_DATA = '/404error';

  const URL_PREFIX = 'internal:';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_wow_promoted';

  //-------------------------------------------------------------------------------------------------
  public function getCurrentWOWLink($tlReturnNodeIDPath)
  {
    $lcLink = self::URL_PREFIX . self::NO_DATA;

    $loViewExecutable = Views::getView(self::VIEW_NAME);
    if (!is_object($loViewExecutable))
    {
      return ($lcLink);
    }

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      $loNode = $loRow->_entity;
      $lnID = $loNode->id();

      $lcLink = ($tlReturnNodeIDPath) ? self::URL_PREFIX . "/node/$lnID" : $loNode->toUrl()->toString();
      // There can only be one.
      break;
    }

    return ($lcLink);
  }

  //-------------------------------------------------------------------------------------------------
  public function getCurrentWOWTitle()
  {
    $lcTitle = '<unknown>';

    $loViewExecutable = Views::getView(self::VIEW_NAME);
    if (!is_object($loViewExecutable))
    {
      return ($lcTitle);
    }

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      $loNode = $loRow->_entity;
      $lcTitle = $loNode->getTitle();
      break;
    }

    return ($lcTitle);
  }

  //-------------------------------------------------------------------------------------------------
  public function clearContentCaches()
  {
    $llOkay = true;
    $lcResults = '';

    try
    {
      $this->flushSpecificCaches();
    }
    catch (Exception $loErr)
    {
      $llOkay = false;
      $lcResults = '<h2>Error</h2><p>An error happened:</p>' . $loErr->getMessage();
    }

    if ($llOkay)
    {
      $lcResults = '<h2 style="text-align: center;">Success</h2><p style="text-align: center;">The content was successfully refreshed. <em>Front Page</em>, <em>Blogs</em> and <em>WOW</em> content have been updated.</p>';
    }

    return ($lcResults);
  }

  //-------------------------------------------------------------------------------------------------
  // Tweaked from
  // https://api.drupal.org/api/drupal/core%21includes%21common.inc/function/drupal_flush_all_caches/8.2.x
  private function flushSpecificCaches()
  {
    $module_handler = Drupal::moduleHandler();

    // Flush all persistent caches.
    // This is executed based on old/previously known information, which is
    // sufficient, since new extensions cannot have any primed caches yet.
    $module_handler->invokeAll('cache_flush');
    foreach (Cache::getBins() as $service_id => $cache_backend)
    {
      $cache_backend->deleteAll();
    }

    /** @var ThemeHandlerInterface $theme_handler */
    $theme_handler = Drupal::service('theme_handler');
    $theme_handler->refreshInfo();

    // Re-initialize the maintenance theme, if the current request attempted to
    // use it. Unlike regular usages of this function, the installer and update
    // scripts need to flush all caches during GET requests/page building.
    if (function_exists('_drupal_maintenance_theme'))
    {
      Drupal::theme()->resetActiveTheme();
      drupal_maintenance_theme();
    }
  }

}
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
