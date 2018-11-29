(function ($) {
    // Get registerBlockType() from wp.blocks in the global scope
    var __ = wp.i18n.__,
        el = wp.element.createElement,
        Fragment = wp.element.Fragment,
        registerBlockType = wp.blocks.registerBlockType,
        RichText = wp.editor.RichText,
        BlockControls = wp.editor.BlockControls,
        InspectorControls = wp.editor.InspectorControls,
        AlignmentToolbar = wp.editor.AlignmentToolbar,
        Components = wp.components,
        RawHTML = wp.element.RawHTML,
        SelectControl = wp.components.SelectControl,
        ToggleControl = wp.components.ToggleControl,
        CheckboxControl = wp.components.CheckboxControl,
        RangeControl = wp.components.RangeControl,
        ColorPicker = wp.components.ColorPicker,
        RadioControl = wp.components.RadioControl,
        TextControl = wp.components.TextControl,
        TextareaControl = wp.components.TextareaControl;

    function create_shortcode(sc_args, sc_name, callback) {
        var gutenberg_preview = '';

        var sc = '[' + sc_name;

        $.each(sc_args.attributes, function ($v, $k) {
            if ($v != 'className') {
                sc += ' ' + $v + '=' + '"' + $k + '"';
            }
        });

        sc += ']';

        var block_id = md5(sc);

        gutenberg_preview = '<span class="ag_block_' + block_id + '">' + sc + '</span>';

        if (callback == 'edit') {
            do_shortcode = (function (block_id) {
                var ajax_call_date = null;
                $.ajax({
                    async: true,
                    url: ajaxurl,
                    method: 'post',
                    data: {action: 'ag_gutenberg_do_shortcode', shortcode: sc},
                    success: function (data) {
                        ajax_call_date = data;

                        if (ajax_call_date != '') {
                            $('.ag_block_' + block_id).html(ajax_call_date);
                        }
                    }
                });
                return ajax_call_date;
            })(block_id);
        }

        gt_block = el(RawHTML, null, gutenberg_preview);

        return gt_block;
    }

    function onChangeEvent(new_value, attribute_name, args, block_type) {
        var attributes = {};
        if (block_type ==  ColorPicker) {
            new_value = new_value.hex;
        }

        attributes[attribute_name] = new_value;
        args.setAttributes(attributes);
        return args;
    }

    registerBlockType( "ag/socials-button", {
        title: __( 'AG Social Buttons', 'ag-gutenberg' ),
        description: __( 'Add social buttons to you post or page', 'ag-gutenberg' ),
        category: 'ag-blocks',
        attributes: {
            buttons: {
                type: 'string',
                default: 'facebook,twitter,linkedin,google-plus,instagram'
            },
            description: {
              type: 'string',
              default: __( 'Follow us on...', 'ag-gutenberg' )
            },
            size: {
                type: 'number',
                default: 2
            },
            facebook_url: {
                type: 'string',
                default: '#'
            },
            twitter_url: {
                type: 'string',
                default: '#'
            },
            google_plus_url: {
                type: 'string',
                default: '#'
            },
            instagram_url: {
                type: 'string',
                default: '#'
            },
            linkedin_url: {
                type: 'string',
                default: '#'
            },
            target: {
              type: 'string',
              default: '_blank'
            },
            color: {
                type: 'string',
                default: '#8c8b8b'
            },
            color_hover: {
                type: 'string',
                default: '#3c21e2'
            },
        },
        icon: 'share-alt2',
        keywords: [ 'socials buttons', 'ag' ],
        edit: function edit(args){
            //Array of Gutenberg's elements
            var elements = new Array();
            elements.push(
                el(
                    SelectControl,
                    {
                        value: args.attributes['buttons'],
                        options: [
                            {
                                label: __( 'Facebook', 'ag-gutenberg' ),
                                value: 'facebook'
                            },
                            {
                                label: __( 'Twitter', 'ag-gutenberg' ),
                                value: 'twitter'
                            },
                            {
                                label: __( 'Linkedin', 'ag-gutenberg' ),
                                value: 'linkedin'
                            },
                            {
                                label: __( 'Google Plus', 'ag-gutenberg' ),
                                value: 'google-plus'
                            },
                            {
                                label: __( 'Instangram', 'ag-gutenberg' ),
                                value: 'instagram'
                            },
                        ],
                        label: __( 'Select social buttons to show', 'ag-gutenberg' ),
                        multiple: true,
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'buttons', args, SelectControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                   TextareaControl,
                    {
                        value: args.attributes['description'],
                        label: __( 'Add description', 'ag-gutenberg' ),
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'description', args, TextareaControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    RangeControl,
                    {
                        value: args.attributes['size'],
                        label: __( 'Icon size', 'ag-gutenberg' ),
                        min: 2,
                        max: 10,
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'size', args, RangeControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    TextControl,
                    {
                        value: args.attributes['facebook_url'],
                        label: __( 'Add Facebook URL', 'ag-gutenberg' ),
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'facebook_url', args, TextControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    TextControl,
                    {
                        value: args.attributes['twitter_url'],
                        label: __( 'Add Twitter URL', 'ag-gutenberg' ),
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'twitter_url', args, TextControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    TextControl,
                    {
                        value: args.attributes['google_plus_url'],
                        label: __( 'Add Google Plus URL', 'ag-gutenberg' ),
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'google_plus_url', args, TextControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    TextControl,
                    {
                        value: args.attributes['instagram_url'],
                        label: __( 'Add Instagram URL', 'ag-gutenberg' ),
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'instagram_url', args, TextControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    TextControl,
                    {
                        value: args.attributes['linkedin_url'],
                        label: __( 'Add Linkedin URL', 'ag-gutenberg' ),
                        onChange: function (new_value) {
                            args = onChangeEvent(new_value, 'linkedin_url', args, TextControl);
                        },
                    }
                )
            );

            elements.push(
                el(
                    'div',
                    {},
                    __( 'Select Icon Color', 'ag-gutenberg' )
                )
            );

            elements.push(
                el(
                    ColorPicker,
                    {
                        color: args.attributes['color'],
                        disableAlpha: true,
                        onChangeComplete: function (new_value) {
                            args = onChangeEvent(new_value, 'color', args, ColorPicker);
                        },
                    }
                )
            );

            elements.push(
                el(
                    'div',
                    {},
                    __( 'Select Icon Color on Hover', 'ag-gutenberg' )
                )
            );

            elements.push(
                el(
                    ColorPicker,
                    {
                        color: args.attributes['color_hover'],
                        disableAlpha: true,
                        onChangeComplete: function (new_value) {
                            args = onChangeEvent(new_value, 'color_hover', args, ColorPicker);
                        },
                    }
                )
            );

            var sc = create_shortcode(args, 'ag_social_buttons', 'edit');

            return [
                el(
                    Fragment,
                    null,
                    el(
                        InspectorControls,
                        null,
                        elements,
                    ),
                    sc,
                )];
        },
        save: function save(args) {
            return create_shortcode(args, 'ag_social_buttons', 'save');
        }
    });
})(jQuery);