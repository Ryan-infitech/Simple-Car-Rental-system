@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verifikasi Pembayaran</h1>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Pembayaran
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Pembayaran #{{ $payment->id }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID Pembayaran</th>
                            <td>{{ $payment->id }}</td>
                        </tr>
                        <tr>
                            <th>ID Pesanan</th>
                            <td>
                                <a href="{{ route('admin.bookings.show', $payment->booking->id) }}">
                                    #{{ $payment->booking->id }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Pelanggan</th>
                            <td>{{ $payment->booking->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                        </tr>
                        <tr>
                            <th>ID Transaksi</th>
                            <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body">
                    @if($payment->payment_proof)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran" class="img-fluid">
                        </div>
                        <div class="text-center">
                            <a href="{{ asset('storage/' . $payment->payment_proof) }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-search-plus"></i> Lihat Gambar Penuh
                            </a>
                            <a href="{{ asset('storage/' . $payment->payment_proof) }}" class="btn btn-secondary" download>
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-file-invoice fa-4x text-gray-300 mb-3"></i>
                            <p>Tidak ada bukti pembayaran yang diunggah</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Verifikasi</h6>
                </div>
                <div class="card-body">
                    @if($payment->status == 'pending')
                        <form action="{{ route('admin.payments.verify.process', $payment->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="verification_note">Catatan Admin (Opsional)</label>
                                <textarea class="form-control" id="verification_note" name="verification_note" rows="3"></textarea>
                            </div>
                            <div class="btn-group w-100">
                                <button type="submit" name="action" value="verify" class="btn btn-success mr-2">
                                    <i class="fas fa-check"></i> Verifikasi Pembayaran
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')">
                                    <i class="fas fa-times"></i> Tolak Pembayaran
                                </button>
                            </div>
                        </form>
                    @elseif($payment->status == 'verified')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Pembayaran ini telah diverifikasi pada {{ $payment->updated_at->format('d M Y H:i') }}
                        </div>
                        @if($payment->admin_note)
                            <div class="form-group">
                                <label>Catatan Admin:</label>
                                <p>{{ $payment->admin_note }}</p>
                            </div>
                        @endif
                    @elseif($payment->status == 'failed')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> Pembayaran ini ditolak pada {{ $payment->updated_at->format('d M Y H:i') }}
                        </div>
                        @if($payment->admin_note)
                            <div class="form-group">
                                <label>Catatan Admin:</label>
                                <p>{{ $payment->admin_note }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
