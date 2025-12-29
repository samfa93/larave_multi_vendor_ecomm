(function ($) {
    'use strict';

    /*Product Details*/
    var productDetails = function () {
        var $big = $('.product-image-slider');
        var $thumbs = $('.slider-nav-thumbnails');

        if (!$big.length || !$thumbs.length) return;

        // Prevent double-init (modals / ajax / partial reloads)
        if ($big.hasClass('slick-initialized')) $big.slick('unslick');
        if ($thumbs.hasClass('slick-initialized')) $thumbs.slick('unslick');

        // RTL detect (works per-page/section)
        var rtl =
            $big.closest('[data-rtl]').data('rtl') === true ||
            $big.closest('[dir]').attr('dir') === 'rtl';

        // Keep icons visually correct (left arrow on left, right arrow on right)
        var prevIcon = 'fi-rs-arrow-small-left';
        var nextIcon = 'fi-rs-arrow-small-right';

        $big.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: false,
            rtl: rtl,
            asNavFor: '.slider-nav-thumbnails'
        });

        $thumbs.slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-image-slider',
            dots: false,
            focusOnSelect: true,
            rtl: rtl,
            prevArrow: '<button type="button" class="slick-prev"><i class="' + prevIcon + '"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="' + nextIcon + '"></i></button>'
        });

        // Force correct sizing/visibility (critical for RTL)
        $big.slick('setPosition');
        $thumbs.slick('setPosition');

        // Remove active class from all thumbnail slides
        $('.slider-nav-thumbnails .slick-slide').removeClass('slick-active');

        // Set active class to first thumbnail slide
        $('.slider-nav-thumbnails .slick-slide').eq(0).addClass('slick-active');

        // On before slide change match active thumbnail to current slide
        $big.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            $('.slider-nav-thumbnails .slick-slide').removeClass('slick-active');
            $('.slider-nav-thumbnails .slick-slide').eq(nextSlide).addClass('slick-active');
        });

        $big.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            var img = $(slick.$slides[nextSlide]).find('img');
            $('.zoomWindowContainer,.zoomContainer').remove();
            if ($(window).width() > 768) {
                $(img).elevateZoom({
                    zoomType: "inner",
                    cursor: "crosshair",
                    zoomWindowFadeIn: 500,
                    zoomWindowFadeOut: 750
                });
            }
        });

        // Elevate Zoom
        if ($(window).width() > 768) {
            $('.product-image-slider .slick-active img').elevateZoom({
                zoomType: "inner",
                cursor: "crosshair",
                zoomWindowFadeIn: 500,
                zoomWindowFadeOut: 750
            });
        }

        // Fix arrow behavior in RTL for thumbnails (visuals stay the same)
        if (rtl) {
            $thumbs.find('.slick-prev').off('click.rtlFix').on('click.rtlFix', function (e) {
                e.preventDefault();
                $thumbs.slick('slickNext');
            });
            $thumbs.find('.slick-next').off('click.rtlFix').on('click.rtlFix', function (e) {
                e.preventDefault();
                $thumbs.slick('slickPrev');
            });
        }

        // Filter color/Size
        $('.list-filter').each(function () {
            $(this).find('a').on('click', function (event) {
                event.preventDefault();
                $(this).parent().siblings().removeClass('active');
                $(this).parent().addClass('active');
                $(this).parents('.attr-detail').find('.current-size').text($(this).text());
                $(this).parents('.attr-detail').find('.current-color').text($(this).attr('data-color'));
            });
        });

        $(document).on('click', '.qty-up', function (event) {
            event.preventDefault();
            var input = $(this).siblings('.qty-val');
            var qtyval = parseInt(input.val(), 10) || 1;
            input.val(qtyval + 1).change();
        });

        $(document).on('click', '.qty-down', function (event) {
            event.preventDefault();
            var input = $(this).siblings('.qty-val');
            var qtyval = parseInt(input.val(), 10) || 1;
            input.val(qtyval - 1).change();
        });

        $('.dropdown-menu .cart_list').on('click', function (event) {
            event.stopPropagation();
        });

        // Recalc on load/resize (RTL sometimes needs this)
        $(window).on('load resize', function () {
            if ($big.hasClass('slick-initialized')) $big.slick('setPosition');
            if ($thumbs.hasClass('slick-initialized')) $thumbs.slick('setPosition');
        });
    };

    // Load functions
    $(document).ready(function () {
        productDetails();
    });

})(jQuery);
