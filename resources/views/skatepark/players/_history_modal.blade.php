<!-- Modal: View Package & Play History -->
<style>
    body.dark-mode #viewHistoryModal input[type="datetime-local"] {
        color-scheme: dark;
    }
</style>
<div class="modal fade" id="viewHistoryModal" tabindex="-1" data-bs-focus="false" aria-labelledby="viewHistoryModalLabel" aria-hidden="true" style="z-index: 1075;">
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
        sn: @json(__('S.N.')),
        date: @json(__('Date')),
        bs: @json(__('BS')),
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
        actions: @json(__('Actions')),
        editTime: @json(__('Edit Time')),
        startDateTime: @json(__('Start Date & Time')),
        endDateTime: @json(__('End Date & Time')),
        save: @json(__('Save')),
        saving: @json(__('Saving...')),
        cancel: @json(__('Cancel')),
        startRequired: @json(__('Start date and time is required.')),
        endAfterStart: @json(__('End date and time must be after or equal to start date and time.')),
        updateFailed: @json(__('Failed to update play record time.')),
    };
    const UPDATE_TIMES_URL_TEMPLATE = @json(route('player_packages.play_records.update-times', ['playRecord' => '__RECORD_ID__']));
    const CSRF_TOKEN = @json(csrf_token());
    let currentHistoryUrl = '';
    let currentHistorySubtitle = '';

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
                    <td class="text-muted font-monospace small">${pkg.play_records.length - i}</td>
                    <td class="font-monospace small">
                        <div class="fw-semibold">${escapeHtml(rec.date_bs || '—')} <span class="text-muted fw-normal" style="font-size: 0.7rem;">(${escapeHtml(HISTORY_LABELS.bs)})</span></div>
                        <div class="text-muted" style="font-size: 0.72rem;">${escapeHtml(rec.date)}</div>
                    </td>
                    <td class="fw-semibold">${escapeHtml(rec.token)}</td>
                    <td>${escapeHtml(rec.game_type)}</td>
                    <td class="font-monospace small">${escapeHtml(rec.start_time)}</td>
                    <td class="font-monospace small">${escapeHtml(rec.end_time)}</td>
                    <td class="fw-bold text-primary">${escapeHtml(rec.actual_time_formatted)}</td>
                    <td class="text-center">${escapeHtml(rec.no_of_players)}</td>
                    <td class="text-center">${renderRecordStatusBadge(rec.status)}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-warning px-2 py-0.5"
                                style="border-radius: 6px;"
                                title="${escapeHtml(HISTORY_LABELS.editTime)}"
                                onclick="toggleEditPlayRecordTimes(${rec.id})">
                            ✏️
                        </button>
                    </td>
                </tr>
                <tr id="edit-record-row-${rec.id}" class="d-none bg-light">
                    <td colspan="10" class="p-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold mb-1" for="edit-start-time-${rec.id}">${escapeHtml(HISTORY_LABELS.startDateTime)}</label>
                                <div class="input-group input-group-sm">
                                    <input type="datetime-local" class="form-control form-control-sm" id="edit-start-time-${rec.id}" value="${escapeHtml(rec.start_time_input || '')}" required>
                                    <button type="button" class="btn btn-outline-secondary" title="${escapeHtml(HISTORY_LABELS.startDateTime)}" onclick="openDateTimePicker('edit-start-time-${rec.id}')">📅</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold mb-1" for="edit-end-time-${rec.id}">${escapeHtml(HISTORY_LABELS.endDateTime)}</label>
                                <div class="input-group input-group-sm">
                                    <input type="datetime-local" class="form-control form-control-sm" id="edit-end-time-${rec.id}" value="${escapeHtml(rec.end_time_input || '')}">
                                    <button type="button" class="btn btn-outline-secondary" title="${escapeHtml(HISTORY_LABELS.endDateTime)}" onclick="openDateTimePicker('edit-end-time-${rec.id}')">📅</button>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary fw-semibold px-3" id="save-record-btn-${rec.id}" onclick="savePlayRecordTimes(${rec.id})">
                                    ${escapeHtml(HISTORY_LABELS.save)}
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary px-3" onclick="toggleEditPlayRecordTimes(${rec.id}, false)">
                                    ${escapeHtml(HISTORY_LABELS.cancel)}
                                </button>
                            </div>
                        </div>
                        <div class="text-danger small mt-2 d-none" id="edit-record-error-${rec.id}"></div>
                    </td>
                </tr>
            `).join('')
            : `
                <tr>
                    <td colspan="10" class="text-center text-muted py-4 small">${escapeHtml(HISTORY_LABELS.noRecords)}</td>
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
                            ${escapeHtml(HISTORY_LABELS.purchased)}: <span class="font-monospace">${escapeHtml(pkg.purchased_date_bs || '—')} (${escapeHtml(HISTORY_LABELS.bs)}) / ${escapeHtml(pkg.purchased_date)}</span>
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
                                    <th>${escapeHtml(HISTORY_LABELS.sn)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.date)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.token)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.game)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.startTime)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.endTime)}</th>
                                    <th>${escapeHtml(HISTORY_LABELS.duration)}</th>
                                    <th class="text-center">${escapeHtml(HISTORY_LABELS.players)}</th>
                                    <th class="text-center">${escapeHtml(HISTORY_LABELS.status)}</th>
                                    <th class="text-end">${escapeHtml(HISTORY_LABELS.actions)}</th>
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

    async function loadHistoryData(url) {
        const bodyEl = document.getElementById('viewHistoryModalBody');

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
    }

    window.openDateTimePicker = function (inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.focus();
        if (typeof input.showPicker === 'function') {
            try {
                input.showPicker();
            } catch (e) {
                // Fallback: focusing already lets user edit/pick natively
            }
        }
    };

    window.toggleEditPlayRecordTimes = function (recordId, forceState) {
        const editRow = document.getElementById(`edit-record-row-${recordId}`);
        const errorEl = document.getElementById(`edit-record-error-${recordId}`);
        if (!editRow) return;

        const shouldShow = typeof forceState === 'boolean'
            ? forceState
            : editRow.classList.contains('d-none');

        editRow.classList.toggle('d-none', !shouldShow);
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('d-none');
        }
    };

    window.savePlayRecordTimes = async function (recordId) {
        const startInput = document.getElementById(`edit-start-time-${recordId}`);
        const endInput = document.getElementById(`edit-end-time-${recordId}`);
        const errorEl = document.getElementById(`edit-record-error-${recordId}`);
        const saveBtn = document.getElementById(`save-record-btn-${recordId}`);

        const showError = (msg) => {
            if (errorEl) {
                errorEl.textContent = msg;
                errorEl.classList.remove('d-none');
            }
        };

        const startVal = startInput ? startInput.value.trim() : '';
        const endVal = endInput ? endInput.value.trim() : '';

        if (!startVal) {
            showError(HISTORY_LABELS.startRequired);
            return;
        }
        if (endVal && new Date(endVal) < new Date(startVal)) {
            showError(HISTORY_LABELS.endAfterStart);
            return;
        }

        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('d-none');
        }

        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = HISTORY_LABELS.saving;
        }

        try {
            const url = UPDATE_TIMES_URL_TEMPLATE.replace('__RECORD_ID__', String(recordId));
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    start_time: startVal,
                    end_time: endVal || null
                })
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                const validationMsg = data.errors
                    ? Object.values(data.errors).flat()[0]
                    : null;
                showError(validationMsg || data.message || HISTORY_LABELS.updateFailed);
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = HISTORY_LABELS.save;
                }
                return;
            }

            if (currentHistoryUrl) {
                await loadHistoryData(currentHistoryUrl);
            }
        } catch (err) {
            showError(HISTORY_LABELS.updateFailed);
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = HISTORY_LABELS.save;
            }
        }
    };

    window.openHistoryModal = async function (url, subtitle) {
        const modalEl = document.getElementById('viewHistoryModal');
        const subtitleEl = document.getElementById('viewHistoryModalSubtitle');
        const bodyEl = document.getElementById('viewHistoryModalBody');

        currentHistoryUrl = url;
        currentHistorySubtitle = subtitle || '';

        subtitleEl.textContent = currentHistorySubtitle;
        bodyEl.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div>${escapeHtml(HISTORY_LABELS.loading)}</div>
            </div>
        `;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        await loadHistoryData(url);
    };

    const historyModalEl = document.getElementById('viewHistoryModal');
    if (historyModalEl) {
        let suspendedFocusTraps = [];

        historyModalEl.addEventListener('show.bs.modal', function () {
            suspendedFocusTraps = [];
            document.querySelectorAll('.modal.show').forEach(function (openModal) {
                if (openModal !== historyModalEl) {
                    const instance = bootstrap.Modal.getInstance(openModal);
                    if (instance && instance._focustrap) {
                        instance._focustrap.deactivate();
                        suspendedFocusTraps.push(instance._focustrap);
                    }
                }
            });
        });

        historyModalEl.addEventListener('shown.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 1) {
                backdrops[backdrops.length - 1].style.zIndex = '1070';
            }
        });

        historyModalEl.addEventListener('hidden.bs.modal', function () {
            suspendedFocusTraps.forEach(function (trap) {
                try {
                    trap.activate();
                } catch (e) {}
            });
            suspendedFocusTraps = [];

            if (document.querySelector('.modal.show')) {
                document.body.classList.add('modal-open');
            }
        });
    }
})();
</script>

