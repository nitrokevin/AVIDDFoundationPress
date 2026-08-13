(function ($) {
  ("use strict");

  if (typeof wp === "undefined" || typeof wp.customize === "undefined") {
    return;
  }

  function aviddResolveSlug(value) {
    if (!value) return "";
    if (/^#([a-f0-9]{3}){1,2}$/i.test(value)) return value;
    if (typeof aviddGradients !== "undefined" && aviddGradients[value]) {
      return aviddGradients[value];
    }
    return value;
  }

  function aviddApplyBackground($el, value) {
    if (!value) return;
    if (value.indexOf("gradient(") !== -1) {
      $el.css("background-color", "").css("background-image", value);
    } else {
      $el.css("background-image", "").css("background-color", value);
    }
  }

  function aviddHexToRgba(hex, alpha) {
    hex = hex.replace("#", "");
    if (hex.length === 3) {
      hex = hex
        .split("")
        .map(function (c) {
          return c + c;
        })
        .join("");
    }
    return (
      "rgba(" +
      parseInt(hex.substring(0, 2), 16) +
      ", " +
      parseInt(hex.substring(2, 4), 16) +
      ", " +
      parseInt(hex.substring(4, 6), 16) +
      ", " +
      alpha +
      ")"
    );
  }

  // ----------------------------------------
  // Nav
  // ----------------------------------------
  wp.customize("color_nav_background", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground($(".top-bar, .title-bar"), aviddResolveSlug(newval));
    });
  });

  wp.customize("color_nav_menu_item", function (value) {
    value.bind(function (newval) {
      $(
        ".top-bar, .top-bar .desktop-menu a:not(.button), .title-bar .mobile-menu a:not(.button)",
      ).css("color", newval);
    });
  });

  // ----------------------------------------
  // Footer
  // ----------------------------------------
  wp.customize("color_footer_background", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground($(".footer"), aviddResolveSlug(newval));
    });
  });

  wp.customize("color_footer_text", function (value) {
    value.bind(function (newval) {
      $(".footer, .footer li").css("color", newval);
    });
  });

  wp.customize("color_footer_link", function (value) {
    value.bind(function (newval) {
      $(".footer a").css("color", newval);
    });
  });

  // ----------------------------------------
  // Page background
  // ----------------------------------------
  wp.customize("color_page_background", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground($("body"), aviddResolveSlug(newval));
    });
  });

  // ----------------------------------------
  // Front hero overlay
  // ----------------------------------------
  wp.customize("color_front_hero_overlay_background", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground(
        $(".front-hero .hero__bg-overlay"),
        aviddResolveSlug(newval),
      );
    });
  });

  wp.customize("hero_overlay_opacity", function (value) {
    value.bind(function (newval) {
      $(".front-hero .hero__bg-overlay").css("opacity", newval / 100);
    });
  });

  wp.customize("front_hero_blend_mode", function (value) {
    value.bind(function (newval) {
      $(".front-hero .hero__bg-overlay").css("mix-blend-mode", newval);
    });
  });

  // ----------------------------------------
  // Front tagline overlay
  // ----------------------------------------
  var frontTaglineColor = "";
  var frontTaglineOpacity = 0.4;

var aviddUpdateFrontTagline = function () {
  if (frontTaglineColor) {
    $(".front-hero").css(
      "--tagline-bg-colour",

      aviddHexToRgba(frontTaglineColor, frontTaglineOpacity),
    );
  }
};

  wp.customize("color_front_tagline_overlay_background", function (value) {
    value.bind(function (newval) {
      frontTaglineColor = aviddResolveSlug(newval);
      aviddUpdateFrontTagline();
    });
  });

  wp.customize("front_tagline_overlay_opacity", function (value) {
    value.bind(function (newval) {
      frontTaglineOpacity = newval / 100;
      aviddUpdateFrontTagline();
    });
  });

  wp.customize("front_tagline_overlay_blur", function (value) {
    value.bind(function (newval) {
      $(".front-hero .tagline__bg-overlay").css(
        "backdrop-filter",
        "blur(" + newval + "px)",
      );
    });
  });

    wp.customize("color_front_tagline_text", function (value) {
      value.bind(function (newval) {
        $( ".front-hero, .tagline, .front-hero, .tagline h1, .front-hero, .tagline p",
        ).css("color", newval);
      });
    });

  // ----------------------------------------
  // Inner hero overlay
  // ----------------------------------------
  wp.customize("color_inner_hero_overlay_background", function (value) {
    value.bind(function (newval) {
      aviddApplyBackground(
        $(".featured-hero .hero__bg-overlay"),
        aviddResolveSlug(newval),
      );
    });
  });

  wp.customize("inner_hero_overlay_opacity", function (value) {
    value.bind(function (newval) {
      $(".featured-hero .hero__bg-overlay").css("opacity", newval / 100);
    });
  });

  wp.customize("inner_hero_blend_mode", function (value) {
    value.bind(function (newval) {
      $(".featured-hero .hero__bg-overlay").css("mix-blend-mode", newval);
    });
  });

  // ----------------------------------------
  // Inner tagline overlay
  // ----------------------------------------
  var innerTaglineColor = "";
  var innerTaglineOpacity = 0.4;

  var aviddUpdateInnerTagline = function () {
    if (innerTaglineColor) {
      $(".featured-hero .tagline__bg-colour").css(
        "background-color",
        aviddHexToRgba(innerTaglineColor, innerTaglineOpacity),
      );
    }
  };

  wp.customize("color_inner_tagline_overlay_background", function (value) {
    value.bind(function (newval) {
      innerTaglineColor = aviddResolveSlug(newval);
      aviddUpdateInnerTagline();
    });
  });

  wp.customize("inner_tagline_overlay_opacity", function (value) {
    value.bind(function (newval) {
      innerTaglineOpacity = newval / 100;
      aviddUpdateInnerTagline();
    });
  });

  wp.customize("inner_tagline_overlay_blur", function (value) {
    value.bind(function (newval) {
      $(".featured-hero .tagline__bg-overlay").css(
        "backdrop-filter",
        "blur(" + newval + "px)",
      );
    });
  });
})(jQuery);
