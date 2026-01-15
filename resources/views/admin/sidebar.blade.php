@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.financial') }}" class="nav-link">
        <i class="bi bi-graph-up"></i> Laporan Keuangan
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-link">
        <i class="bi bi-box-seam"></i> Inventori
    </a>
    <a href="{{ route('admin.bookings') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Bookings
    </a>
    <a href="{{ route('admin.payments') }}" class="nav-link">
        <i class="bi bi-credit-card"></i> Payments
    </a>
    <a href="{{ route('admin.service-prices') }}" class="nav-link">
        <i class="bi bi-tags"></i> Harga Service
    </a>
    <a href="{{ route('admin.attendance-codes') }}" class="nav-link">
        <i class="bi bi-key"></i> Kode Absen
    </a>
    <a href="{{ route('admin.attendances') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Absensi
    </a>
    <a href="{{ route('admin.salaries') }}" class="nav-link">
        <i class="bi bi-cash-stack"></i> Gaji Karyawan
    </a>
    <a href="{{ route('admin.bonuses') }}" class="nav-link">
        <i class="bi bi-gift"></i> Bonus
    </a>
    <a href="{{ route('admin.promo-codes') }}" class="nav-link">
        <i class="bi bi-ticket-perforated"></i> Kode Promo
    </a>
@endsection
