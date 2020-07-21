define([
    "jquery"
], function($){
    'use strict';

    return function() {
        this.currentTextValue = '';
        this.init = function () {
            this.initSearch();
        };

        this.initSearch = function () {
            var instance = this;

            $('.htmlsitemap-search-input').each(function () {
                $(this).keyup(function(el){
                    var val = el.target.value;
                    if (instance.currentTextValue != val.toLocaleLowerCase()) {
                        instance.contentSearch(val);
                    }
                });

                $('.am-sitemap-wrap a').each(function (key, el) {
                    el.defaultTextContent = el.innerText ? el.innerHTML : el.textContent;
                });
            });
        };

        this.contentSearch = function (text) {
            var instance = this;
            text = text.replace(/^\s+/, '').replace(/\s+$/, '');

            instance.currentTextValue = text.toLowerCase();

            $('.am-always-visible').each(function (key, el) {
                $(el).removeClass('am-always-visible');
            });

            $('.am-sitemap-wrap a').each(function(key, el) {
                el.textContent = el.defaultTextContent.replace(/&amp;/g, '&');
                if (instance.currentTextValue.replace(/\s+/, '') != '') {
                    if (el.defaultTextContent.toLowerCase().indexOf(instance.currentTextValue) == -1) {
                        if (!$(el).parent().hasClass('am-always-visible')) {
                            $(el).parent().hide();
                        }
                    } else {
                        $(el).parent().show();
                        instance.highlight($(el), "text-highlight");

                        var leaf = $(el).parent('li.tree-leaf');
                        while (leaf.length) {
                            leaf.show();
                            leaf.addClass('am-always-visible');
                            leaf = leaf.parent('li.tree-leaf');
                        }

                        instance.showAllLeafs($(el).parent('li.tree-leaf'));
                    }
                } else {
                    $(el).parent().show();
                }
            });
        };

        this.showAllLeafs = function (el) {
            var instance = this;
            if (!el) {
                return false;
            }

            var leafs = el.children('li.tree-leaf');
            leafs.each(function() {
                $(this).show();
                $(this).addClass('am-always-visible');
                instance.showAllLeafs($(this));
            });
        };

        this.highlight = function(element, className) {
            var term = this.currentTextValue;
            var node = element.get(0);
            this.innerHighLight(node, term, className);
        };

        this.innerHighLight = function(element, term, className) {
            className = className || 'highlight';
            term = (term || '').toUpperCase();
            if (term.replace(/\s+/, '') == '') {
                return false
            }

            var skip = 0;
            if (element.nodeType == Node.TEXT_NODE) {
                var pos = element.data.toUpperCase().indexOf(term);
                if (pos >= 0) {
                    var middlebit = element.splitText(pos),
                        endbit = middlebit.splitText(term.length),
                        middleclone = middlebit.cloneNode(true),
                        spannode = document.createElement('span');

                    spannode.className = className;
                    spannode.appendChild(middleclone);
                    middlebit.parentNode.replaceChild(spannode, middlebit);
                    skip = 1;
                }
            } else if (element.nodeType == Node.ELEMENT_NODE && element.childNodes && !/(script|style)/i.test(element.tagName)) {
                for (var i = 0; i < element.childNodes.length; ++i) {
                    i += this.innerHighLight(element.childNodes[i], term, className);
                }
            }
            return skip;
        };
    };
});