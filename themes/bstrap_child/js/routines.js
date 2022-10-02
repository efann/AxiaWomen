var Routines =
  {
    CONTACT_BLOCK: '#contact-message-feedback-form',
    PARALLAX_CLASS: '.parallax-window',

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

      Beo.setupWatermark(lcForm + ' #edit-name', 'Your Name');
      Beo.setupWatermark(lcForm + ' #edit-mail', 'Your@E-mail.com');
      Beo.setupWatermark(lcForm + ' #edit-subject-0-value', 'Subject of Question');
      Beo.setupWatermark(lcForm + ' #edit-message-0-value', 'Question for Axia Women');

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
    duplicateWOWListingButton: function ()
    {
      let loButton = jQuery('#block-wowlisting a.btn');
      if (loButton.length == 0)
      {
        return;
      }

      let lcCloneID = 'block-wowlisting-clone';
      let loClone = loButton.clone();
      loClone.insertBefore('h1.page-header').wrap(`<div id='${lcCloneID}' class='col-sm-12'></div>`);
    },

    //----------------------------------------------------------------------------------------------------
    setupWOWWrapper: function ()
    {
      let loBlock = jQuery('#block-awallwowblock');
      if (loBlock.length == 0)
      {
        return;
      }

      loBlock.find('img').css('display', 'inline-block');

      Routines.generateWOWWrapper(loBlock);

      jQuery(window).resize(function ()
      {
        Routines.generateWOWWrapper(loBlock);
      });

    },
    //----------------------------------------------------------------------------------------------------
    generateWOWWrapper: function (toBlock)
    {
      let loDivs = toBlock.find('div.wow-cell');
      // From https://errorsandanswers.com/css-media-queries-and-jquery-window-width-do-not-match/
      let lnColumns = (window.innerWidth) >= 768 ? 3 : 2;
      let lcColumns = `count${lnColumns}`;

      if (toBlock.find(`.row.${lcColumns}`).length > 0)
      {
        return;
      }

      loDivs.each(function ()
      {
        let loThis = jQuery(this);
        if (loThis.parent().is('div.row'))
        {
          // Remove the parent.
          loThis.unwrap();
        }
      });

      for (let i = 0; i < loDivs.length; i += lnColumns)
      {
        loDivs.slice(i, i + lnColumns).wrapAll(`<div class='row ${lcColumns}'></div>`);
      }

    },

    //----------------------------------------------------------------------------------------------------
    showAJAX: function (tlShow)
    {
      let lcAJAX = '#ajax-loading';
      let loAJAX = jQuery(lcAJAX);
      if (loAJAX.length == 0)
      {
        alert('The HTML element ' + lcAJAX + ' does not exist!');
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
