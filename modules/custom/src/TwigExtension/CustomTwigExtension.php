<?php

namespace Drupal\custom\TwigExtension;

use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\custom\Plugin\Block\AWBlock;
use Twig_Extension;
use Twig_SimpleFunction;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Copied from Twig Tweak
 */
class CustomTwigExtension extends Twig_Extension
{

  //-------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getFunctions()
  {
    return [
        new Twig_SimpleFunction('custom_field_image', [$this, 'customFieldImage']),
    ];
  }

  //-------------------------------------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'custom';
  }

  //-------------------------------------------------------------------------------------------------
  public function customFieldImage($tnID, $tcFieldName)
  {
    $loEntity = Drupal::entityTypeManager()->getStorage('node')->load($tnID);

    // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
    // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
    // By the way, title has this problem as it's a plain text field with no conversion.
    $lcTitle = HTML::escape(AWBlock::getNodeField($loEntity, 'title'));
    $lcImage = AWBlock::getNodeField($loEntity, $tcFieldName);

    return ("<img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' />");
  }

  //-------------------------------------------------------------------------------------------------

}
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

