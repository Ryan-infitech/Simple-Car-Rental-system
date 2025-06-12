@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pembayaran</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Semua Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <form action="{{ route('admin.payments.index') }}" method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <select name="payment_method" class="form-control">
                                    <option value="">Semua Metode Pembayaran</option>
                                    <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="paypal" {{ request('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary ml-2">Reset</a>
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('admin.payments.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export
                        </a>
                    </div>
                </div>
                <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $payment->booking->id) }}">
                                    #{{ $payment->booking->id }}
                                </a>
                            </td>
                            <td>{{ $payment->booking->user->name }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>
                                @if($payment->status == 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($payment->status == 'verified')
                                    <span class="badge badge-success">Terverifikasi</span>
                                @elseif($payment->status == 'failed')
                                    <span class="badge badge-danger">Gagal</span>
                                @elseif($payment->status == 'refunded')
                                    <span class="badge badge-info">Dikembalikan</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payments.verify', $payment->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($payment->status == 'pending')
                                    <a href="{{ route('admin.payments.verify', $payment->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                    @endif
                                    @if($payment->status == 'verified')
                                    <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Apakah Anda yakin ingin menandai pembayaran ini sebagai dikembalikan?')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('admin.payments.invoice', $payment->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada pembayaran ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pembayaran Terverifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $verifiedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pembayaran Menunggu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pembayaran Gagal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $failedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#paymentsTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": true
        });
    });
</script>
@endsection
