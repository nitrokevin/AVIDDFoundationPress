/**
 * Column Background + XY Grid — Block Editor Extension
 *
 * Extends core/column with:
 *   - backgroundImage attribute  (written to saved HTML via extraProps)
 *   - xyGrid attribute           (server-side only via PHP render_block filter)
 *
 * Extends core/columns with:
 *   - useFoundationGrid attribute (server-side only via PHP render_block filter)
 *
 * backgroundImage is the only attribute that needs to appear in saved HTML,
 * because it affects the visual output and WordPress validates saved markup
 * against what the save() function would produce. xyGrid and useFoundationGrid
 * are applied exclusively by the PHP render_block filter reading block attrs
 * from the block comment delimiter — no save() change needed for those.
 */

const { addFilter } = wp.hooks;
const { __ } = wp.i18n;
const { Fragment, createElement } = wp.element;
const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { PanelBody, Button, ToggleControl, SelectControl } = wp.components;
const { createHigherOrderComponent } = wp.compose;
const { select } = wp.data;

const BREAKPOINTS = ["small", "medium", "large"];
const GRID_OPTIONS = [
  "",
  "1",
  "2",
  "3",
  "4",
  "5",
  "6",
  "7",
  "8",
  "9",
  "10",
  "11",
  "12",
];
const OFFSET_OPTIONS = [
  "",
  "1",
  "2",
  "3",
  "4",
  "5",
  "6",
  "7",
  "8",
  "9",
  "10",
  "11",
];

// =============================================================================
// ATTRIBUTE REGISTRATION
// =============================================================================

/**
 * Add backgroundImage and xyGrid attributes to core/column.
 */
addFilter(
  "blocks.registerBlockType",
  "cbg/extend-column-attributes",
  (settings, name) => {
    if (name !== "core/column") return settings;

    return {
      ...settings,
      attributes: {
        ...settings.attributes,
        backgroundImage: {
          type: "string",
          default: "",
        },
        xyGrid: {
          type: "object",
          default: {
            small: "",
            medium: "",
            large: "",
            offsetSmall: "",
            offsetMedium: "",
            offsetLarge: "",
          },
        },
      },
    };
  },
);

/**
 * Add useFoundationGrid attribute to core/columns.
 */
addFilter(
  "blocks.registerBlockType",
  "cbg/extend-columns-attributes",
  (settings, name) => {
    if (name !== "core/columns") return settings;

    return {
      ...settings,
      attributes: {
        ...settings.attributes,
        useFoundationGrid: {
          type: "boolean",
          default: false,
        },
      },
    };
  },
);

// =============================================================================
// SAVE PROPS — write backgroundImage into saved HTML
// =============================================================================

/**
 * Persist backgroundImage as an inline CSS custom property on the saved block.
 *
 * This is the fix for R-06: without this filter, backgroundImage is stored in
 * the block comment delimiter but never written into the saved HTML. WordPress
 * then flags the block as invalid because the serialised markup doesn't match
 * what it expects. The PHP render_block filter reads --column-bg from the
 * inline style to apply the background server-side.
 */
addFilter(
  "blocks.getSaveContent.extraProps",
  "cbg/column-save-props",
  (props, blockType, attributes) => {
    if (blockType.name !== "core/column") return props;

    const { backgroundImage } = attributes;

    if (!backgroundImage) return props;

    return {
      ...props,
      className: [props.className, "has-background-image"]
        .filter(Boolean)
        .join(" "),
      style: {
        ...(props.style || {}),
        "--column-bg": `url(${backgroundImage})`,
      },
    };
  },
);

// =============================================================================
// INSPECTOR CONTROLS — core/column
// =============================================================================

const withColumnControls = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    if (props.name !== "core/column") return createElement(BlockEdit, props);

    const { attributes, setAttributes } = props;
    const { backgroundImage, xyGrid = {} } = attributes;

    const setXyGrid = (bp, key, value) =>
      setAttributes({ xyGrid: { ...xyGrid, [key]: value } });

    return createElement(
      Fragment,
      null,
      createElement(BlockEdit, props),
      createElement(
        InspectorControls,
        null,

        // Background image panel
        createElement(
          PanelBody,
          {
            title: __("Background Image", "foundationpress"),
            initialOpen: true,
          },
          createElement(
            MediaUploadCheck,
            null,
            createElement(MediaUpload, {
              onSelect: (media) =>
                setAttributes({ backgroundImage: media.url || "" }),
              allowedTypes: ["image"],
              value: backgroundImage,
              render: ({ open }) =>
                createElement(
                  Button,
                  { variant: "secondary", onClick: open },
                  backgroundImage
                    ? __("Change Image", "foundationpress")
                    : __("Select Image", "foundationpress"),
                ),
            }),
          ),
          backgroundImage &&
            createElement(
              Button,
              {
                variant: "link",
                isDestructive: true,
                style: { marginTop: "8px", display: "block" },
                onClick: () => setAttributes({ backgroundImage: "" }),
              },
              __("Remove Image", "foundationpress"),
            ),
        ),

        // Foundation XY Grid panel
        createElement(
          PanelBody,
          {
            title: __("Foundation XY Grid", "foundationpress"),
            initialOpen: false,
          },
          ...BREAKPOINTS.map((bp) => {
            const label = bp.charAt(0).toUpperCase() + bp.slice(1);
            const offsetKey = "offset" + label;

            return createElement(
              Fragment,
              { key: bp },
              createElement(SelectControl, {
                label: __(label + " Width", "foundationpress"),
                value: xyGrid[bp] || "",
                options: GRID_OPTIONS.map((v) => ({
                  label: v || __("None", "foundationpress"),
                  value: v,
                })),
                onChange: (v) => setXyGrid(bp, bp, v),
              }),
              createElement(SelectControl, {
                label: __(label + " Offset", "foundationpress"),
                value: xyGrid[offsetKey] || "",
                options: OFFSET_OPTIONS.map((v) => ({
                  label: v || __("None", "foundationpress"),
                  value: v,
                })),
                onChange: (v) => setXyGrid(bp, offsetKey, v),
              }),
            );
          }),
        ),
      ),
    );
  },
  "withColumnControls",
);

