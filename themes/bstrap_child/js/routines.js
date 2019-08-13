var Routines =
  {
    CONTACT_BLOCK: "#contact-message-feedback-form",

    //----------------------------------------------------------------------------------------------------
    initializeRoutines: function ()
    {
      Beo.initializeBrowserFixes();

      // Google Analytics code.
      (function (i, s, o, g, r, a, m)
      {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function ()
        {
          (i[r].q = i[r].q || []).push(arguments);
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
          m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m);
      })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

      ga('create', 'UA-70963631-1', 'auto');
      ga('send', 'pageview');

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
      Beo.setupWatermark(lcForm + " #edit-message-0-value", "Question for Grounded Health");

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
