<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Database\Database;
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
 *   id = "aw_front_page_blog_block",
 *   admin_label = @Translation("AW Front Page Blog Block"),
 *   category = @Translation("Custom block for displaying the top 3 blogs.")
 * )
 */
class FrontPageBlogBlock extends AWBlock
{
  const NO_DATA = 'Not much data to show here. . . .';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_front_page_blogs';

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

    $lcContent = "<div class='col-sm-12'>\n";

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);
    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      $lcContent .= "<div class='col-sm-4 views-row row$lnIndex'>\n";

      $loNode = $loRow->_entity;
      $lnID = $loNode->id();

      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcURLTitle = HTML::escape($this->getNodeField($loNode, 'title'));
      // From https://drupal.stackexchange.com/questions/230746/get-path-alias-from-nid-or-node-object
      $lcURLAlias = Url::fromRoute('entity.node.canonical', ['node' => $lnID])->toString();
      $lcPostDate = $this->getNodeField($loNode, 'field_post_date');
      $lcPostDateFormat = date('l, F j, Y', strtotime($lcPostDate));

      $loReferencedParagraph = $this->getReferencedEntity($loNode, 'field_row_of_image_text');
      $lcText = text_summary($this->getNodeField($loReferencedParagraph, 'field_html_text'), null, 750);

      $loReferencedImage = $this->getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcTitle = HTML::escape($this->getNodeField($loReferencedImage, 'title'));
      $lcImage = $this->getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'><a href='$lcURLAlias' hreflang='en'>$lcURLTitle</a></span></div>";
      $lcContent .= "<div class='views-field views-field-field-post-date'><div class='field-content'>$lcPostDateFormat</div></div>";

      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' /></div></div>";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";

      $lcContent .= "<div class='views-field views-field-more-button'><div class='field-content'><a class='views-more-link ui-button ui-corner-all ui-widget' href='$lcURLAlias' hreflang='en'>More</a></div></div>";

      $lcContent .= "</div>\n";
    }

    $lcContent .= "</div>\n";

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
