<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Core\Url;
use Drupal\views\Views;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Provides an 'all images' block.
 *
 * @Block(
 *   id = "aw_all_images_block",
 *   admin_label = @Translation("AW All Images Block"),
 *   category = @Translation("Custom block for displaying all of the images.")
 * )
 */
class AllImageBlock extends AWBlock
{
  const NO_DATA = 'Not much data to show here. . . .';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_images_page';

  //-------------------------------------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function build()
  {

    $loViewExecutable = Views::getView(self::VIEW_NAME);
    if (!is_object($loViewExecutable))
    {
      return array(
          '#type' => 'markup',
          '#markup' => t(self::NO_DATA),
      );
    }

    $lcContent = "";

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    $lnTrack = 0;
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      if (($lnIndex % 2) == 0)
      {
        $lnRow = $lnIndex / 2;
        $lcContent .= "<div class='col-sm-12 views-row row$lnRow'>\n";
        $lnTrack = 0;
      }

      $loNode = $loRow->_entity;

      $lcTitle = $this->getNodeField($loNode, 'title');
      $lcCopyright = $this->getNodeField($loNode, 'body');
      $lcImage = $this->getNodeField($loNode, 'field_image');

      // From https://stackoverflow.com/questions/36087896/drupal-8-get-the-image-width-height-alt-etc-in-a-twig-or-preprocess-fi/52944485#52944485
      $loImage = $loNode->field_image[0]->getValue();
      $lnWidth = $loImage['width'];
      $lnHeight = $loImage['height'];

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img data-natural-width='$lnWidth' data-natural-height='$lnHeight' src='$lcImage' /></div></div>";
      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'>$lcTitle</span></div>";
      $lcContent .= "<div class='views-field views-field-copyright'><span class='field-content'>$lcCopyright</span></div>";
      $lcContent .= "</div>\n";

      if (++$lnTrack >= 2)
      {
        $lcContent .= "</div>\n";
        $lnTrack = 0;
      }
    }

    // Means if not set to zero then the last div.col-sm-12 was not closed.
    if ($lnTrack != 0)
    {
      $lcContent .= "</div>\n";
    }

    // From https://drupal.stackexchange.com/questions/199527/how-do-i-correctly-setup-caching-for-my-custom-block-showing-content-depending-o
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
