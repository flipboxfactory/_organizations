/** global: Craft */
/** global: Garnish */
/**
 * User Category editor
 */

Craft.TableOrganizationUserIndexView = Craft.TableElementIndexView.extend({
    afterInit: function () {
        this.base();

        // Append our action to rows
        this.appendActionToRows(this.getAllElements());
    },

    initTableHeaders: function () {
        this.base();

        var $tr = $('<th />')
            .addClass('thin');

        this.$table.find('thead tr').append($tr);
    },


    appendElements: function ($newElements) {
        this.base($newElements);
        this.appendActionToRows($newElements);
    },

    appendActionToRows: function (rows) {
        for (var i = 0; i < rows.length; i++) {
            this.appendActionToRow(rows.eq(i));
        }
    },

    appendActionToRow: function (row) {
        var $action = $('<span />')
            .addClass('settings icon manage-categories hud-editor-toggle')
            .attr('title', 'Manage Categories')
            .attr('role', 'button');

        var $td = $('<td />').append($action);

        row.append($td);

        this.addListener($action, 'click', this.handleActionColumnClick);

        if ($.isTouchCapable()) {
            this.addListener($action, 'taphold', this.handleActionColumnClick);
        }
    },

    handleActionColumnClick: function (ev) {
        this.createCategoryEditor($(ev.target));
    },

    createCategoryEditor: function ($category) {
        var params = $.extend({}, this.elementIndex.settings.viewParams);
        params.user = $category.parents('tr').data('id');

        return new Craft.OrganizationUserCategoryEditor($category, {
            params: params,
            onSaveElement: $.proxy(function (response) {
                console.log(response);
            }, this)
        });
    }
});