$(init);

function init() {
    $('.destination').prepend($('.message'));
}

var arrow = $('i#arrow');

$('.user-panel').click(function(event) {
    var open = $('.collapse').hasClass("show");
    console.log(open);
    if (!open) {
        arrow.removeClass('fas fa-chevron-down').addClass('fas fa-chevron-up');
    } else {
        arrow.removeClass('fas fa-chevron-up').addClass('fas fa-chevron-down');
    }
})

$(document).click(function(event) {
    var clickover = $(event.target);
    var _opened = $(".collapse").hasClass("show");
    if (_opened === true && !clickover.hasClass("navbar-toggler")) {
        $(".navbar-toggler").click();
        arrow.removeClass('fas fa-chevron-up').addClass('fas fa-chevron-down');
    }
})