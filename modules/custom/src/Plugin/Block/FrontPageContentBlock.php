<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Core\Database\Database;
use Drupal\Core\Modules\Text;
use Drupal\views\Views;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Provides an 'all blogs' block.
 *
 * @Block(
 *   id = "aw_derived_from_front_page_block",
 *   admin_label = @Translation("AW Derived from Front Page Block"),
 *   category = @Translation("Custom block for displaying content derived from Front Page content.")
 * )
 */
class FrontPageContentBlock extends AWBlock
{
  const NO_DATA = 'Not much data to show here. . . .';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_derived_from_front_page';

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

    $lcContent = "";;

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      $loNode = $loRow->_entity;
      $lnID = $loNode->id();

       // Top Parallax
      $loReferencedParagraph = $this->getReferencedEntity($loNode, 'field_top_parallax');
      $lcText = $this->getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = $this->getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = $this->getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='parallax-window' data-parallax='scroll' data-image-src='$lcImage'></div>\n";

      $lcContent .= "<div class='col-sm-12 views-row row$lnIndex'>\n";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= " </div > \n";

      // First Things
      $lcContent .= "<div class='col-sm-12 views-row row$lnIndex'>\n";

      $loReferencedParagraph = $this->getReferencedEntity($loNode, 'field_first_things');
      $lcText = $this->getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = $this->getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = $this->getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= " </div > \n";

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcImage' /></div></div>";
      $lcContent .= " </div > \n";

      $lcContent .= " </div > \n";
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
