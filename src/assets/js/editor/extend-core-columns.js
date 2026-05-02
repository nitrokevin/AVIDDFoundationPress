(function(wp) {
    const { addFilter } = wp.hooks;
    const { __ } = wp.i18n;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, Button } = wp.components;
    const { createHigherOrderComponent } = wp.compose;

    // =============================================================================
    // EXTEND COLUMN ATTRIBUTES
    // =============================================================================

    addFilter('blocks.registerBlockType', 'cbg/column-bg-image-attr', (settings, name) => {
        if (name !== 'core/column') return settings;
        
        return {
            ...settings,
            attributes: {
                ...settings.attributes,
                backgroundImage: { 
                    type: 'string', 
                    default: '' 
                }
            }
        };
    });

    // =============================================================================
    // COLUMN INSPECTOR - BACKGROUND IMAGE CONTROL
    // =============================================================================

    const withColumnBackgroundImage = createHigherOrderComponent(BlockEdit => props => {
        if (props.name !== 'core/column') return el(BlockEdit, props);
        
        const { attributes, setAttributes } = props;
        const { backgroundImage = '' } = attributes;

        return el(Fragment, null,
            el(BlockEdit, props),
            el(InspectorControls, null,
                el(PanelBody, { 
                    title: __('Background Image', 'cbg'), 
                    initialOpen: false 
                },
                    el(MediaUploadCheck, null,
                        el(MediaUpload, {
                            onSelect: media => setAttributes({ backgroundImage: media.url || '' }),
                            allowedTypes: ['image'],
                            value: backgroundImage,
                            render: ({ open }) => el(Button, {
                                variant: 'secondary',
                                onClick: open
                            }, backgroundImage ? __('Change Image', 'cbg') : __('Select Image', 'cbg'))
                        })
                    ),
                    backgroundImage && el(Button, {
                        variant: 'link',
                        isDestructive: true,
                        style: { marginTop: '10px' },
                        onClick: () => setAttributes({ backgroundImage: '' })
                    }, __('Remove Image', 'cbg'))
                )
            )
        );
    }, 'withColumnBackgroundImage');
    addFilter('editor.BlockEdit', 'cbg/column-bg-image-inspector', withColumnBackgroundImage);

    // =============================================================================
    // EDITOR PREVIEW - APPLY BACKGROUNDS
    // =============================================================================

    const withColumnEditorPreview = createHigherOrderComponent(BlockListBlock => props => {
        if (props.name !== 'core/column') return el(BlockListBlock, props);
        
        const { attributes } = props;
        const { 
            backgroundImage = '',
            backgroundColor,
            gradient,
            style = {}
        } = attributes;

        // Only apply preview styles if parent columns has edge-to-edge style
        const parentClientId = wp.data.select('core/block-editor').getBlockParents(props.clientId)[0];
        const parentBlock = parentClientId ? wp.data.select('core/block-editor').getBlock(parentClientId) : null;
        const isEdgeToEdge = parentBlock?.attributes?.className?.includes('is-style-edge-to-edge');

        if (!isEdgeToEdge) return el(BlockListBlock, props);

        // Build inline styles for editor preview
        const previewStyle = { ...(props.wrapperProps?.style || {}) };
        let cssVars = '';

        // Background image
        if (backgroundImage) {
            cssVars += `--column-bg-image:url(${backgroundImage});`;
            previewStyle.position = 'relative';
        }

        // Custom background color
        if (style?.color?.background) {
            cssVars += `--column-bg-color:${style.color.background};`;
            previewStyle.position = 'relative';
        }

        // Custom gradient
        if (style?.color?.gradient) {
            cssVars += `--column-bg-gradient:${style.color.gradient};`;
            previewStyle.position = 'relative';
        }

        // Merge CSS variables
        const existingCssText = props.wrapperProps?.style?.cssText || '';
        const mergedCssText = existingCssText + cssVars;

        return el(BlockListBlock, {
            ...props,
            wrapperProps: {
                ...props.wrapperProps,
                style: {
                    ...previewStyle,
                    cssText: mergedCssText
                }
            }
        });
    }, 'withColumnEditorPreview');
    addFilter('editor.BlockListBlock', 'cbg/column-editor-preview', withColumnEditorPreview);

})(window.wp);