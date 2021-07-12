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

    $lcContent = "";

    $lcTerm = "Sustainer";
    $lcContent .= "<div class='row'>\n";
    $lcContent .= "<a href='/taxonomy/sustainer'>$lcTerm</a>\n";
    $lcContent .= "</div>\n";

    $lnTermID = AWBlock::getTermID($lcTerm);
    $laArgs = [$lnTermID];

    $loViewExecutable->setArguments($laArgs);

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
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


    $lcTerm = "Supporter";
    $lcContent .= "<div class='row'>\n";
    $lcContent .= "<a href='/taxonomy/supporter'>$lcTerm</a>\n";
    $lcContent .= "</div>\n";

    $lnTermID = AWBlock::getTermID($lcTerm);
    $laArgs = [$lnTermID];

    $loViewExecutable->setArguments($laArgs);
    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    $lnTrack = 0;
    $lnMultiple = 4;
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
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
      // Format must be an existing text formatter.
      $lcText = text_summary(AWBlock::getNodeField($loReferencedParagraph, 'field_html_text'), 'full_html', 750);

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
