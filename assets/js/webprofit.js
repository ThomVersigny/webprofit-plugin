jQuery(document).ready(function ($) {
    // Accordions
    matAddAccordionIcons();

    $('.accordion-header').on('click', function (event) {
        matAccordionToggle($(this));
    });
});

function matAddAccordionIcons() {
    // Add accordion icon to each accordion
    $(".accordion").each(function () {
        $(".accordion-header", this).append('<i class="fa fa-chevron-down accordion-icon" aria-hidden="true"></i>');
    });
}

function matAccordionToggle(element) {
    accordion = $(element).closest('.accordion');

    // Close opened accordions
    $(".accordion").each(function () {
        if (!$(accordion).hasClass('opened')) {
            $(this).removeClass('opened');
        }
    });

    // Open accordion
    $(accordion).toggleClass('opened');
}