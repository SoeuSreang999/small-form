  <script>
    let formDuration = {{ $formDuration }};

    $(document).ready(function () {
      UI.initDropify();
      initPreviewMatchingDragula();
      initPreviewGapFillingDragDrop();
      initGapWordsTouchScroll();
      countDownDuration(formDuration);
      window.__previewGapDragSourceContentId = null;

      if (!document.getElementById('preview-matching-drag-style')) {
        $('head').append(`
          <style id="preview-matching-drag-style">
            .gu-mirror.matching-answer-card,
            .gu-mirror.ordering-answer-card {
              border: 1px solid #1573b1;
            }

            .gu-transit.matching-answer-card,
            .gu-transit.ordering-answer-card {
              border-color: red;
            }

            .word-item.is-dragging {
              opacity: 0.5;
              transform: scale(0.95);
              cursor: grabbing !important;
            }

            .word-item.is-dragging .vr {
              display: none;
            }

            body.gap-word-dragging,
            body.gap-word-dragging * {
              cursor: grabbing !important;
            }
          </style>
        `);
      }

      function countDownDuration(duration) {
        let remaining = duration;
            remaining = remaining * 60;

        const hoursEl = document.querySelector('.hours_duration');
        const minutesEl = document.querySelector('.minutes_duration');
        const secondsEl = document.querySelector('.seconds_duration');

        const intervalId = setInterval(function () {
          if (remaining <= 0) {
            clearInterval(intervalId);
            return;
          }

          const hours = Math.floor(remaining / 3600);
          const minutes = Math.floor((remaining % 3600) / 60);
          const seconds = remaining % 60;

          if (hoursEl) {
            hoursEl.textContent = String(hours).padStart(2, '0');
          }
          if (minutesEl) {
            minutesEl.textContent = String(minutes).padStart(2, '0');
          }
          if (secondsEl) {
            secondsEl.textContent = String(seconds).padStart(2, '0');
          }

          remaining--;
        }, 1000);
      }

      function initPreviewMatchingDragula() {
        if (!window.dragula) {
          return;
        }

        window.__previewMatchingDrakes = window.__previewMatchingDrakes || {};

        $('[id^="content-question-"]').each(function () {
          const contentEl = this;
          const contentId = contentEl.id;

          if (window.__previewMatchingDrakes[contentId]) {
            return;
          }

          const answerList =
            $(contentEl).find('[data-answer-list]').get(0) ||
            detectOrderingAnswerList(contentEl);

          if (!answerList) {
            return;
          }

          const drake = window.dragula([answerList], {
            moves: function (el, source, handle) {
              return $(el).is('[data-move-item="true"]') && $(source).is('[data-answer-list="true"]') && $(handle).closest('[data-move-item="true"]').length > 0;
            },
            accepts: function (el, target) {
              return $(target).is('[data-answer-list="true"]');
            },
            revertOnSpill: true,
            removeOnSpill: false,
            direction: 'vertical'
          });

          window.__previewMatchingDrakes[contentId] = drake;

          drake.on('drag', function () {
            document.body.style.cursor = 'grabbing';
          });

          drake.on('cloned', function (clone, original, type) {
            if (type !== 'mirror') {
              return;
            }

            $(clone)
              .find('i.mdi')
              .first()
              .attr('class', 'mdi mdi-arrow-up-down');
          });

          drake.on('drop', function () {
            document.body.style.cursor = '';
          });

          drake.on('cancel', function () {
            document.body.style.cursor = '';
          });

          drake.on('dragend', function () {
            document.body.style.cursor = '';
          });
        });
      }

      function detectOrderingAnswerList(contentEl) {
        const orderingCards = $(contentEl).find('.ordering-answer-card[data-move-item="true"]');
        if (orderingCards.length < 2) {
          return null;
        }

        const firstItemWrapper = orderingCards.first().closest('.ordering-answer-card');
        if (!firstItemWrapper.length) {
          return null;
        }

        const listContainer = firstItemWrapper.parent();
        if (listContainer.find('.ordering-answer-card[data-move-item="true"]').length < 2) {
          return null;
        }

        listContainer.attr('data-answer-list', 'true');
        return listContainer.get(0);
      }

      function initPreviewGapFillingDragDrop() {
        $('[id^="content-question-"]').each(function () {
          const contentEl = this;
          const contentId = contentEl.id;
          const $content = $(contentEl);
          const $gapInputs = $content.find('.gapfilling-content input');
          const dragDropEnabled = $(contentEl)
            .find('[gapfilling-drag-drop]')
            .first()
            .attr('gapfilling-drag-drop');

          $gapInputs.each(function () {
            const input = this;

            if (input.dataset.valueSyncReady !== '1') {
              input.dataset.valueSyncReady = '1';
              input.setAttribute('value', input.value || '');

              input.addEventListener('input', function () {
                input.setAttribute('value', input.value || '');
              });

              input.addEventListener('change', function () {
                input.setAttribute('value', input.value || '');
              });
            }
          });

          if (!isTruthy(dragDropEnabled)) {
            return;
          }

          $(contentEl)
            .find('.gapping-words .word-item[data-word]')
            .each(function () {
              const $wordItem = $(this);
              const wordItem = this;

              if (wordItem.dataset.dragReady === '1') {
                return;
              }

              wordItem.dataset.dragReady = '1';
              wordItem.setAttribute('draggable', 'true');

              $wordItem
                .off('dragstart.gapWord drag.gapWord dragend.gapWord')
                .on('dragstart.gapWord', function (event) {
                  const nativeEvent = event.originalEvent || event;
                  const word = $wordItem.data('word') || '';

                  nativeEvent.dataTransfer.setData('text/plain', String(word));
                  nativeEvent.dataTransfer.setData('word-item-id', wordItem.id || '');
                  nativeEvent.dataTransfer.setData('content-question-id', contentId);
                  nativeEvent.dataTransfer.effectAllowed = 'move';
                  window.__previewGapDragSourceContentId = contentId;
                  document.addEventListener('dragover', onGlobalGapDragOver, true);
                  document.addEventListener('dragenter', onGlobalGapDragEnter, true);

                  $('body').addClass('gap-word-dragging');
                    document.body.style.cursor = 'grabbing';
                  })
                .on('drag.gapWord', function () {
                  $wordItem.addClass('is-dragging');
                  $('body').addClass('gap-word-dragging');
                  document.body.style.cursor = 'grabbing';
                })
                .on('dragend.gapWord', function () {
                  $wordItem.removeClass('is-dragging');
                  endGapWordDrag();
                });
            });

          $gapInputs.each(function () {
              const input = this;

              if (input.dataset.dropReady === '1') {
                return;
              }

              input.dataset.dropReady = '1';

              input.addEventListener('dragover', function (event) {
                if (window.__previewGapDragSourceContentId !== contentId) {
                  return;
                }

                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
              });

              input.addEventListener('drop', function (event) {
                event.preventDefault();

                const sourceContentId = event.dataTransfer.getData('content-question-id') || '';
                if (sourceContentId !== contentId || window.__previewGapDragSourceContentId !== contentId) {
                  return;
                }

                const word = event.dataTransfer.getData('text/plain') || '';
                if (!word) {
                  return;
                }

                setGapInputValue(input, word);

                const draggedItemId = event.dataTransfer.getData('word-item-id');
                if (draggedItemId) {
                  const draggedItem = document.getElementById(draggedItemId);
                  if (draggedItem) {
                    draggedItem.classList.add('text-decoration-line-through');
                  }
                }

                endGapWordDrag();
              });
            });

          const gapContentEl = $content.find('.gapfilling-content').get(0);
          if (gapContentEl && gapContentEl.dataset.dropZoneReady !== '1') {
            gapContentEl.dataset.dropZoneReady = '1';

            gapContentEl.addEventListener('dragover', function (event) {
              if (window.__previewGapDragSourceContentId !== contentId) {
                return;
              }

              event.preventDefault();
              event.dataTransfer.dropEffect = 'move';
            });

            gapContentEl.addEventListener('drop', function (event) {
              if (window.__previewGapDragSourceContentId !== contentId) {
                return;
              }

              const sourceContentId = event.dataTransfer.getData('content-question-id') || '';
              if (sourceContentId !== contentId) {
                return;
              }

              const word = event.dataTransfer.getData('text/plain') || '';
              if (!word) {
                return;
              }

              const targetInput = event.target.closest('input')
                || $gapInputs.filter(function () { return !this.value; }).first().get(0)
                || $gapInputs.first().get(0);

              if (!targetInput) {
                return;
              }

              event.preventDefault();
              setGapInputValue(targetInput, word);

              const draggedItemId = event.dataTransfer.getData('word-item-id');
              if (draggedItemId) {
                const draggedItem = document.getElementById(draggedItemId);
                if (draggedItem) {
                  draggedItem.classList.add('text-decoration-line-through');
                }
              }

              endGapWordDrag();
            });
          }
        });
      }

      function onGlobalGapDragOver(event) {
        if (!window.__previewGapDragSourceContentId) {
          return;
        }

        event.preventDefault();
        if (event.dataTransfer) {
          event.dataTransfer.dropEffect = 'move';
        }
      }

      function onGlobalGapDragEnter(event) {
        if (!window.__previewGapDragSourceContentId) {
          return;
        }

        event.preventDefault();
        if (event.dataTransfer) {
          event.dataTransfer.dropEffect = 'move';
        }
      }

      function endGapWordDrag() {
        $('body').removeClass('gap-word-dragging');
        window.__previewGapDragSourceContentId = null;
        document.body.style.cursor = '';
        document.removeEventListener('dragover', onGlobalGapDragOver, true);
        document.removeEventListener('dragenter', onGlobalGapDragEnter, true);
      }

      function initGapWordsTouchScroll() {
        const isTouchDevice = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;
        if (!isTouchDevice) {
          return;
        }

        $('.gapping-words').each(function () {
          const container = this;

          if (container.dataset.touchScrollReady === '1') {
            return;
          }

          container.dataset.touchScrollReady = '1';

          let startX = 0;
          let startY = 0;
          let scrollLeft = 0;
          let isDragging = false;
          let lockHorizontal = false;

          container.addEventListener('touchstart', function (event) {
            const touch = event.touches[0];
            startX = touch.pageX;
            startY = touch.pageY;
            scrollLeft = container.scrollLeft;
            isDragging = true;
            lockHorizontal = false;
          }, { passive: true });

          container.addEventListener('touchmove', function (event) {
            if (!isDragging) {
              return;
            }

            const touch = event.touches[0];
            const deltaX = touch.pageX - startX;
            const deltaY = touch.pageY - startY;

            if (!lockHorizontal) {
              lockHorizontal = Math.abs(deltaX) > Math.abs(deltaY);
            }

            if (!lockHorizontal) {
              return;
            }

            container.scrollLeft = scrollLeft - deltaX;
            event.preventDefault();
          }, { passive: false });

          container.addEventListener('touchend', function () {
            isDragging = false;
            lockHorizontal = false;
          }, { passive: true });
        });
      }

      function setGapInputValue(inputEl, value) {
        const nextValue = String(value || '');
        inputEl.value = nextValue;
        inputEl.setAttribute('value', nextValue);
        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
        inputEl.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function isTruthy(value) {
        const normalized = String(value || '').trim().toLowerCase();
        return normalized === '1' || normalized === 'true' || normalized === 'yes' || normalized === 'on';
      }

      $(document)
        .off('change.previewDropifyMaxFiles', 'input.dropify')
        .on('change.previewDropifyMaxFiles', 'input.dropify', function () {
          const maxFiles = parseInt($(this).data('max-files'), 10);
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
        })
        .off('dropify.errors.previewDropifyValidation', 'input.dropify')
        .on('dropify.errors.previewDropifyValidation', 'input.dropify', function (event) {
          const $input = $(this);
          const errors = event?.errors || [];

          if (errors.includes('fileSize')) {
            const maxSize = $input.data('max-file-size') || 'the allowed limit';
            const message = `File size is too large. Max ${maxSize} per file.`;
            if (typeof notyfForm !== 'undefined') {
              notyfForm.error(message);
            } else {
              alert(message);
            }
            return;
          }

          if (errors.includes('fileExtension')) {
            const message = 'This file type is not allowed.';
            if (typeof notyfForm !== 'undefined') {
              notyfForm.error(message);
            } else {
              alert(message);
            }
          }
        });
    });

    tinymce.init({
      selector: '.text-editor-tinymce',
      height: 200,
      license_key: 'gpl',
      menubar: false,
      plugins: [
        'advlist autolink lists link image charmap preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
      ],
      toolbar:
        'undo redo | formatselect | bold italic underline backcolor | ' +
        'alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist outdent indent | removeformat | help',
      setup: function (editor) {
        editor.on('init', function () {
          const container = editor.getContainer();
          if (!container) {
            return;
          }

          const header = container.querySelector('.tox-editor-header');
          if (!header) {
            return;
          }

          header.style.display = 'flex';
          header.style.position = 'static';
          header.style.bottom = 'auto';
        });
      }
    });
  </script>
