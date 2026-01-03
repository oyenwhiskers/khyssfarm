<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $worker->name }} - {{ $monthName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payslip-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .payslip-title {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .info-section {
            padding: 2rem;
        }
        .earnings-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .signature-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
        }
        .signature-box {
            border-top: 1px solid #000;
            padding-top: 0.5rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <!-- Header -->
        <div class="header-section">
            <div class="company-name">KHYSS Farm</div>
            <div class="payslip-title">Employee Payslip</div>
            <div class="mt-3">
                <strong>Period:</strong> {{ $monthName }}
            </div>
        </div>

        <!-- Worker Information -->
        <div class="info-section">
            <div class="row mb-4">
                <div class="col-6">
                    <h5 class="mb-3">Employee Details</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="120"><strong>Name:</strong></td>
                            <td>{{ $worker->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Role:</strong></td>
                            <td>{{ \App\Models\Worker::ROLES[$worker->role] ?? ucfirst(str_replace('_', ' ', $worker->role)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contact:</strong></td>
                            <td>{{ $worker->contact ?: '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6">
                    <h5 class="mb-3">Payslip Summary</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150"><strong>Pay Period:</strong></td>
                            <td>{{ $monthName }}</td>
                        </tr>
                        <tr>
                            <td><strong>Generated On:</strong></td>
                            <td>{{ now()->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Tasks:</strong></td>
                            <td>{{ $tasks->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Earnings Breakdown -->
            <h5 class="mb-3">Earnings Breakdown</h5>
            <div class="table-responsive">
                <table class="table earnings-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Task</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center">Workers</th>
                            <th class="text-end">Total Cost</th>
                            <th class="text-end">My Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                        @endphp
                        @forelse($tasks as $task)
                            @php
                                $workerCount = $task->workers->count();
                                $myShare = $workerCount > 0 ? ($task->cost / $workerCount) : 0;
                                $grandTotal += $myShare;
                            @endphp
                            <tr>
                                <td>{{ $task->work_date->format('M d') }}</td>
                                <td>{{ $task->title }}</td>
                                <td>
                                    <small>{{ \App\Models\Task::TYPES[$task->type] ?? ucfirst($task->type) }}</small>
                                </td>
                                <td>
                                    @if($task->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">Planned</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $workerCount }}</td>
                                <td class="text-end">RM{{ number_format($task->cost, 2) }}</td>
                                <td class="text-end">RM{{ number_format($myShare, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">
                                    No tasks recorded for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="6" class="text-end">TOTAL EARNINGS:</td>
                            <td class="text-end">RM{{ number_format($totalEarnings, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Additional Information -->
            <div class="alert alert-info mt-4">
                <strong>Note:</strong> This payslip shows your earnings for {{ $monthName }}. 
                For tasks with multiple workers, the cost is divided equally among all assigned workers.
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="signature-box">
                                Employee Signature
                            </div>
                            <small class="text-muted">{{ $worker->name }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="signature-box">
                                Employer Signature
                            </div>
                            <small class="text-muted">KHYSS Farm Management</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="text-center my-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print me-2"></i>Print Payslip
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg">
            <i class="fas fa-times me-2"></i>Close
        </button>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
