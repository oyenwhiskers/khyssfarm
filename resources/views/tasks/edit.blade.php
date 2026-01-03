@extends('layouts.app')

@section('title', 'Edit Task')
@section('page-title', 'Edit Task')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Edit Task</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Task Details Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Task Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3" id="title-wrapper">
                                    <label for="title" class="form-label">
                                        Task Title *
                                        <span id="title-notice" class="badge bg-info ms-2 d-none">Auto-generated</span>
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" placeholder="Generating auto-generated title..." style="background-color: #f8f9fa;">
                                    <div class="form-text" id="title-hint">For preset task types, title will be auto-generated in format: [TYPE_CODE][DATE]/[JOB_NUMBER]</div>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="type" class="form-label">
                                        Task Type *
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Select the type of work: Harvesting, Spraying, Grass Cut, Sanitation, or Maintenance"></i>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        @foreach($types as $key => $label)
                                            <option value="{{ $key }}" {{ (old('type') ?? $task->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="work_date" class="form-label">
                                        Date *
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="When was or will the task be performed?"></i>
                                    </label>
                                    <input type="date" class="form-control @error('work_date') is-invalid @enderror" id="work_date" name="work_date" value="{{ old('work_date', $task->work_date->toDateString()) }}" required>
                                    @error('work_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3" id="quantity-wrapper">
                                    <label for="quantity_kg" class="form-label">
                                        Quantity (kg)
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Amount harvested in kilograms (required for Harvesting tasks)"></i>
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('quantity_kg') is-invalid @enderror" id="quantity_kg" name="quantity_kg" value="{{ old('quantity_kg', $task->quantity_kg) }}" placeholder="e.g., 25.5">
                                    <div class="form-text">Required for Harvesting.</div>
                                    @error('quantity_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3 d-none" id="tree-count-wrapper">
                                    <label for="tree_count" class="form-label">
                                        Trees Count
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Number of chilli trees serviced (required for Manuring)"></i>
                                    </label>
                                    <input type="number" step="1" min="0" class="form-control @error('tree_count') is-invalid @enderror" id="tree_count" name="tree_count" value="{{ old('tree_count', $task->tree_count) }}" placeholder="e.g., 150">
                                    <div class="form-text">Required for Manuring.</div>
                                    @error('tree_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">
                                        Status *
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Current status of the task: Pending, In Progress, or Completed"></i>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ (old('status') ?? $task->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Worker Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Workers</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    Assign Workers *
                                    <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Select workers to perform the task. Harvesting requires exactly one worker."></i>
                                </label>
                                <div class="d-flex gap-2 mb-2">
                                    <select class="form-select form-select-sm" id="worker_picker">
                                        <option value="">Select worker</option>
                                        @foreach($workers as $worker)
                                            <option value="{{ $worker->id }}">{{ $worker->name }} ({{ ucfirst(str_replace('_', ' ', $worker->role)) }})</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add_worker_btn">Add</button>
                                </div>
                                <div id="selected_workers" class="d-flex flex-wrap gap-2 mb-3" style="min-height: 38px; align-content: flex-start;"></div>
                                <div class="form-text" id="worker-hint">Harvesting requires exactly one worker.</div>
                                <select class="form-select d-none @error('workers') is-invalid @enderror" id="workers" name="workers[]" multiple>
                                    @foreach($workers as $worker)
                                        <option value="{{ $worker->id }}" {{ collect(old('workers', $task->workers->pluck('id')->toArray()))->contains($worker->id) ? 'selected' : '' }}>{{ $worker->name }} ({{ ucfirst(str_replace('_', ' ', $worker->role)) }})</option>
                                    @endforeach
                                </select>
                                @error('workers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Cost Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Payment & Cost</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="rate" class="form-label">
                                        Payment Rate
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Harvesting: per kg. Manuring: per tree. Others: per job per worker."></i>
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate', $task->rate) }}" placeholder="Rate per kg or per job">
                                    <div class="form-text" id="rate-hint">Harvesting: rate per kg; Manuring: per tree; others: per job.</div>
                                    @error('rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Estimated Cost
                                        <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Auto-calculated but can be edited manually if needed"></i>
                                    </label>
                                    <input type="text" class="form-control" id="cost_display" value="RM{{ number_format($task->cost ?? 0, 2) }}" placeholder="RM0.00">
                                    <input type="hidden" name="cost" id="cost_value" value="{{ $task->cost ?? 0 }}">
                                    <div class="form-text">Auto-calculated. You can edit manually if needed.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachment Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachment</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    Upload File (optional)
                                    <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Add supporting documents: photos, invoices, reports, etc. (Max 5 MB)"></i>
                                </label>
                                @if($task->attachment_path)
                                    <div class="mb-2 alert alert-info alert-sm">
                                        <strong>Current:</strong> <a href="{{ asset('storage/'.$task->attachment_path) }}" target="_blank"><i class="fas fa-download me-1"></i>View attachment</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <div class="form-text">Formats: JPG, PNG, PDF, DOC, DOCX | Max 5 MB</div>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Work Done / Notes
                                    <i class="fas fa-question-circle ms-1" style="cursor: help; font-size: 0.85rem; opacity: 0.6;" data-bs-toggle="tooltip" title="Describe what was done, any issues encountered, or additional notes about the task."></i>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Describe the work done...">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<script>
(function() {
    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeForm);
    } else {
        initializeForm();
    }

    function initializeForm() {
        const typeEl = document.getElementById('type');
        const qtyWrap = document.getElementById('quantity-wrapper');
        const qtyEl = document.getElementById('quantity_kg');
        const treeWrap = document.getElementById('tree-count-wrapper');
        const treeEl = document.getElementById('tree_count');
        const rateEl = document.getElementById('rate');
        const workersEl = document.getElementById('workers');
        const workerPicker = document.getElementById('worker_picker');
        const addWorkerBtn = document.getElementById('add_worker_btn');
        const selectedWrap = document.getElementById('selected_workers');
        const costDisplay = document.getElementById('cost_display');
        const costValue = document.getElementById('cost_value');
        const workerHint = document.getElementById('worker-hint');
        const rateHint = document.getElementById('rate-hint');
        const titleEl = document.getElementById('title');
        const titleNotice = document.getElementById('title-notice');
        const titleHint = document.getElementById('title-hint');
        const workDateEl = document.getElementById('work_date');

        // Safety check - ensure all critical elements exist
        if (!typeEl || !titleEl || !workDateEl) {
            console.error('Required form elements not found');
            return;
        }

        // Type prefixes for auto-generation
        const typePrefixes = {
            'harvesting': 'HA',
            'spraying': 'SP',
            'grass_cut': 'GC',
            'sanitation': 'SA',
            'maintenance': 'MA',
            'manuring': 'MN',
        };

        function generatePreviewTitle() {
            const type = typeEl.value;
            const workDate = workDateEl.value;

            if (!type || !workDate || !typePrefixes[type]) {
                return null;
            }

            // Format: [PREFIX][YYYYMMDD]/[N]
            // Fetch actual job number from API
            const prefix = typePrefixes[type];
            const dateFormatted = workDate.replace(/-/g, '');
            
            // Fetch the job number from API
            fetch(`/api/tasks/next-job-number?type=${type}&date=${workDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.job_number !== undefined) {
                        const actualTitle = `${prefix}${dateFormatted}/${data.job_number}`;
                        // Always set the value for preset tasks
                        titleEl.value = actualTitle;
                        titleEl.placeholder = `Auto-generated: ${actualTitle}`;
                        if (titleHint) {
                            titleHint.innerHTML = `<i class="fas fa-lightbulb me-1 text-warning"></i>Title will be auto-generated as <strong>${actualTitle}</strong>.`;
                        }
                    }
                })
                .catch(error => console.error('Error fetching job number:', error));

            // Return placeholder for immediate display
            return `${prefix}${dateFormatted}/[calculating...]`;
        }

        function updateTitleField() {
            const type = typeEl.value;
            const isPreset = typePrefixes[type] !== undefined;

            if (isPreset) {
                // Preset task - make it readonly and auto-fill
                if (titleNotice) titleNotice.classList.remove('d-none');
                titleEl.setAttribute('readonly', 'readonly');
                titleEl.style.backgroundColor = '#e9ecef';
                titleEl.style.cursor = 'not-allowed';
                titleEl.removeAttribute('required');
                if (titleHint) titleHint.innerHTML = `<i class="fas fa-lock me-1 text-muted"></i>Auto-generated title. Cannot be edited.`;
                generatePreviewTitle();
            } else {
                // Non-preset task - require manual title
                if (titleNotice) titleNotice.classList.add('d-none');
                titleEl.removeAttribute('readonly');
                titleEl.style.backgroundColor = '#ffffff';
                titleEl.style.cursor = 'auto';
                titleEl.setAttribute('required', 'required');
                titleEl.placeholder = 'Enter task title';
                titleEl.value = '';
                if (titleHint) titleHint.innerHTML = `Enter a custom title for this task.`;
            }
        }

        function updateVisibilityAndDefaults() {
            const type = typeEl.value;

            // Update title field based on preset/custom
            updateTitleField();

            if (qtyWrap) qtyWrap.style.display = type === 'harvesting' ? 'block' : 'none';
            if (treeWrap) {
                const isManuring = type === 'manuring';
                treeWrap.classList.toggle('d-none', !isManuring);
                treeWrap.style.display = isManuring ? 'block' : 'none';
            }

            if (rateEl && !rateEl.value) {
                if (type === 'spraying' || type === 'grass_cut' || type === 'sanitation') {
                    rateEl.value = 20;
                } else if (type === 'manuring') {
                    rateEl.value = 0.10;
                }
            }

            if (workerHint) {
                if (type === 'harvesting') {
                    workerHint.textContent = 'Harvesting requires exactly one worker.';
                } else if (type === 'manuring') {
                    workerHint.textContent = 'Select workers applying fertilizer. Rate is per tree.';
                } else if (type === 'maintenance') {
                    workerHint.textContent = 'Select one or more workers. Rate is per job per worker.';
                } else {
                    workerHint.textContent = 'Select one or more workers. Standard rate applies per worker per job.';
                }
            }

            if (rateHint) {
                if (type === 'harvesting') {
                    rateHint.textContent = 'Rate per kg for harvesting.';
                } else if (type === 'manuring') {
                    rateHint.textContent = 'Rate per tree for manuring.';
                } else if (type === 'maintenance') {
                    rateHint.textContent = 'Set a job rate (per worker).';
                } else {
                    rateHint.textContent = 'Standard job rate (per worker).';
                }
            }

            updateCost();
        }

        function updateCost() {
            if (!rateEl || !workersEl || !costDisplay) return;

            const type = typeEl.value;
            const workers = Array.from(workersEl.selectedOptions).length;
            const rate = parseFloat(rateEl.value) || 0;
            const qty = parseFloat(qtyEl.value) || 0;
            const trees = parseFloat(treeEl ? treeEl.value : 0) || 0;
            let cost = 0;

            if (type === 'harvesting') {
                cost = qty * rate;
            } else if (type === 'manuring') {
                cost = trees * rate;
            } else {
                cost = rate * (workers || 1);
            }

            costDisplay.value = 'RM' + cost.toFixed(2);
            if (costValue) costValue.value = cost.toFixed(2);
        }

        function parseCostDisplay() {
            if (!costDisplay || !costValue) return;
            const displayVal = costDisplay.value.replace(/[^\d.]/g, '');
            const numericCost = parseFloat(displayVal) || 0;
            costValue.value = numericCost.toFixed(2);
            costDisplay.value = 'RM' + numericCost.toFixed(2);
        }

        function enforceHarvestingSingleSelect() {
            if (!workersEl) return;
            if (typeEl.value === 'harvesting' && workersEl.selectedOptions.length > 1) {
                const last = workersEl.selectedOptions[workersEl.selectedOptions.length - 1];
                Array.from(workersEl.options).forEach(opt => opt.selected = false);
                last.selected = true;
            }
        }

        function renderSelected() {
            if (!selectedWrap || !workersEl) return;
            selectedWrap.innerHTML = '';
            const selected = Array.from(workersEl.selectedOptions);
            
            if (selected.length === 0) {
                selectedWrap.innerHTML = '<div class="text-muted" style="font-size: 0.9rem;"><i class="fas fa-info-circle me-1"></i>No workers selected yet</div>';
                return;
            }
            
            selected.forEach(opt => {
                const chip = document.createElement('div');
                chip.className = 'badge bg-primary text-white d-inline-flex align-items-center gap-2';
                chip.style.padding = '0.5rem 0.75rem';
                chip.style.fontSize = '0.95rem';
                chip.style.fontWeight = '500';
                chip.style.cursor = 'default';
                
                const text = document.createElement('span');
                text.textContent = opt.text;
                chip.appendChild(text);
                
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.className = 'btn-close btn-close-white';
                closeBtn.style.padding = '0';
                closeBtn.style.width = '1rem';
                closeBtn.style.height = '1rem';
                closeBtn.style.marginLeft = '0.25rem';
                closeBtn.setAttribute('aria-label', 'Remove worker');
                closeBtn.onclick = (e) => {
                    e.preventDefault();
                    opt.selected = false;
                    renderSelected();
                    updateCost();
                };
                chip.appendChild(closeBtn);
                
                selectedWrap.appendChild(chip);
            });
        }

        function addWorker() {
            if (!workerPicker || !workersEl) return;
            const id = workerPicker.value;
            if (!id) return;
            const option = Array.from(workersEl.options).find(o => o.value === id);
            if (!option) return;

            if (typeEl.value === 'harvesting') {
                Array.from(workersEl.options).forEach(o => o.selected = false);
            }

            option.selected = true;
            renderSelected();
            updateCost();
        }

        // Attach event listeners with null checks
        typeEl.addEventListener('change', updateVisibilityAndDefaults);
        if (workDateEl) workDateEl.addEventListener('change', updateTitleField);
        if (rateEl) rateEl.addEventListener('input', updateCost);
        if (qtyEl) qtyEl.addEventListener('input', updateCost);
        if (treeEl) treeEl.addEventListener('input', updateCost);
        if (workersEl) workersEl.addEventListener('change', () => { enforceHarvestingSingleSelect(); renderSelected(); updateCost(); });
        if (addWorkerBtn) addWorkerBtn.addEventListener('click', addWorker);
        if (costDisplay) costDisplay.addEventListener('blur', parseCostDisplay);

        // Initialize
        if (selectedWrap) renderSelected();
        updateVisibilityAndDefaults();
    }
})();
</script>
@endsection
