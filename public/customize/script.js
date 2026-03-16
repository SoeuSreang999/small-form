$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).on('change', 'select.select-page-length', function() {
    var val = $(this).val();
    var $table = $(this).closest('.row').find('.datatable-table');
    if ($table.length === 0) {
        $table = $('.datatable-table');
    }
    if ($table.length) {
        var tableId = $table.attr('id');
        var dt = window.LaravelDataTables ? window.LaravelDataTables[tableId] : null;
        if (dt) {
            dt.page.len(val).draw();
        } else {
            console.warn('⚠️ No DataTable instance found for', tableId);
        }
    } else {
        console.warn('⚠️ No table found near dropdown.');
    }
});

let firstLoadDone = false;

$(document).on('preXhr.dt', function () {
    if (firstLoadDone) {
        showLoader(true);
    }
});

$(document).on('xhr.dt', function () {
    if (firstLoadDone) {
        showLoader(false);
    }
    firstLoadDone = true;
});

$('body .form-select2').select2({
    minimumResultsForSearch: -1,
    width: 'auto'
});

window.getWidthFromClasses = function($el) {
    if (!$el || !$el.length) {
        return '100%';
    }
    
    var className = $el.attr('class') || '';
    var match = className.match(/(?:^|\s)(w-(?:25|50|75|100|auto))(?:\s|$)/);
    
    if (!match) {
        return '100%';
    }
    
    var widthClass = match[1];
    switch (widthClass) {
        case 'w-25':
            return '25%';
        case 'w-50':
            return '50%';
        case 'w-75':
            return '75%';
        case 'w-100':
            return '100%';
        case 'w-auto':
            return 'auto';
        default:
            return '100%';
    }
};

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

const notyf = new Notyf({
    duration: 3000,
    ripple: false,
    dismissible: false,
    position: { x: 'right', y: 'top' }
});

const notyfForm = new Notyf({
    duration: 3000,
    position: {
        x: 'center',
        y: 'bottom'
    },
    ripple: false,
    dismissible: false,
    types: [
        {
            type: 'success',
            icon: false
        },
        {
            type: 'error',
            icon: false
        }
    ]
});

function statusMessage(response){
    const title         = response.title;
    const message       = response.message;
    const type          = response.success === true ? 'success' : 'error';
    notyf.open({
        type: type,
        message: `
        <h5>${title}</h5>
        <p>${message}</p>
        `
    });
}

$(document).ready(function () {
    $(".page-loader-wrapper").fadeOut("slow");
});

function showLoader(trueFalse){
    if(trueFalse){
        $(".page-loader-wrapper").css({'background': '#ffffff00'}).fadeIn("slow");
    } else {
        $(".page-loader-wrapper").css({'background': '#ffffff00'}).fadeOut("slow");
    }
}

function handleValidationErrors(form, xhr) {
    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;
        form.addClass('was-validated');
        $.each(errors, function(key, messages) {
            let parts = key.split('.');
            if (parts.length === 2) {
                let field = parts[0];
                let index = parts[1];
                let input = $(`#${field}_${index}`);
                
                if (input.length > 0) {
                    input
                        .addClass('is-invalid')
                        .prop('required', true);
                    
                    let rawErrorMsg = messages[0];
                    let errorMsg = rawErrorMsg.replace(new RegExp(`${field}\\.${index}`, 'g'), field);
                    let errorDiv = input.siblings('.invalid-feedback');
                    if (errorDiv.length === 0) {
                        errorDiv = $('<div class="invalid-feedback d-block"></div>').insertAfter(input);
                    }
                    errorDiv.text(errorMsg).show();
                }
            }
        });
        
        if (form.find('.is-invalid').length > 0) {
            form.find('.is-invalid').first().closest('.mb-2')[0].scrollIntoView({ behavior: 'smooth' });
        }
    }
}

function handleValidation(form, xhr){
    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;
        form.find('.required').removeClass('required');
        form.addClass('was-validated');
        $.each(errors, function(key, value) {
            let input = $('[name="'+key+'"]');
            input.attr('required', true);
            let errorMsg = value[0];
            let errorDiv = input.siblings('.invalid-feedback');
            if (errorDiv.length === 0) {
                errorDiv = $('<div class="invalid-feedback"></div>').insertAfter(input);
            }
            errorDiv.text(errorMsg).show();
        });
    }
}

function handleErrorMessage(xhr){
    let message = "Something went wrong.";
    if (xhr.responseJSON && xhr.responseJSON.error) {
        message = xhr.responseJSON.error;
    } else if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    } else if (xhr.statusText) {
        message = xhr.statusText;
    }
    notyf.open({
        type: 'error',
        message: `
        <h5>Error</h5>
        <p>${message}</p>
        `
    });
}

