<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\block\Entity\Block;
use Drupal\Core\Block\BlockBase;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Provides an 'combo header' block.
 *
 * @Block(
 *   id = "combo_header_block",
 *   admin_label = @Translation("Combo Header Block"),
 *   category = @Translation("Custom block for displaying the logo and search form.")
 * )
 */
class HeaderBlock extends BlockBase
{
  //-------------------------------------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $lcContent = "";
    $lcContent .= "<div class='row'>\n";

    $loHeaderBlock = \Drupal::entityTypeManager()
        ->getStorage('block')
        ->load('header');
    if (!empty($loHeaderBlock))
    {
      $lcPreRender = \Drupal::entityTypeManager()
          ->getViewBuilder('block')
          ->view($loHeaderBlock);

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= \Drupal::service('renderer')->renderRoot($lcPreRender);
      $lcContent .= "</div>\n";
    }

    $loSearchBlock = \Drupal::entityTypeManager()
        ->getStorage('block')
        ->load('searchform');

    $loSearchBlock = Block::load('searchform');

    if (!empty($loSearchBlock))
    {
      $lcPreRender = \Drupal::entityTypeManager()
          ->getViewBuilder('block')
          ->view($loSearchBlock);

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= \Drupal::service('renderer')->renderRoot($lcPreRender);
      $lcContent .= "</div>\n";
    }
    else
    {
      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= "nothing";
      $lcContent .= "</div>\n";

    }

    $lcContent .= "</div>\n";

    return (array(
        '#type' => 'markup',
        '#markup' => $lcContent,
    ));
  }

  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
