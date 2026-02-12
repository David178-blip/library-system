@extends('layouts.app')

@section('content')
@php
    $borrows = $borrows ?? collect();
    $overdue = $overdue ?? collect();
    $lost = $lost ?? collect();
    $chartBorrowsOverTimeLabels = $borrowsOverTimeLabels ?? [];
    $chartBorrowsOverTime = $borrowsOverTime ?? [];
    $chartStatusData = $statusData ?? ['borrowed' => 0, 'returned' => 0, 'overdue' => 0, 'rejected' => 0];
    $chartMonthlyLabels = $monthlyLabels ?? [];
    $chartMonthlyData = $monthlyData ?? [];
    $chartCourseLabels = $courseLabels ?? [];
    $chartCourseData = $courseData ?? [];
@endphp

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">📊 Library Reports & Analytics</h1>
        <div>
            <form id="download-form" action="{{ route('admin.reports.download') }}" method="POST" class="d-inline">
                @csrf
                <!-- Preserve existing filters -->
                @foreach(request()->except(['statusChart', 'monthlyChart', 'courseChart']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <!-- Chart data placeholders -->
                <input type="hidden" name="statusChart" id="statusChartData">
                <input type="hidden" name="monthlyChart" id="monthlyChartData">
                <input type="hidden" name="courseChart" id="courseChartData">
                
                <button type="button" onclick="downloadReportWithCharts()" class="btn btn-success shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Download PDF Report
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Borrows</h6>
                            <h2 class="mb-0 text-primary">{{ number_format($totalBorrows ?? 0) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-journal-text text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Returned</h6>
                            <h2 class="mb-0 text-success">{{ number_format($totalReturned ?? 0) }}</h2>
                            <span class="badge bg-success bg-opacity-10 text-success">{{ $returnRate ?? 0 }}% Rate</span>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-check2-circle text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Overdue</h6>
                            <h2 class="mb-0 text-danger">{{ number_format($totalOverdue ?? 0) }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-clock-history text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Lost Copies</h6>
                            <h2 class="mb-0 text-warning">{{ number_format($totalLost ?? 0) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-x-octagon text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-funnel"></i> Filter Records
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">User Name</label>
                        <input type="text" name="user" value="{{ request('user') }}" class="form-control" placeholder="Search user...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Book Title</label>
                        <input type="text" name="book" value="{{ request('book') }}" class="form-control" placeholder="Search book...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Accession Number</label>
                        <input type="text" name="accession_number" value="{{ request('accession_number') }}" class="form-control" placeholder="e.g. ACC-000123">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-primary fw-bold">Select Month</label>
                        <input type="month" name="month" value="{{ request('month') }}" class="form-control border-primary">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All Courses</option>
                            <option value="BSIT" {{ request('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                            <option value="BSCRIM" {{ request('course') == 'BSCRIM' ? 'selected' : '' }}>BSCRIM</option>
                            <option value="BSBA" {{ request('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                            <option value="BSED" {{ request('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                            <option value="BEED" {{ request('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Borrowed From</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Borrowed To</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100 me-2">
                            <i class="bi bi-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Borrows Over Time (Last 30 Days) -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-graph-up"></i> Borrows Over Time (Last 30 Days)
                </div>
                <div class="card-body">
                    <canvas id="borrowsOverTimeChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-pie-chart"></i> Status Distribution
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-calendar-month"></i> Monthly Trends (Last 12 Months)
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Course Distribution -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-people"></i> Borrows by Course
                </div>
                <div class="card-body">
                    @if(isset($courseLabels) && count($courseLabels) > 0)
                        <canvas id="courseChart" height="100"></canvas>
                    @else
                        <p class="text-center text-muted py-5">No course data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Borrowed Books -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-trophy"></i> Top 10 Most Borrowed Books
                </div>
                <div class="card-body">
                    @if(isset($topBooks) && is_iterable($topBooks) && count($topBooks) > 0)
                        @php
                            $maxBorrows = collect($topBooks)->max('count') ?? 1;
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Book Title</th>
                                        <th>Total Borrows</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topBooks as $index => $book)
                                        @php
                                            $percentage = $maxBorrows > 0 ? (($book['count'] ?? 0) / $maxBorrows) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">#{{ $index + 1 }}</span>
                                            </td>
                                            <td><strong>{{ $book['title'] ?? 'Unknown' }}</strong></td>
                                            <td>{{ $book['count'] ?? 0 }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                         style="width: {{ $percentage }}%" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $book['count'] ?? 0 }}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- BORROWS TABLE -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-secondary text-white">
            <i class="bi bi-book"></i> Borrowing Records
        </div>
        <div class="card-body p-0">
            @if($borrows->isEmpty())
                <p class="text-center py-4 text-muted">No records found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>User</th>
                                <th>Book</th>
                                <th>Accession #</th>
                                <th>Course</th>
                                <th>Date Borrowed</th>
                                <th>Due Date</th>
                                <th>Date Returned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borrows as $borrow)
                                <tr>
                                    <td>{{ $borrow->user->name }}</td>
                                    <td>{{ $borrow->book->title }}</td>
                                    <td><code>{{ $borrow->accession_number ?? '—' }}</code></td>
                                    <td>{{ $borrow->user->role === 'student' ? $borrow->user->course : '-' }}</td>
                                    <td>{{ optional($borrow->borrowed_at)->format('M d, Y') ?? '—' }}</td>
                                    <td>{{ optional($borrow->due_at)->format('M d, Y') ?? '—' }}</td>
                                    <td>{{ optional($borrow->returned_at)->format('M d, Y') ?? '—' }}</td>
                                    <td>
                                        @if($borrow->status === 'borrowed')
                                            <span class="badge bg-primary">Borrowed</span>
                                        @elseif($borrow->status === 'returned')
                                            <span class="badge bg-success">Returned</span>
                                        @elseif($borrow->status === 'overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($borrow->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $borrows->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- OVERDUE BOOKS TABLE -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-danger text-white">
            <i class="bi bi-clock-history"></i> Overdue Books
        </div>
        <div class="card-body p-0">
            @if($overdue->isEmpty())
                <p class="text-center py-4 text-muted">No overdue books 🎉</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>User</th>
                                <th>Book</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdue as $borrow)
                                @php
                                    $daysOverdue = $borrow->due_at ? now()->diffInDays($borrow->due_at, false) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $borrow->user->name }}</td>
                                    <td>{{ $borrow->book->title }}</td>
                                    <td>{{ optional($borrow->due_at)->format('M d, Y') ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ abs($daysOverdue) }} day(s)</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- LOST / MISSING COPIES TABLE -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark">
            <i class="bi bi-exclamation-triangle"></i> Lost / Missing Copies
        </div>
        <div class="card-body p-0">
            @if($lost->isEmpty())
                <p class="text-center py-4 text-muted">No lost or removed copies.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Book</th>
                                <th>Accession #</th>
                                <th>Course</th>
                                <th>User (Who Lost)</th>
                                <th>Date Marked Lost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lost as $lostCopy)
                                <tr>
                                    <td>{{ $lostCopy->book->title ?? '—' }}</td>
                                    <td><code>{{ $lostCopy->accession_number }}</code></td>
                                    <td>{{ $lostCopy->book->course ?? '—' }}</td>
                                    <td>{{ $lostCopy->user->name ?? '—' }}</td>
                                    <td>{{ optional($lostCopy->removed_at)->format('M d, Y') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
function downloadReportWithCharts() {
    const statusChart = document.getElementById('statusChart');
    const monthlyChart = document.getElementById('monthlyChart');
    const courseChart = document.getElementById('courseChart');
    
    if (statusChart) document.getElementById('statusChartData').value = statusChart.toDataURL('image/png');
    if (monthlyChart) document.getElementById('monthlyChartData').value = monthlyChart.toDataURL('image/png');
    if (courseChart) document.getElementById('courseChartData').value = courseChart.toDataURL('image/png');
    
    document.getElementById('download-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    var colors = {
        primary: 'rgba(13, 110, 253, 0.8)',
        success: 'rgba(25, 135, 84, 0.8)',
        danger: 'rgba(220, 53, 69, 0.8)',
        warning: 'rgba(255, 193, 7, 0.8)',
        info: 'rgba(13, 202, 240, 0.8)',
        secondary: 'rgba(108, 117, 125, 0.8)'
    };
    var borrowsOverTimeLabels = @json($chartBorrowsOverTimeLabels);
    var borrowsOverTime = @json($chartBorrowsOverTime);
    var statusData = @json($chartStatusData);
    var monthlyLabels = @json($chartMonthlyLabels);
    var monthlyData = @json($chartMonthlyData);
    var courseLabels = @json($chartCourseLabels);
    var courseData = @json($chartCourseData);

    var borrowsOverTimeCtx = document.getElementById('borrowsOverTimeChart');
    if (borrowsOverTimeCtx && borrowsOverTimeLabels.length) {
        new Chart(borrowsOverTimeCtx, {
            type: 'line',
            data: {
                labels: borrowsOverTimeLabels,
                datasets: [{
                    label: 'Borrows',
                    data: borrowsOverTime,
                    borderColor: colors.primary,
                    backgroundColor: colors.primary.replace('0.8', '0.1'),
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    var statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Borrowed', 'Returned', 'Overdue', 'Rejected'],
                datasets: [{
                    data: [
                        statusData.borrowed || 0,
                        statusData.returned || 0,
                        statusData.overdue || 0,
                        statusData.rejected || 0
                    ],
                    backgroundColor: [colors.primary, colors.success, colors.danger, colors.secondary]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx && monthlyLabels.length) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Borrows',
                    data: monthlyData,
                    backgroundColor: colors.success,
                    borderColor: colors.success.replace('0.8', '1'),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    var courseCtx = document.getElementById('courseChart');
    if (courseCtx && courseLabels.length) {
        new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: courseLabels,
                datasets: [{
                    label: 'Borrows',
                    data: courseData,
                    backgroundColor: [colors.primary, colors.success, colors.warning, colors.info, colors.danger]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
});
</script>

@endsection
