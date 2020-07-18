define([
    'jquery',
    'amrevloader'
], function ($, amloader) {

    $.widget('mage.amReviewCommentsLoad', {
        options: {
            updateUrl: '',
            submitUrl: '',
            wrapper: '[data-amreview-js="description-wrap"]',
            reviewEntity: '[data-amreview-js="review-entity"]',
            reviewContainer: '[data-amreview-js="review-container"]',
            commentBlock: '[data-amreview-js="comment-block-review-id-{review_id}"]',
            commentsBtn: '[data-amreview-js="comments"]',
            replyBtn: '[data-amreview-js="reply"]',
            listBlock: '[data-amreview-js="comment-list-block"]',
            list: '[data-amreview-js="comment-list"]',
            formBlock: '[data-amreview-js="comment-form-block"]',
            form: '[data-amreview-js="comment-form"]',
            qty: '[data-review-js="comment-qty"]',
            emptyClass: '-empty',
            attrs: {
                reivewId: 'data-review-id'
            }
        },

        _create: function () {
            var self = this;

            if ($(self.options.reviewEntity).length) {
                this._loadComments();
            }
            $('#product-review-container').on('contentUpdated', self._loadComments.bind(this));

            $(self.options.replyBtn).on('click', self.__openForm.bind(this));
            $(self.options.commentsBtn).on('click', self._openList.bind(this));
        },

        _loadComments: function () {
            var reviewIds = [],
                self = this,
                loader = amloader;

            $(this.options.reviewEntity).each(function (index, review) {
                reviewIds.push($(review).attr(self.options.attrs.reivewId));
            });

            if (reviewIds.length) {
                loader.init($('[data-amload-js="container"]'));

                $.ajax({
                    url: this.options.updateUrl,
                    cache: false,
                    dataType: 'json',
                    showLoader: false,
                    loaderContext: $('.product.data.items'),
                    type: 'get',
                    data: {
                        review_ids: reviewIds
                    }
                }).done(function (data) {

                    loader.stop($('[data-amload-js="container"]'));

                    if (typeof data.error === 'undefined') {
                        var formKey = $.mage.cookies.get('form_key'),
                            commentBlock = null;

                        $.each(data.items, function (reviewId, comment) {
                            commentBlock = $(self.options.commentBlock.replace('{review_id}', reviewId));

                            commentBlock.html(comment.html);
                            self._changeCounter(commentBlock, comment.count);
                        });

                        $(self.options.form).on('submit', self._addComment.bind(self));
                        if (formKey) {
                            $(self.options.form).find('input[name="form_key"]').val(formKey);
                        }
                    }
                });
            }
        },

        /**
         * Add Comment to comments block
         */
        _addComment: function (event) {
            event.preventDefault();
            var self = this,
                form = $(event.currentTarget),
                loader = amloader;

            if (form.validation() && form.validation('isValid')) {
                loader.init(form);

                $.ajax({
                    url: this.options.submitUrl,
                    dataType: 'json',
                    showLoader: false,
                    loaderContext: $('.product.data.items'),
                    type: 'post',
                    data: form.serialize(),
                }).done(function (data) {
                    loader.stop(form);
                    if (data.approved) {
                        self._getWrapper(form).find(self.options.list).append(data.html);
                        self._getWrapper(form).find('.' + self.options.emptyClass).removeClass(self.options.emptyClass);
                        self._changeCounter(form, data.qty);
                    } else {
                        self._getWrapper(form).find(self.options.formBlock).after(data.html);

                        setTimeout(function () {
                            self._getWrapper(form).find('.message').fadeOut('fast', function (elem) {
                                self._getWrapper(form).find('.message').remove();
                            });
                        }, 10000);
                    }
                    $(form).trigger("reset");
                });
            }
        },

        /**
         * get Wrapper of review block
         */
        _getWrapper: function (el) {
            var self = this;
            return $(el).closest(self.options.wrapper);
        },

        /**
         * Open List of comments
         */
        _openList: function (e) {
            var self = this,
                button = $(e.currentTarget),
                list = self._getWrapper(e.currentTarget).find(self.options.listBlock),
                form = self._getWrapper(e.currentTarget).find(self.options.formBlock),
                isActive = button.hasClass('-active'),
                qty = button.find(self.options.qty).data('qty');

            if (isActive) {
                self._collapseBlocks(e);
            }

            if (!isActive && qty) {
                $(button).addClass('-active');
                $(form).addClass('-active');
                $(list).addClass('-active');
            }
        },

        /**
         * Open Reply Form in comments Block
         */
        __openForm: function (e) {
            var self = this,
                button = $(e.currentTarget),
                form = self._getWrapper(e.currentTarget).find(self.options.formBlock),
                isActive = button.hasClass('-active');

            if (isActive) {
                self._collapseBlocks(e);
            }

            if (!isActive) {
                $(button).addClass('-active');
                $(form).addClass('-active');
            }
        },

        /**
         * Collapsing Comments Block and Reply Form Block
         */
        _collapseBlocks: function (e) {
            var self = this,
                wrapper = self._getWrapper(e.currentTarget);

            $(wrapper).find('.-active').removeClass('-active');
        },

        /**
         * Change Comment counter in review list Block, searching by qty selector
         */
        _changeCounter: function (el, count) {
            var self = this,
                qtyBlock = self._getWrapper(el).find(self.options.qty),
                qtyParent = qtyBlock.parent();

            if (!count) {
                qtyParent.addClass(self.options.emptyClass);
            }

            qtyBlock.data('qty', count);
            qtyBlock.html(count);
        }
    });

    return $.mage.amReviewCommentsLoad;
});
