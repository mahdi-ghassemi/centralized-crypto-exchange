$(document).ready(function () {
    $(".header").on("click", function () {
        $(this).parent().toggleClass("active");
        $(this).parent().children('.body').slideToggle("slow");

    });
});
