var Routines =
  {
    CONTACT_BLOCK: "#contact-message-feedback-form",
    PARALLAX_CLASS: ".parallax-window",

    //----------------------------------------------------------------------------------------------------
    initializeRoutines: function ()
    {
      Beo.initializeBrowserFixes();

      // I no longer paste the Google Analytics here as I'm tired of tracking whatever changes
      // Google adds. Plus, their code does not format correctly when I auto-format javascript.
      // So now I'm just using the Drupal Module, Google Analytics:
      //   https://www.drupal.org/project/google_analytics
    },

    //----------------------------------------------------------------------------------------------------
    // Only change the default behaviour of the logo if NOT on the front page.
    // If not on the front page, then change the href to the home page.
    // Otherwise, leave alone in order to work with Lightbox.
    setupLogo: function ()
    {
      if (jQuery('body.path-frontpage').length == 0)
      {
        let loLink = jQuery('#block-header a');
        loLink.removeAttr('data-lightbox');
        loLink.removeAttr('data-alt');
        loLink.removeAttr('data-title');

        loLink.attr('href', '/');
      }

    },

    //----------------------------------------------------------------------------------------------------
    setupWatermarks: function ()
    {
      var lcForm = Routines.CONTACT_BLOCK;
      if (jQuery(lcForm).length == 0)
      {
        return;
      }

      Beo.setupWatermark(lcForm + " #edit-name", "Your Name");
      Beo.setupWatermark(lcForm + " #edit-mail", "Your@E-mail.com");
      Beo.setupWatermark(lcForm + " #edit-subject-0-value", "Subject of Question");
      Beo.setupWatermark(lcForm + " #edit-message-0-value", "Question for Axia Women");

    },

    //----------------------------------------------------------------------------------------------------
    setupAdditionalButtons: function (tcSelect)
    {
      jQuery(tcSelect).button();
    },
    //----------------------------------------------------------------------------------------------------
    // When an image is clicked, using jQuery dialog, the picture is displayed like
    // that of FancyBox.
    //
    // Unfortunately, at the moment, I don't have a way to determine the title bar height
    // before it displays. So I use Chrome Inspect when viewing a dialog box to determine
    // the height.
    // From https://stackoverflow.com/questions/8998612/how-to-pass-the-value-undefined-to-a-function-with-multiple-parameters
    // By the way, to pass undefined as a parameter, use void 0
    setupLightbox: function ()
    {
      let llCheckClass = true;
      let lcMainContent = "div.main-container";
      let lcImageClass = "responsive-image-large"

      // Unfortunately, I can't get the title in the template of field.html.twig.
      // to override the image output.
      let lcPageTitle = jQuery(document).attr('title').split('|')[0].trim();

      jQuery(lcMainContent + " img").each(function ()
      {
        let loImage = jQuery(this);
        if (!loImage.attr('alt'))
        {
          loImage.attr('alt', lcPageTitle);
        }

        if (!loImage.attr('title'))
        {
          loImage.attr('title', lcPageTitle);
        }

        loImage.removeAttr('width');
        loImage.removeAttr('height');
        loImage.removeAttr('style');

        if (llCheckClass)
        {
          let lcClasses = loImage.attr('class');

          if ((typeof lcClasses === 'undefined') || (lcClasses.indexOf('responsive-image') < 0))
          {
            loImage.addClass(lcImageClass);
          }
        }

        if (!loImage.parent().is('a'))
        {
          let lcSource = loImage.attr('src');
          // From https://stackoverflow.com/questions/610406/javascript-equivalent-to-printf-string-format
          loImage.wrap(`<a href="${lcSource}" data-lightbox="${lcPageTitle}" data-alt="${lcPageTitle}" data-title="${lcPageTitle}"></a>`);
        }
      });

    },

    //----------------------------------------------------------------------------------------------------
    showAJAX: function (tlShow)
    {
      var lcAJAX = "#ajax-loading";
      var loAJAX = jQuery(lcAJAX);
      if (loAJAX.length == 0)
      {
        alert("The HTML element " + lcAJAX + " does not exist!");
        return;
      }

      if (tlShow)
      {
        loAJAX.show();
      }
      else
      {
        loAJAX.hide();
      }

    }
    //----------------------------------------------------------------------------------------------------
  };
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
