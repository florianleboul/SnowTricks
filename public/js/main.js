jQuery(document).ready(function () {
    // Set default image for all img
    $( "img" ).on('error', function() {
        // If default image isn't set (if default is already set, DON'T LOOP)
        if ($(this).attr("src") !== "/img/default.jpg"){
            $( this ).attr( "src", "/img/default.jpg" );
        }
    });
});
