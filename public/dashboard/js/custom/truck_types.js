$(document).ready(function() {
    $(document).on('change', '.truck_id', function () {
        var truckId = $('.truck_id option:selected').val();
        if(truckId) {
            $.ajax({
                url: "/dashboard/trucks/types",
                data_type: 'html',
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    truck_id: truckId
                }, success: function (data) {
                    $('.truck_types').html(data);
                }
            });
        } else {
            $('.truck_types').html('');
        }
    });
});
