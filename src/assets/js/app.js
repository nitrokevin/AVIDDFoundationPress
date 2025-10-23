import $ from 'jquery';
import whatInput from 'what-input';

window.$ = $;

import Foundation from 'foundation-sites';
// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
// import './lib/foundation-explicit-pieces';
import './lib/swiper';

$(document).foundation();
  // Close off-canvas when clicking same-page anchor links
  $('.off-canvas a[href^="/#"]').on('click', function (e) {
    var target = this.hash;

    // Only handle links that point to an anchor on the current page
    if (target && $(target).length) {
      var $offcanvas = $(this).closest('.off-canvas');

      // Close the off-canvas menu
      if ($offcanvas.length) {
        $offcanvas.foundation('close');
      }
    }
  });
