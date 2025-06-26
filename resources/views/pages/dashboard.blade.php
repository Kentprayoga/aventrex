@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Selamat Datang, Admin!</h1>

  <!-- Kartu Statistik -->
  <div class="row">
    <!-- Surat Masuk -->
    <div class="col-xl-4 col-md-6 mb-4">
      <a href="{{ route('approvals.list') }}" class="text-decoration-none">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Surat Masuk</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suratMasuk }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-envelope-open-text fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Surat Keluar -->
    <div class="col-xl-4 col-md-6 mb-4">
      <a href="{{ route('history.history') }}" class="text-decoration-none">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Surat Keluar</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suratKeluar }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Pesan Masuk -->
    <div class="col-xl-4 col-md-6 mb-4">
      <a href="{{ route('admin.chat') }}" class="text-decoration-none">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pesan Masuk</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $incomingMessages }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-comments fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <!-- Chart dan Filter Hari -->
  <div class="row">
    <div class="col-xl-8 col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <div>
            <h6 class="m-0 font-weight-bold text-primary">Pengajuan dalam {{ $days }} Hari Terakhir</h6>
          </div>
          <form method="GET" action="{{ route('pages.dashboard') }}">
            <select name="days" class="form-select form-control" onchange="this.form.submit()">
              <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Hari</option>
              <option value="14" {{ $days == 14 ? 'selected' : '' }}>14 Hari</option>
              <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
            </select>
          </form>
        </div>
        <div class="card-body">
          <div class="chart-area">
            <canvas id="documentsChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">List Sisa Cuti Kurang dari 5</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="thead-light text-center">
          <tr>
            <th>#</th>
            <th><i class="fas fa-user"></i> Nama Karyawan</th>
            <th><i class="fas fa-file-alt"></i> Sisa Cuti</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($dataCutiKurang as $index => $item)
          <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $item->nama_karyawan }}</td>
            <td class="text-center">{{ $item->remaining_leave }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="text-center">Tidak ada data cuti kurang dari 5</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($dates ?? []);
    const values = @json($counts ?? []);

    if (labels.length === values.length && labels.length > 0) {
      const ctx = document.getElementById("documentsChart").getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: "Jumlah Pengajuan",
            data: values,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: { left: 10, right: 25, top: 25, bottom: 0 }
          },
          scales: {
            x: { ticks: { maxTicksLimit: 7 }, grid: { display: false } },
            y: { beginAtZero: true }
          },
          plugins: {
            legend: { display: false }
          }
        }
      });
    }
  });
</script>
@endsection
