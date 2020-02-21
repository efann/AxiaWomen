<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Component\Utility\Html;
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

      // Top Parallax
      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_top_parallax');
      $lcText = AWBlock::getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      // From https://stackoverflow.com/questions/36087896/drupal-8-get-the-image-width-height-alt-etc-in-a-twig-or-preprocess-fi/52944485#52944485
      $loImage = $loReferencedImage->field_image[0]->getValue();
      $lnWidth = $loImage['width'];
      $lnHeight = $loImage['height'];

      $lcContent .= "<div class='top-parallax col-sm-12 views-row row0'>\n";

      $lcStyle = "style='height: $lnHeight" . "px;'";
      $lcContent .= "<div class='parallax-window' data-parallax='scroll' data-image-src='$lcImage' data-natural-width='$lnWidth' data-natural-height='$lnHeight' $lcStyle>";
      $lcContent .= "<div class='parallax-text views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= " </div>\n";

      $lcContent .= " </div>\n";

      // Billboard & Tiles
      $lcContent .= "<div class='billboard-tile col-sm-12 views-row row$lnIndex'>\n";
      $lcBillboard = AWBlock::getNodeField($loNode, 'field_billboard');

      $lcContent .= "<div class='col-sm-4'>\n";
      $lcContent .= "<div class='views-field views-field-field_billboard'><div class='field-content'>$lcBillboard</div></div>";
      $lcContent .= "</div>\n";

      $loEntityRows = $loNode->get('field_row_of_image_text')->referencedEntities();

      $lcContent .= "<div class='col-sm-8'>\n";
      $lnTrack = 0;
      foreach ($loEntityRows as $lnIndex => $loRow)
      {
        if (($lnIndex % 2) == 0)
        {
          $lnRow = $lnIndex / 2;
          $lcContent .= "<div class='col-sm-12 views-row row$lnRow'>\n";
          $lnTrack = 0;
        }

        $lcCaption = AWBlock::getNodeField($loRow, 'field_html_text');
        $loReferencedTileImage = AWBlock::getReferencedEntity($loRow, 'field_image_content_id');
        // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
        // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
        // By the way, title has this problem as it's a plain text field with no conversion.
        $lcTitle = HTML::escape(AWBlock::getNodeField($loReferencedTileImage, 'title'));
        $lcTileImage = AWBlock::getNodeField($loReferencedTileImage, 'field_image');

        $lcContent .= "<div class='col-sm-6'>\n";
        $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcTileImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' /></div></div>";
        $lcContent .= "<div class='views-field views-field-caption'><span class='field-content'>$lcCaption</span></div>";
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
      $lcContent .= "</div>\n";

      $lcContent .= "</div>\n";

      // Middle Parallax
      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_middle_parallax');
      $lcText = AWBlock::getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      // From https://stackoverflow.com/questions/36087896/drupal-8-get-the-image-width-height-alt-etc-in-a-twig-or-preprocess-fi/52944485#52944485
      $loImage = $loReferencedImage->field_image[0]->getValue();
      $lnWidth = $loImage['width'];
      $lnHeight = $loImage['height'];

      $lcContent .= "<div class='middle-parallax col-sm-12 views-row row0'>\n";

      $lcStyle = "style='height: $lnHeight" . "px;'";
      $lcContent .= "<div class='parallax-window' data-parallax='scroll' data-image-src='$lcImage' data-natural-width='$lnWidth' data-natural-height='$lnHeight' $lcStyle>";
      $lcContent .= "<div class='parallax-text views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= " </div>\n";

      $lcContent .= " </div>\n";

      // Bottom Section
      $lcContent .= "<div class='bottom-section col-sm-12 views-row row$lnIndex'>\n";

      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_bottom_section');
      $lcText = AWBlock::getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcTitle = HTML::escape(AWBlock::getNodeField($loReferencedImage, 'title'));
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= "</div>\n";

      $lcContent .= "<div class='col-sm-6'>\n";
      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' /></div></div>";
      $lcContent .= "</div>\n";

      $lcContent .= "</div>\n";
    }

    // From https://drupal.stackexchange.com/questions/184963/pass-raw-html-to-markup/243216
    // Normally, I would not like to use raw. However, it is stripping out the style.
    return (array(
        '#type' => 'inline_template',
        '#template' => '{{ generatedcontent|raw }}',
        '#context' => [
            'generatedcontent' => $lcContent
        ]
    ));


  }

  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