addFilter("editor.BlockEdit", "cbg/column-inspector", withColumnControls);

// =============================================================================
// INSPECTOR CONTROLS — core/columns
// =============================================================================

const withColumnsToggle = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    if (props.name !== "core/columns") return createElement(BlockEdit, props);

    const { attributes, setAttributes } = props;
    const { useFoundationGrid = false } = attributes;

    return createElement(
      Fragment,
      null,
      createElement(BlockEdit, props),
      createElement(
        InspectorControls,
        null,
        createElement(
          PanelBody,
          { title: __("Column Layout", "foundationpress"), initialOpen: true },
          createElement(ToggleControl, {
            label: __("Use Foundation XY Grid", "foundationpress"),
            checked: useFoundationGrid,
            onChange: (val) => setAttributes({ useFoundationGrid: val }),
          }),
        ),
      ),
    );
  },
  "withColumnsToggle",
);

addFilter("editor.BlockEdit", "cbg/columns-toggle", withColumnsToggle);

// =============================================================================
// EDITOR PREVIEW — core/column
// =============================================================================

const withColumnStyle = createHigherOrderComponent(
  (BlockListBlock) => (props) => {
    if (props.name !== "core/column")
      return createElement(BlockListBlock, props);

    const { attributes, clientId } = props;
    const { backgroundImage, xyGrid = {}, width } = attributes;

    // Resolve parent columns block attributes
    const parentId = select("core/block-editor")
      .getBlockParents(clientId)
      .find(
        (id) => select("core/block-editor").getBlockName(id) === "core/columns",
      );

    const parentAttributes = parentId
      ? select("core/block-editor").getBlockAttributes(parentId)
      : {};
    const useFoundationGrid = parentAttributes.useFoundationGrid || false;

    // Build preview class string
    let xyClasses = "cbg-xy-grid";
    BREAKPOINTS.forEach((bp) => {
      const val = xyGrid[bp];
      const offsetKey = "offset" + bp.charAt(0).toUpperCase() + bp.slice(1);
      if (val) xyClasses += ` ${bp}-${val}`;
      if (xyGrid[offsetKey]) xyClasses += ` ${bp}-offset-${xyGrid[offsetKey]}`;
    });
    if (backgroundImage) xyClasses += " has-background-image";

    // Build preview inline style
    const style = { ...(props.wrapperProps?.style || {}) };

    if (backgroundImage) {
      style["--column-bg"] = `url(${backgroundImage})`;
      style.backgroundImage = `var(--column-bg)`;
      style.backgroundSize = "cover";
      style.backgroundPosition = "center";
    }

    if (useFoundationGrid) {
      style.flexBasis = "unset";
      style.flexGrow = "unset";
    } else if (width) {
      style.flexBasis = width;
    }

    return createElement(BlockListBlock, {
      ...props,
      wrapperProps: {
        ...props.wrapperProps,
        style,
        className: [props.wrapperProps?.className, xyClasses]
          .filter(Boolean)
          .join(" "),
      },
    });
  },
  "withColumnStyle",
);

addFilter(
  "editor.BlockListBlock",
  "cbg/column-background-style",
  withColumnStyle,
);

// =============================================================================
// EDITOR PREVIEW — core/columns wrapper
// =============================================================================

const withColumnsWrapperStyle = createHigherOrderComponent(
  (BlockListBlock) => (props) => {
    if (props.name !== "core/columns")
      return createElement(BlockListBlock, props);

    const { useFoundationGrid = false } = props.attributes;
    const wrapperProps = { ...(props.wrapperProps || {}) };

    // Strip any stale Foundation classes before conditionally re-adding
    let className = (wrapperProps.className || "wp-block-columns")
      .replace(/\s*grid-x\b/g, "")
      .replace(/\s*grid-margin-x\b/g, "")
      .trim();

    if (useFoundationGrid) {
      className += " grid-x grid-margin-x";
    }

    return createElement(BlockListBlock, {
      ...props,
      wrapperProps: { ...wrapperProps, className },
    });
  },
  "withColumnsWrapperStyle",
);

addFilter(
  "editor.BlockListBlock",
  "cbg/columns-wrapper-style",
  withColumnsWrapperStyle,
);
