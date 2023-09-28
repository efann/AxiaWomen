<?php

namespace Drupal\custom\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

abstract class AWBlock extends BlockBase
{

  //-------------------------------------------------------------------------------------------------
  public static function getNodeField($toNode, $tcField)
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

      if ($loField->entity instanceof File)
      {
        $lcPublicValue = $loField->entity->uri->value;
        $lcURL = Drupal::service('stream_wrapper_manager')->getViaUri($lcPublicValue)->getExternalUrl();

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
  public static function getNode($tnNodeID)
  {
    // From https://drupal.stackexchange.com/questions/225209/load-term-by-name
    $loNode = Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($tnNodeID);

    return ($loNode);
  }

  //-------------------------------------------------------------------------------------------------
  public static function getReferencedEntity($toNode, $tcField)
  {
    // From https://drupal.stackexchange.com/questions/186315/how-to-get-instance-of-referenced-entity
    // Geesh. . . .
    $loParagraphItem = $toNode->get($tcField)->first();
    $loEntityReference = $loParagraphItem->get('entity');
    $loEntityAdapter = $loEntityReference->getTarget();
    if ($loEntityAdapter == null)
    {
      return (null);
    }

    $loReferencedEntity = $loEntityAdapter->getValue();
    return ($loReferencedEntity);
  }

  //-------------------------------------------------------------------------------------------------
  public static function getTermID($tcTerm)
  {
    $lnID = -1;

    try
    {// From https://drupal.stackexchange.com/questions/225209/load-term-by-name
      $laTerms = Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['name' => $tcTerm]);
    }
    catch (Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException $e)
    {
      $laTerms = null;
    }
    catch (Drupal\Component\Plugin\Exception\PluginNotFoundException $e)
    {
      $laTerms = null;
    }

    if (!$laTerms)
    {
      return ($lnID);
    }

    // reset() rewinds array's internal pointer to the first element and returns the
    // value of the first array element, or FALSE if the array is empty.
    $lnID = (int)reset($laTerms)->id();

    return ($lnID);
  }

  //-------------------------------------------------------------------------------------------------
  public static function getConvertFromLastFirstName($tcLastFirstName)
  {
    $lnPos = strpos($tcLastFirstName, ",");
    if ($lnPos === false)
    {
      return ($tcLastFirstName);
    }

    $lcFirst = trim(substr($tcLastFirstName, $lnPos + 1));
    $lcLast = trim(substr($tcLastFirstName, 0, $lnPos));
    $lcName = "$lcFirst $lcLast";

    return ($lcName);
  }
  //-------------------------------------------------------------------------------------------------


}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
