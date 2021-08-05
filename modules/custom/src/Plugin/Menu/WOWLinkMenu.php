<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Modules\Text;
use Drupal\custom\Service\CustomFunctions;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

class WOWLinkMenu extends MenuLinkDefault
{
  const TITLE = 'WOW';
  const DESCRIPTION = 'Link to current Woman of the Week';

  //-------------------------------------------------------------------------------------------------
  // Interesting: with Java, super() has to be the first statement in a constructor.
  // From https://stackoverflow.com/questions/1168345/why-do-this-and-super-have-to-be-the-first-statement-in-a-constructor?rq=1
  // Not the case with PHP:
  // From https://stackoverflow.com/questions/39748226/must-i-call-parent-construct-in-the-first-line-of-the-constructor
  public function __construct(array $taConfiguration, $tcPluginID, $tcPluginDefinition, StaticMenuLinkOverridesInterface $tiStaticOverride)
  {
    $loCustom = new CustomFunctions();

    $tcPluginDefinition['url'] = $loCustom->getCurrentWOWLink(true);

    parent::__construct($taConfiguration, $tcPluginID, $tcPluginDefinition, $tiStaticOverride);
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
