@include('layouts.header')

    <div class="container-fluid">
      <form action="/dashboard" method="GET" class="mb-4">
        <div class="row">
          <div class="col-3">
            <span>Start Date</span>
            <input type="date" class="form-control" value="{{ $start }}" name="dari">
          </div>
          <div class="col-3">
            <span>End Date</span>
            <input type="date" class="form-control" value="{{ $end }}" name="sampai">
          </div>
          <div class="col-3 mt-4">
            <button type="submit" class="btn btn-primary" style="color:white;">Search</button>
          </div>
        </div>
      </form>
    </div>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <h4>Top Transactions</h4>
        @php
            $cabang = $cabang->sortByDesc(function($c) use ($count) {
                return $count[$c->id] ?? 0;
            });
        @endphp

        @foreach($cabang as $c)
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <a href="{{ route('outletsview', ['id' => $c->id, 'start' => $start, 'end' => $end]) }}">
                    <div class="card mb-4">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-12">
                                    <div class="numbers" style="padding-left: 10px;">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">{{ $c->cabang_name }}</p>
                                        <h5 class="font-weight-bolder">
                                            {{ $custcount[$c->id] ?? 0 }} Customers
                                        </h5>
                                        <h5 class="font-weight-bolder">
                                            {{ $count[$c->id] ?? 0 }} Transactions
                                        </h5>
                                        <h5 class="text-success font-weight-bolder">
                                            Rp {{ number_format($totalpayment[$c->id] ?? 0, 0, ',', '.') }}
                                        </h5>
                                        <h5 class="font-weight-bolder">
                                            Top Menu : {{ $topmenu[$c->id]['description'] ?? 'No Top Menu' }}
                                        </h5>
                                    </div>
                                </div>
                                <!-- <div class="col-2 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach




      </div>
      <div class="container-fluid py-4">
        <div class="row">
          <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
              <div class="card-header pb-0 pt-3 bg-transparent">
                <h6 class="text-capitalize">Outlets Transactions</h6>
                <p class="text-sm mb-0">
                  <i class="fa fa-arrow-up text-success"></i>
                </p>
              </div>
              <div class="card-body p-3">
                <div class="chart">
                  <canvas id="outletChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid py-4">
    <div class="row mt-1">
        <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card">
              <div class="card-header pb-0 p-3">
                  <h6 class="text-center">Warehouse Database</h6>
              </div>
              <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                  <table class="table table-bordered text-center align-middle">
                      <thead>
                          <tr>
                              <th rowspan="3">Outlets</th>
                              @foreach($dates as $date)
                                  <th colspan="4">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</th>
                              @endforeach
                          </tr>
                          <tr>
                              @foreach($dates as $date)
                                  <th colspan="2">Warehouse</th>
                                  <th colspan="2">Outlet</th>
                              @endforeach
                          </tr>
                          <tr>
                              @foreach($dates as $date)
                                  <th>cust</th>
                                  <th>trans</th>
                                  <th>cust</th>
                                  <th>trans</th>
                              @endforeach
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($cabang as $outlet)
                              <tr>
                                  <td>{{ $outlet['cabang_name'] }}</td>
                                  @foreach ($dates as $date)
                                      <td>{{ $custWH[$outlet['id']][$date]->total_count ?? 0 }}</td>
                                      <td>{{ $transWH[$outlet->id][$date]->total_count ?? 0 }}</td>
                                      <td>
                                          {{ ($custOUTP[$outlet->id][$date]->total_count ?? 0) + ($custOUTM[$outlet->id][$date]->total_count ?? 0) }}
                                      </td>
                                      <td>
                                          {{ ($transOUTP[$outlet->id][$date]->total_count ?? 0) + ($transOUTM[$outlet->id][$date]->total_count ?? 0) }}
                                      </td>
                                  @endforeach
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
          </div>

        </div>
    </div>
</div>


      
  </main>
  <script>
    const ctx = document.getElementById('outletChart').getContext('2d');
    const labels = <?php echo $labels; ?>;
    const datasets = {!! $datasets !!};
    const outletChart = new Chart(ctx, {
      type: 'bar',
      data: {
                labels: labels,
                datasets: datasets
            },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top', // Posisi legenda
          },
          title: {
            display: true,
            text: 'Outlets Transactions', // Judul grafik
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            position: 'left', // Sumbu kiri
            title: {
              display: true,
              text: 'Total Items / Transactions', // Label sumbu kiri
            },
          },
          y1: {
            beginAtZero: true,
            position: 'right', // Sumbu kanan
            title: {
              display: true,
              text: 'Total Price (IDR)', // Label sumbu kanan
            },
            grid: {
              drawOnChartArea: false, // Tidak menggambar garis pada area grafik
            },
          },
        },
      },
    });
  </script>
  <!--   Core JS Files   -->
  @include('layouts.footer')