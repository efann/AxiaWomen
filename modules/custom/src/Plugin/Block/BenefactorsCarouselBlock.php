<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Modules\Text;
use Drupal\Core\Url;
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

  private $fnTrack;
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

    $this->fnTrack = 0;

    $lcContent = "";

    $lcContent .= "<div class='container'>\n";
    $lcContent .= "<div class='row'>\n";

    $lcContent .= "<div class='col-sm-3'>\n";
    $lcContent .= "</div>\n";

    $lcContent .= "<div class='col-sm-6'>\n";

    $lcBlockID = self::BLOCK_SLIDE_ID;
    $lcContent .= "<div id='$lcBlockID' class='carousel slide' data-ride='carousel' data-interval='4000'>\n";

    $lcContent .= "<div class='carousel-inner' role='listbox'>\n";
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

    $lcContent .= $this->getControls();

    $lcContent .= "</div>\n";
    $lcContent .= "</div>\n";

    $lcContent .= "<div class='col-sm-3'>\n";
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
      $lnID = $loNode->id();

      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcURLTitle = HTML::escape(AWBlock::getNodeField($loNode, 'title'));
      // From https://drupal.stackexchange.com/questions/230746/get-path-alias-from-nid-or-node-object
      $lcURLAlias = Url::fromRoute('entity.node.canonical', ['node' => $lnID])->toString();

      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_benefactor_image_text');

      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      $lcClass = ($this->fnTrack == 0) ? "item active" : "item";

      $lcContent .= "<div class='$lcClass'>\n";
      $lcContent .= "<img src = '$lcImage'>\n";
      $lcContent .= "<div class='carousel-caption'>\n";
      $lcContent .= "<a href='$lcURLAlias' hreflang='en'>$lcURLTitle</a>";
      $lcContent .= "</div>\n";
      $lcContent .= "</div>\n";

      // This way, $this->fnTrack will equal the actual count.
      $this->fnTrack++;
    }

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------
  private function getControls()
  {
    $lcBlockID = self::BLOCK_SLIDE_ID;

    $lcContent = <<<EOD
    <!-- Controls -->
    <a class="left carousel-control" href="#$lcBlockID" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#$lcBlockID" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
EOD;

    return ($lcContent);
  }
  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
