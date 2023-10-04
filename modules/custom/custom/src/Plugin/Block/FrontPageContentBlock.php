<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\custom\Service\CustomFunctions;
use Drupal\views\Views;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Provides an 'all blogs' block.
 *
 * @Block(
 *   id = "aw_front_page_main_block",
 *   admin_label = @Translation("AW Front Page Main Block"),
 *   category = @Translation("Custom block for displaying content derived from Front Page content.")
 * )
 */
class FrontPageContentBlock extends AWBlock
{
  const NO_DATA = 'Not much data to show here. . . .';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_front_page_main';

  //-------------------------------------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function build(): array
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

    $loViewExecutable->execute(self::VIEW_BLOCK_ID);
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      $loNode = $loRow->_entity;

      //**********************************
      // Top Parallax
      //**********************************
      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_top_parallax');
      $lcText = AWBlock::getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      // From https://stackoverflow.com/questions/36087896/drupal-8-get-the-image-width-height-alt-etc-in-a-twig-or-preprocess-fi/52944485#52944485
      $loImage = $loReferencedImage->field_image[0]->getValue();
      $lnWidth = $loImage['width'];
      $lnHeight = $loImage['height'];

      $lcContent .= "<div class='top-parallax col-sm-12'>\n";

      $lcStyle = "style='height: $lnHeight" . "px;'";
      $lcContent .= "<div class='parallax-window' data-parallax='scroll' data-image-src='$lcImage' data-natural-width='$lnWidth' data-natural-height='$lnHeight' $lcStyle>";
      $lcContent .= "<div class='parallax-text views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= " </div>\n";

      $lcContent .= " </div>\n";

      //**********************************
      // WOW Summary
      //**********************************
      $loCustom = new CustomFunctions();
      $lcWOWUrl = $loCustom->getCurrentWOWLink(false);
      $lcWOWTitle = $loCustom->getCurrentWOWTitle();
      $lcWOWUrlTitle = 'Link to current Woman of the Week';

      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_wow_summary');
      $lcText = AWBlock::getNodeField($loReferencedParagraph, 'field_html_text');
      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcTitle = HTML::escape(AWBlock::getNodeField($loReferencedImage, 'title'));
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='wow-summary col-sm-12'>\n";
      //-
      /*
        Force the cell of the title to match the cell of the image.
      */
      $lcContent .= "<div class='col-sm-12'><div class='col-sm-5 title'><a href='$lcWOWUrl' title='$lcWOWUrlTitle'>WOMAN OF THE WEEK</a></div></div>\n";
      //-
      $lcContent .= "<div class='col-sm-12 flexdisplay'>\n";
      //-
      $lcContent .= "<div class='col-sm-5'>\n";
      $lcContent .= "<div class='views-field views-field-field_image'><a href='$lcWOWUrl' hreflang='en'><img src='$lcImage' alt='$lcTitle' title='$lcTitle'></a></div>";
      $lcContent .= "</div>\n";
      //-
      $lcContent .= "<div class='col-sm-7'>\n";
      $lcContent .= "<div class='views-field-field_html_text'>$lcText</div>\n";
      $lcContent .= "<div class='field-append-title'><h3>$lcWOWTitle</h3></div>\n";
      $lcContent .= "<div class='field-append-button'><a class='btn-primary btn' href='$lcWOWUrl' title='$lcWOWUrlTitle'>Read full story...</a></div>\n";
      $lcContent .= "</div>\n";
      //-
      $lcContent .= "</div>\n";
      //-
      $lcContent .= "</div>\n";
      //**********************************
      // Benenfactors Carousel block
      //**********************************
      // From https://drupal.stackexchange.com/questions/171686/how-can-i-programmatically-display-a-block/171733#171733
      // It's a plugin block.
      // By the way, the id is defined in modules/custom/src/Plugin/Block/BenefactorsCarouselBlock.php
      // up at the top.
      $lcBlockID = 'aw_benefactors_carousel_block';
      $loBlockManager = \Drupal::service('plugin.manager.block');
      $laConfig = [];
      $loPluginBlock = $loBlockManager->createInstance($lcBlockID, $laConfig);
      $lcPluginBlock = \Drupal::service('renderer')->render($loPluginBlock->build());

      $lcContent .= "<div class='benefactors-carousel col-sm-12'>\n";
      $lcContent .= $lcPluginBlock;
      $lcContent .= "</div>\n";

      //**********************************
      // Bottom Section
      //**********************************
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
