'use strict';

define([
    'jquery',
    'underscore',
    'oro/translator',
    'routing',
    'pim/filter/filter',
    'pim/fetcher-registry',
    'pim/user-context',
    'pim/i18n',
    'text!pim/template/filter/attribute/multiselect',
    'jquery.select2'
], function (
    $,
    _,
    __,
    Routing,
    BaseFilter,
    FetcherRegistry,
    UserContext,
    i18n,
    template
) {
    return BaseFilter.extend({
        shortname: 'multi',
        template: _.template(template),
        choicePromise: null,
        events: {
            'change [name="filter-value"], [name="filter-operator"]': 'updateState'
        },

        /**
         * {@inherit}
         */
        initialize: function (config) {
            this.config = config.config;

            return BaseFilter.prototype.initialize.apply(this, arguments);
        },

        /**
         * {@inherit}
         */
        isEmpty: function () {
            return 'EMPTY' !== this.getOperator() &&
                (undefined === this.getValue() || '' === this.getValue());
        },

        /**
         * {@inherit}
         */
        renderInput: function () {
            return this.template({
                __: __,
                value: this.getValue(),
                field: this.getField(),
                operator: this.getOperator(),
                editable: this.isEditable(),
                operators: this.config.operators
            });
        },

        /**
         * {@inherit}
         */
        postRender: function (templateContext) {
            this.$('.operator').select2({
                minimumResultsForSearch: -1
            });

            if ('EMPTY' !== this.getOperator()) {
                this.$('.value').select2(templateContext.select2Options);
            }
        },

        /**
         * {@inherit}
         */
        getTemplateContext: function () {
            var field = this.getField().replace(/\.code$/, '');
            var fetchAttributePromise = FetcherRegistry.getFetcher('attribute').fetch(field);

            return fetchAttributePromise.then(function (attribute) {
                return this.getSelect2Options(attribute).then(function (select2Options) {
                    return {
                        label: i18n.getLabel(attribute.labels, UserContext.get('uiLocale'), attribute.code),
                        select2Options: select2Options,
                        removable: this.isRemovable(),
                        editable: this.isEditable()
                    };
                }.bind(this));
            }.bind(this));
        },

        /**
         * {@inherit}
         */
        updateState: function () {
            var cleanedValues = [];
            var operator = this.$('[name="filter-operator"]').val();

            if ('EMPTY' !== operator) {
                var value = this.$('[name="filter-value"]').val().split(/[\s,]+/);
                cleanedValues = _.reject(value, function (val) {
                    return '' === val;
                });
            }

            this.setData({
                field: this.getField(),
                operator: operator,
                value: cleanedValues
            });
        },

        /**
         * Return a promise which, once resolved, returns the choice options
         * reference data to populate the select2.
         *
         * @returns {Promise}
         */
        getSelect2Options: function (attribute) {
            return $.Deferred().resolve(
                Routing.generate(
                    'pim_ui_ajaxentity_list',
                    {
                        'class': 'PimCatalogBundle:AttributeOption',
                        'dataLocale': UserContext.get('uiLocale'),
                        'collectionId': attribute.id,
                        'options': {'type': 'code'}
                    }
                ))
                .then(function (choiceUrl) {
                    return {
                        ajax: {
                            url: choiceUrl,
                            cache: true,
                            data: function (term) {
                                return {
                                    search: term,
                                    options: {
                                        locale: UserContext.get('catalogLocale')
                                    }
                                };
                            },
                            results: function (data) {
                                return data;
                            }
                        },
                        initSelection: function (element, callback) {
                            if (null === this.choicePromise) {
                                this.choicePromise = $.get(choiceUrl);
                            }

                            this.choicePromise.then(function (response) {
                                var results = response.results;
                                var choices = _.map($(element).val().split(','), function (choice) {
                                    return _.findWhere(results, {id: choice});
                                });
                                callback(choices);
                            });
                        }.bind(this),
                        multiple: true
                    };
                }.bind(this));
        },

        /**
         * {@inherit}
         *
         * We override the getField method to add the trailing '.code' needed for the backend filter
         */
        getField: function () {
            var fieldName = BaseFilter.prototype.getField.apply(this, arguments);

            if (-1 === fieldName.indexOf('.code')) {
                fieldName += '.code';
            }

            return fieldName;
        }
    });
});
