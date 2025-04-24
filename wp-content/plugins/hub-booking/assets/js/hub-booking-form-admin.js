jQuery(document).ready(function($) {
    
    $('.hub-booking-set-status').on('click', function(e){

        var post_id = $(this).data("post-id");
        var set = $(this).data("set");

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: { 
                action: 'booking_set_status',
                post_id: post_id,
                status: set
            },
            success: function(response){
                if ( response.error && response.alert ) {
                    alert( response.alert );
                } else {
                    $('#post-' + post_id).find('.status').html(response.out);
                }
            },
        });
        e.preventDefault();
    });

    const elem = document.querySelector('#datepicker');
    if ( elem ) {
        const options = JSON.parse(elem.getAttribute('data-options'));
        const onSelect = ({ date, datepicker }) => {
            if ( date.length == 2 ){
    
            }
        };
        options.onSelect = onSelect;
        options.minDate = new Date();
        const datepicker = new AirDatepicker(elem, options);
    }
    
});
