<?php

namespace Drupal\custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

abstract class AWBlock extends BlockBase
{

  //-------------------------------------------------------------------------------------------------
  protected function getNodeField($toNode, $tcField)
  {
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
  protected function getNode($tnNodeID)
  {
    // From https://drupal.stackexchange.com/questions/225209/load-term-by-name
    $loNode = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($tnNodeID);

    return ($loNode);
  }

  //-------------------------------------------------------------------------------------------------
  protected function getReferencedEntity($toNode, $tcField)
  {
    // From https://drupal.stackexchange.com/questions/186315/how-to-get-instance-of-referenced-entity
    // Geesh. . . .
    $loParagraphItem = $toNode->get($tcField)->first();
    $loEntityReference = $loParagraphItem->get('entity');
    $loEntityAdapter = $loEntityReference->getTarget();
    $loReferencedEntity = $loEntityAdapter->getValue();

    return ($loReferencedEntity);
  }
  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
