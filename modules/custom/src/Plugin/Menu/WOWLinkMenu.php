<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Modules\Text;
use Drupal\views\Views;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

class WOWLinkMenu extends MenuLinkDefault
{
  // Not really the exact url. However, the 404 error will be thrown.
  const NO_DATA = '/404error';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_wow_promoted';

  const TITLE = 'WOW';
  const DESCRIPTION = 'Link to current Woman of the Week';
  const URL_PREFIX = 'internal:';

  //-------------------------------------------------------------------------------------------------
  // Interesting: with Java, super() has to be the first statement in a constructor.
  // From https://stackoverflow.com/questions/1168345/why-do-this-and-super-have-to-be-the-first-statement-in-a-constructor?rq=1
  // Not the case with PHP:
  // From https://stackoverflow.com/questions/39748226/must-i-call-parent-construct-in-the-first-line-of-the-constructor
  public function __construct(array $taConfiguration, $tcPluginID, $tcPluginDefinition, StaticMenuLinkOverridesInterface $tiStaticOverride)
  {
    $tcPluginDefinition['url'] = $this->getCurrentWOW();

    parent::__construct($taConfiguration, $tcPluginID, $tcPluginDefinition, $tiStaticOverride);
  }

  //-------------------------------------------------------------------------------------------------
  private function getCurrentWOW()
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

      $lcLink = self::URL_PREFIX . "/node/$lnID";
    }

    return ($lcLink);
  }

  //-------------------------------------------------------------------------------------------------

  /**
   * @inheritDoc
   */
  public function getTitle()
  {
    return (self::TITLE);
  }
  //-------------------------------------------------------------------------------------------------

  /**
   * @inheritDoc
   */
  public function getDescription()
  {
    return (self::DESCRIPTION);
  }
  //-------------------------------------------------------------------------------------------------
}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
