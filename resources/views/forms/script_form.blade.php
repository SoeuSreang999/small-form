<script>
    $(document).ready(function () {
        initVoicePlayers('body');
        UI.initDropify();
        initQuestionDragula();
        initSectionDragula();
        initTabPersistence();
    });

    function initTabPersistence() {
        function activateTabFromUrl() {
            const params    = new URLSearchParams(window.location.search);
            const tabParam  = params.get('tab');

            if (tabParam) {
                const tabLink = document.querySelector(`a[data-bs-toggle="tab"][url="${tabParam}"]`);
                if (tabLink) {
                    const tab = new bootstrap.Tab(tabLink);
                    tab.show();
                }
            }
        }

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tabLink => {
            tabLink.addEventListener('shown.bs.tab', function (e) {
                const tabName = this.getAttribute('url');
                if (tabName) {
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tabName);
                    window.history.pushState(null, '', url);
                }
            });
        });

        activateTabFromUrl();
    }

    let itemTimers   = {};

    $(document).on('mousedown click', '.question-move-item', function (e) {
        e.stopPropagation();
    });

    $(document).on('mouseenter', '.question-move-item', function () {
        this.style.cursor = 'grab';
        this.style.userSelect = 'none';
    });

    $(document).on('mousedown', '.question-move-item', function () {
        this.style.cursor = 'grabbing';
        document.body.style.cursor = 'grabbing';
    });

    $(document).on('mouseup', function () {
        document.body.style.cursor = '';
        $('.question-move-item').css('cursor', 'grab');
    });

    $(document).on('mousedown click', '.section-move-item', function (e) {
        e.stopPropagation();
    });

    $(document).on('mouseenter', '.section-move-item', function () {
        this.style.cursor = 'grab';
        this.style.userSelect = 'none';
    });

    $(document).on('mousedown', '.section-move-item', function () {
        this.style.cursor = 'grabbing';
        document.body.style.cursor = 'grabbing';
    });

    function initSectionDragula() {
        if (!window.dragula) {
            return;
        }

        if (window.__sectionDrake) {
            return;
        }

        const container = document.getElementById('form');
        if (!container) {
            return;
        }

        const drake = window.dragula([container], {
            moves: function (el, source, handle) {
                return $(handle).closest('.section-move-item').length > 0;
            },
            direction: 'vertical'
        });

        window.__sectionDrake = drake;

        function toggleSectionQuestions(sectionEl, isHide) {
            const $section = $(sectionEl);
            const $questions = $section.find('.question-item');

            if (isHide) {
                $questions.each(function () {
                    const $q = $(this);
                    if (!$q.hasClass('d-none')) {
                        $q.addClass('js-section-drag-hidden d-none');
                    }
                });
            } else {
                $questions.filter('.js-section-drag-hidden').removeClass('d-none js-section-drag-hidden');
            }
        }

        window.__sectionDragScroll = window.__sectionDragScroll || {
            timer: null,
            lastY: null,
            onMove: null,
        };

        function startSectionAutoScroll() {
            if (window.__sectionDragScroll.timer) {
                return;
            }

            window.__sectionDragScroll.onMove = function (e) {
                window.__sectionDragScroll.lastY = e.clientY;
            };

            document.addEventListener('mousemove', window.__sectionDragScroll.onMove, { passive: true });

            window.__sectionDragScroll.timer = setInterval(function () {
                const y = window.__sectionDragScroll.lastY;
                if (y === null || y === undefined) {
                    return;
                }

                const threshold = 90;
                const speed = 28;

                if (y < threshold) {
                    window.scrollBy(0, -speed);
                } else if (y > (window.innerHeight - threshold)) {
                    window.scrollBy(0, speed);
                }
            }, 16);
        }

        function stopSectionAutoScroll() {
            if (window.__sectionDragScroll.onMove) {
                document.removeEventListener('mousemove', window.__sectionDragScroll.onMove);
                window.__sectionDragScroll.onMove = null;
            }
            if (window.__sectionDragScroll.timer) {
                clearInterval(window.__sectionDragScroll.timer);
                window.__sectionDragScroll.timer = null;
            }
            window.__sectionDragScroll.lastY = null;
        }

        drake.on('drop', function (el, target) {
            toggleSectionQuestions(el, false);
            stopSectionAutoScroll();
            persistSectionOrder(target);
            document.body.style.cursor = '';
        });

        drake.on('drag', function (el) {
            toggleSectionQuestions(el, true);
            startSectionAutoScroll();
            document.body.style.cursor = 'grabbing';
        });

        drake.on('cancel', function (el) {
            if (el) {
                toggleSectionQuestions(el, false);
            }
            stopSectionAutoScroll();
            document.body.style.cursor = '';
        });

        drake.on('dragend', function (el) {
            if (el) {
                toggleSectionQuestions(el, false);
            }
            stopSectionAutoScroll();
            document.body.style.cursor = '';
        });
    }

    function persistSectionOrder(containerEl) {
        const formId = getCurrentFormId();
        if (!formId) {
            console.warn('Missing form_id for section reorder');
            return;
        }

        const sectionIds = $(containerEl)
            .children('.list-section-item')
            .map(function () {
                const sid = $(this).find('.section-item').first().data('section-id');
                return sid;
            })
            .get()
            .filter(Boolean);

        const url = "{{ route('forms.sections.reorder') }}";

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                form_id: formId,
                section_ids: sectionIds,
            },
            beforeSend: function () {
                showAutoSaveSpaner(true);
            },
            success: function () {
                showAutoSaveSpaner(false);
            },
            error: function (xhr) {
                showAutoSaveSpaner(false);
                handleErrorMessage(xhr);
            }
        });
    }

    function getCurrentFormId() {
        return $('#form').data('form-id') || null;
    }

    function initQuestionDragula() {
        if (!window.dragula) {
            return;
        }

        window.__questionDragulaContainerIds = window.__questionDragulaContainerIds || new Set();
        window.__questionDrake = window.__questionDrake || null;

        function toggleQuestionDetails(el, isHide) {
            const $el = $(el);
            const $targets = $el.find('.question-file, .question-content');

            if (isHide) {
                $targets.each(function () {
                    const $t = $(this);
                    if (!$t.hasClass('d-none')) {
                        $t.addClass('js-drag-hidden d-none');
                    }
                });
            } else {
                $targets.filter('.js-drag-hidden').removeClass('d-none js-drag-hidden');
            }
        }


        // Create one drake that can move questions between section containers
        if (!window.__questionDrake) {
            const containers = $('.list-section-item').toArray();

            const drake = window.dragula(containers, {
                moves: function (el, source, handle) {
                    return $(el).find('.question-item').length > 0 && $(handle).closest('.question-move-item').length > 0;
                },
                accepts: function (el, target, source, sibling) {
                    // Only allow dropping into section containers
                    if (!target || !$(target).hasClass('list-section-item')) {
                        return false;
                    }

                    // Block dropping before the section card (first child in the container)
                    if (sibling && $(sibling).find('.section-item').length > 0) {
                        return false;
                    }

                    return true;
                },
                direction: 'vertical'
            });

            window.__questionDrake = drake;

            window.__questionDragScroll = window.__questionDragScroll || {
                timer: null,
                lastY: null,
                onMove: null,
            };

            function startQuestionAutoScroll() {
                if (window.__questionDragScroll.timer) {
                    return;
                }

                window.__questionDragScroll.onMove = function (e) {
                    window.__questionDragScroll.lastY = e.clientY;
                };

                document.addEventListener('mousemove', window.__questionDragScroll.onMove, { passive: true });

                window.__questionDragScroll.timer = setInterval(function () {
                    const y = window.__questionDragScroll.lastY;
                    if (y === null || y === undefined) {
                        return;
                    }

                    const threshold = 90;
                    const speed = 280;

                    if (y < threshold) {
                        window.scrollBy(0, -speed);
                    } else if (y > (window.innerHeight - threshold)) {
                        window.scrollBy(0, speed);
                    }
                }, 100);
            }

            function stopQuestionAutoScroll() {
                if (window.__questionDragScroll.onMove) {
                    document.removeEventListener('mousemove', window.__questionDragScroll.onMove);
                    window.__questionDragScroll.onMove = null;
                }
                if (window.__questionDragScroll.timer) {
                    clearInterval(window.__questionDragScroll.timer);
                    window.__questionDragScroll.timer = null;
                }
                window.__questionDragScroll.lastY = null;
            }

            drake.on('drop', function (el, target, source) {
                toggleQuestionDetails(el, false);
                stopQuestionAutoScroll();

                const fromSectionId = $(source).find('.section-item').first().data('section-id') || null;
                const toSectionId = $(target).find('.section-item').first().data('section-id') || null;

                if (fromSectionId && toSectionId && fromSectionId !== toSectionId) {
                    persistQuestionOrderCrossSection(source, target, el);
                } else {
                    persistQuestionOrder(target, el);
                }
            });

            drake.on('drag', function (el) {
                toggleQuestionDetails(el, true);
                document.body.style.cursor = 'grabbing';
                startQuestionAutoScroll();
            });

            drake.on('cancel', function (el) {
                toggleQuestionDetails(el, false);
                stopQuestionAutoScroll();
                document.body.style.cursor = '';
            });

            drake.on('dragend', function (el) {
                if (el) {
                    toggleQuestionDetails(el, false);
                }
                stopQuestionAutoScroll();
                document.body.style.cursor = '';
            });

            drake.on('cloned', function (clone, original, type) {
                if (type === 'mirror') {
                    toggleQuestionDetails(clone, true);
                }
            });
        } else {
            $('.list-section-item').each(function () {
                const container = this;
                const containerId = container.id || null;
                if (containerId && window.__questionDragulaContainerIds.has(containerId)) {
                    return;
                }
                if (containerId) {
                    window.__questionDragulaContainerIds.add(containerId);
                }
                if (window.__questionDrake.containers.indexOf(container) === -1) {
                    window.__questionDrake.containers.push(container);
                }
            });
        }
    }

    function getQuestionIdsFromContainer(containerEl) {
        return $(containerEl)
            .children('form')
            .filter(function () {
                return $(this).find('.question-item').length > 0;
            })
            .map(function () {
                return $(this).find('.question-item').first().data('question-id');
            })
            .get()
            .filter(Boolean);
    }

    function persistQuestionOrder(containerEl, movedEl) {
        const $container = $(containerEl);
        const $moved = $(movedEl);

        const $movedCard = $moved.find('.question-item').first();

        const formId = getCurrentFormId() || $movedCard.data('form-id') || null;
        const sectionId = $movedCard.data('section-id') || null;

        if (!formId || !sectionId) {
            console.warn('Missing form_id/section_id on moved question item');
            return;
        }

        const questionIds = getQuestionIdsFromContainer($container);

        const url = "{{ route('forms.questions.reorder') }}";

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                form_id: formId,
                section_id: sectionId,
                question_ids: questionIds,
            },
            beforeSend: function () {
                showAutoSaveSpaner(true);
            },
            success: function () {
                showAutoSaveSpaner(false);
            },
            error: function (xhr) {
                showAutoSaveSpaner(false);
                handleErrorMessage(xhr);
            }
        });
    }

    function persistQuestionOrderCrossSection(sourceEl, targetEl, movedEl) {
        const $moved = $(movedEl);
        const $movedCard = $moved.find('.question-item').first();

        const formId = getCurrentFormId() || $movedCard.data('form-id') || null;
        const fromSectionId = $(sourceEl).find('.section-item').first().data('section-id') || null;
        const toSectionId = $(targetEl).find('.section-item').first().data('section-id') || null;

        if (!formId || !fromSectionId || !toSectionId) {
            console.warn('Missing ids for cross-section reorder');
            return;
        }

        const sourceQuestionIds = getQuestionIdsFromContainer(sourceEl);
        const targetQuestionIds = getQuestionIdsFromContainer(targetEl);

        const url = "{{ route('forms.questions.reorder.cross') }}";

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            contentType: 'application/json; charset=utf-8',
            processData: false,
            data: JSON.stringify({
                form_id: formId,
                from_section_id: fromSectionId,
                to_section_id: toSectionId,
                source_question_ids: sourceQuestionIds,
                target_question_ids: targetQuestionIds,
            }),
            beforeSend: function () {
                showAutoSaveSpaner(true);
            },
            success: function () {
                showAutoSaveSpaner(false);
                // Update moved card section id so other actions (copy, add, etc.) stay correct
                $movedCard.attr('data-section-id', toSectionId).data('section-id', toSectionId);
            },
            error: function (xhr) {
                showAutoSaveSpaner(false);
                handleErrorMessage(xhr);
            }
        });
    }

    // tinymce.init({
    //     selector: '.tinymce-editor',
    //     min_height: 60,
    //     autoresize_bottom_margin: 0,
    //     autoresize_overflow_padding: 0,
    //     menubar: false,
    //     plugins: [
    //         'autoresize',
    //         'lists',
    //         'link',
    //         'advlist',
    //         'autolink',
    //     ],
    //     toolbar: `
    //         formatselect |
    //         fontsize |
    //         bold italic underline |
    //         bullist numlist |
    //         link |
    //         removeformat | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry
    //     `,
    //     font_size_formats: "8px 10px 12px 14px 16px 18px 20px 24px 28px 32px",
    //     external_plugins: {
    //         tiny_mce_wiris: "{{ asset('plugins/tinymce5/plugin.js') }}"
    //     },
    //     content_style: `
    //         @font-face {
    //             font-family: 'KhmerOSBattambang';
    //             src: url('../backend/fonts/KhmerOSbattambang.ttf') format('truetype');
    //             font-weight: normal;
    //             font-style: normal;
    //         }
    //         body {
    //             line-height: 1;
    //             font-family: 'Roboto' ,sans-serif, 'KhmerOSBattambang' !important;
    //             font-size: 16px;
    //         }
    //     `,
    //     setup: function (editor) {
    //         editor.on('init', function () {
    //             setTimeout(() => {
    //                 applyBootstrapTooltips(editor);
    //             }, 300);
    //         });
    //         editor.on('NodeChange', function () {
    //             applyBootstrapTooltips(editor);
    //         });
    //         editor.on('keyup change', function () {
    //             const content = editor.getContent({ format: 'text' }).trim();
    //             if (!content) {
    //                 editor.execCommand('mceAutoResize');
    //             } else {
    //                 editor.execCommand('mceAutoResize');
    //             }
    //         });
    //     },
    //     init_instance_callback : function(editor) {
    //         editor.on('focus', function () {
    //             removeActiveItem();
    //             const el            = editor.getElement();
    //             const boxItem       = $(el).closest('.item');
    //             boxItem.addClass('active');
    //             showTinymce(boxItem, true);
    //         });

    //         editor.on('blur', function () {
    //             editor.save();
    //             const el            = editor.getElement();
    //             const boxItem       = $(el).closest('.item');
    //             showTinymce(boxItem, false);
    //             sectionDataChange(el);
    //         });
    //     }
    // });


    var activeEditor    = null;
    var activeGap       = null;

    function loadEditorFromTextarea($editor) {
        const $textarea = $editor.closest('.col-lg-12').find('.gap-textarea');
        let html = $textarea.val() || '';

        html = html.replace(/<input[^>]*class="[^"]*gap-input[^"]*"[^>]*value="([^"]*)"[^>]*>/g, function(_, value) {
            return `<span class="gap gap-item">${value}</span>`;
        });

        $editor.html(html);
        reindexGaps($editor);
    }

    function initGapEditors(root = document) {
        const $root = root instanceof jQuery ? root : $(root);
        $root.find('.gap-editor').each(function () {
            loadEditorFromTextarea($(this));
        });
    }

    $(function () {
        $(document).on('click focus', '.gap-editor', function () {
            activeEditor = $(this);
        });

        $(document).on('click', '.gap-item', function (e) {
            e.stopPropagation();
            $('.gap-item').removeClass('active');
            $(this).addClass('active');
            activeGap = $(this);
        });

        $(document).on('click', function (e) {
            if ($(e.target).closest('.gap-item').length === 0) {
                $('.gap-item').removeClass('active');
                activeGap = null;
            }
        });

        $(document).on('input change paste', '.gap-editor', function (e) {
            buildTextareaValue($(this));
            questionDataChange(this);
        });

        initGapEditors(document);
    });

    function addGap(el) {
        if (!activeEditor) {
            notyfForm.success('Click inside the editor first');
            return;
        }

        const sel = window.getSelection();
        if (!sel.rangeCount) return;

        const range = sel.getRangeAt(0);
        if (range.collapsed) {
            notyfForm.success('Select text first');
            return;
        }

        if ($(range.startContainer).closest('.gap-item').length) {
            notyfForm.success('Already a gap');
            return;
        }

        const index = activeEditor.find('.gap-item').length + 1;
        const span  = document.createElement('span');
        span.className = 'gap gap-item';
        span.setAttribute('data-index', index);
        span.textContent = range.toString();

        range.deleteContents();
        range.insertNode(span);

        reindexGaps(activeEditor);
        buildTextareaValue(activeEditor);
        questionDataChange(el);
    }

    function removeGap(el) {
        if (!activeGap || !activeGap.length) {
            notyfForm.success('Select a gap first');
            return;
        }

        activeGap.replaceWith(
            document.createTextNode(activeGap.text())
        );

        activeGap = null;
        reindexGaps(activeEditor);
        buildTextareaValue(activeEditor);
        questionDataChange(el);
    }

    function clearStyleGap(el) {
        if (!activeEditor) {
            notyfForm.success('Click inside the editor first');
            return;
        }

        const text = activeEditor.text();

        activeEditor.html(
            $('<div>').text(text).html().replace(/\n/g, '<br>')
        );

        activeGap = null;

        reindexGaps(activeEditor);
        buildTextareaValue(activeEditor);
        questionDataChange(el);
    }

    function reindexGaps($editor) {
        $editor.find('.gap-item').each(function (i) {
            $(this).attr('data-index', i + 1);
        });
    }

    function buildTextareaValue($editor) {
        const questionId = $editor.data('question-id');
        const $textarea = $editor.closest('.col-lg-12').find('.gap-textarea');

        let html = $editor.html();

        html = html.replace(
            /<span class="gap gap-item" data-index="(\d+)">(.*?)<\/span>/g,
            function (_, index, text) {
                return `<input type="text" class="gap-input gap-${index}" name="gaps_${questionId}[]" data-index="${index}" value="${text}">`;
            }
        );

        $textarea.val(html.trim());
    }

    function showTinymce(el, isShow = false) {
        if (isShow === true) {
            $(el).addClass('text-editor').find('.footer-buttom').css({
                'margin-top': '60px',
                'transition': 'margin-top 0.3s ease',
            });
        } else {
            $(el).removeClass('text-editor').find('.footer-buttom').css({
                'margin-top': '0px',
                'transition': 'margin-top 0.3s ease',
            });
        }
    }

    $('.form-select2').each(function() {
        var $this           = $(this);
        var dynamicWidth    = getWidthFromClasses($this);
        var $parentModal    = $this.closest('.modal');
        $this.select2({
            minimumResultsForSearch: -1,
            dropdownParent: $parentModal.length ? $parentModal : $(document.body),
            width: dynamicWidth
        });
    });

    function applyBootstrapTooltips(editor) {
        const container = editor.getContainer();
        if (!container) return;

        const buttons = container.querySelectorAll(
            'button[title], button[aria-label], .tox-split-button'
        );

        buttons.forEach(btn => {
            const title =
                btn.getAttribute('title') ||
                btn.getAttribute('aria-label');

            if (!title) return;

            if (btn.dataset.bsToggle === 'tooltip') return;

            btn.setAttribute('data-bs-toggle', 'tooltip');
            btn.setAttribute('data-bs-placement', 'top');
            btn.setAttribute('title', title);
            new bootstrap.Tooltip(btn);
        });
    }

    function removeActiveItem(){
        $("#form .section-item").removeClass('active');
        $("#form .question-item").removeClass('active');
    }

    function sectionClick(el){
        removeActiveItem();
        $(el).addClass('active');
    }

    function questionClick(el){
        removeActiveItem();
        $(el).addClass('active');
    }

    function createForm(){
        const url = "{{ route('forms.create') }}";
        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function() {
                showLoader(true);
            },
            dataType: "JSON",
            success: function(response) {
                let formId = response.form.uuid;
                let newUrl = "{{ route('forms.index',':id') }}".replace(':id', formId);
                window.location.href = newUrl;
            },
            error: function(xhr) {
                showLoader(false);
            }
        });
    }

    function lockUI() {
        $('input, button').attr('disabled', 'disabled');

        $('a').not('.allow-active')
            .addClass('disabled-link')
            .css('pointer-events', 'none')
            .on('click.disable', function(e){ e.preventDefault(); });
    }

    function unlockUI() {
        $('input, button').removeAttr('disabled');

        $('a')
            .removeClass('disabled-link')
            .css('pointer-events', '')
            .off('click.disable');
    }

    function showAutoSaveSpaner(status = false){
        if (status === true) {
            // lockUI();
            $("#auto-save-spinner").removeClass('opacity-0');
        } else {
            // unlockUI();
            setTimeout(() => {
                $("#auto-save-spinner").addClass('opacity-0');
            }, 500);
        }
    }

    function sectionDataChange(el){
        let form        = $(el).parents('form');
        let sectionId   = form.data('section-id');

        if (itemTimers[sectionId]) {
            clearTimeout(itemTimers[sectionId]);
        }
        itemTimers[sectionId] = setTimeout(() => {
            autoSaveSection(form);
        }, 2000);
    }

    function autoSaveSection(form){
        let datas   = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: datas,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            dataType: "JSON",
            success: function(response) {
                showAutoSaveSpaner(false);
            },
            error: function(xhr) {
                showAutoSaveSpaner(false);
            }
        });
    }

    let isRecord        = false;
    let mediaRecorder   = null;
    let audioChunks     = [];
    let recordInterval  = null;
    let recordSeconds   = 0;
    let audioStream     = null;

    function sectionVoice(el) {
        let icon = $(el).find('i');
        if (!isRecord) {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    audioStream = stream;
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];
                    mediaRecorder.ondataavailable = e => {
                        audioChunks.push(e.data);
                    };
                    mediaRecorder.onstop = () => {
                        let audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        uploadVoice(audioBlob, el);
                        stopTimer();
                        audioStream.getTracks().forEach(track => track.stop());
                        audioStream = null;
                    };
                    mediaRecorder.start();
                    isRecord = true;
                    startTimer();
                    icon.removeClass('mdi-microphone').addClass('mdi-stop');
                    $(el).addClass('recording');
                })
                .catch(err => {
                    alert('Microphone permission denied');
                });

        } else {
            mediaRecorder.stop();
            isRecord = false;
            icon.removeClass('mdi-stop').addClass('mdi-microphone');
            $(el).removeClass('recording');
        }
    }

    function sectionBrowseFile(el) {
        const sectionId = $(el).closest('.section-item').data('section-id');
        const url = "{{ route('forms.sections.files.browse', ':section') }}".replace(':section', sectionId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("#modal-element").html(res);
                $("#modal-element #browse-section-files-modal").modal('show');
                UI.initDropify();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function sectionBrowseFilePC(el) {
        const sectionId = $(el).closest('.section-item').data('section-id');
        const url = "{{ route('forms.sections.files.browse', ':section') }}".replace(':section', sectionId) + '?tab=upload';

        $.ajax({
            url: url,
            type: 'GET',
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("#modal-element").html(res);
                $("#modal-element #browse-section-files-modal").modal('show');
                UI.initDropify();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function sectionUploadFilePC(sectionId, file) {
        const url = "{{ route('forms.sections.files.upload.pc', ':section') }}".replace(':section', sectionId);
        const formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                const html = res.document_html || '';
                if (html) {
                    $("#section-document" + sectionId).append(html);
                }
                $("#modal-element #browse-section-files-modal").modal('hide');
                initVoicePlayers('body');
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function sectionAttachHistoryFile(sectionId, fileId) {
        const url = "{{ route('forms.sections.files.attach.history', ':section') }}".replace(':section', sectionId);
        const formData = new FormData();
        formData.append('file_id', fileId);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                const html = res.document_html || '';
                if (html && !res.exists) {
                    $("#section-document" + sectionId).append(html);
                }
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function startTimer() {
        recordSeconds = 0;
        updateTimer();

        recordInterval = setInterval(() => {
            recordSeconds++;
            updateTimer();
        }, 1000);
    }

    function stopTimer() {
        clearInterval(recordInterval);
        recordInterval = null;
        recordSeconds = 0;
        updateTimer();
        $('#rec-hh').removeClass('text-danger');
        $('#rec-mm').removeClass('text-danger');
        $('#rec-ss').removeClass('text-danger');
    }

    function updateTimer() {
        let hh = String(Math.floor(recordSeconds / 3600)).padStart(2, '0');
        let mm = String(Math.floor((recordSeconds % 3600) / 60)).padStart(2, '0');
        let ss = String(recordSeconds % 60).padStart(2, '0');

        $('#rec-hh').text(hh).addClass('text-danger');
        $('#rec-mm').text(mm).addClass('text-danger');
        $('#rec-ss').text(ss).addClass('text-danger');
    }

    function uploadVoice(blob, el) {
        let url         = "{{ route('forms.sections.voices.store') }}";
        let section_id  = $(el).closest('.item').data('item-id');
        let formData    = new FormData();
        formData.append('voice', blob, 'voice.ogg');
        formData.append('section_id', section_id);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                let documents = res.document_html || '';
                $("#section-document" + section_id).append(documents);
                initVoicePlayers('body');
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function activeNexQuestion(el){
        const $currentForm = $(el).closest('form');
        const $nextQuestionCard = $currentForm.next('form').find('.question-item').first();
        if ($nextQuestionCard.length) {
            $nextQuestionCard.addClass('active');
        }
    }

    function scrollToElement(el) {
        if (el && el.length) {
            $('html, body').animate({
                scrollTop: el.offset().top - 50
            }, 0);
        }
    }

    function addQuestion(el){
        const $contextCard = $(el).closest('.item');
        const $anchorForm = $contextCard.closest('form');
        let url         = "{{ route('forms.questions.create') }}";
        let section_id  = $contextCard.data('section-id');
        let form_id     = getCurrentFormId() || $contextCard.data('form-id');
        let after_question_id = $contextCard.data('question-id');
        if (!after_question_id && $contextCard.hasClass('section-item')) {
            // Insert at top of section (right after section card)
            after_question_id = 0;
        }
        let formData    = new FormData();
        formData.append('form_id', form_id);
        formData.append('section_id', section_id);
        formData.append('after_question_id', after_question_id);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                var newItem = $(res);
                $anchorForm.after(newItem);
                showAutoSaveSpaner(false);
                initSelect2();
                initTooltips();
                initGapEditors(newItem);
                removeActiveItem();
                activeNexQuestion(el);
                scrollToElement(newItem);
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function copyQuestion(el) {
        const $card      = $(el).closest('.question-item');
        const questionId = $card.data('question-id');
        const formId     = getCurrentFormId() || $card.data('form-id');
        const sectionId  = $card.data('section-id');

        if (!questionId || !formId || !sectionId) {
            console.warn('Missing ids for copyQuestion');
            return;
        }

        const url = "{{ route('forms.questions.copy', ':question') }}".replace(':question', questionId);
        const $currentForm = $card.closest('form');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                form_id: formId,
                section_id: sectionId,
                after_question_id: questionId,
            },
            dataType: 'HTML',
            beforeSend: function () {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                const $newItem = $(res);
                $currentForm.after($newItem);
                showAutoSaveSpaner(false);
                initSelect2();
                initTooltips();
                initGapEditors($newItem);
                removeActiveItem();
                scrollToElement($newItem);
            },
            error: function (xhr) {
                showAutoSaveSpaner(false);
                handleErrorMessage(xhr);
            }
        });
    }

    function deleteQuestion(el) {
        confirmAction().then(isConfirmed => {
            let section_id      = $(el).parents('.item').data('section-id');
            let question_id     = $(el).parents('.item').data('question-id');
            let form_id         = $(el).parents('.item').data('form-id');
            let url             = "{{ route('forms.questions.destroy') }}";
            if (isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        form_id: form_id,
                        section_id: section_id,
                        question_id: question_id
                    },
                    dataType: "JSON",
                    beforeSend: function() {
                        showAutoSaveSpaner(true);
                    },
                    success: function (response) {
                        showAutoSaveSpaner(false);
                        $(el).closest('.question-item').remove();
                    },
                    error: function () {
                        showAutoSaveSpaner(false);
                    }
                });
            }
        });
    }

    function deleteInstruction(el) {
        return deleteQuestion(el);
    }

    function addInstruction(el){
        const $contextCard = $(el).closest('.item');
        const $anchorForm = $contextCard.closest('form');
        let url         = "{{ route('forms.questions.create') }}";
        let section_id  = $contextCard.data('section-id');
        let form_id     = getCurrentFormId() || $contextCard.data('form-id');
        let after_question_id = $contextCard.data('question-id');
        let formData    = new FormData();
        formData.append('form_id', form_id);
        formData.append('section_id', section_id);
        formData.append('after_question_id', after_question_id);
        formData.append('question_type', 'instruction');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                var newItem = $(res);
                $anchorForm.after(newItem);
                showAutoSaveSpaner(false);
                initSelect2();
                initTooltips();
                initGapEditors(newItem);
                removeActiveItem();
                activeNexQuestion(el);
                scrollToElement(newItem);
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function addSection(el) {
        const $sectionCard = $(el).closest('.section-item');
        const $questionCard = $(el).closest('.question-item');
        const $contextCard = $sectionCard.length ? $sectionCard : $questionCard;

        const formId = getCurrentFormId() || ($contextCard.length ? $contextCard.data('form-id') : null);
        const afterSectionId =
            ($sectionCard.length ? $sectionCard.data('section-id') : null) ||
            ($questionCard.length ? $questionCard.data('section-id') : null);

        if (!formId) {
            console.warn('Missing form_id for addSection');
            return;
        }

        const url = "{{ route('forms.sections.create') }}";
        const $currentListSection = $(el).closest('.list-section-item');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'HTML',
            data: {
                form_id: formId,
                after_section_id: afterSectionId || null,
            },
            beforeSend: function () {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                const $newSection = $(res);
                if ($currentListSection.length) {
                    $currentListSection.after($newSection);
                } else {
                    $('#form').append($newSection);
                }
                showAutoSaveSpaner(false);
                initSelect2();
                initTooltips();
                initQuestionDragula();
                removeActiveItem();
                scrollToElement($newSection);

                const formEl = document.getElementById('form');
                if (formEl) {
                    persistSectionOrder(formEl);
                }
            },
            error: function (xhr) {
                showAutoSaveSpaner(false);
                handleErrorMessage(xhr);
            }
        });
    }

    function copySection(el) {
        const $sectionCard = $(el).closest('.section-item');
        const sectionId = $sectionCard.data('section-id');
        const formId = getCurrentFormId();

        if (!sectionId || !formId) {
            console.warn('Missing ids for copySection');
            return;
        }

        const url = "{{ route('forms.sections.copy', ':section') }}".replace(':section', sectionId);
        const $currentListSection = $(el).closest('.list-section-item');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'HTML',
            data: {
                form_id: formId,
            },
            beforeSend: function () {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                const $newItem = $(res);
                $currentListSection.after($newItem);
                showAutoSaveSpaner(false);
                initSelect2();
                initTooltips();
                initQuestionDragula();
                scrollToElement($newItem);
            },
            error: function (xhr) {
                showAutoSaveSpaner(false);
                handleErrorMessage(xhr);
            }
        });
    }

    function deleteSection(el) {
        const $currentListSection = $(el).closest('.list-section-item');
        const $sectionCard = $(el).closest('.section-item');
        const sectionId = $sectionCard.data('section-id');
        const formId = getCurrentFormId();

        if (!sectionId || !formId) {
            console.warn('Missing ids for deleteSection');
            return;
        }

        const $questionForms = $currentListSection
            .children('form')
            .filter(function () {
                return $(this).find('.question-item').length > 0;
            });

        const hasQuestions = $questionForms.length > 0;
        const $prevListSection = $currentListSection.prev('.list-section-item');
        const prevSectionId = $prevListSection.length
            ? ($prevListSection.find('.section-item').first().data('section-id') || null)
            : null;
        const $nextListSection = $currentListSection.next('.list-section-item');
        const nextSectionId = $nextListSection.length
            ? ($nextListSection.find('.section-item').first().data('section-id') || null)
            : null;

        function doDeleteSection(questionAction = null, destinationId = null, $destinationListSection = null) {
            const url = "{{ route('forms.sections.destroy') }}";

            const payload = {
                form_id: formId,
                section_id: sectionId,
            };

            if (questionAction) {
                payload.question_action = questionAction;
            }
            if (questionAction === 'move' && destinationId) {
                payload.destination_section_id = destinationId;
            }

            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'JSON',
                data: payload,
                beforeSend: function () {
                    showAutoSaveSpaner(true);
                },
                success: function () {
                    if (questionAction === 'move' && destinationId && $destinationListSection && $destinationListSection.length) {
                        $questionForms.each(function () {
                            const $form = $(this);
                            const $card = $form.find('.question-item').first();
                            $card.attr('data-section-id', destinationId).data('section-id', destinationId);

                            $form.find('.file-item').each(function () {
                                $(this).attr('data-section-id', destinationId).data('section-id', destinationId);
                            });
                        });
                        $destinationListSection.append($questionForms);
                        initQuestionDragula();
                    }

                    $currentListSection.remove();
                    showAutoSaveSpaner(false);
                    persistSectionOrder(document.getElementById('form'));
                },
                error: function (xhr) {
                    showAutoSaveSpaner(false);
                    handleErrorMessage(xhr);
                }
            });
        }

        if (!hasQuestions) {
            confirmAction().then(isConfirmed => {
                if (!isConfirmed) return;
                doDeleteSection();
            });
            return;
        }

        // If there is a section above, auto-move questions there (no alert)
        if (prevSectionId) {
            doDeleteSection('move', prevSectionId, $prevListSection);
            return;
        }

        if (window.Swal) {
            const swalOpts = {
                title: 'Section Confirmation?',
                text: "This section has questions please choose an action!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Remove Question',
                cancelButtonText: 'Cancel',
            };

            if (nextSectionId) {
                swalOpts.showDenyButton = true;
                swalOpts.denyButtonText = 'Move to Next section';
            }

            Swal.fire(swalOpts).then((result) => {
                if (result.isConfirmed) {
                    doDeleteSection('delete');
                } else if (result.isDenied && nextSectionId) {
                    doDeleteSection('move', nextSectionId, $nextListSection);
                }
            });
        } else {
            const remove = window.confirm('Question confirm.....?\n\nOK = Remove Question\nCancel = Cancel');
            if (remove) {
                doDeleteSection('delete');
            }
        }
    }

    function questionDataChange(el){
        let form            = $(el).parents('form');
        let questionId      = form.data('question-id');

        if (itemTimers[questionId]) {
            clearTimeout(itemTimers[questionId]);
        }
        itemTimers[questionId] = setTimeout(() => {
            autoSaveQuestion(form);
        }, 500);
    }

    function autoSaveQuestion(form){
        let formData    = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    $(document).on('change', '.question-item input, .question-item .true-false-option, .question-item .setting', function (e) {
        let form            = $(this).parents('form');
        let questionId      = form.data('section-id');

        if (itemTimers[questionId]) {
            clearTimeout(itemTimers[questionId]);
        }
        itemTimers[questionId] = setTimeout(() => {
            autoSaveQuestion(form);
        }, 1000);
    });

    function formDeleteFile(el, file_id){
        let url             = "{{ route('forms.files.destroy', ':file') }}".replace(':file', file_id);
        let section_id      = $(el).closest('.file-item').data('section-id');
        let question_id     = $(el).closest('.file-item').data('question-id');
        let file_for        = $(el).closest('.file-item').data('file-for');

        $.ajax({
            url: url,
            type: 'DELETE',
            data: {section_id: section_id, question_id: question_id, file_for: file_for},
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $(el).closest('.file-item').remove();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function questionVoice(el){
        let form_id         = $(el).parents('.item').data('form-id');
        let question_id     = $(el).parents('.item').data('question-id');
        const url           = "{{ route('forms.questions.voices.create',':question') }}".replace(':question', question_id);

        $.ajax({
            url: url,
            type: "GET",
            data: {
                'form_id': form_id,
                'question_id': question_id,
            },
            dataType: "HTML",
            success: function (res) {
                $("modal-element").html(res);
                $("#modal-element #question-record-modal").modal('show');
            },
            error: function () {

            }
        });
    }

    function addOption(questionId, optionId){
        const url = "{{ route('forms.questions.options.add', ':question') }}".replace(':question', questionId);

        $.ajax({
            url: url,
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("#question-content" + questionId).append(res);
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function removeOption(questionId, optionId){
        const url = "{{ route('forms.questions.options.remove', ':question') }}".replace(':question', questionId);

        $.ajax({
            url: url,
            type: 'DELETE',
            data: {'option_id': optionId},
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("#option-item" + optionId).remove();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function questionChangeType(el, questionId){
        let type    = $(el).val();
        const url   = "{{ route('forms.questions.change.type', ':question') }}".replace(':question', questionId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {type: type},
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("#question-content" + questionId).html(res);
                initTooltips();
                initSelect2();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function toggleIsLimitWords(checkbox) {
        const inputGroup    = checkbox.closest('.input-group');
        const inputs        = inputGroup.querySelectorAll('input[type="number"]');

        if (checkbox.checked) {
            inputs.forEach(input => {
                input.disabled = false;
            });
        } else {
            inputs.forEach(input => {
                input.value = '';
                input.disabled = true;
            });
        }
    }

    function toggleIsValidateText(checkbox) {
        const inputGroup    = checkbox.closest('.input-group');
        const inputs        = inputGroup.querySelectorAll('input:not([type="checkbox"])');

        if (checkbox.checked) {
            inputs.forEach(input => {
                input.disabled = false;
            });
        } else {
            inputs.forEach(input => {
                input.value = '';
                input.disabled = true;
            });
        }
    }

    function toggleAssignPoint(checkbox) {
        const inputGroup = checkbox.closest('.input-group');
        const input = inputGroup.querySelector('input[name="point"]');

        if (!input) return;

        if (checkbox.checked) {
            input.disabled = false;
        } else {
            input.value = '';
            input.disabled = true;
        }
    }

    $(document).on('change', '[id^="flexSwitchCheck"]', function () {
        const $switch       = $(this);
        const questionId    = this.id.replace('flexSwitchCheck', '');
        const $container    = $('#question-item' + questionId);

        const $options      = $container.find('.file-type-options');

        if ($switch.is(':checked')) {
            $options.each(function () {
                $(this).addClass('show').css('max-height', this.scrollHeight + 'px');
            });
        } else {
            $options.each(function () {
                $(this).removeClass('show').css('max-height', '0px');
                $container.find('.check-input-file-type').prop('checked', false);
            });
        }
    });

    $(document).on('change', '.check-input-file-type', function (e) {
        const $checkbox     = $(this);
        const $container    = $checkbox.closest('[id^="question-item"]');
        const questionId    = $container.attr('id').replace('question-item', '');

        const $switch       = $('#flexSwitchCheck' + questionId);

        if (!$switch.is(':checked')) {
            e.preventDefault();
            $checkbox.prop('checked', false);
        }
    });

    function optionBrowseImage(optionId) {
        const url = "{{ route('forms.questions.options.browse.image', ':option') }}".replace(':option', optionId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("modal-element").html(res);
                $("#modal-element #browse-image-modal").modal('show');
                UI.initDropify();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    $(document).on('change', '.dropify', function (e) {
        const file          = this.files && this.files[0];
        const optionId      = $(this).data('option-id') || null;
        const questionId    = $(this).data('question-id') || null;
        const sectionId     = $(this).data('section-id') || null;
        const type          = $(this).data('upload-type') || null;
        if (file && optionId && type === 'option') {
            uploadOptionImage(optionId, file);
        }
        if (file && questionId && type === 'question') {
            questionUploadImage(questionId, file);
        }
        if (file && sectionId && type === 'section') {
            sectionUploadFilePC(sectionId, file);
        }
    });

    function optionImageSpaceToggle(optionFile){
        const hasImg            = optionFile.find("img").length > 0;
        optionFile.toggleClass("mb-2", hasImg);
        optionFile.toggleClass("mt-2", hasImg);
    }

    function uploadOptionImage(optionId, file) {
        const url       = "{{ route('forms.questions.options.upload.image', ':option') }}".replace(':option', optionId);
        const formData  = new FormData();
        formData.append('image', file);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                let image_html          = res.image_html || '';
                const optionFile        = $("#option-file" + optionId);
                optionFile.html(image_html);
                optionImageSpaceToggle(optionFile);
                $("#modal-element #browse-image-modal").modal('hide');
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function optionImageRemove(el, optionId) {
        const url = "{{ route('forms.questions.options.remove.image', ':option') }}".replace(':option', optionId);

        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                const optionFile        = $("#option-file" + optionId);
                optionFile.html('');
                optionImageSpaceToggle(optionFile);
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function questionAttachImage(questionId) {
        const url = "{{ route('forms.questions.images.browse', ':question') }}".replace(':question', questionId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: "HTML",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                $("#modal-element").html(res);
                $("#modal-element #browse-images-modal").modal('show');
                UI.initDropify();
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function questionUploadImage(questionId, file) {
        const url       = "{{ route('forms.questions.images.upload', ':question') }}".replace(':question', questionId);
        const formData  = new FormData();
        formData.append('image', file);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                let image_html              = res.image_html || '';
                const documentFile          = $("#question-document" + questionId);
                if (image_html) {
                    documentFile.append(image_html);
                }
                $("#modal-element #browse-images-modal").modal('hide');
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function questionRemoveImage(el, questionId) {
        const url = "{{ route('forms.questions.images.remove', ':question') }}".replace(':question', questionId);

        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: "JSON",
            beforeSend: function() {
                showAutoSaveSpaner(true);
            },
            success: function (res) {
                showAutoSaveSpaner(false);
                const optionFile        = $("#question-file" + questionId);
                optionFile.html('');
            },
            error: function () {
                showAutoSaveSpaner(false);
            }
        });
    }

    function confirmPublicForm() {
        return Swal.fire({
            title: 'Confirm Publish Form',
            text: 'Are you sure you want to make this form public?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Publish',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            return result.isConfirmed;
        });
    }

    function publicForm(el, formId) {
        confirmPublicForm().then(isConfirmed => {
            if (!isConfirmed) return;
            const url = "{{ route('forms.public.index', ':form') }}".replace(':form', formId);
             $.ajax({
                url: url,
                type: 'POST',
                processData: false,
                contentType: false,
                dataType: "JSON",
                beforeSend: function() {
                    showAutoSaveSpaner(true);
                },
                success: function (res) {
                    showAutoSaveSpaner(false);
                    $(el).closest('li').remove();
                },
                error: function () {
                    showAutoSaveSpaner(false);
                }
            });
        });
    }
</script>
