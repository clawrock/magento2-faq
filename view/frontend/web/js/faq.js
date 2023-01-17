define([
    'jquery',
    'mage/template',
    'jquery/ui',
    'accordion',
    'mage/translate'
], function ($, mageTemplate) {
    'use strict';

    $.widget('clawrock.faq', {
        options: {
            limit: 10
        },

        _create: function () {
            this.element.find('.faq-questions').accordion({
                active: []
            });

            this.element.find('[data-category]').on('click', function (e) {
                e.preventDefault();
                $('[data-category]').removeClass('active');
                this.loadCategory($(e.currentTarget).addClass('active').data('category'));
            }.bind(this));

            this.element.find('form').on('submit', function (e) {
                e.preventDefault();
                $('[data-category]').removeClass('active');
                this.search($(e.currentTarget).find('input').val());
            }.bind(this));

            $('body').on('click', '#faq-questions-pagination a', function (e) {
                e.preventDefault();
                var page = $(e.currentTarget).addClass('active').data('page'),
                    form = this.element.find('form'),
                    data = {
                        currentPage: page,
                        categoryId: $('[data-category].active').data('category'),
                        query: $(form).find('input').val()
                    };

                $('#faq-questions-pagination a').removeClass('active');
                this.paginate(data);
            }.bind(this));
        },

        loadCategory: function (categoryId) {
            var query = this.element.find('form').find('input').val()
            this.fetch({
                criteria: {
                    filterGroups: [{
                        filters: [
                            {
                                field: 'category_id',
                                value: categoryId
                            },
                            {
                                field: 'question',
                                value: '%' + query + '%',
                                condition_type: 'like'
                            }
                        ]
                    },
                    {
                        filters: [
                            {
                                field: 'active',
                                value: 1
                            }
                        ]
                    }]
                }
            });
        },

        search: function (query) {
            this.fetch({
                criteria: {
                    filterGroups: [{
                        filters: [{
                            field: 'question',
                            value: '%' + query + '%',
                            condition_type: 'like'
                        }]
                    }, {
                        filters: [{
                            field: 'active',
                            value: 1
                        }]
                    }]
                }
            });
        },

        paginate: function (data) {
            var filters = [{
                    field: 'category_id',
                    value: data.categoryId
                }];

            if (data.query !== "") {
                filters = [{
                    field: 'question',
                    value: '%' + data.query + '%',
                    condition_type: 'like'
                }]
            }

            this.fetch({
                criteria: {
                    pageSize: this.options.limit,
                    currentPage: data.currentPage,
                    filterGroups: [{
                        filters: filters
                    }, {
                        filters: [{
                            field: 'active',
                            value: 1
                        }]
                    }]
                }
            }, true);
        },

        fetch: function (data, paginate) {
            var success = typeof paginate !== 'undefined' ? this.renderPaginatedQuestions.bind(this) : this.renderQuestions.bind(this);
            $.ajax({
                url: this.options.url,
                method: 'GET',
                showLoader: true,
                data: data,
                success: success
            });
        },

        renderPaginatedQuestions: function (response) {
            var template = mageTemplate('#faq-question-template'),
                $questionList = $('#clawrock-faq-questions ul'),
                i = 1,
                self = this;

            $questionList.remove();
            $questionList = $('<ul></ul>');
            $('#clawrock-faq-questions').prepend($questionList);
            if (!response.items.length) {
                $questionList.append($('<li><p>' + $.mage.__('No questions found.') + '</p></li>'));
                return;
            }
            this.items = response.items;

            response.items.forEach(function (el) {
                if (i++ > self.options.limit) {
                    return;
                }
                $questionList.append(template({
                    data: {
                        question: el.question,
                        answer: el.answer
                    }
                }));
            });

            $questionList.accordion();
        },

        renderQuestions: function (response) {
            var paginationTemplate = mageTemplate('#faq-question-pagination-template'),
                $paginationList = $('#faq-questions-pagination');

            this.renderPaginatedQuestions(response);

            $paginationList.html('');

            var pages = Math.ceil(response.items.length / this.options.limit);
            if (pages <= 1) {
                return;
            }
            for (var j = 1; j <= pages; j++) {
                $paginationList.append(paginationTemplate({
                    data: {
                        page: j
                    }
                }));
            }
        }
    });

    return $.clawrock.faq;
});
