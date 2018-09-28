/** global: Craft */
/** global: Garnish */
/**
 * Organization User Select input
 */
Craft.OrganizationUserSelectInput = Garnish.Base.extend({

    modal: null,
    elements: [],

    $container: null,
    $addElementBtn: null,

    _initialized: false,

    init: function (settings) {
        this.setSettings(settings, Craft.OrganizationUserSelectInput.defaults);

        if (settings.elements.length) {
            this.addElements(settings.elements);
        }

        // Apply the storage key prefix
        if (this.settings.modalStorageKey) {
            this.modalStorageKey = 'BaseElementSelectInput.' + this.settings.modalStorageKey;
        }

        this.$container = $('#' + this.settings.id);
        this.$addElementBtn = this.$container.children('.btn.add');

        if (this.$addElementBtn && this.settings.limit === 1) {
            this.$addElementBtn
                .css('position', 'absolute')
                .css('top', 0)
                .css(Craft.left, 0);
        }

        if (this.$addElementBtn) {
            this.addListener(this.$addElementBtn, 'activate', 'showModal');
        }

        this._initialized = true;
    },

    canAddMoreElements: function () {
        return (!this.settings.limit || this.$elements.length < this.settings.limit);
    },

    updateAddElementsBtn: function () {
        if (this.canAddMoreElements()) {
            this.enableAddElementsBtn();
        } else {
            this.disableAddElementsBtn();
        }
    },

    disableAddElementsBtn: function () {
        if (this.$addElementBtn && !this.$addElementBtn.hasClass('disabled')) {
            this.$addElementBtn.addClass('disabled');

            if (this.settings.limit === 1) {
                if (this._initialized) {
                    this.$addElementBtn.velocity('fadeOut', Craft.BaseElementSelectInput.ADD_FX_DURATION);
                } else {
                    this.$addElementBtn.hide();
                }
            }
        }
    },

    enableAddElementsBtn: function () {
        if (this.$addElementBtn && this.$addElementBtn.hasClass('disabled')) {
            this.$addElementBtn.removeClass('disabled');

            if (this.settings.limit === 1) {
                if (this._initialized) {
                    this.$addElementBtn.velocity('fadeIn', Craft.BaseElementSelectInput.REMOVE_FX_DURATION);
                } else {
                    this.$addElementBtn.show();
                }
            }
        }
    },

    addElements: function (elements) {
        elements = $.makeArray(elements);

        for (var i = 0; i < elements.length; i++) {
            this.addElement(elements[i]);
        }
    },

    addElement: function (element) {
        var elementId = this._getElementId(element);
        if (elementId === null) {
            return;
        }
        this.elements.push(elementId);

        this.updateAddElementsBtn();
        this.onAddElement(elementId);
    },

    removeElements: function (elements) {
        elements = $.makeArray(elements);

        for (var i = 0; i < elements.length; i++) {
            this.removeElement(elements[i]);
        }
    },

    removeElement: function (element) {
        var elementId = this._getElementId(element);
        if (elementId === null) {
            return;
        }

        if (true === Craft.removeFromArray(elementId, this.elements)) {
            if (this.modal) {
                this.modal.elementIndex.enableElementsById([elementId]);
            }

            this.updateAddElementsBtn();
            this.onRemoveElement(elementId);
        }
    },

    showModal: function () {
        if (!this.canAddMoreElements()) {
            return;
        }

        if (!this.modal) {
            this.modal = this.createModal();
        } else {
            this.modal.show();
        }
    },

    createModal: function () {
        return Craft.createElementSelectorModal(this.settings.elementType, this.getModalSettings());
    },

    getModalSettings: function () {
        return $.extend({
            closeOtherModals: false,
            storageKey: this.modalStorageKey,
            sources: this.settings.sources,
            criteria: this.settings.criteria,
            multiSelect: (this.settings.limit !== 1),
            showSiteMenu: this.settings.showSiteMenu,
            disabledElementIds: this.getDisabledElementIds(),
            onSelect: $.proxy(this, 'onModalSelect')
        }, this.settings.modalSettings);
    },

    getSelectedElementIds: function () {
        return this.elements;
    },

    getDisabledElementIds: function () {
        var ids = this.getSelectedElementIds();

        if (this.settings.sourceElementId) {
            ids.push(this.settings.sourceElementId);
        }

        return ids;
    },

    onModalSelect: function (elements) {
        if (this.settings.limit) {
            var slotsLeft = this.settings.limit - this.$elements.length;

            if (elements.length > slotsLeft) {
                elements = elements.slice(0, slotsLeft);
            }
        }

        this.selectElements(elements);
        this.updateDisabledElementsInModal();
    },

    getData: function () {
        var data = $.extend({}, this.settings.params);

        if (this.settings.attributes) {
            data.attributes = this.settings.attributes;
        }

        return data;
    },

    _getElementId: function (element) {
        if (isNaN(element)) {
            if (typeof element === 'object') {
                if (element.hasOwnProperty('id')) {
                    return parseFloat(element.id);
                }

                var id = $(element).data('id');
                if (id) {
                    return parseFloat(id);
                }
            }

            return;
        }

        return parseFloat(element);
    },

    selectElements: function (elements) {
        for (var i = 0; i < elements.length; i++) {
            var elementId = this._getElementId(elements[i]);

            if (elementId === null) {
                continue;
            }

            var data = this.getData();

            // Payload data
            data.user = elementId;
            data.organization = this.settings.sourceElementId;

            Craft.actionRequest('POST', this.settings.addAction, data, $.proxy(function (response, textStatus, jqXHR) {
                if (jqXHR.status === 204) {
                    Craft.cp.displayNotice(
                        Craft.t('organizations', 'Association successful')
                    );

                    // Add element
                    this.addElement(elementId);
                    Craft.elementIndex.updateElements();
                    this.updateDisabledElementsInModal();

                    this.onSelectElements(elements);
                } else {
                    Craft.cp.displayError(
                        Craft.t('organizations', 'Association failed')
                    );
                }
            }, this));
        }

        this.onSelectElements(elements);
    },
    updateDisabledElementsInModal: function () {
        if (this.modal.elementIndex) {
            this.modal.elementIndex.disableElementsById(this.getDisabledElementIds());
        }
    },

    onSelectElements: function (elements) {
        this.trigger('selectElements', {elements: elements});
        this.settings.onSelectElements(elements);
    },

    onAddElement: function (element) {
        this.trigger('addElement', {element: element});
        this.settings.onAddElement(element);
    },

    onRemoveElement: function (element) {
        this.trigger('removeElement', {element: element});
        this.settings.onRemoveElement(element);
    }

}, {

    ADD_FX_DURATION: 200,
    REMOVE_FX_DURATION: 200,

    defaults: {
        id: null,
        elementType: null,
        sources: null,
        criteria: {},
        sourceElementId: null,
        viewMode: 'list',
        limit: null,
        showSiteMenu: false,
        modalStorageKey: null,
        modalSettings: {},

        onSelectElements: $.noop,
        onAddElement: $.noop,
        onRemoveElement: $.noop,

        addAction: '',
        disabledElementIds: []
    }
});