function initVoicePlayers(containerSelector) {
    let currentAudio = null;

    function generateWaveform($container) {
        $container.empty();

        const containerWidth = $container.width();
        const barWidth = 3;
        const gap = 3;
        const barCount = Math.floor(containerWidth / (barWidth + gap));

        for (let i = 0; i < barCount; i++) {
            const heightPercent = Math.floor(Math.random() * 60) + 40; // 40-100%
            const $bar = $('<span class="waveform-bar"></span>').css('height', heightPercent + '%');
            $container.append($bar);
        }
    }

    function setupWaveforms() {
        $(containerSelector).find('.waveform-container').each(function () {
            generateWaveform($(this));
        });
    }

    setupWaveforms();

    // Re-generate bars on window resize
    $(window).on('resize', function () {
        setupWaveforms();
    });

    // Click waveform-container to seek
    $(containerSelector).find('.waveform-container').off('click').on('click', function (e) {
        const $container = $(this);
        const audio = $container.closest('.voice-message-player').find('audio')[0];
        if (!audio.duration || audio.paused) return;

        const rect = this.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const ratio = clickX / rect.width;
        audio.currentTime = ratio * audio.duration;
    });

    // Play / Pause + smooth progress
    $(containerSelector).find('.play-pause-btn').off('click').on('click', function () {
        const $player   = $(this).closest('.voice-message-player');
        const audio     = $player.find('audio')[0];
        const $icon     = $(this).find('i');
        const $bars     = $player.find('.waveform-bar');
        const $duration = $player.find('.duration-label');

        if (currentAudio && currentAudio !== audio) {
            currentAudio.pause();
            $(currentAudio).closest('.voice-message-player')
                .find('.play-pause-btn i').removeClass('mdi-pause').addClass('mdi-play');
            $(currentAudio).closest('.voice-message-player')
                .find('.waveform-bar').removeClass('active');
        }
        currentAudio = audio;

        if (audio.paused) {
            audio.play();
            $icon.removeClass('mdi-play').addClass('mdi-pause');
        } else {
            audio.pause();
            $icon.removeClass('mdi-pause').addClass('mdi-play');
        }

        audio.ontimeupdate = function () {
            if (!audio.duration) return;
            const progress = audio.currentTime / audio.duration;
            const activeCount = progress * $bars.length;

            $bars.each(function (index) {
                $(this).toggleClass('active', index < activeCount);
            });

            $duration.text(formatTime(audio.currentTime));
        };

        audio.onended = function () {
            $icon.removeClass('mdi-pause').addClass('mdi-play');
            $bars.removeClass('active');
            $duration.text(formatTime(audio.duration));
        };
    });

    function formatTime(seconds) {
        if (isNaN(seconds)) return '0:00';
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }
}

function initTooltips() {
    $('body>.tooltip').remove();
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initSelect2() {
    $('.form-select2').each(function() {
        var $this        = $(this);
        var dynamicWidth = getWidthFromClasses($this);
        var $parentModal = $this.closest('.modal');

        if ($this.hasClass("select2-hidden-accessible")) {
            $this.select2('destroy');
        }

        $this.select2({
            minimumResultsForSearch: -1,
            dropdownParent: $parentModal.length ? $parentModal : $(document.body),
            width: dynamicWidth
        });
    });
}

function confirmAction() {
    return Swal.fire({
        title: window.trans.confirm_delete,
        text: window.trans.confirm_text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: window.trans.confirm_yes,
        cancelButtonText: window.trans.confirm_no,
    }).then(result => result.isConfirmed);
}

function allowNumberOnly(event) {
    const allowedKeys = [
        'Backspace', 'Delete', 'Tab', 'Enter', 'Escape',
        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'
    ];

    if (allowedKeys.includes(event.key)) return;

    if ((event.ctrlKey || event.metaKey) && ['a', 'c', 'v', 'x'].includes(event.key.toLowerCase())) {
        return;
    }

    if (!/^[0-9]$/.test(event.key)) {
        event.preventDefault();
    }
}


function allowNumberDecimal(event, input) {
    const allowedKeys = [
        'Backspace', 'Delete', 'Tab', 'Enter', 'Escape',
        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'
    ];

    if (allowedKeys.includes(event.key)) return;

    if ((event.ctrlKey || event.metaKey) && ['a', 'c', 'v', 'x'].includes(event.key.toLowerCase())) {
        return;
    }

    if (/^[0-9]$/.test(event.key)) return;

    if (event.key === '.' && !input.value.includes('.')) return;
    event.preventDefault();
}

window.UI = class UI {
    static initDropify(root = document) {
        const $root = root instanceof jQuery ? root : $(root);
        $root.find('input.dropify').each(function () {
        const $el = $(this);
        const maxFiles = parseInt($el.data('max-files'), 10);

        $el.off('change.dropifyMaxFiles').on('change.dropifyMaxFiles', function () {
            if (!Number.isInteger(maxFiles) || maxFiles < 1 || !this.files) {
                return;
            }

            if (this.files.length > maxFiles) {
                this.value = '';
                const message = `You can upload up to ${maxFiles} file${maxFiles > 1 ? 's' : ''}.`;
                if (typeof notyfForm !== 'undefined') {
                    notyfForm.error(message);
                } else {
                    alert(message);
                }
            }
        });

        $el.off('dropify.errors.dropifyValidation').on('dropify.errors.dropifyValidation', function (event) {
            if (typeof notyfForm === 'undefined') {
                return;
            }

            const errors = event?.errors || [];
            if (errors.includes('fileSize')) {
                const maxSize = $el.data('max-file-size') || 'the allowed limit';
                notyfForm.error(`File size is too large. Max ${maxSize} per file.`);
                return;
            }

            if (errors.includes('fileExtension')) {
                notyfForm.error('This file type is not allowed.');
            }
        });

        if ($el.data('dropify')) return;
        $el.dropify();
        });
    }
};




