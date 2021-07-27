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
 * Provides an 'image slider' block.
 *
 * @Block(
 *   id = "aw_benefactors_carousel_block",
 *   admin_label = @Translation("AW Benefactors Carousel Block"),
 *   category = @Translation("Custom block for displaying the Benefactors Carousel block.")
 * )
 */
class BenefactorsCarouselBlock extends AWBlock
{
  const NO_DATA = 'Not much data to show here. . . .';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_benefactors_page';

  const BLOCK_SLIDE_ID = 'block-aw-benefactors-carousel-block';
  const CAROUSEL_ID = 'benefactors-carousel';

  //-------------------------------------------------------------------------------------------------
  // I got examples for using the Bootstrap Carousel from the following links:
  //   https://getbootstrap.com/docs/4.0/components/carousel/
  // From https://codepen.io/thiagobraga/pen/xevGJJ

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

    $lcBlockID = self::BLOCK_SLIDE_ID;
    $lcContent .= "<div id='$lcBlockID'>\n";
    $lcContent .= "<div class='row'>\n";

    $lcContent .= "<h3>Our Benefactors</h3>\n";

    // If you don't use these outside columns, then
    // the previous & next buttons will be off the screen.
    $lcContent .= "<div class='col-xs-1'>\n";
    $lcContent .= "</div>\n";

    $lcContent .= "<div class='col-xs-10'>\n";
    $lcCarouselID = self::CAROUSEL_ID;
    $lcContent .= "<div id='$lcCarouselID'>\n";
    // So far, the below code does not clear caches, and the view will just returns the previous result.
    //   $toViewExecutable->storage->invalidateCaches();
    //      nor
    //   \Drupal::service('cache.render')->invalidateAll()
    // So, I'm having to reset and re-initializing the loViewExecutable variable each time.
    $lcContent .= $this->buildViewList($loViewExecutable, "Champion");
    unset($loViewExecutable);
    $loViewExecutable = Views::getView(self::VIEW_NAME);
    $lcContent .= $this->buildViewList($loViewExecutable, "Sustainer");
    $lcContent .= "</div>\n";
    $lcContent .= "</div>\n";

    $lcContent .= "<div class='col-xs-1'>\n";
    $lcContent .= "</div>\n";

    $lcContent .= "</div>\n";
    $lcContent .= "</div>\n";

    // From https://drupal.stackexchange.com/questions/199527/how-do-i-correctly-setup-caching-for-my-custom-block-showing-content-depending-o
    return (array(
        '#type' => 'markup',
        '#markup' => $lcContent,
    ));

  }

  //-------------------------------------------------------------------------------------------------
  private function buildViewList($toViewExecutable, $lcTerm)
  {
    $lcContent = "";

    $lnTermID = AWBlock::getTermID($lcTerm);
    $laArgs = [$lnTermID];

    $toViewExecutable->setArguments($laArgs);
    $toViewExecutable->execute(Self::VIEW_BLOCK_ID);

    foreach ($toViewExecutable->result as $lnIndex => $loRow)
    {
      $loNode = $loRow->_entity;

      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcURLTitle = HTML::escape(AWBlock::getNodeField($loNode, 'title'));
      $lcURLTitle = AWBlock::getConvertFromLastFirstName($lcURLTitle);
      // From https://drupal.stackexchange.com/questions/230746/get-path-alias-from-nid-or-node-object
      // However, rather than use
      //   $lnID = $loNode->id();
      //   $lcURLAlias = Url::fromRoute('entity.node.canonical', ['node' => $lnID])->toString();
      // we want the links to take the user to the benefactors page.
      $lcURLAlias = '/view/all-benefactors';

      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_benefactor_image_text');

      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div>\n";

      $lcContent .= "<a href='$lcURLAlias' hreflang='en'>\n";
      $lcContent .= "<img src = '$lcImage' alt='$lcURLTitle' title='$lcURLTitle'>\n";
      $lcContent .= "</a>\n";

      $lcContent .= "<div class='slick-caption'>\n";
      $lcContent .= "<a href='$lcURLAlias' hreflang='en'>$lcURLTitle</a>\n";
      $lcContent .= "</div>\n";

      $lcContent .= "</div>\n";
    }

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
