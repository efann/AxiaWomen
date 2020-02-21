<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Menu;

use Drupal\Component\Utility\Html;
use Drupal\Core\Menu\MenuLinkBase;
use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Modules\Text;
use Drupal\Core\Url;
use Drupal\views\Views;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

class WOWLinkMenu extends MenuLinkDefault
{
  const NO_DATA = '/#';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_wow_promoted';

  const TITLE = 'WOW';
  const DESCRIPTION = 'Link to current Woman of the Week';

  //-------------------------------------------------------------------------------------------------

  public function __construct(array $configuration, $plugin_id, $plugin_definition, StaticMenuLinkOverridesInterface $static_override)
  {
    $plugin_definition['url'] = 'internal:/node/2';

    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override);
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
