(function ($) {
    "use strict";

    var defaultOptions = {
        tagClass: function (item) {
            return 'label label-info';
        },
        focusClass: 'focus',
        itemValue: function (item) {
            return item ? item.toString() : item;
        },
        itemText: function (item) {
            return this.itemValue(item);
        },
        itemTitle: function (item) {
            return null;
        },
        freeInput: true,
        addOnBlur: true,
        maxTags: undefined,
        maxChars: undefined,
        confirmKeys: [13, 44],
        delimiter: ',',
        delimiterRegex: null,
        cancelConfirmKeysOnEmpty: false,
        onTagExists: function (item, $tag) {
            $tag.hide().fadeIn();
        },
        trimValue: false,
        allowDuplicates: false,
        triggerChange: true
    };

    /**
     * Constructor function
     */
    function TagsInput(element, options) {
        this.isInit = true;
        this.itemsArray = [];

        this.$element = $(element);
        this.$element.hide();

        this.isSelect = (element.tagName === 'SELECT');
        this.multiple = (this.isSelect && element.hasAttribute('multiple'));
        this.objectItems = options && options.itemValue;
        this.placeholderText = element.hasAttribute('placeholder') ? this.$element.attr('placeholder') : '';
        this.inputSize = Math.max(1, this.placeholderText.length);

        this.$container = $('<div class="bootstrap-tagsinput"></div>');
        this.$input = $('<input type="text" placeholder="' + this.placeholderText + '"/>').appendTo(this.$container);

        this.$element.before(this.$container);

        this.build(options);
        this.isInit = false;
    }

    TagsInput.prototype = {
        constructor: TagsInput,

        /**
         * Adds the given item as a new tag. Pass true to dontPushVal to prevent
         * updating the elements val()
         */
        add: function (item, dontPushVal, options) {
            var self = this;

            if (self.options.maxTags && self.itemsArray.length >= self.options.maxTags)
                return;

            // Ignore falsey values, except false
            if (item !== false && !item)
                return;

            // Trim value
            if (typeof item === "string" && self.options.trimValue) {
                item = $.trim(item);
            }

            // Throw an error when trying to add an object while the itemValue option was not set
            if (typeof item === "object" && !self.objectItems)
                throw("Can't add objects when itemValue option is not set");

            // Ignore strings only containg whitespace
            if (item.toString().match(/^\s*$/))
                return;

            // If SELECT but not multiple, remove current tag
            if (self.isSelect && !self.multiple && self.itemsArray.length > 0)
                self.remove(self.itemsArray[0]);

            if (typeof item === "string" && this.$element[0].tagName === 'INPUT') {
                var delimiter = (self.options.delimiterRegex) ? self.options.delimiterRegex : self.options.delimiter;
                var items = item.split(delimiter);
                if (items.length > 1) {
                    for (var i = 0; i < items.length; i++) {
                        this.add(items[i], true);
                    }

                    if (!dontPushVal)
                        self.pushVal(self.options.triggerChange);
                    return;
                }
            }

            var itemValue = self.options.itemValue(item),
                itemText = self.options.itemText(item),
                tagClass = self.options.tagClass(item),
                itemTitle = self.options.itemTitle(item);

            // Ignore items allready added
            var existing = $.grep(self.itemsArray, function (item) {
                return self.options.itemValue(item) === itemValue;
            })[0];
            if (existing && !self.options.allowDuplicates) {
                // Invoke onTagExists
                if (self.options.onTagExists) {
                    var $existingTag = $(".tag", self.$container).filter(function () {
                        return $(this).data("item") === existing;
                    });
                    self.options.onTagExists(item, $existingTag);
                }
                return;
            }

            // if length greater than limit
            if (self.items().toString().length + item.length + 1 > self.options.maxInputLength)
                return;

            // raise beforeItemAdd arg
            var beforeItemAddEvent = $.Event('beforeItemAdd', {item: item, cancel: false, options: options});
            self.$element.trigger(beforeItemAddEvent);
            if (beforeItemAddEvent.cancel)
                return;

            // register item in internal array and map
            self.itemsArray.push(item);

            // add a tag element

            var $tag = $('<span class="tag ' + htmlEncode(tagClass) + (itemTitle !== null ? ('" title="' + itemTitle) : '') + '">' + htmlEncode(itemText) + '<span data-role="remove"></span></span>');
            $tag.data('item', item);
            self.findInputWrapper().before($tag);
            $tag.after(' ');

            // Check to see if the tag exists in its raw or uri-encoded form
            var optionExists = (
                $('option[value="' + encodeURIComponent(itemValue) + '"]', self.$element).length ||
                $('option[value="' + htmlEncode(itemValue) + '"]', self.$element).length
            );

            // add <option /> if item represents a value not present in one of the <select />'s options
            if (self.isSelect && !optionExists) {
                var $option = $('<option selected>' + htmlEncode(itemText) + '</option>');
                $option.data('item', item);
                $option.attr('value', itemValue);
                self.$element.append($option);
            }

            if (!dontPushVal)
                self.pushVal(self.options.triggerChange);

            // Add class when reached maxTags
            if (self.options.maxTags === self.itemsArray.length || self.items().toString().length === self.options.maxInputLength)
                self.$container.addClass('bootstrap-tagsinput-max');

            // If using typeahead, once the tag has been added, clear the typeahead value so it does not stick around in the input.
            if ($('.typeahead, .twitter-typeahead', self.$container).length) {
                self.$input.typeahead('val', '');
            }

            if (this.isInit) {
                self.$element.trigger($.Event('itemAddedOnInit', {item: item, options: options}));
            } else {
                self.$element.trigger($.Event('itemAdded', {item: item, options: options}));
            }
        },

        /**
         * Removes the given item. Pass true to dontPushVal to prevent updating the
         * elements val()
         */
        remove: function (item, dontPushVal, options) {
            var self = this;

            if (self.objectItems) {
                if (typeof item === "object")
                    item = $.grep(self.itemsArray, function (other) {
                        return self.options.itemValue(other) == self.options.itemValue(item);
                    });
                else
                    item = $.grep(self.itemsArray, function (other) {
                        return self.options.itemValue(other) == item;
                    });

                item = item[item.length - 1];
            }

            if (item) {
                var beforeItemRemoveEvent = $.Event('beforeItemRemove', {item: item, cancel: false, options: options});
                self.$element.trigger(beforeItemRemoveEvent);
                if (beforeItemRemoveEvent.cancel)
                    return;

                $('.tag', self.$container).filter(function () {
                    return $(this).data('item') === item;
                }).remove();
                $('option', self.$element).filter(function () {
                    return $(this).data('item') === item;
                }).remove();
                if ($.inArray(item, self.itemsArray) !== -1)
                    self.itemsArray.splice($.inArray(item, self.itemsArray), 1);
            }

            if (!dontPushVal)
                self.pushVal(self.options.triggerChange);

            // Remove class when reached maxTags
            if (self.options.maxTags > self.itemsArray.length)
                self.$container.removeClass('bootstrap-tagsinput-max');

            self.$element.trigger($.Event('itemRemoved', {item: item, options: options}));
        },

        /**
         * Removes all items
         */
        removeAll: function () {
            var self = this;

            $('.tag', self.$container).remove();
            $('option', self.$element).remove();

            while (self.itemsArray.length > 0)
                self.itemsArray.pop();

            self.pushVal(self.options.triggerChange);
        },

        /**
         * Refreshes the tags so they match the text/value of their corresponding
         * item.
         */
        refresh: function () {
            var self = this;
            $('.tag', self.$container).each(function () {
                var $tag = $(this),
                    item = $tag.data('item'),
                    itemValue = self.options.itemValue(item),
                    itemText = self.options.itemText(item),
                    tagClass = self.options.tagClass(item);

                // Update tag's class and inner text
                $tag.attr('class', null);
                $tag.addClass('tag ' + htmlEncode(tagClass));
                $tag.contents().filter(function () {
                    return this.nodeType == 3;
                })[0].nodeValue = htmlEncode(itemText);

                if (self.isSelect) {
                    var option = $('option', self.$element).filter(function () {
                        return $(this).data('item') === item;
                    });
                    option.attr('value', itemValue);
                }
            });
        },

        /**
         * Returns the items added as tags
         */
        items: function () {
            return this.itemsArray;
        },

        /**
         * Assembly value by retrieving the value of each item, and set it on the
         * element.
         */
        pushVal: function () {
            var self = this,
                val = $.map(self.items(), function (item) {
                    return self.options.itemValue(item).toString();
                });

            self.$element.val(val, true);

            if (self.options.triggerChange)
                self.$element.trigger('change');
        },

        /**
         * Initializes the tags input behaviour on the element
         */
        build: function (options) {
            var self = this;

            self.options = $.extend({}, defaultOptions, options);
            // When itemValue is set, freeInput should always be false
            if (self.objectItems)
                self.options.freeInput = false;

            makeOptionItemFunction(self.options, 'itemValue');
            makeOptionItemFunction(self.options, 'itemText');
            makeOptionFunction(self.options, 'tagClass');

            // Typeahead Bootstrap version 2.3.2
            if (self.options.typeahead) {
                var typeahead = self.options.typeahead || {};

                makeOptionFunction(typeahead, 'source');

                self.$input.typeahead($.extend({}, typeahead, {
                    source: function (query, process) {
                        function processItems(items) {
                            var texts = [];

                            for (var i = 0; i < items.length; i++) {
                                var text = self.options.itemText(items[i]);
                                map[text] = items[i];
                                texts.push(text);
                            }
                            process(texts);
                        }

                        this.map = {};
                        var map = this.map,
                            data = typeahead.source(query);

                        if ($.isFunction(data.success)) {
                            // support for Angular callbacks
                            data.success(processItems);
                        } else if ($.isFunction(data.then)) {
                            // support for Angular promises
                            data.then(processItems);
                        } else {
                            // support for functions and jquery promises
                            $.when(data)
                                .then(processItems);
                        }
                    },
                    updater: function (text) {
                        self.add(this.map[text]);
                        return this.map[text];
                    },
                    matcher: function (text) {
                        return (text.toLowerCase().indexOf(this.query.trim().toLowerCase()) !== -1);
                    },
                    sorter: function (texts) {
                        return texts.sort();
                    },
                    highlighter: function (text) {
                        var regex = new RegExp('(' + this.query + ')', 'gi');
                        return text.replace(regex, "<strong>$1</strong>");
                    }
                }));
            }

            // typeahead.js
            if (self.options.typeaheadjs) {
                // Determine if main configurations were passed or simply a dataset
                var typeaheadjs = self.options.typeaheadjs;
                if (!$.isArray(typeaheadjs)) {
                    typeaheadjs = [null, typeaheadjs];
                }

                $.fn.typeahead.apply(self.$input, typeaheadjs).on('typeahead:selected', $.proxy(function (obj, datum, name) {
                    var index = 0;
                    typeaheadjs.some(function (dataset, _index) {
                        if (dataset.name === name) {
                            index = _index;
                            return true;
                        }
                        return false;
                    });

                    // @TODO Dep: https://github.com/corejavascript/typeahead.js/issues/89
                    if (typeaheadjs[index].valueKey) {
                        self.add(datum[typeaheadjs[index].valueKey]);
                    } else {
                        self.add(datum);
                    }

                    self.$input.typeahead('val', '');
                }, self));
            }

            self.$container.on('click', $.proxy(function (event) {
                if (!self.$element.attr('disabled')) {
                    self.$input.removeAttr('disabled');
                }
                self.$input.focus();
            }, self));

            if (self.options.addOnBlur && self.options.freeInput) {
                self.$input.on('focusout', $.proxy(function (event) {
                    // HACK: only process on focusout when no typeahead opened, to
                    //       avoid adding the typeahead text as tag
                    if ($('.typeahead, .twitter-typeahead', self.$container).length === 0) {
                        self.add(self.$input.val());
                        self.$input.val('');
                    }
                }, self));
            }

            // Toggle the 'focus' css class on the container when it has focus
            self.$container.on({
                focusin: function () {
                    self.$container.addClass(self.options.focusClass);
                },
                focusout: function () {
                    self.$container.removeClass(self.options.focusClass);
                },
            });

            self.$container.on('keydown', 'input', $.proxy(function (event) {
                var $input = $(event.target),
                    $inputWrapper = self.findInputWrapper();

                if (self.$element.attr('disabled')) {
                    self.$input.attr('disabled', 'disabled');
                    return;
                }

                switch (event.which) {
                    // BACKSPACE
                    case 8:
                        if (doGetCaretPosition($input[0]) === 0) {
                            var prev = $inputWrapper.prev();
                            if (prev.length) {
                                self.remove(prev.data('item'));
                            }
                        }
                        break;

                    // DELETE
                    case 46:
                        if (doGetCaretPosition($input[0]) === 0) {
                            var next = $inputWrapper.next();
                            if (next.length) {
                                self.remove(next.data('item'));
                            }
                        }
                        break;

                    // LEFT ARROW
                    case 37:
                        // Try to move the input before the previous tag
                        var $prevTag = $inputWrapper.prev();
                        if ($input.val().length === 0 && $prevTag[0]) {
                            $prevTag.before($inputWrapper);
                            $input.focus();
                        }
                        break;
                    // RIGHT ARROW
                    case 39:
                        // Try to move the input after the next tag
                        var $nextTag = $inputWrapper.next();
                        if ($input.val().length === 0 && $nextTag[0]) {
                            $nextTag.after($inputWrapper);
                            $input.focus();
                        }
                        break;
                    default:
                    // ignore
                }

                // Reset internal input's size
                var textLength = $input.val().length,
                    wordSpace = Math.ceil(textLength / 5),
                    size = textLength + wordSpace + 1;
                $input.attr('size', Math.max(this.inputSize, $input.val().length));
            }, self));

            self.$container.on('keypress', 'input', $.proxy(function (event) {
                var $input = $(event.target);

                if (self.$element.attr('disabled')) {
                    self.$input.attr('disabled', 'disabled');
                    return;
                }

                var text = $input.val(),
                    maxLengthReached = self.options.maxChars && text.length >= self.options.maxChars;
                if (self.options.freeInput && (keyCombinationInList(event, self.options.confirmKeys) || maxLengthReached)) {
                    // Only attempt to add a tag if there is data in the field
                    if (text.length !== 0) {
                        self.add(maxLengthReached ? text.substr(0, self.options.maxChars) : text);
                        $input.val('');
                    }

                    // If the field is empty, let the event triggered fire as usual
                    if (self.options.cancelConfirmKeysOnEmpty === false) {
                        event.preventDefault();
                    }
                }

                // Reset internal input's size
                var textLength = $input.val().length,
                    wordSpace = Math.ceil(textLength / 5),
                    size = textLength + wordSpace + 1;
                $input.attr('size', Math.max(this.inputSize, $input.val().length));
            }, self));

            // Remove icon clicked
            self.$container.on('click', '[data-role=remove]', $.proxy(function (event) {
                if (self.$element.attr('disabled')) {
                    return;
                }
                self.remove($(event.target).closest('.tag').data('item'));
            }, self));

            // Only add existing value as tags when using strings as tags
            if (self.options.itemValue === defaultOptions.itemValue) {
                if (self.$element[0].tagName === 'INPUT') {
                    self.add(self.$element.val());
                } else {
                    $('option', self.$element).each(function () {
                        self.add($(this).attr('value'), true);
                    });
                }
            }
        },

        /**
         * Removes all tagsinput behaviour and unregsiter all event handlers
         */
        destroy: function () {
            var self = this;

            // Unbind events
            self.$container.off('keypress', 'input');
            self.$container.off('click', '[role=remove]');

            self.$container.remove();
            self.$element.removeData('tagsinput');
            self.$element.show();
        },

        /**
         * Sets focus on the tagsinput
         */
        focus: function () {
            this.$input.focus();
        },

        /**
         * Returns the internal input element
         */
        input: function () {
            return this.$input;
        },

        /**
         * Returns the element which is wrapped around the internal input. This
         * is normally the $container, but typeahead.js moves the $input element.
         */
        findInputWrapper: function () {
            var elt = this.$input[0],
                container = this.$container[0];
            while (elt && elt.parentNode !== container)
                elt = elt.parentNode;

            return $(elt);
        }
    };

    /**
     * Register JQuery plugin
     */
    $.fn.tagsinput = function (arg1, arg2, arg3) {
        var results = [];

        this.each(function () {
            var tagsinput = $(this).data('tagsinput');
            // Initialize a new tags input
            if (!tagsinput) {
                tagsinput = new TagsInput(this, arg1);
                $(this).data('tagsinput', tagsinput);
                results.push(tagsinput);

                if (this.tagName === 'SELECT') {
                    $('option', $(this)).attr('selected', 'selected');
                }

                // Init tags from $(this).val()
                $(this).val($(this).val());
            } else if (!arg1 && !arg2) {
                // tagsinput already exists
                // no function, trying to init
                results.push(tagsinput);
            } else if (tagsinput[arg1] !== undefined) {
                // Invoke function on existing tags input
                if (tagsinput[arg1].length === 3 && arg3 !== undefined) {
                    var retVal = tagsinput[arg1](arg2, null, arg3);
                } else {
                    var retVal = tagsinput[arg1](arg2);
                }
                if (retVal !== undefined)
                    results.push(retVal);
            }
        });

        if (typeof arg1 == 'string') {
            // Return the results from the invoked function calls
            return results.length > 1 ? results : results[0];
        } else {
            return results;
        }
    };

    $.fn.tagsinput.Constructor = TagsInput;

    /**
     * Most options support both a string or number as well as a function as
     * option value. This function makes sure that the option with the given
     * key in the given options is wrapped in a function
     */
    function makeOptionItemFunction(options, key) {
        if (typeof options[key] !== 'function') {
            var propertyName = options[key];
            options[key] = function (item) {
                return item[propertyName];
            };
        }
    }

    function makeOptionFunction(options, key) {
        if (typeof options[key] !== 'function') {
            var value = options[key];
            options[key] = function () {
                return value;
            };
        }
    }

    /**
     * HtmlEncodes the given value
     */
    var htmlEncodeContainer = $('<div />');

    function htmlEncode(value) {
        if (value) {
            value = htmlEncodeContainer.text(value).html();
            value = value.replace(/[\"]/g, '&quot;');
            return value;
        } else {
            return '';
        }
    }

    /**
     * Returns the position of the caret in the given input field
     * http://flightschool.acylt.com/devnotes/caret-position-woes/
     */
    function doGetCaretPosition(oField) {
        var iCaretPos = 0;
        if (document.selection) {
            oField.focus();
            var oSel = document.selection.createRange();
            oSel.moveStart('character', -oField.value.length);
            iCaretPos = oSel.text.length;
        } else if (oField.selectionStart || oField.selectionStart == '0') {
            iCaretPos = oField.selectionStart;
        }
        return (iCaretPos);
    }

    /**
     * Returns boolean indicates whether user has pressed an expected key combination.
     * @param object keyPressEvent: JavaScript event object, refer
     *     http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
     * @param object lookupList: expected key combinations, as in:
     *     [13, {which: 188, shiftKey: true}]
     */
    function keyCombinationInList(keyPressEvent, lookupList) {
        var found = false;
        $.each(lookupList, function (index, keyCombination) {
            if (typeof (keyCombination) === 'number' && keyPressEvent.which === keyCombination) {
                found = true;
                return false;
            }

            if (keyPressEvent.which === keyCombination.which) {
                var alt = !keyCombination.hasOwnProperty('altKey') || keyPressEvent.altKey === keyCombination.altKey,
                    shift = !keyCombination.hasOwnProperty('shiftKey') || keyPressEvent.shiftKey === keyCombination.shiftKey,
                    ctrl = !keyCombination.hasOwnProperty('ctrlKey') || keyPressEvent.ctrlKey === keyCombination.ctrlKey;
                if (alt && shift && ctrl) {
                    found = true;
                    return false;
                }
            }
        });

        return found;
    }

    /**
     * Initialize tagsinput behaviour on inputs and selects which have
     * data-role=tagsinput
     */
    $(function () {
        $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
    });
})(window.jQuery);


//Blogs
(function ($) {
    $.fn.sq_TopMenu = function (options) {
        var $this = this;

        /**
         * Set the fields in vars
         */
        var settings = $.extend({
            'called': 'normal',
            'sq_settings_form': $this.find('.sq_settings_form'),
            'editButton': $this.find('.sq_edit'),
            'saveButton': $this.find('.sq_save'),
            'cancelButton': $this.find('.sq_cancel'),
            //'previewButton': $this.find('#sq_preview'),
            'last_tab': null,
            //'previewing': null,
            //'is_preview': false,
            'closeButton': $this.find('#sq_close'),
            'sq_url': $this.find('#sq_url'),
            'sq_doseo': $this.find('input[name=sq_doseo]'),
            'sq_toggle': $this.find('.sq-toggle'),
            /* ==== meta inputs ==== */
            'sq_title': $this.find('#sq_title'),
            'sq_description': $this.find('#sq_description'),
            'sq_keywords': $this.find('#sq_keywords'),
            'sq_noindex': $this.find('input[name=sq_noindex]'),
            'sq_nofollow': $this.find('input[name=sq_nofollow]'),
            'sq_nositemap': $this.find('input[name=sq_nositemap]'),
            'sq_canonical': $this.find('#sq_canonical'),
            /* ==== og inputs ==== */
            'sq_og_media': $this.find('#sq_og_media'),
            'sq_og_media_preview': $this.find('#sq_og_media_preview'),
            'og_image_close': $this.find('.og_image_close'),
            'sq_og_title': $this.find('#sq_og_title'),
            'sq_og_description': $this.find('#sq_og_description'),
            'sq_og_author': $this.find('#sq_og_author'),
            'sq_og_type': $this.find('#sq_og_type'),
            'sq_og_pixel': $this.find("#sq_og_pixel_id"),
            /* ==== tw inputs ==== */
            'sq_tw_media': $this.find('#sq_tw_media'),
            'sq_tw_media_preview': $this.find('#sq_tw_media_preview'),
            'tw_image_close': $this.find('.tw_image_close'),
            'sq_tw_title': $this.find('#sq_tw_title'),
            'sq_tw_description': $this.find('#sq_tw_description'),

            'previewTab': $this.find('.sq_tab_preview'),
            'editTab': $this.find('.sq_tab_edit'),

            'validKeyword': false,
            '__sq_save_message': (typeof __sq_save_message !== 'undefined' ? __sq_save_message : 'Saved!'),
            '__sq_save_message_preview': (typeof __sq_save_message_preview !== 'undefined' ? __sq_save_message_preview : 'Saved! Reload to see the changes.')

        }, options);


        /**
         * Remove the Wordpress Events and Add the Qss events
         */
        $this.initNav = function () {
            if ($('#sq_blocksnippet').length > 0) {
                //remove the hover event from Wordpress
                $this.off("hover");
                settings.closeButton.hide();
            } else {
                //remove the hover event from Wordpress
                $this.off("hover");
                //check the top menu from Wordpress
                $this.find('.ab-item').on("click", function () {
                    $this.addClass('open');
                });
                settings.closeButton.on("click", function () {
                    $this.removeClass('open');
                    $this.removeClass('hover');
                });

                $this.find('#sq_settings_body').show();
            }

            //Enable a feature if disabled
            $this.find('.sq_disabled').on('click', function () {
                $option = $(this);
                $option.addClass('sq_minloading');
                $.post(sqQuery.ajaxurl,
                    {
                        "action": 'sq_settingsseo_option',
                        "option": $option.data('option'),
                        "value": $option.data('value'),
                        "nonce": sqQuery.nonce
                    }, function () {
                    }
                ).done(function (response) {
                    $option.removeClass('sq_minloading');
                    if (typeof response !== 'undefined') {
                        if (typeof response.saved !== 'undefined') {
                            if ($option.data('option') === 'sq_use') {
                                $.sq_loadFrontMenu();
                            } else {
                                $option.removeClass('sq_disabled');
                                $option.off('click');
                            }
                        } else if (typeof response.error !== 'undefined') {
                            alert(response.error);
                        }
                    } else {
                        $option.removeClass('sq_minloading');
                    }
                }).fail(function () {
                    $option.removeClass('sq_minloading');
                });
            });
        };

        /**
         * Listen the DOSEO button and hide the snippet option when needed
         */
        $this.listenDoSeo = function () {
            //Listen the DoSeo button
            $this.find('#sq_doseo_on').on('change', function () {
                $this.saveSEO();
            });

            $this.find('#sq_doseo_off').on('change', function () {
                $this.saveSEO();
            });

            //Check if the SEO is activated for the current page
            if (!$('input[name=sq_doseo]').prop("checked")) {
                $this.find('.sq_showhide').hide();
                $this.find('.sq_hideshow').show();

                settings.previewTab.hide();
                settings.editTab.show();
                settings.cancelButton.hide();

                $this.find('.sq_optimize_page_activate').on('click', function () {
                    settings.previewTab.show();
                    settings.editTab.hide();
                    settings.cancelButton.show();

                    $this.find('input[name=sq_doseo]').prop('checked', true);
                    $this.find('#sq_doseo_on').trigger('click');
                });
            }

        };

        $this.tabsListen = function () {
            /* =========== Tabs ============= */
            $this.find('#sq_tabs').find('li').on('click', function (event) {
                event.preventDefault();

                $li = $(this);
                $this.find('#sq_tabs').find('li').each(function () {
                    $(this).removeClass('active');
                });
                $this.find('.sq_tabcontent').each(function () {
                    $(this).hide();
                });

                //settings.is_preview = false;
                //settings.previewButton.val("PREVIEW");
                $this.find('#sq_tab_' + $li.find('a').text().toString().toLowerCase()).show();
                $li.addClass('active');
            });
        };

        /**
         * Save the SEO into database
         * Send Sanitize and ajax to SQ_Settings
         */
        $this.saveSEO = function () {
            $this.preventLeave(false);
            settings.saveButton.addClass('sq_minloading');

            var $sq_hash = $this.find('#sq_hash');
            if ($sq_hash.val() !== '') {

                $.post(sqQuery.ajaxurl,
                    {
                        "action": "sq_saveseo",
                        "sq_title": settings.sq_title.length > 0 ? $this.escapeHtml(settings.sq_title.val()) : false,
                        "sq_description": settings.sq_description.length > 0 ? $this.escapeHtml(settings.sq_description.val()) : false,
                        "sq_keywords": settings.sq_keywords.length > 0 ? $this.escapeHtml(settings.sq_keywords.val()) : false,
                        "sq_canonical": settings.sq_canonical.length > 0 ? $this.escapeHtml(settings.sq_canonical.val()) : false,
                        //
                        "sq_noindex": settings.sq_noindex.length > 0 ? parseInt($this.find('input[name=sq_noindex]:checked').val()) : false,
                        "sq_nofollow": settings.sq_nofollow.length > 0 ? parseInt($this.find('input[name=sq_nofollow]:checked').val()) : false,
                        "sq_nositemap": settings.sq_nositemap.length > 0 ? parseInt($this.find('input[name=sq_nositemap]:checked').val()) : false,
                        //
                        "sq_tw_title": settings.sq_tw_title.length > 0 ? $this.escapeHtml(settings.sq_tw_title.val()) : false,
                        "sq_tw_description": settings.sq_tw_description.length > 0 ? $this.escapeHtml(settings.sq_tw_description.val()) : false,
                        "sq_tw_media": settings.sq_tw_media.length > 0 ? settings.sq_tw_media.val() : false,
                        //
                        "sq_og_title": settings.sq_og_title.length > 0 ? $this.escapeHtml(settings.sq_og_title.val()) : false,
                        "sq_og_description": settings.sq_og_description.length > 0 ? $this.escapeHtml(settings.sq_og_description.val()) : false,
                        "sq_og_type": settings.sq_og_type.length > 0 ? settings.sq_og_type.find('option:selected').val() : 'website',
                        "sq_og_author": settings.sq_og_author.length > 0 ? $this.escapeHtml(settings.sq_og_author.val()) : false,

                        "sq_og_media": settings.sq_og_media.length > 0 ? settings.sq_og_media.val() : false,
                        //
                        // "sq_page_tw_media": _sq_page_tw_media,
                        "sq_url": settings.sq_url.length > 0 ? $this.escapeHtml(settings.sq_url.val()) : false,
                        "sq_hash": $sq_hash.val(),
                        //
                        "post_id": ($('input[name=sq_post_id]').length > 0 ? $('input[name=sq_post_id]').val() : 0),
                        "term_taxonomy_id": ($('input[name=sq_term_taxonomy_id]').length > 0 ? $('input[name=sq_term_taxonomy_id]').val() : 0),
                        "taxonomy": ($('input[name=sq_taxonomy]').length > 0 ? $('input[name=sq_taxonomy]').val() : 0),

                        "sq_doseo": parseInt($this.find('input[name=sq_doseo]:checked').val()),
                        "nonce": sqQuery.nonce
                    }, function () {
                    }
                ).done(function (response) {
                    settings.saveButton.removeClass('sq_minloading');
                    if (typeof response.saved !== 'undefined') {
                        if (typeof response !== 'undefined') {
                            if (typeof response.html !== 'undefined') {
                                var $ctab = $this.find('#sq_tabs').find('li.active a').data('tab');
                                $this.find('#sq_settings_body').replaceWith(response.html);
                                $this.sq_TopMenu({'called': 'ajax'});
                                $this.find('#sq_tabs').find('li a.' + $ctab).trigger('click');

                                //Snippet is loaded. Let SLA know
                                $('#sq_blocksnippet').trigger('sq_snippet_loaded');
                            } else {
                                location.reload();
                            }
                        } else {
                            settings.saveButton.removeClass('sq_minloading');
                            $this.showError(2000);
                        }

                        $this.showSaved(2000, $ctab);
                    }
                    else {
                        $this.showError(2000);
                    }
                }).fail(function () {
                    settings.saveButton.removeClass('sq_minloading');
                    $this.showError(2000);
                });
            }
        };

        $this.showSaved = function (time, tab) {
            if (tab === 'tab4') {
                $this.find('#sq_settings_body_content').prepend('<div class="sq_savenotice sq_absolute" ><span class="sq_success">' + settings.__sq_save_message + '</span></div>');
            } else {
                $this.find('#sq_settings_body_content').prepend('<div class="sq_savenotice sq_absolute" ><span class="sq_success">' + settings.__sq_save_message_preview + '</span></div>');
            }
            if (typeof time !== 'undefined') {
                setTimeout(function () {
                    jQuery('.sq_savenotice').hide();
                }, time);
            }
        };

        $this.showError = function (time) {
            $this.find('#sq_settings_body_content').prepend('<div class="sq_savenotice sq_absolute" ><span class="sq_warning">ERROR! Could not save the data. Please refresh</span></div>');
            if (typeof time !== 'undefined') {
                setTimeout(function () {
                    jQuery('.sq_savenotice').hide();
                }, time);
            }
        };

        /**
         * Populates all titles and descriptions
         */
        $this.populateInputs = function () {
            var $title = $(document).find("head title").text();
            if (!$title) $title = '';

            var $description = $('meta[name="description"]').attr('content');
            if (!$description) $description = '';

            /* Meta Inputs */
            if ($this.find('.sq-title-value').length > 0) {
                if ($('#sq_blocksnippet').length == 0 && $this.find('.sq-title-value').parents('.sq_disabled').length > 0) {
                    $this.find('.sq-title-value').text($title);
                    $this.find('.sq-title-value').attr('title', $title);
                    $this.find('#sq_tab_meta').find('#sq_snippet_title').text($title);
                }
                $this.find('.sq-value.sq-title-value').sq_checkMax();
            } else if ($this.find('#sq_title').length > 0) {
                $this.find('#sq_title').sq_checkMax();
            }
            if ($this.find('.sq-description-value').length > 0) {
                if ($('#sq_blocksnippet').length == 0 && $this.find('.sq-description-value').parents('.sq_disabled').length > 0) {
                    $this.find('.sq-description-value').text($description);
                    $this.find('.sq-description-value').attr('title', $description);
                    $this.find('#sq_tab_meta').find('#sq_snippet_description').text($description);
                }
                $this.find('.sq-value.sq-description-value').sq_checkMax();
            } else if ($this.find('#sq_description').length > 0) {
                $this.find('#sq_description').sq_checkMax();
            }

            if ($this.find('#sq_tab_facebook').find('.sq_disabled').length > 0) {
                $this.find('#sq_tab_facebook').find('#sq_snippet_title').text($this.find('#sq_tab_meta').find('#sq_snippet_title').text());
                $this.find('#sq_tab_facebook').find('#sq_snippet_description').text($this.find('#sq_tab_meta').find('#sq_snippet_description').text());
            }

            if ($this.find('#sq_tab_twitter').find('.sq_disabled').length > 0) {
                $this.find('#sq_tab_twitter').find('#sq_snippet_title').text($this.find('#sq_tab_meta').find('#sq_snippet_title').text());
                $this.find('#sq_tab_twitter').find('#sq_snippet_description').text($this.find('#sq_tab_meta').find('#sq_snippet_description').text());
            }

            if (settings.sq_og_media_preview && settings.sq_og_media.val() !== '') {
                settings.sq_og_media_preview.attr('src', settings.sq_og_media.val());
                settings.og_image_close.show();
            }

            settings.og_image_close.on('click', function () {
                settings.sq_og_media_preview.attr('src', '');
                settings.sq_og_media.val('');
                $(this).hide();
            });

            if (settings.sq_tw_media_preview && settings.sq_tw_media.val() !== '') {
                settings.sq_tw_media_preview.attr('src', settings.sq_tw_media.val());
                settings.tw_image_close.show();
            }

            settings.tw_image_close.on('click', function () {
                settings.sq_tw_media_preview.attr('src', '');
                settings.sq_tw_media.val('');
                $(this).hide();
            });

            $this.find('#sq_blocksnippet_refresh').on('click', function () {
                $.sq_loadFrontMenu();
            });

            $this.keywordsListen();

            //Listen the Edit Button
            settings.editButton.on('click', function () {
                settings.previewTab.hide();
                settings.editTab.show();

                //Listen the Cancel Button
                settings.cancelButton.on('click', function () {
                    settings.previewTab.show();
                    settings.editTab.hide();

                });
            });

            //Add the pattens in the right side of the input/textarea
            if ($.isFunction($.fn.sq_patterns)) {
                //call the patterns after save
                $('.sq_pattern_field').each(function () {
                    $(this).sq_patterns();
                });
            }
        };

        /**
         * Listen the Image Media from Wordpress
         */
        $this.mediaListen = function () {
            $('#sq_get_og_media, #sq_get_tw_media').click(function (e) {

                e.preventDefault();

                var btn_id = this.id;

                var image_frame;
                if (image_frame) {
                    image_frame.open();
                }
                // Define image_frame as wp.media object
                image_frame = wp.media({
                    title: 'Select Media',
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                image_frame.on('close', function () {
                    // On close, get selections and save to the hidden input
                    // plus other AJAX stuff to refresh the image preview
                    var selection = image_frame.state().get('selection');
                    var gallery_ids = null;
                    var my_index = 0;
                    selection.each(function (attachment) {
                        gallery_ids = attachment['attributes']['url'];
                        my_index++;
                    });
                    if (btn_id === 'sq_get_tw_media' && gallery_ids !== null) {
                        settings.sq_tw_media.val(gallery_ids);
                        settings.sq_tw_media_preview.attr('src', gallery_ids);
                        settings.tw_image_close.show();
                    }
                    else if (btn_id === 'sq_get_og_media' && gallery_ids !== null) {
                        settings.sq_og_media.val(gallery_ids);
                        settings.sq_og_media_preview.attr('src', gallery_ids);
                        settings.og_image_close.show();
                    }
                });
                image_frame.on('open', function () {
                    // On open, get the id from the hidden input
                    // and select the appropiate images in the media manager
                    var selection = image_frame.state().get('selection');
                });

                image_frame.open();
            });
        };

        //Init
        $this.dropDownListen = function () {
            var actionDivSelected, actionDiv, dropdown, input, next;
            settings.sq_toggle.on('focus', function () {
                $(this).trigger('click');
            });
            settings.sq_toggle.on('click', function () {
                input = $(this);
                dropdown = input.parent('.input-group').find(".sq-actions");
                if (dropdown.data('position') == 'top') {
                    dropdown.css('top', '-82px');
                    dropdown.css('height', '80px');
                } else if (dropdown.data('position') == 'small') {
                    dropdown.css('top', '35px');
                    dropdown.css('height', '36px');
                }
                actionDiv = dropdown.find(".sq-action");
                dropdown.show();

                actionDiv.on('click keyup', function (e) {
                    if (typeof actionDivSelected !== 'undefined' && e.which !== 1) {
                        var actionValue = actionDivSelected.find('.sq-value');
                    } else {
                        var actionValue = $(this).find('.sq-value');
                    }

                    if (typeof actionValue !== "undefined" && actionValue !== "") {
                        if (e.which === 13 || e.which === 1) {
                            if ($(this).hasClass("focused")) {
                                $(this).removeClass("focused");
                            }
                        }

                        //Set the Value
                        input.val(actionValue.html());
                        input.sq_checkMax();
                    }

                });

                input.outside("click", function () {
                    $(this).parent('.input-group').find(".sq-actions").hide();
                });
            });

            settings.sq_toggle.on('click keydown', function (e) {
                //If enter press, trigger click for this filter
                if (e.which === 13) {
                    dropdown.find(".sq-action.focused").trigger('click');
                    return false;
                }

                if (e.which === 27) {
                    $this.find(".sq-actions").hide();
                }

                //Listen for arrows.
                if (e.which === 40) {
                    if ($this.find(".sq-actions").is(':visible')) {
                        if (actionDivSelected) {
                            actionDivSelected.removeClass('focused');
                            next = actionDivSelected.nextAll().first();
                            if (next.length > 0) {
                                actionDivSelected = next.addClass('focused');
                            } else {
                                actionDivSelected = actionDiv.eq(0).addClass('focused');
                            }
                        } else {
                            actionDivSelected = actionDiv.eq(0).addClass('focused');
                        }
                        actionDiv.trigger('keyup');
                    }
                } else if (e.which === 38) {
                    if ($this.find(".sq-actions").is(':visible')) {
                        if (actionDivSelected) {
                            actionDivSelected.removeClass('focused');
                            next = actionDivSelected.prevAll().first();
                            if (next.length > 0) {
                                actionDivSelected = next.addClass('focused');
                            } else {
                                actionDivSelected = actionDiv.last().addClass('focused');
                            }
                        } else {
                            actionDivSelected = actionDiv.last().addClass('focused');
                        }
                        actionDiv.trigger('keyup');
                    }
                } else if (e.which > 1) {
                    $this.find(".sq-actions").hide();
                }

            });

        };

        $this.keywordsListen = function () {
            settings.sq_keywords.tagsinput('items');
        };

        $this.escapeHtml = function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.toString().replace(/[&<>"']/g, function (m) {
                return map[m];
            });
        };

        $this.preventLeave = function (on) {
            //Only if there is outside th eeditor
            if ($('form#post').length == 0) {
                if (on) {
                    $(window).on('beforeunload', function () {
                        return confirm('You have unsave changes in Squirrly Snippet. Are you sure you want to proceed?');
                    });
                } else {
                    $(window).off('beforeunload');
                }
            }
        };


        //Initiate the Nav events
        $this.initNav();
        $this.listenDoSeo();

        // Uploading files
        $this.mediaListen();
        $this.tabsListen();
        $this.populateInputs();
        $this.dropDownListen();

        $this.find('input[type="text"], textarea').on('keyup paste', function () {
            $this.preventLeave(true);
            $(this).sq_checkMax();
        });

        settings.saveButton.on('click', function (event) {
            event.preventDefault();
            console.log('save');

            $this.preventLeave(false);
            $this.saveSEO();
        });

        if (typeof $.sq_blockseo !== 'undefined') {
            $.sq_blockseo.on('sq_seo_refresh', function () {
                settings.sq_settings_form.submit();
            });
        }
        return $this;
    };

    $.fn.sq_checkMax = function () {
        var $element = $(this);
        var val;

        if (!$element.length > 0)
            return;

        if ($element.is('input, textarea')) {
            val = $element.val();
        } else {
            val = $element.html();
        }

        var valLength = val.length;
        var maxCount = parseInt($element.parents('.row:last').find('.sq_length').data('maxlength'));

        $element.parents('.row:last').find('.sq_length').text(valLength);

        if (valLength === 0 || valLength > maxCount) {
            $element.css('border', 'solid 1px red');
        } else {
            $element.css('border', 'solid 1px white');
        }
    };

    $.fn.outside = function (ename, cb) {
        return this.each(function () {
            var $this = $(this),
                self = this;

            $(document).on(ename, function sqtempo(e) {
                if (e.target !== self && !$.contains(self, e.target)) {
                    cb.apply(self, [e]);
                    $(document).off(ename, sqtempo);
                }
            });

            $this.on('keydown blur', function sqtabpress(e) {
                if (e.which === 9) {
                    cb.apply(self, [e]);
                    $this.off('keydown', sqtabpress);
                }
            });
        });
    };

    $.fn.toggleSwitch = function (checked) {
        var element = $(this);

        if ((element.prop('checked') && checked == false) || (!element.prop('checked') && checked == true)) {
            element.trigger('click');
        }
    };

    $.sq_loadFrontMenu = function () {

        if ($('body.wp-admin').find('input[name=post_ID]').length > 0) {
            $('#sq_blocksnippet').find('.inside').html('');
            $('#sq_blocksnippet').parent().prepend($('#sq_blocksnippet'));
            $.sq_getFrontMenu($('input[name=post_ID]').val(), $('input[name=post_type]').val());
            $('#wp-admin-bar-sq_bar_menu').find('.ab-item').on("click", function () {
                if ($('.edit-post-layout__content').length > 0) {
                    $('.edit-post-layout__content').scrollTop($('.edit-post-layout__content').scrollTop() + $('#sq_blocksnippet').offset().top - 50);
                } else {
                    $('html,body').scrollTop($('#sq_blocksnippet').offset().top - 50);
                }
            });
        }

        if ($('body.wp-admin').find('input[name=tag_ID]').length > 0) {
            $('#sq_blocksnippet').find('.inside').html('');
            $.sq_getFrontMenuTax($('input[name=tag_ID]').val(), $('input[name=taxonomy]').val());
            $('#wp-admin-bar-sq_bar_menu').find('.ab-item').on("click", function () {
                if ($('.edit-post-layout__content').length > 0) {
                    $('.edit-post-layout__content').scrollTop($('.edit-post-layout__content').scrollTop() + $('#sq_blocksnippet').offset().top - 50);
                } else {
                    $('html,body').scrollTop($('#sq_blocksnippet').offset().top - 50);
                }
            });
        }
    };

    $.sq_getFrontMenu = function (post_id, post_type) {
        if (post_id) {
            if ($('#sq_blocksnippet').length > 0) {
                $('#sq_blocksnippet').addClass('sq_minloading');
            }

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_getfrontmenu',
                    post_id: post_id,
                    post_type: post_type,
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                if (typeof response !== 'undefined') {
                    if (typeof response.html !== 'undefined') {
                        if ($('#sq_blocksnippet').length == 0) {
                            $('#wp-admin-bar-sq_bar_menu').find('.inside').html(response.html);
                            $('#wp-admin-bar-sq_bar_menu').sq_TopMenu();
                        } else {
                            //if embeded in post edit
                            $('#sq_blocksnippet').find('.inside').html(response.html);
                            $('#sq_blocksnippet').sq_TopMenu();
                            $('#sq_blocksnippet').parent().prepend($('#sq_blocksnippet'));
                        }

                        //Snippet is loaded. Let SLA know
                        $('#sq_blocksnippet').trigger('sq_snippet_loaded');
                    }
                } else {
                    $('#sq_blocksnippet').removeClass('sq_minloading');
                }

                if ($('#sq_blocksnippet').length > 0) {
                    $('#sq_blocksnippet').removeClass('sq_minloading');
                    $('#sq_blocksnippet').find('#sq_settings_body').show();
                }
            }).fail(function () {
                $('#sq_blocksnippet').removeClass('sq_minloading');
            });
        }
    };

    $.sq_getFrontMenuTax = function (term_taxonomy_id, taxonomy) {

        if (term_taxonomy_id) {
            if ($('#sq_blocksnippet').length > 0) {
                $('#sq_blocksnippet').addClass('sq_minloading');
            }

            $.post(
                sqQuery.ajaxurl,
                {
                    action: 'sq_getfrontmenu',
                    term_taxonomy_id: term_taxonomy_id,
                    taxonomy: taxonomy,
                    nonce: sqQuery.nonce
                }
            ).done(function (response) {
                if (typeof response !== 'undefined') {
                    if (typeof response.html !== 'undefined') {
                        if ($('#sq_blocksnippet').length == 0) {
                            $('#wp-admin-bar-sq_bar_menu').find('.inside').html(response.html);
                            $('#wp-admin-bar-sq_bar_menu').sq_TopMenu();
                        } else {
                            //if embeded in post edit
                            $('#sq_blocksnippet').find('.inside').html(response.html);
                            $('#sq_blocksnippet').sq_TopMenu();

                            $('#sq_blocksnippet').find('#sq_blocksnippet_refresh').on('click', function () {
                                $.sq_loadFrontMenu();
                            });
                        }

                        //Snippet is loaded. Let SLA know
                        $('#sq_blocksnippet').trigger('sq_snippet_loaded');
                    }
                } else {
                    $('#sq_blocksnippet').removeClass('sq_minloading');
                }

                if ($('#sq_blocksnippet').length > 0) {
                    $('#sq_blocksnippet').removeClass('sq_minloading');
                    $('#sq_blocksnippet').find('#sq_settings_body').show();
                }
            }).fail(function () {
                $('#sq_blocksnippet').removeClass('sq_minloading');
            });
        }
    };

    $(document).ready(function () {
        //li id from topbar
        $('#wp-admin-bar-sq_bar_menu').sq_TopMenu();


        if ($('#wp-admin-bar-sq_bar_menu').length > 0 && $('body.wp-admin').find('input[name=tag_ID]').length > 0) {
            $('#edittag').after($('<div id="sq_blocksnippet" class="postbox "><div class="inside"></div></div>'));
        }

        if ($('#sq_blocksnippet').length > 0) {
            $(document).on('after-autosave.update-post-slug', function (e, data) {
                if ($('input[name=post_ID]').length > 0) {
                    if ($('#sq_blocksnippet').find("#sq_save").length > 0) {
                        $('#sq_blocksnippet').find("#sq_save").trigger('click');
                    }
                }
            });

            //Load the FrontMenu in Snippet
            $.sq_loadFrontMenu();

        }
    });

})(jQuery);