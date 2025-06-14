@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pesanan #{{ $booking->id }}</h6>
            <div>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                
                @if($booking->status == 'pending')
                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Pelanggan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama</th>
                            <td>{{ $booking->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $booking->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td>{{ $booking->user->phone ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Pesanan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>ID Pesanan</th>
                            <td>{{ $booking->id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($booking->status == 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($booking->status == 'confirmed')
                                    <span class="badge badge-success">Dikonfirmasi</span>
                                @elseif($booking->status == 'completed')
                                    <span class="badge badge-primary">Selesai</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $booking->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Detail Mobil</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Mobil</th>
                            <td>{{ $booking->car->brand }} {{ $booking->car->model }}</td>
                        </tr>
                        <tr>
                            <th>Plat Nomor</th>
                            <td>{{ $booking->car->license_plate }}</td>
                        </tr>
                        <tr>
                            <th>Tarif Harian</th>
                            <td>Rp {{ number_format($booking->car->daily_rate, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Sewa</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>{{ $booking->start_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>{{ $booking->end_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>{{ $booking->start_date->diffInDays($booking->end_date) }} hari</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Informasi Pembayaran</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Pembayaran</th>
                            <td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran</th>
                            <td>
                                @if($booking->payment_status == 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($booking->payment_status == 'paid')
                                    <span class="badge badge-success">Dibayar</span>
                                @elseif($booking->payment_status == 'refunded')
                                    <span class="badge badge-info">Dikembalikan</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Aksi</h5>
                    <div class="btn-group">
                        @if($booking->status == 'pending')
                            <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success mr-2">
                                    <i class="fas fa-check"></i> Konfirmasi Pesanan
                                </button>
                            </form>
                            <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                    <i class="fas fa-times"></i> Batalkan Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
