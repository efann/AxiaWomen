<?php

// From http://valuebound.com/resources/blog/drupal-8-how-to-create-a-custom-block-programatically

namespace Drupal\custom\Plugin\Block;

use Drupal\Core\Database\Database;
use Drupal\Core\Modules\Text;
use Drupal\Core\Url;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Provides an 'image slider' block.
 *
 * @Block(
 *   id = "front_page_blog_block",
 *   admin_label = @Translation("Front Page Blog Block"),
 *   category = @Translation("Custom block for displaying the top 3 blogs.")
 * )
 */
class FrontPageBlogBlock extends AWBlock
{
  //-------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function build()
  {

    // I generated a View to get the SQL statement.
    $lcSelect = "SELECT "
        . "node__field_post_date.field_post_date_value AS node__field_post_date_field_post_date_value, node_field_data.nid AS nid "
        . "FROM "
        . "{node_field_data} node_field_data "
        . "LEFT JOIN {node__field_post_date} node__field_post_date ON node_field_data.nid = node__field_post_date.entity_id AND node__field_post_date.deleted = '0' "
        . "WHERE (node_field_data.status = '1') AND (node_field_data.type IN ('blog')) "
        . "ORDER BY node__field_post_date_field_post_date_value DESC "
        . "LIMIT 3 OFFSET 0 ";

    $loConnection = Database::getConnection();
    $loResults = $loConnection->query($lcSelect);

    $lcContent = "<div class='col-sm-12'>\n";
    foreach ($loResults as $lnIndex => $loRow)
    {
      $lcContent .= "<div class='col-sm-4 views-row row$lnIndex'>\n";

      $lnID = $loRow->nid;

      $loNode = $this->getNode($lnID);
      $lcTitle = $this->getNodeField($loNode, 'title');
      // From https://drupal.stackexchange.com/questions/230746/get-path-alias-from-nid-or-node-object
      $lcURLAlias = Url::fromRoute('entity.node.canonical', ['node' => $lnID])->toString();
      $lcPostDate = $this->getNodeField($loNode, 'field_post_date');
      $lcPostDateFormat = date('l, F j, Y', strtotime($lcPostDate));

      $loReferencedParagraph = $this->getReferencedEntity($loNode, 'field_row_of_image_text');
      $lcText = text_summary($this->getNodeField($loReferencedParagraph, 'field_html_text'), null, 750);

      $loReferencedImage = $this->getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      $lcImage = $this->getNodeField($loReferencedImage, 'field_image');

      $lcContent .= "<div class='views-field views-field-title'><span class='field-content'><a href='$lcURLAlias' hreflang='en'>$lcTitle</a></span></div>";
      $lcContent .= "<div class='views-field views-field-field-post-date'><div class='field-content'>$lcPostDateFormat</div></div>";

      $lcContent .= "<div class='views-field views-field-field_image'><div class='field-content'><img src='$lcImage' /></div></div>";
      $lcContent .= "<div class='views-field views-field-field_html_text'><div class='field-content'>$lcText</div></div>";

      $lcContent .= "<div class='field-content'><a class='views-more-link ui-button ui-corner-all ui-widget' href='$lcURLAlias' hreflang='en'>More</a></div>";

//      $lcContent .= " < div>\n";
//      $lcContent .= " < img src = '$lcImage' alt = '$lcTitle' title = '$lcTitle' />\n";
//      $lcContent .= " </div > \n";

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
