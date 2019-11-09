<?php

namespace Drupal\custom\Controller;

use Symfony\Component\HttpFoundation\Response;

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
class AdminController
{
  // The controller method receives these parameters as arguments.
  // The parameters are mapped to the arguments with the same name.
  // So in this case, the page method of the NodeController has one argument: $tcCustomCategory. There may be multiple parameters in a
  // route, but their names should be unique.
  //-------------------------------------------------------------------------------------------------
  public function runTask($tcTask)
  {
    $lcContent = '<h2 style="text-align: center">Admin task has been requested.</h2>';

    if ($tcTask === 'ClearContentCache')
    {
      $lcContent .= '<p style="text-align: center">' . \Drupal::service('custom.service')->clearContentCaches() . '</p>';
    }

    /*
    /*
       Awesome!!!!
       From https://drupal.stackexchange.com/questions/182022/how-to-output-from-custom-module-without-rest-of-theme

    $loResponse = new Response();
    // From https://symfony.com/doc/2.1/components/http_foundation/introduction.html
    $loResponse->headers->set('Content-Type', 'text/html; charset=utf-8');
    $loResponse->setContent($lcContent);

    return ($loResponse);
*/
    $loBuild = [
        '#markup' => t($lcContent),
    ];

    return ($loBuild);
  }

  //-------------------------------------------------------------------------------------------------
  private function generateNodeContent($tnNodeID)
  {
    $loNode = $this->getNode($tnNodeID);
    $lcBody = '';

    $lcBody .= "<div class='presentation_title'>" . $loNode->get('title')->value . "</div>\n";

    $lcBody .= "<div class='flexslider'>\n";
    $lcBody .= "<ul class='slides'>\n";

    $lnCount = sizeof($loNode->get('field_presentation_slide'));
    for ($i = 0; $i < $lnCount; ++$i)
    {
      $lcBody .= "<li>\n";
      $lcBody .= $loNode->get('field_presentation_slide')[$i]->value;
      $lcBody .= "</li>\n";
    }

    $lcBody .= "</ul>\n";
    $lcBody .= "</div>\n";

    return ($lcBody);
  }

  //-------------------------------------------------------------------------------------------------

  private function getNode($tnNodeID)
  {
    // From https://drupal.stackexchange.com/questions/225209/load-term-by-name
    $loNode = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($tnNodeID);

    return ($loNode);
  }

  //-------------------------------------------------------------------------------------------------

}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------


