<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

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


    $lcContent ="";

    $lcContent .= "<div class='container'>\n";
    $lcContent .= "<div id='carousel-example-generic' class='carousel slide' data-ride='carousel'>\n";
    $lcContent .= "<div class='carousel-inner' role='listbox'>\n";


      // So far, the below code does not clear caches, and the view will just returns the previous result.
    //   $toViewExecutable->storage->invalidateCaches();
    //      nor
    //   \Drupal::service('cache.render')->invalidateAll()
    // So, I'm having to reset and re-initializing the loViewExecutable variable each time.
    /*
     $lcContent = $this->buildChampion($loViewExecutable);

        unset($loViewExecutable);
        $loViewExecutable = Views::getView(self::VIEW_NAME);
        $lcContent .= $this->buildSustainer($loViewExecutable);
    */

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
  private function buildTest()
  {
    $lcContent = <<<EOD
    <h1>
  Bootstrap v3.4.1 Carousel<br>
  <small>dark indicators on light background</small>
</h1>

<div class="container">
  <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
      <li data-target="#carousel-example-generic" data-slide-to="1"></li>
      <li data-target="#carousel-example-generic" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <div class="item active">
        <img src="https://dummyimage.com/920x480/eeeeee/aaaaaa&text=Example%20light%20background%20image">
      </div>

      <div class="item">
        <img src="https://dummyimage.com/920x480/eeeeee/aaaaaa&text=Example%20light%20background%20image%20with%20caption">
        <div class="carousel-caption">
          <h4>Bootstrap caption example</h4>
          <p>Bootstrap 3.4.1 is not so old, but you can use Bootstrap 4 already.</p>
        </div>
      </div>

      <div class="item">
        <img src="https://dummyimage.com/920x480/eeeeee/aaaaaa&text=Another%20example%20without%20caption">
      </div>
    </div>

    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  
  </div>
</div>
EOD;

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------
  private function getControls()
  {
    $lcContent = <<<EOD
    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
EOD;

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------
  private function buildChampion($toViewExecutable)
  {
    $lcContent = "";

    $lcContent .= "<div class='champion'>\n";

    $lcTerm = "Champion";
    $lcURL = strtolower("/taxonomy/$lcTerm");
    $lcContent .= "<div class='row category'>\n";
    $lcContent .= "<a href='$lcURL'>$lcTerm</a>\n";
    $lcContent .= "</div>\n";

    $lnTermID = AWBlock::getTermID($lcTerm);
    $laArgs = [$lnTermID];

    $toViewExecutable->setArguments($laArgs);
    $toViewExecutable->execute(Self::VIEW_BLOCK_ID);

    foreach ($toViewExecutable->result as $lnIndex => $loRow)
    {
      $lcContent .= "<div class='row row$lnIndex'>\n";
      $loNode = $loRow->_entity;
      $lnID = $loNode->id();

      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcURLTitle = HTML::escape(AWBlock::getNodeField($loNode, 'title'));
      // From https://drupal.stackexchange.com/questions/230746/get-path-alias-from-nid-or-node-object
      $lcURLAlias = Url::fromRoute('entity.node.canonical', ['node' => $lnID])->toString();

      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_benefactor_image_text');
      // Format must be an existing text formatter.
      $lcText = text_summary(AWBlock::getNodeField($loReferencedParagraph, 'field_html_text'), 'full_html', 750);

      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcTitle = HTML::escape(AWBlock::getNodeField($loReferencedImage, 'title'));
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='col-sm-4'>\n";
      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' /></div></div>";
      $lcContent .= "</div>\n";

      $lcContent .= "<div class='col-sm-8'>\n";
      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'>$lcURLTitle</span></div>";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= "</div>\n";

      $lcContent .= "</div>\n";
    }

    $lcContent .= "</div>\n";

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------
  private function buildSustainer($toViewExecutable)
  {
    $lcContent = "";

    $lcContent .= "<hr>\n";

    $lcContent .= "<div class='sustainer'>\n";

    $lcTerm = "Sustainer";
    $lcURL = strtolower("/taxonomy/$lcTerm");
    $lcContent .= "<div class='row category'>\n";
    $lcContent .= "<a href='$lcURL'>$lcTerm</a>\n";
    $lcContent .= "</div>\n";

    $lnTermID = AWBlock::getTermID($lcTerm);
    $laArgs = [$lnTermID];

    $toViewExecutable->setArguments($laArgs);
    $toViewExecutable->execute(Self::VIEW_BLOCK_ID);

    $lnTrack = 0;
    $lnMultiple = 4;
    foreach ($toViewExecutable->result as $lnIndex => $loRow)
    {
      if (($lnIndex % $lnMultiple) == 0)
      {
        $lnRow = $lnIndex / $lnMultiple;
        $lcContent .= "<div class='row views-row row$lnRow'>\n";
        $lnTrack = 0;
      }

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
      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcTitle = HTML::escape(AWBlock::getNodeField($loReferencedImage, 'title'));
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='col-sm-3'>\n";
      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' /></div></div>";
      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'>$lcURLTitle</span></div>";
      $lcContent .= "</div>\n";

      if (++$lnTrack >= $lnMultiple)
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

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
