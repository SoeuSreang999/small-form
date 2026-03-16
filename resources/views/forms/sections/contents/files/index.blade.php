@php
    $monthNames = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
        '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
        '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec',
    ];

    $groupedHistories = $histories
        ->filter(fn($file) => !empty($file->created_at))
        ->groupBy(fn($file) => $file->created_at->format('Y'))
        ->map(function ($filesByYear) {
            return $filesByYear->groupBy(fn($file) => $file->created_at->format('m'));
        })
        ->sortKeysDesc();

    $formatSize = function ($bytes) {
        $bytes = (int) ($bytes ?? 0);
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = (int) floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);
        $value = $bytes / (1024 ** $pow);
        return number_format($value, $pow === 0 ? 0 : 1) . ' ' . $units[$pow];
    };

    $fileIconClass = function ($type) {
        $type = strtolower($type ?? '');
        if ($type === 'pdf') {
            return 'fa-solid fa-file-pdf text-danger';
        }
        if (in_array($type, ['doc', 'docx', 'txt', 'rtf'])) {
            return 'fa-solid fa-file-lines text-primary';
        }
        if (in_array($type, ['xls', 'xlsx', 'csv'])) {
            return 'fa-solid fa-file-excel text-success';
        }
        if (in_array($type, ['ppt', 'pptx'])) {
            return 'fa-solid fa-file-powerpoint text-warning';
        }
        if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'])) {
            return 'fa-solid fa-file-image text-info';
        }
        if (in_array($type, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'])) {
            return 'fa-solid fa-file-audio text-pink';
        }
        if (in_array($type, ['mp4', 'webm', 'mov', 'avi', 'mkv'])) {
            return 'fa-solid fa-file-video text-purple';
        }
        return 'fa-solid fa-file text-secondary';
    };
@endphp

<div class="modal fade" id="browse-section-files-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ ($activeTab ?? 'history') === 'upload' ? 'active' : '' }}"
                                        data-bs-toggle="tab" href="#section-upload" role="tab" aria-selected="{{ ($activeTab ?? 'history') === 'upload' ? 'true' : 'false' }}">
                                        Upload File
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ ($activeTab ?? 'history') === 'history' ? 'active' : '' }}"
                                        data-bs-toggle="tab" href="#section-history" role="tab" aria-selected="{{ ($activeTab ?? 'history') === 'history' ? 'true' : 'false' }}">
                                        History Files
                                    </a>
                                </li>
                                <li class="nav-item ms-auto">
                                    <i class="fas fa-close fs-5 m-2 cursor-pointer" data-bs-dismiss="modal" aria-label="Close"></i>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane p-3 {{ ($activeTab ?? 'history') === 'upload' ? 'active' : '' }}" id="section-upload" role="tabpanel">
                                    <input type="file" name="file" class="form-control dropify" data-upload-type="section" data-section-id="{{ $section->id }}">
                                </div>
                                <div class="tab-pane p-3 {{ ($activeTab ?? 'history') === 'history' ? 'active' : '' }}" id="section-history" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-3 border-end">
                                            <div id="section-history-tree" class="small">
                                                <ul>
                                                    <li data-filter="all">All Files</li>
                                                    @foreach ($groupedHistories as $year => $months)
                                                        <li data-filter="year-{{ $year }}">
                                                            {{ $year }}
                                                            <ul>
                                                                @foreach ($months->sortKeysDesc() as $month => $filesInMonth)
                                                                    <li data-filter="{{ $year }}-{{ $month }}">
                                                                        {{ $monthNames[$month] ?? $month }} ({{ $filesInMonth->count() }})
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="table-responsive browser_users">
                                                <table class="table mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="border-top-0">Name</th>
                                                            <th class="border-top-0 text-end">Last Modified</th>
                                                            <th class="border-top-0 text-end">Size</th>
                                                            <th class="border-top-0 text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="section-history-file-rows">
                                                        @forelse ($histories as $history)
                                                            @php
                                                                $yearMonth = $history->created_at ? $history->created_at->format('Y-m') : 'unknown';
                                                                $yearOnly = $history->created_at ? 'year-' . $history->created_at->format('Y') : 'year-unknown';
                                                            @endphp
                                                            <tr class="history-file-row" data-filter="{{ $yearMonth }}" data-year-filter="{{ $yearOnly }}">
                                                                <td>
                                                                   {{ $history->name ?? $history->name_en ?? basename($history->path) }}
                                                                </td>
                                                                <td class="text-end">{{ $history->created_at ? $history->created_at->format('d M Y') : '-' }}</td>
                                                                <td class="text-end">{{ $formatSize($history->size ?? 0) }}</td>
                                                                <td class="text-end">
                                                                    <a href="javascript:void(0);" title="Use File"
                                                                        onclick="sectionAttachHistoryFile({{ $section->id }}, {{ $history->id }});">
                                                                        <i class="las la-plus-circle text-secondary fs-18"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">No history files found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        function loadJsTree(callback) {
            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.jstree) {
                callback();
                return;
            }

            if (!document.getElementById('jstree-style-cdn')) {
                const link = document.createElement('link');
                link.id = 'jstree-style-cdn';
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/jstree@3.3.16/dist/themes/default/style.min.css';
                document.head.appendChild(link);
            }

            if (document.getElementById('jstree-script-cdn')) {
                const timer = setInterval(function () {
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.jstree) {
                        clearInterval(timer);
                        callback();
                    }
                }, 120);
                return;
            }

            const script = document.createElement('script');
            script.id = 'jstree-script-cdn';
            script.src = 'https://cdn.jsdelivr.net/npm/jstree@3.3.16/dist/jstree.min.js';
            script.onload = callback;
            document.body.appendChild(script);
        }

        function filterHistoryRows(filter) {
            const $rows = $('#section-history-file-rows').find('.history-file-row');
            if (!$rows.length) {
                return;
            }

            if (!filter || filter === 'all') {
                $rows.show();
                return;
            }

            if (String(filter).startsWith('year-')) {
                $rows.hide().filter('[data-year-filter="' + filter + '"]').show();
                return;
            }

            $rows.hide().filter('[data-filter="' + filter + '"]').show();
        }

        function initHistoryTree() {
            const $tree = $('#section-history-tree');
            if (!$tree.length || !$.fn.jstree) {
                return;
            }

            if ($tree.data('jstree')) {
                $tree.jstree('destroy');
            }

            $tree
                .jstree({
                    core: {
                        themes: {
                            dots: true,
                            icons: true
                        }
                    },
                    plugins: ['wholerow']
                })
                .on('ready.jstree', function () {
                    const root = $tree.find('li').first();
                    if (root.length) {
                        $tree.jstree('select_node', root.attr('id'));
                    }
                })
                .on('select_node.jstree', function (event, data) {
                    const filter = $(data.node.li_attr).data('filter') || 'all';
                    filterHistoryRows(filter);
                });
        }

        loadJsTree(initHistoryTree);
    })();
</script>
