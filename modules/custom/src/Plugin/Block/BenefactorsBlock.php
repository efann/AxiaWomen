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
 *   id = "aw_benefactors_block",
 *   admin_label = @Translation("AW Benefactors Block"),
 *   category = @Translation("Custom block for displaying the Benefactors block.")
 * )
 */
class BenefactorsBlock extends AWBlock
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

    // So far, the below code does not clear caches, and the view will just returns the previous result.
    //   $toViewExecutable->storage->invalidateCaches();
    //      nor
    //   \Drupal::service('cache.render')->invalidateAll()
    // So, I'm having to reset and re-initializing the loViewExecutable variable each time.
    $lcContent = $this->buildChampion($loViewExecutable);

    unset($loViewExecutable);
    $loViewExecutable = Views::getView(self::VIEW_NAME);
    $lcContent .= $this->buildSustainer($loViewExecutable);

    unset($loViewExecutable);
    $loViewExecutable = Views::getView(self::VIEW_NAME);
    $lcContent .= $this->buildDonor($loViewExecutable);

    // From https://drupal.stackexchange.com/questions/199527/how-do-i-correctly-setup-caching-for-my-custom-block-showing-content-depending-o
    return (array(
        '#type' => 'markup',
        '#markup' => $lcContent,
    ));

  }

  //-------------------------------------------------------------------------------------------------
  private function buildChampion($toViewExecutable)
  {
    $lcContent = "";

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
      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'><a href='$lcURLAlias' hreflang='en'>$lcURLTitle</a></span></div>";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";
      $lcContent .= "</div>\n";

      $lcContent .= "</div>\n";
    }

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------
  private function buildSustainer($toViewExecutable)
  {
    $lcContent = "";

    $lcContent .= "<hr>\n";

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
      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'><a href='$lcURLAlias' hreflang='en'>$lcURLTitle</a></span></div>";
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

    return ($lcContent);
  }

  //-------------------------------------------------------------------------------------------------
  private function buildDonor($toViewExecutable)
  {
    $lcContent = "";

    $lcContent .= "<hr>\n";

    $lcTerm = "Donor";
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

      $lcContent .= "<div class='col-sm-3'>\n";
      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'><a href='$lcURLAlias' hreflang='en'>$lcURLTitle</a></span></div>";
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

    return ($lcContent);
  }
  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
