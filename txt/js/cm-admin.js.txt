jQuery(document).ready(function ($) {
    // Add presenter
    $('#add_presenter_button').on('click', function () {
        var selectedPresenter = $('#presenter_select').val();
        var selectedPresenterText = $('#presenter_select option:selected').text();

        if (selectedPresenter) {
            $('#presenter_list').append('<li data-id="' + selectedPresenter + '">' + selectedPresenterText + ' <button type="button" class="remove_presenter_button">Remove</button></li>');

            // Remove selected presenter from dropdown
            $('#presenter_select option[value="' + selectedPresenter + '"]').remove();

            // Update hidden input field
            updatePresenterField();
        }
    });

    // Remove presenter
    $('#presenter_list').on('click', '.remove_presenter_button', function () {
        var presenterId = $(this).parent().data('id');
        var presenterText = $(this).parent().text().replace('Remove', '').trim();

        // Add presenter back to dropdown
        $('#presenter_select').append('<option value="' + presenterId + '">' + presenterText + '</option>');

        // Remove presenter from list
        $(this).parent().remove();

        // Update hidden input field
        updatePresenterField();
    });

    function updatePresenterField() {
        var presenterIds = [];
        $('#presenter_list li').each(function () {
            presenterIds.push($(this).data('id'));
        });
        $('#session_presenters').val(presenterIds.join(','));
    }

    // Initial update of hidden input field
    updatePresenterField();

    // Room capacity display
    $('#session_room').on('change', function () {
        var roomId = $(this).val();

        if (!roomId) {
            $('#room_capacity_display').text('');
            $('#capacity_warning').hide();
            return;
        }

        $.ajax({
            url: cm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'cm_get_room_capacity',
                room_id: roomId,
                nonce: cm_ajax_object.nonce
            },
            success: function (response) {
                if (response.success) {
                    var capacity = response.data.capacity;
                    $('#room_capacity_display').text('Room Capacity: ' + capacity).data('capacity', capacity);

                    // Check registration capacity
                    var registrationCapacity = parseInt($('#registration_capacity').val());
                    if (registrationCapacity > capacity) {
                        $('#capacity_warning').text('Warning: Registration capacity exceeds room capacity!').show();
                    } else {
                        $('#capacity_warning').hide();
                    }
                }
            }
        });
    });

    // Check registration capacity on input change
    $('#registration_capacity').on('input', function () {
        var registrationCapacity = parseInt($(this).val());
        var roomCapacity = parseInt($('#room_capacity_display').data('capacity'));

        if (registrationCapacity > roomCapacity) {
            $('#capacity_warning').text('Warning: Registration capacity exceeds room capacity!').show();
        } else {
            $('#capacity_warning').hide();
        }
    });
});
