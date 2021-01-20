(function ($) {
    'use strict';

    var Marmot = {

        translate: [],

        init: function () {
            Marmot.translate = MarmotData.translate;
            Marmot.initHqtButtons();
        },
        // HQ Tabs
        initHqtButtons: function () {
            // Install / Activate plugins
            $('[data-hqt-btn="install-activate-plugin"]').each(function (i, el) {
                var $button = $(this);

                // Change button text
                if ($button.data('action-label').length) {
                    var label = $button.html(), action;

                    if ($button.data('install-url').length) {
                        action = Marmot._('install');
                    } else {
                        action = Marmot._('activate');
                    }
                    switch ($button.data('action-label')) {
                        case 'prepend':
                            $button.html(action + ' ' + label);
                            break;
                        case 'replace':
                            $button.html(action);
                            break;
                    }
                }
                // Wrap button text in span tag
                if (!$button.find('.btn-label').length) {
                    $button.wrapInner('<span class="btn-label"></div>');
                }

                // Bind button click event
                Marmot.bindButtonClick($button);

                // Bind show/hide button loader
                $button.on('loader/show', function () {
                    $(this).off('click');
                    $(this).addClass('loading');
                });
                $button.on('loader/hide', function () {
                    Marmot.bindButtonClick($(this));
                    $(this).removeClass('loading');
                });
            });
        },
        bindButtonClick: function (button) {
            var $button = $(button);
            // Append some html for ajax spinner
            if (!$button.find('.hqt-btn-ellipsis').length) {
                $button.append('<span class="hqt-btn-ellipsis"><span></span><span></span><span></span><span></span></span>');
            }

            $button.on('click', function (e) {
                e.preventDefault();
                var $this = $(this);

                if ($this.data('install-url').length) {
                    // Install if plugin is missing
                    Marmot.installPluginByBtn($this);
                } else {
                    // Activate if plugin is installed
                    Marmot.activatePluginByBtn($this);
                }
            });
        },
        installPluginByBtn: function (button) {
            var $button = $(button);
            if (!$button.data('install-url').length) {
                return false;
            }
            $.ajax({
                url: $button.data('install-url'),
                type: 'GET',
                beforeSend: function (xhr) {
                    $button.data('install-url', '')
                    $button.trigger('loader/show')
                }
            }).complete(function (jqXHR) {
                $button.trigger('loader/hide')
            }).fail(function (jqXHR) {
                alert('Plugin installation fail. Please try again.')
            }).done(function (result) {
                $button.find('.btn-label').html(Marmot._('activate'))
                Marmot.bindButtonClick($button)
            });
        },
        activatePluginByBtn: function (button) {
            var $button = $(button);
            if (!$button.data('activate-url').length) {
                return false;
            }
            $.ajax({
                url: $button.data('activate-url'),
                type: 'GET',
                beforeSend: function (xhr) {
                    $button.trigger('loader/show')
                }
            }).complete(function (jqXHR) {
                $button.trigger('loader/hide')
            }).fail(function (jqXHR) {
                alert('Plugin activation fail. Please try again.');
            }).done(function (result) {
                Marmot.buttonCallbackAction($button);
            });
        },
        buttonCallbackAction: function (button) {
            var $button = $(button);
            if (!$button.data('callback').length) {
                return false;
            }
            switch ($button.data('callback')) {
                case 'refresh-page':
                    location.reload();
                    break;
                case 'replace-button-label-activated':
                    $button.replaceWith(Marmot._('activated'));
                    break;
            }
        },
        _: function (key) {
            if (Marmot.translate[key].length) {
                return Marmot.translate[key];
            }
            return false;
        }
    }

    $(document).ready(function () {
        Marmot.init();
    });
})(jQuery);