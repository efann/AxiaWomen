<?php
namespace Drupal\custom\TwigExtension;

use Drupal\Component\Utility\Html;
use Drupal\node\Entity\Node;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

/**
 * Copied from Twig Tweak
 */
class CustomTwigExtension extends \Twig_Extension
{

  //-------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getFunctions()
  {
    return [
        new \Twig_SimpleFunction('custom_field_image', [$this, 'customFieldImage']),
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
    $loEntity = \Drupal::entityTypeManager()->getStorage('node')->load($tnID);

    // If you don't convert to the appropriate HTML codes, then if you have an apostrophe,
    // then wrong title, Credits, will appear instead 'cause Drupal corrects HTML mistakes.
    // By the way, title has this problem as it's a plain text field with no conversion.
    $lcTitle = HTML::escape($this->getNodeField($loEntity, 'title'));
    $lcImage = $this->getNodeField($loEntity, $tcFieldName);

    return ("<img src='$lcImage' aria-label='$lcTitle' alt='$lcTitle' title='$lcTitle' />");
  }

  //-------------------------------------------------------------------------------------------------
  private function getNodeField($toNode, $tcField)
  {
    // And yes, you want to use ==
    if ($toNode == null)
    {
      return ("Node does not exist for $tcField. A linked / used image was probably deleted.");
    }

    $lcValue = '';
    if ($toNode->hasField($tcField))
    {
      $loField = $toNode->get($tcField);

      if ($loField->entity instanceof \Drupal\file\Entity\File)
      {
        $lcPublicValue = $loField->entity->uri->value;
        $lcURL = \Drupal::service('stream_wrapper_manager')->getViaUri($lcPublicValue)->getExternalUrl();

        $laURL = parse_url($lcURL);
        $lcValue = $laURL['path'];
      }
      else
      {
        $lcValue = $loField->value;
      }
    }

    return ($lcValue);
  }

  //-------------------------------------------------------------------------------------------------

}
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

