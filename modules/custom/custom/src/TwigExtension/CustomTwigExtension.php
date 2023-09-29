<?php

namespace Drupal\custom\TwigExtension;

use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\custom\Plugin\Block\AWBlock;
use Twig\TwigFunction;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Copied from Twig Tweak
 */
class CustomTwigExtension extends Drupal\twig_tweak\TwigTweakExtension
{

  //-------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array
  {
    return [
        new TwigFunction('custom_field_image', [$this, 'customFieldImage']),
    ];
  }

  //-------------------------------------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getName(): string
  {
    return 'custom';
  }

  //-------------------------------------------------------------------------------------------------
  public function customFieldImage($tnID, $tcFieldName): string
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

