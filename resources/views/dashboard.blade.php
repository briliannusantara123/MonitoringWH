@include('layouts.header')
<style type="text/css">
    .row span {
        color: white;
    }
</style>
    <div class="container-fluid">
      <form action="dashboard" method="GET" class="mb-4">
        <div class="row">
          <div class="col-3">
            <span>Start Date</span>
            <input type="date" class="form-control" value="{{ $start }}" name="dari" id="startDatePicker">
          </div>
          <div class="col-3">
            <span>End Date</span>
            <input type="date" class="form-control" value="{{ $end }}" name="sampai" id="endDatePicker">
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
        <h4 style="color:white;">Top Transactions</h4>
        @php
            $cabang = $cabang->sortByDesc(function($c) use ($totalpayment) {
                return $totalpayment[$c->id] ?? 0;
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
                      <a href="{{url('cekserver')}}" class="btn btn-primary" style="float:right">Refresh Server</a>
                      <form action="updateserverD" method="POST">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="startDate" id="start">
                            <input type="hidden" name="endDate" id="end">
                          <div class="col-12">
                            <button type="submit" style="float:right" class="btn btn-warning" style="color:white;">Update Server</button>
                          </div>
                        </div>
                      </form>
                  </div>
                  <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                      <!-- REALL -->
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
                                @php
                                  $status = DB::table('sh_m_cabang')
                                  ->where('id', $outlet->id)
                                  ->select('status_ip_pagi', 'status_ip_malam','ip_malam')
                                  ->first();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $outlet['cabang_name'] }}
                                        @if(empty($status->ip_malam))
                                          @if($status->status_ip_pagi === 'offline')
                                            <label style="background-color: red; border-radius: 20px; color: white; padding: 5px;">
                                                M
                                            </label>
                                        @endif
                                        @else
                                          @if($status->status_ip_pagi === 'offline' || $status->status_ip_malam === 'offline')
                                            <label style="background-color: red; border-radius: 20px; color: white; padding: 5px;">
                                                {{ $status->status_ip_pagi === 'offline' ? 'M' : 'N' }}
                                            </label>
                                          @endif
                                        @endif
                                    </td>
                                    @foreach ($dates as $date)
                                        @php
                                            $custOutlet = 0;
                                            $transOutlet = 0;
                                            $custpagi = \App\Models\Customers::getDataOutlet($outlet->id, $date,'pagi');
                                            $transpagi = \App\Models\Transactions::getDataOutlet($outlet->id, $date,'pagi');
                                            if ($outlet['ip_malam']) {
                                                $custmalam = \App\Models\Customers::getDataOutlet($outlet->id, $date,'malam');
                                                $transmalam = \App\Models\Transactions::getDataOutlet($outlet->id, $date,'malam');
                                                if($status->status_ip_pagi === 'online' && $status->status_ip_malam === 'online'){
                                                  $custOutlet = ($custpagi[$outlet->id][$date]->total_count ?? 0) + ($custmalam[$outlet->id][$date]->total_count ?? 0);
                                                  $transOutlet = ($transpagi[$outlet->id][$date]->total_count ?? 0) + ($transmalam[$outlet->id][$date]->total_count ?? 0);
                                                }else{
                                                  $custOutlet = ($custpagi[$outlet->id][$date]->total_count ?? 0);
                                                  $transOutlet = ($transpagi[$outlet->id][$date]->total_count ?? 0);
                                                }
                                    
                                            }else{
                                              $custOutlet = ($custpagi[$outlet->id][$date]->total_count ?? 0);
                                              $transOutlet = ($transpagi[$outlet->id][$date]->total_count ?? 0);
                                            }
                                        @endphp
                                        <td>{{ $custWH[$outlet['id']][$date]->total_count ?? 0 }}</td>
                                        <td>{{ $transWH[$outlet->id][$date]->total_count ?? 0 }}</td>
                                        <td>{{ $custOutlet }}</td>
                                        <td>{{ $transOutlet }}</td>
                                    @endforeach
                                </tr>
                            @endforeach


                          </tbody>
                      </table>
                      <!-- <table class="table table-bordered text-center align-middle">
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
                        </tbody>
                      </table> -->
                      <div class="row" style="margin-left:10px;">
                        <div class="col-12">
                          <div class="col-6">
                            <label style="background-color: red; border-radius: 20px; color: white; padding: 5px;"> N </label>
                            <label>Night Server Offline</label>
                          </div>
                          <div class="col-6">
                            <label style="background-color: red; border-radius: 20px; color: white; padding: 5px;"> M </label>
                            <label>Morning Server Offline</label>
                          </div>
                        </div>
                      </div>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    const startDatePicker = document.getElementById('startDatePicker');
    const endDatePicker = document.getElementById('endDatePicker');
    const startText = document.getElementById('start');
    const endText = document.getElementById('end');

    startDatePicker.addEventListener('change', () => {
        startText.value = startDatePicker.value;
    });
    endDatePicker.addEventListener('change', () => {
        endText.value = endDatePicker.value; 
    });
    window.addEventListener('load', () => {
        startText.value = startDatePicker.value;
        endText.value = endDatePicker.value;
    });
</script>
<!-- <script>
    $(document).ready(function() {
        // When the page loads, call the function to load table data
        loadTableData();

        function loadTableData() {
            $.ajax({
                url: '{{ route("getOutletData") }}',  // Define the route in Laravel
                type: 'GET',
                success: function(response) {
                    populateTable(response);
                },
                error: function() {
                    alert('Error loading data');
                }
            });
        }

        // Function to populate table with data
        function populateTable(data) {
            // Clear the existing table data
            $('tbody').empty();

            data.cabang.forEach(function(outlet) {
                let row = '<tr>';
                row += `<td>${outlet.cabang_name}</td>`;

                data.dates.forEach(function(date) {
                    row += `<td>${outlet.outlet[date].cust}</td>`;
                    row += `<td>${outlet.outlet[date].trans}</td>`;
                    row += `<td>${outlet.warehouse[date].cust}</td>`;
                    row += `<td>${outlet.warehouse[date].trans}</td>`;
                });

                row += '</tr>';
                $('tbody').append(row);
            });
        }
    });
</script> -->
  <!--   Core JS Files   -->
  @include('layouts.footer')