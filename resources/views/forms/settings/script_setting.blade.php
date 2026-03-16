<script>
    let itemTimeOut             = 0;
    const publish_result_date   = "{{ $publish_result_date??null }}";
    const existingDate          = publish_result_date
                                    ? moment(publish_result_date, ['YYYY-MM-DD HH:mm', 'ddd, DD-MM-YYYY hh:mm A'], true)
                                    : null;

    $(function () {
        $('#publish_result_date').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: false,
            timePickerSeconds: false,
            showDropdowns: false,
            minYear: moment(),
            maxYear: parseInt(moment().format('YYYY'),10),
            minDate: moment(),
            startDate: (existingDate && existingDate.isValid() && existingDate.isAfter(moment()))
                        ? existingDate
                        : moment(),
            locale: {
                format: 'ddd, DD-MM-YYYY hh:mm'
            },
            parentEl: "#edit_assignments"
        }).on('apply.daterangepicker', function(ev, picker) {
            $('#publish_result_date').val(picker.startDate.format('ddd, DD-MM-YYYY hh:mm'));
            $(this).val(picker.startDate.format('ddd, DD-MM-YYYY hh:mm'));
            autoSaveSetting($('#settings_form'));
        });
    });

    $(document).on('change', 'input[name="immediate_results"]', function() {
        const $checkbox     = $(this);
        const $container    = $('#publish_result_date_container');
        const $input        = $('#publish_result_date');
        const $box          = $('#publish_date_box');

        if ($checkbox.is(':checked')) {
            $container.fadeOut(300, function() {
                $input.prop('disabled', true);
                $input.val('');
                $box.css({'opacity': '0.5', 'pointer-events': 'none', 'cursor': 'not-allowed'});
            });
        } else {
            $container.fadeIn(300, function() {
                $input.prop('disabled', false);
                $box.css({'opacity': '1', 'pointer-events': 'auto', 'cursor': 'pointer'});
                if ($input.data('daterangepicker')) {
                    $input.data('daterangepicker').remove();
                }
                $input.daterangepicker({
                    singleDatePicker: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    timePickerSeconds: false,
                    autoUpdateInput: true,
                    locale: {
                        format: 'ddd, DD-MM-YYYY hh:mm'
                    },
                    startDate: moment().startOf('hour')+1
                });
            });
        }
    });

    function autoSaveSetting(form) {
        if (itemTimeOut) {
            clearTimeout(itemTimeOut);
        }
        itemTimeOut = setTimeout(() => {
            submitForm(form);
        }, 1000);
    }

    $(document).on('change', '#settings_form', function(e) {
        autoSaveSetting($(this));
    });

    function submitForm(form) {
        var $form       = (form && form.jquery) ? form : $(form);
        var formEl      = $form[0];
        if (!formEl) return;

        var formData = new FormData(formEl);
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function(response) {
                showAutoSaveSpaner(false);
            },
            error: function(xhr) {
                showAutoSaveSpaner(false);
            }
        });
    }
</script>
