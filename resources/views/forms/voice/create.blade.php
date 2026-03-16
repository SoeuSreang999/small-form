<div class="modal fade bd-example-modal-xl" id="question-record-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="voice-form" action="{{ route('forms.questions.voices.store', $question->id) }}" method="POST"
                    enctype="multipart/form-data"
                    data-form-id="{{ $request->form_id }}"
                    data-question-id="{{ $question->id }}"
                >
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 text-center mb-2">
                            <h5>Voice Record</h5>
                            <p class="text-muted">Click icon micophone to start record</p>
                        </div>
                        <div class="col-12 d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-primary btn-lg rounded-circle" onclick="startRecord(this);">
                                <i class="mdi mdi-microphone mdi-36px"></i>
                            </button>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-center">
                            <div class="mb-2 px-3 d-flex gap-3">
                                <div class="tracking-date fs-3">
                                    <p class="p-0 m-0" id="rec-hh">00</p>
                                    <span class="d-block fs-12 text-muted text-center">HH</span>
                                </div>
                                <div class="tracking-date fs-3">
                                    <p class="p-0 m-0" id="rec-mm">00</p>
                                    <span class="d-block fs-12 text-muted text-center">MM</span>
                                </div>
                                <div class="tracking-date fs-3">
                                    <p class="p-0 m-0" id="rec-ss">00</p>
                                    <span class="d-block fs-12 text-muted text-center">SS</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="list-recorded-audio">

                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('action.cancel')}}</button>
                    <button type="button" class="btn btn-primary" onclick="saveQuestionRecord(this);">{{ __('action.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        let isRecordItem        = false;
        let audioStream         = null;
        let mediaRecorderItem   = null;
        let audioChunkItem      = [];
        let recordIntervalItem  = null;
        let recordSecondItem    = 0;
        let recordedAudioItems  = [];

        window.startRecord = function (el) {
            let icon = $(el).find('i');
            if (!isRecordItem) {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        audioStream = stream;
                        mediaRecorderItem = new MediaRecorder(stream);
                        audioChunkItem = [];

                        mediaRecorderItem.ondataavailable = e => {
                            audioChunkItem.push(e.data);
                        };

                        mediaRecorderItem.onstop = () => {
                            let audioBlob = new Blob(audioChunkItem, { type: 'audio/webm' });
                            saveRecordToList(audioBlob, el);
                            stopTimer(el);
                            audioStream.getTracks().forEach(track => track.stop());
                            audioStream = null;
                        };

                        mediaRecorderItem.start();
                        isRecordItem = true;
                        startTimer(el);

                        icon.removeClass('mdi-microphone').addClass('mdi-stop');
                        $(el).addClass('recording');
                    })
                    .catch(() => alert('Microphone permission denied'));
            } else {
                mediaRecorderItem.stop();
                isRecordItem = false;
                icon.removeClass('mdi-stop').addClass('mdi-microphone');
                $(el).removeClass('recording');
            }
        };

        function startTimer(el) {
            recordSecondItem = 0;
            updateTimer(el);

            recordIntervalItem = setInterval(() => {
                recordSecondItem++;
                updateTimer(el);
            }, 1000);
        }

        function stopTimer(el) {
            clearInterval(recordIntervalItem);
            recordIntervalItem = null;
            recordSecondItem = 0;

            let form = $(el).closest('form');
            form.find('#rec-hh, #rec-mm, #rec-ss').removeClass('text-danger').text('00');
        }

        function updateTimer(el) {
            let form    = $(el).parents('form');
            let hh      = String(Math.floor(recordSecondItem / 3600)).padStart(2, '0');
            let mm      = String(Math.floor((recordSecondItem % 3600) / 60)).padStart(2, '0');
            let ss      = String(recordSecondItem % 60).padStart(2, '0');

            form.find('#rec-hh').text(hh).addClass('text-danger');
            form.find('#rec-mm').text(mm).addClass('text-danger');
            form.find('#rec-ss').text(ss).addClass('text-danger');
        }

        function saveRecordToList(audioBlob, el) {
            let form        = $(el).closest('form');
            let audioUrl    = URL.createObjectURL(audioBlob);
            let audioName   = `${Date.now()}.webm`;

            recordedAudioItems.push({
                blob: audioBlob,
                name: audioName
            });

            $('#list-recorded-audio').append(`
                <div class="col-12 mb-2 file-item audio-file-item position-relative"
                    >
                    <div class="col-12 text-center position-absolute p-0 m-0" style="top:-8px;left:50%;transform:translateX(-50%)">
                        <i class="mdi mdi-drag-horizontal fs-4"></i>
                    </div>
                    <div class="col-12">
                        <div class="voice-message-player">
                            <button type="button" class="play-pause-btn">
                                <i class="mdi mdi-play"></i>
                            </button>
                            <div class="waveform-container" data-bars="50"></div>
                            <span class="duration-label">0:00</span>
                            <audio src="${audioUrl}"></audio>
                        </div>
                        <button type="button" class="btn_remove_voice" onclick="removeRecordItem(this, null);">
                            <i class="fas fa-trash text-danger"></i>
                        </button>
                    </div>
                </div>
            `);
            initVoicePlayers('body');
        }

        window.removeRecordItem = function (el) {
            let index = $(el).closest('.file-item').index();
            recordedAudioItems.splice(index, 1);
            $(el).closest('.file-item').remove();
        };

        window.saveQuestionRecord = function (el) {
            let form     = $('#voice-form')[0];
            let formData = new FormData(form);

            if (recordedAudioItems.length === 0) {
                return;
            }

            recordedAudioItems.forEach((item, index) => {
                formData.append('records[]', item.blob, item.name);
            });

            $.ajax({
                url: form.action,
                method: form.method,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    showAutoSaveSpaner(true);
                },
                success: function (res) {
                    let file_ids        = res.file_ids;
                    recordedAudioItems  = [];
                    showAutoSaveSpaner(false);
                    $(el).parents('.modal').modal('hide');
                    listVoiceToQuestion(el, file_ids);
                },
                error: function (xhr) {
                    showAutoSaveSpaner(false);
                    $(el).parents('.modal').modal('hide');
                }
            });
        }

        function listVoiceToQuestion(el, file_ids) {
            if (file_ids.length === 0) return;

            let form_id         = $(el).parents('form').data('form-id');
            let question_id     = $(el).parents('form').data('question-id');
            const url           = "{{ route('forms.questions.voices.index',':question') }}".replace(':question', question_id);

            $.ajax({
                url: url,
                type: "GET",
                data: {
                    'form_id': form_id,
                    'question_id': question_id,
                    'file_ids': file_ids
                },
                dataType: "HTML",
                beforeSend: function () {
                    showAutoSaveSpaner(true);
                },
                success: function (res) {
                    showAutoSaveSpaner(false);
                    $("#question-document" + question_id).append(res);
                    initVoicePlayers('body');
                },
                error: function () {
                    showAutoSaveSpaner(false);
                }
            });
        }

        $('#question-record-modal').on('hidden.bs.modal', function () {
            if (mediaRecorderItem && isRecordItem) {
                mediaRecorderItem.stop();
                isRecordItem = false;
            }
        });

    })();
</script>

