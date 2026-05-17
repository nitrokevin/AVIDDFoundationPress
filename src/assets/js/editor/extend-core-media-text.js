import { addFilter } from "@wordpress/hooks";
import { InspectorControls, PanelColorSettings } from "@wordpress/block-editor";
import { createHigherOrderComponent } from "@wordpress/compose";
import { Fragment } from "@wordpress/element";
import { useSelect } from "@wordpress/data";

const normaliseHex = (value) => value?.replace("#", "").toLowerCase();

const addMediaTextColorControl = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    if (props.name !== "core/media-text") {
      return <BlockEdit {...props} />;
    }

    const { attributes, setAttributes } = props;
    const { mediaBackgroundColor } = attributes;

    const colors = useSelect(
      (select) => select("core/block-editor").getSettings().colors,
      [],
    );

    // Resolve stored slug back to hex for the colour picker's value prop
    const resolvedColor =
      colors.find((c) => c.slug === mediaBackgroundColor)?.color ?? undefined;

    const handleColorChange = (value) => {
      if (!value) {
        setAttributes({ mediaBackgroundColor: "" });
        return;
      }
      const matched = colors.find(
        (c) => normaliseHex(c.color) === normaliseHex(value),
      );

      console.log("Color picked:", value);
      console.log("Matched:", matched);
      console.log("Available colors:", colors);

      setAttributes({ mediaBackgroundColor: matched?.slug ?? "" });
    };

    return (
      <Fragment>
        <BlockEdit {...props} />
        <InspectorControls>
          <PanelColorSettings
            title="Media Background"
            colorSettings={[
              {
                label: "Media Background Colour",
                value: resolvedColor,
                onChange: handleColorChange,
              },
            ]}
          />
        </InspectorControls>
      </Fragment>
    );
  };
}, "addMediaTextColorControl");

addFilter(
  "editor.BlockEdit",
  "avidd/media-text-color-control",
  addMediaTextColorControl,
);
