'use strict';

let EVENT_FILTER = {

    init: function init() {

        EVENT_FILTER.ajax_filter_form();
        EVENT_FILTER.ajax_pagination();

        $('#location').change( function () {
            EVENT_FILTER.set_filter_type('filter');
            EVENT_FILTER.submit_form();
        });
        
        $(document).on('click', '#filter_form input[type=checkbox]', function () {
            EVENT_FILTER.set_filter_type('filter');
            EVENT_FILTER.submit_form();
        });

        $(document).on('click', '.js-open-filter', function () {
            $(".js-filter").slideToggle("slow");
        });
    },

    ajax_filter_form: function ajax_filter_form() {

        $(document).on('submit', '#filter_form', function (event) {
            event.preventDefault();
            // update type filter (filter, loadmore, pagination)
            if ($('#filter_type').val() === 'filter') {
                $('#paged').val(1);
            } 
    
            var data = {
                action: 'filter',
                ajax_nonce: ut_params.ajax_nonce,
                form: $(this).serialize(),
            };

            $.ajax({
                type: 'POST',
                url: ut_params.ajax_url,
                data: data,
                beforeSend: function () {
                    $('.ut-loader').addClass('loading');
                },
                success: function (response) {
                    // update filter options & update product list html
                    if (response.success) {
                        let filter_type = $('#filter_type').val();
                        // update products for filter and load more button
                        $('.catalog-cards').html(response.data.events_html);
                        // update pagination
                        $('.js-pagination').html(response.data.pagination_html);
            
                        if (response.data.url) {
                            history.pushState(null, null, response.data.url);
                        }
            
                        if ( 'pagination' == filter_type ) {
                            $('html, body').animate({
                                scrollTop: $(".catalog-cards").offset().top - 180
                            }, 1000);
                        }
                    }
            
                    $('.ut-loader').removeClass('loading');
                }
            });
        });
    },

    submit_form: function submit_form() {
        $('#filter_form').submit();
    },

    set_filter_type: function set_filter_type(type) {
        $('#filter_type').val(type);
    },

    ajax_pagination: function ajax_pagination() {

        $(document).on('click', '.catalog-content a.page-numbers', function () {
          event.preventDefault();
          let url = $(this).attr('href');
          // get current page of url
          let pathname = url.split("/").filter(entry => entry !== "");
          let lastPath = pathname[pathname.length - 1];
          let page_num = lastPath;
    
          $('#paged').val(page_num);
          EVENT_FILTER.set_filter_type('pagination');
          EVENT_FILTER.submit_form();
        });
    },

};

$(document).ready( EVENT_FILTER.init() );