<?php


namespace Drupal\custom\Controller;

use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Drupal\custom\Plugin\Block\AWBlock;
use Drupal\views\Plugin\views\pager\Full;
use Drupal\views\Views;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
class AllWOWController
{
  const NO_DATA = 'Not much data to show here. . . .';
  const VIEW_NAME = 'views_for_custom_programmatically';
  const VIEW_BLOCK_ID = 'block_for_wow_page';

  // The controller method receives these parameters as arguments.
  // The parameters are mapped to the arguments with the same name.
  // So in this case, the page method of the NodeController has one argument: $tcCustomCategory. There may be multiple parameters in a
  // route, but their names should be unique.
//-------------------------------------------------------------------------------------------------
  public function getContent()
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

    $lcPage = Drupal::request()->query->get('page');
    // Convert to integer.
    $lnPage = $lcPage + 0;

    $lnItems = $loViewExecutable->getItemsPerPage();
    $loViewExecutable->setOffset($lnPage * $lnItems);

    $loViewExecutable->execute(Self::VIEW_BLOCK_ID);

    $lcContent .= '<section id="block-awallwowblock" class="block block-custom block-aw-all-wow-block clearfix">' . "\n";

    foreach ($loViewExecutable->result as $lnIndex => $loRow)
    {
      $lcContent .= "<div class='wow-cell col-sm-4 col-xs-6'>\n";

      $loNode = $loRow->_entity;
      $lnID = $loNode->id();

      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcURLTitle = HTML::escape(AWBlock::getNodeField($loNode, 'title'));
      // From https://drupal.stackexchange.com/questions/230746/get-path-alias-from-nid-or-node-object
      $lcURLAlias = Url::fromRoute('entity.node.canonical', ['node' => $lnID])->toString();
      $lcPostDate = AWBlock::getNodeField($loNode, 'field_post_date');
      $lcPostDateFormat = date('l, F j, Y', strtotime($lcPostDate));

      $loReferencedParagraph = AWBlock::getReferencedEntity($loNode, 'field_row_of_image_text');

      $loReferencedImage = AWBlock::getReferencedEntity($loReferencedParagraph, 'field_image_content_id');
      // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
      // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
      // By the way, title has this problem as it's a plain text field with no conversion.
      $lcTitle = HTML::escape(AWBlock::getNodeField($loReferencedImage, 'title'));
      $lcImage = AWBlock::getNodeField($loReferencedImage, 'field_image');

      //$lcContent .= "<div class='col-xs-12'>";
      $lcContent .= "<div>";
      $lcContent .= "<a href='$lcURLAlias' hreflang='en'>";
      $lcContent .= "<div class='col-xs-12'><img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' /></div>";
      $lcContent .= "<div class='col-xs-12 title'>$lcURLTitle</div>";
      $lcContent .= "<div class='col-xs-12 postdate'>$lcPostDateFormat</div>";
      $lcContent .= "</a>\n";
      $lcContent .= "</div>\n";

      $lcContent .= "</div>\n";
    }

    $lcContent .= '</section>' . "\n";

    $loPager = $loViewExecutable->pager;
    if ($loPager instanceof Full)
    {
      $loRenderer = Drupal::service('renderer');
      $lcPagerHTML = "<p>&nbsp;</p>" . $loRenderer->render($loPager->render(array()));

      // From https://drupal.stackexchange.com/questions/199527/how-do-i-correctly-setup-caching-for-my-custom-block-showing-content-depending-o
      return (array(
          '#type' => 'markup',
          '#cache' => array('max-age' => 0),
          '#markup' => $lcContent . $lcPagerHTML,
      ));
    }

    return (array(
        '#type' => 'markup',
        '#markup' => $lcContent,
    ));
  }

  //-------------------------------------------------------------------------------------------------
  public function getTitle()
  {
    $lcValue = "Woman of the Week Listing";

    return (ucwords($lcValue, " "));
  }
  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
