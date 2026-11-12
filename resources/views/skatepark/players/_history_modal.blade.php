<!-- Modal: View Package & Play History -->
<div class="modal fade" id="viewHistoryModal" tabindex="-1" aria-labelledby="viewHistoryModalLabel" aria-hidden="true" style="z-index: 1075;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="viewHistoryModalLabel">📜 {{ __('Package & Play History') }}</h5>
                    <small class="text-white-50" id="viewHistoryModalSubtitle"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewHistoryModalBody">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div>{{ __('Loading history...') }}</div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const HISTORY_LABELS = {
        loading: @json(__('Loading history...')),
        failed: @json(__('Failed to load play history. Please try again.')),
        noPackages: @json(__('No packages purchased yet.')),
        noRecords: @json(__('No play records recorded for this package yet.')),
        purchased: @json(__('Purchased')),
        totalHoursPlayed: @json(__('Total Hours Played')),
        totalDaysPlayed: @json(__('Total Days Played')),
        remainingDays: @json(__('Remaining Days')),
        remainingHours: @json(__('Remaining Hours')),
        days: @json(__('Days')),
        sessions: @json(__('sessions')),
        outOf: @json(__('of')),
        min: @json(__('min')),
        minPerDay: @json(__('min/day')),
        playRecords: @json(__('Play Records')),
        date: @json(__('Date')),
        token: @json(__('Token')),
        game: @json(__('Game')),
        startTime: @json(__('Start Time')),
        endTime: @json(__('End Time')),
        duration: @json(__('Duration')),
        players: @json(__('Players')),
        status: @json(__('Status')),
        completed: @json(__('Completed')),
        playing: @json(__('Playing')),
        pending: @json(__('Not Started')),
        pkgNotPlayed: @json(__('Not Played')),
        pkgStarted: @json(__('Started')),
        pkgCompleted: @json(__('Completed')),
        pkgInactive: @json(__('Inactive')),
        remarks: @json(__('Remarks')),
    };

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderPackageStatusBadge(status) {
        if (status === 'completed') {
            return `<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">${escapeHtml(HISTORY_LABELS.pkgCompleted)}</span>`;
        }
        if (status === 'started') {
            return `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">${escapeHtml(HISTORY_LABELS.pkgStarted)}</span>`;
        }
        if (status === 'inactive') {
            return `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">${escapeHtml(HISTORY_LABELS.pkgInactive)}</span>`;
        }
        return `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">${escapeHtml(HISTORY_LABELS.pkgNotPlayed)}</span>`;
    }

    function renderRecordStatusBadge(status) {
        if (status === 'completed') {
            return `<span class="badge bg-success-subtle text-success border border-success-subtle">${escapeHtml(HISTORY_LABELS.completed)}</span>`;
        }
        if (status === 'playing') {
            return `<span class="badge bg-warning-subtle text-warning border border-warning-subtle">${escapeHtml(HISTORY_LABELS.playing)}</span>`;
        }
        return `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">${escapeHtml(HISTORY_LABELS.pending)}</span>`;
    }

    function renderPackageSection(pkg, index, totalCount) {
        const recordsRows = pkg.play_records.length > 0
            ? pkg.play_records.map((rec, i) => `
                <tr>
                    <td class="text-muted font-monospace small">${i + 1}</td>
                    <td class="font-monospace small">${escapeHtml(rec.date)}</td>
                    <td class="fw-semibold">${escapeHtml(rec.token)}</td>
                    <td>${escapeHtml(rec.game_type)}</td>
                    <td class="font-monospace small">${escapeHtml(rec.start_time)}</td>
                    <td class="font-monospace small">${escapeHtml(rec.end_time)}</td>
                    <td class="fw-bold text-primary">${escapeHtml(rec.actual_time_formatted)}</td>
                    <td class="text-center">${escapeHtml(rec.no_of_players)}</td>
                    <td class="text-center">${renderRecordStatusBadge(rec.status)}</td>
                </tr>
            `).join('')
            : `
                <tr>
                    <td colspan="9" class="text-center text-muted py-4 small">${escapeHtml(HISTORY_LABELS.noRecords)}</td>
                </tr>
            `;

        const packageHeaderLabel = totalCount > 1 ? `Package #${index + 1} — ` : '';
        const remarksHtml = pkg.remarks
            ? `<div class="small text-muted mt-1">${escapeHtml(HISTORY_LABELS.remarks)}: ${escapeHtml(pkg.remarks)}</div>`
            : '';

        return `
            <div class="card border shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4 border-bottom">
                    <div>
                        <div class="fw-bold fs-6 text-dark">
                            ${escapeHtml(packageHeaderLabel)}${escapeHtml(pkg.game_name)}
                            <span class="text-muted fw-normal small ms-1">
                                (${escapeHtml(pkg.time_per_day)} ${escapeHtml(HISTORY_LABELS.minPerDay)} • ${escapeHtml(pkg.no_of_days)} ${escapeHtml(HISTORY_LABELS.days)})
                            </span>
                        </div>
                        <div class="small text-muted">
                            ${escapeHtml(HISTORY_LABELS.purchased)}: <span class="font-monospace">${escapeHtml(pkg.purchased_date)}</span>
                            • Rs. ${Number(pkg.total_amount || 0).toLocaleString()}
                        </div>
                        ${remarksHtml}
                    </div>
                    <div>
                        ${renderPackageStatusBadge(pkg.package_status)}
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded border text-center h-100">
                                <div class="small text-muted text-uppercase fw-semibold mb-1">${escapeHtml(HISTORY_LABELS.totalHoursPlayed)}</div>
                                <div class="fs-4 fw-bold text-primary">${escapeHtml(pkg.total_hours_played_formatted)}</div>
                                <div class="small text-muted">${escapeHtml(pkg.total_played_minutes)} ${escapeHtml(HISTORY_LABELS.min)}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded border text-center h-100">
                                <div class="small text-muted text-uppercase fw-semibold mb-1">${escapeHtml(HISTORY_LABELS.totalDaysPlayed)}</div>
                                <div class="fs-4 fw-bold text-success">${escapeHtml(pkg.total_days_played)} ${escapeHtml(HISTORY_LABELS.days)}</div>
                                <div class="small text-muted">${escapeHtml(pkg.total_sessions)} ${escapeHtml(HISTORY_LABELS.sessions)}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded border text-center h-100">
                                <div class="small text-muted text-uppercase fw-semibold mb-1">${escapeHtml(HISTORY_LABELS.remainingDays)}</div>
                                <div class="fs-4 fw-bold ${pkg.remaining_days > 0 ? 'text-warning' : 'text-muted'}">${escapeHtml(pkg.remaining_days)} ${escapeHtml(HISTORY_LABELS.days)}</div>
                                <div class="small text-muted">${escapeHtml(HISTORY_LABELS.outOf)} ${escapeHtml(pkg.no_of_days)} ${escapeHtml(HISTORY_LABELS.days)}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded border text-center h-100">
                                <div class="small text-muted text-uppercase fw-semibold mb-1">${escapeHtml(HISTORY_LABELS.remainingHours)}</div>
                                <div class="fs-4 fw-bold ${pkg.remaining_minutes > 0 ? 'text-info' : 'text-muted'}">${escapeHtml(pkg.remaining_hours_formatted)}</div>
                                <div class="small text-muted">${escapeHtml(pkg.remaining_minutes)} / ${escapeHtml(pkg.allowed_minutes)} ${escapeHtml(HISTORY_LABELS.min)}</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2">${escapeHtml(HISTORY_LABELS.playRecords)} (${escapeHtml(pkg.play_records.length)})</h6>
                    <div class="table-responsive border rounded">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>${escapeHtml(HISTORY_LABELS.date)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.token)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.game)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.startTime)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.endTime)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.duration)}</th>
                                    <th class="text-center">${escapeHtml(HISTORY_LABELS.players)}</th>
                                    <th class="text-center">${escapeHtml(HISTORY_LABELS.status)}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${recordsRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    window.openHistoryModal = async function (url, subtitle) {
        const modalEl = document.getElementById('viewHistoryModal');
        const subtitleEl = document.getElementById('viewHistoryModalSubtitle');
        const bodyEl = document.getElementById('viewHistoryModalBody');

        subtitleEl.textContent = subtitle || '';
        bodyEl.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div>${escapeHtml(HISTORY_LABELS.loading)}</div>
            </div>
        `;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const payload = await response.json();
            if (!payload.success) {
                throw new Error(payload.message || 'Error');
            }

            const packages = Array.isArray(payload.packages) ? payload.packages : [];
            if (packages.length === 0) {
                bodyEl.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="fs-1 mb-2">📦</div>
                        <div class="fw-semibold">${escapeHtml(HISTORY_LABELS.noPackages)}</div>
                    </div>
                `;
                return;
            }

            bodyEl.innerHTML = packages
                .map((pkg, idx) => renderPackageSection(pkg, idx, packages.length))
                .join('');
        } catch (err) {
            bodyEl.innerHTML = `
                <div class="alert alert-danger mb-0">${escapeHtml(HISTORY_LABELS.failed)}</div>
            `;
        }
    };

    const historyModalEl = document.getElementById('viewHistoryModal');
    if (historyModalEl) {
        historyModalEl.addEventListener('shown.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 1) {
                backdrops[backdrops.length - 1].style.zIndex = '1070';
            }
        });
        historyModalEl.addEventListener('hidden.bs.modal', function () {
            if (document.querySelector('.modal.show')) {
                document.body.classList.add('modal-open');
            }
        });
    }
})();
</script>

