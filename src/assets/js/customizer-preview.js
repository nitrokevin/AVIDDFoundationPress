/**
 * Customizer Preview JavaScript
 * Handles live preview updates for theme customizer settings.
 *
 * Background settings (nav bg, footer bg, page bg) may store either a hex
 * value or a gradient slug. aviddResolveSlug() resolves the stored value to
 * a CSS-ready string using the gradient map injected via wp_localize_script.
 *
 * Text/colour settings always store a plain hex value and need no resolution.
 */
(function ($) {
  "use strict";

  if (typeof wp === "undefined" || typeof wp.customize === "undefined") {
    return;
  }

  /**
   * Resolve a stored theme_mod value to a CSS value.
   *
   * - Hex values (#rrggbb / #rgb) are returned as-is.
   * - Gradient slugs are looked up in aviddGradients (injected by PHP).
   * - Unresolvable values are returned as-is (safe fallback).
   *
   * @param  {string} value  Stored theme_mod value — hex or gradient slug.
   * @return {string}        CSS-ready value.
   */
  function aviddResolveSlug(value) {
    if (!value) return "";

    // Already a hex colour — no resolution needed
    if (/^#([a-f0-9]{3}){1,2}$/i.test(value)) {
      return value;
    }

    // Look up gradient slug in the PHP-injected map
    if (typeof aviddGradients !== "undefined" && aviddGradients[value]) {
      return aviddGradients[value];
    }

    // Return as-is if unresolvable
    return value;
  }

  /**
   * Apply a background value to a jQuery-selected element.
   * Chooses background-color for hex, background for gradients.
   *
   * @param {jQuery} $el    Target element(s).
   * @param {string} value  Resolved CSS value.
   */
function aviddApplyBackground($el, value) {
  if (!value) return;

  if (value.indexOf("gradient(") !== -1) {
    $el.css("background-color", "");
    $el.css("background-image", value);
  } else {
    $el.css("background-image", "");
    $el.css("background-color", value);
  }
}

  // ----------------------------------------
  // Nav background — hex or gradient slug
  // ----------------------------------------
  wp.customize("color_palette_setting_0", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground(
        $(".top-bar, .title-bar"),
        aviddResolveSlug(newval),
      );
    });
  });

  // ----------------------------------------
  // Nav menu item colour — hex only
  // ----------------------------------------
  wp.customize("color_palette_setting_1", function (value) {
    value.bind(function (newval) {
      $(
        ".top-bar, .top-bar .desktop-menu a:not(.button), .title-bar .mobile-menu a:not(.button)",
      ).css("color", newval);
    });
  });

  // ----------------------------------------
  // Footer background — hex or gradient slug
  // ----------------------------------------
  wp.customize("color_palette_setting_3", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground($(".footer"), aviddResolveSlug(newval));
    });
  });

  // ----------------------------------------
  // Footer text colour — hex only
  // ----------------------------------------
  wp.customize("color_palette_setting_4", function (value) {
    value.bind(function (newval) {
      $(".footer, .footer li").css("color", newval);
    });
  });

  // ----------------------------------------
  // Footer link colour — hex only
  // ----------------------------------------
  wp.customize("color_palette_setting_5", function (value) {
    value.bind(function (newval) {
      $(".footer a").css("color", newval);
    });
  });

  // ----------------------------------------
  // Page background — hex or gradient slug
  // ----------------------------------------
  wp.customize("color_palette_setting_10", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground($("body"), aviddResolveSlug(newval));
    });
  });
})(jQuery);
