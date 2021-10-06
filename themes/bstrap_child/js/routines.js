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
      let lcForm = Routines.CONTACT_BLOCK;
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
    // Setup the carousel
    setupSlick: function (tcSelect)
    {
      let loSlick = jQuery(tcSelect);

      if (loSlick.length == 0)
      {
        return;
      }

      loSlick.slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
          {
            // Matches Bootstrap grid
            // Means less than 768.
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
            }
          },
          // You can unslick at a given breakpoint now by adding:
          // settings: "unslick"
          // instead of a settings object
        ]
      });
    },

    //----------------------------------------------------------------------------------------------------
    // Now ensuring that external links display in a separate tab.
    updateExternalURLs: function (tcSelect)
    {
      jQuery(tcSelect).find('a').each(function ()
      {
        let loThis = jQuery(this);
        let lcHref = loThis.attr('href');
        let lcHostname = window.location.hostname;

        if (Boolean(lcHref) && (!lcHref.startsWith("/")) && (!lcHref.includes(lcHostname)))
        {
          loThis.attr('target', '_blank');
        }

      });

      jQuery(tcSelect).find('form').each(function ()
      {
        let loThis = jQuery(this);
        let lcAction = loThis.attr('action');
        let lcHostname = window.location.hostname;

        if (Boolean(lcAction) && (!lcAction.startsWith("/")) && (!lcAction.includes(lcHostname)))
        {
          loThis.attr('target', '_blank');
        }

      });

    },

    //----------------------------------------------------------------------------------------------------
    showAJAX: function (tlShow)
    {
      let lcAJAX = "#ajax-loading";
      let loAJAX = jQuery(lcAJAX);
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
