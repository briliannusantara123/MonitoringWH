@include('layouts.header')
<style type="text/css">
  .pagination {
    font-size: 0.8rem; /* Sesuaikan ukuran teks */
}

.pagination .page-link {
    color: #333; /* Warna teks */
    border-radius: 4px; /* Membuat sudut lebih halus */
}

.pagination .page-item.active .page-link {
    background-color: #007bff; /* Warna aktif (Bootstrap default) */
    color: white; /* Warna teks halaman aktif */
    border: none; /* Hilangkan border */
}

</style>
    <div class="container-fluid">
      <form action="{{ route('outletssearch') }}" method="POST">
        @csrf
        <div class="row">
          <div class="col-3">
            <span>Start Date</span>
            <input type="date" class="form-control" value="{{ $startDate }}" name="startDate">
          </div>
          <div class="col-3">
            <span>End Date</span>
            <input type="date" class="form-control" value="{{ $endDate }}" name="endDate">
          </div>
          <div class="col-3">
            <span>Outlet</span>
            <select name="id_cabang" id="cabang" class="form-control">
                  @foreach($cabang as $c)
                      @if($data)
                          <option value="{{ $c['id'] }}" 
                              {{ $c['cabang_name'] == $data->cabang_name ? 'selected' : '' }}>
                              {{ $c['cabang_name'] }}
                          </option>
                      @else
                          <option value="{{ $c['id'] }}" >
                              {{ $c['cabang_name'] }}
                          </option>
                      @endif
                  @endforeach
              </select>


          </div>
          <div class="col-3 mt-4">
            <button type="submit" class="btn btn-primary" style="color:white;">Search</button>
          </div>
        </div>
      </form>
    </div>
    @if($data)
      <div class=" mt-2" style="margin-left: 10px;">
        <div class="row">
          <div class="col-3">
            <div class="card">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-12">
                        <div class="numbers">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold">{{ $data->cabang_name }}</p>
                            <h6 class="font-weight-bolder mb-1">
                                {{ $custcount[$data->id] ?? 0 }} Customers
                            </h6>
                            <h6 class="font-weight-bolder mb-1">
                                {{ $transcount[$data->id] ?? 0 }} Transactions
                            </h6>
                            <h6 class="text-success font-weight-bolder mb-1">
                                Rp {{ number_format($totalpayment[$data->id] ?? 0, 0, ',', '.') }}
                            </h6>
                            <h6 class="font-weight-bolder mb-0">
                                Top Menu : {{ $topmenuone[$data->id]['description'] ?? 'No Top Menu' }}
                            </h6>
                        </div>

                    </div>
                    <!-- <div class="col-4 text-end">
                      <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                          <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class=" mt-2" style="margin-left: 10px;">
        <div class="row">
          <div class="col-3">
            <div class="card">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-12">
                        <div class="numbers">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold">Cabang</p>
                            <h6 class="font-weight-bolder mb-1">
                                0 Customers
                            </h6>
                            <h6 class="font-weight-bolder mb-1">
                                0 Transactions
                            </h6>
                            <h6 class="text-success font-weight-bolder mb-1">
                                Rp 0
                            </h6>
                            <h6 class="font-weight-bolder mb-0">
                                Top Menu : No Top Menu
                            </h6>
                        </div>

                    </div>
                    <!-- <div class="col-4 text-end">
                      <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                          <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
      <div class="container-fluid py-4">
          <div class="row">
              <div class="col-lg-12 mb-lg-0 mb-4">
                  <div class="card">
                      <div class="card-header pb-0 p-3">
                          <h6 class="text-center">Top Menu Items Ordered ( {{ $startDate }} - {{ $endDate }} )</h6>
                      </div>
                      <div class="container-fluid">
                        <div class="table-responsive">
                          <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Item Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                              @if($data)
                                @php
                                    $no = ($topmenu->currentPage() - 1) * $topmenu->perPage() + 1; // Untuk nomor halaman
                                @endphp

                                @foreach($topmenu as $t)
                                    <tr>
                                        <td>{{ $no++ }}</td> <!-- Increment di setiap iterasi -->
                                        <td>{{ $t->item_code }}</td>
                                        <td>{{ $t->description }}</td>
                                        <td>Rp{{ number_format($t->unit_price, 0, ',', '.') }}</td>
                                        <td>{{ $t->total_qty }}</td>
                                        <td>Rp{{ number_format($t->unit_price * $t->total_qty, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                  <td colspan="6"><strong>Data Not Found </strong></td>
                                </tr>
                              @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"><strong>Grand Total</strong></td>
                                    @if($data)
                                      <td><strong>{{ $topmenu->sum('total_qty') }}</strong></td>
                                      <td><strong>Rp{{ number_format($topmenu->sum(fn($t) => $t->unit_price * $t->total_qty), 0, ',', '.') }}</strong></td>
                                    @else
                                      <td><strong>0</strong></td>
                                      <td><strong>0</strong></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                        @if($data)
                          <div class="d-flex justify-content-end mt-3 mb-3">
                              <ul class="pagination" style="margin: 0; font-size: 1rem;">
                                  @foreach ($topmenu->getUrlRange(1, $topmenu->lastPage()) as $page => $url)
                                      <li class="page-item {{ $page == $topmenu->currentPage() ? 'active' : '' }}">
                                          <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                      </li>
                                  @endforeach
                              </ul>
                          </div>
                        @endif
                        </div>
                      </div>
                      

                  </div>
              </div>
          </div>
      </div>

      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-3">
            <div class="chart">
              <canvas id="topMenuChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="container-fluid py-4">
          <div class="row">
              <div class="col-lg-12 mb-lg-0 mb-4">
                  <div class="card">
                      <div class="card-header pb-0 p-3">
                          <h6 class="text-center">Details ( {{ $startDate }} - {{ $endDate }} )</h6>
                      </div>
                      <div class="container-fluid">
                        <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                          <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Outlet</th>
                                    <th>TransDate</th>
                                    <th>TransNo</th>
                                    <th>Time</th>
                                    <th>CustName</th>
                                    <th>Table</th>
                                    <th>Pax</th>
                                    <th>Cashier</th>
                                    <th>Menu</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>TotalPrice</th>
                                    <th>Disc</th>
                                    <th>SubTotal</th>
                                    <th>MemberPoint</th>
                                    <th>Disc(Bank Promotion)</th>
                                    <th>ServiceCharge</th>
                                    <th>Tax</th>
                                    <th>DP</th>
                                    <th>Disc(Voucher)</th>
                                    <th>GrandTotal</th>
                                    <th>PaymentType</th>
                                    <th>PaymentAmount</th>
                                    <th>Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data)
                                  @php
                                      $no = ($details->currentPage() - 1) * $details->perPage() + 1; // For page numbering
                                  @endphp

                                  @foreach($details as $d)
                                      @php
                                          $getDetails = App\Models\Details::getDetailsReport($d->trstoreid);
                                      @endphp

                                      @foreach($getDetails as $index => $menu)
                                          <tr>
                                              @if($index == 0)
                                                  <td rowspan="{{ count($getDetails) }}">{{ $no++ }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->cabang_name }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ \Carbon\Carbon::parse($d->create_date)->format('d-M-y') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->order_no }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ \Carbon\Carbon::parse($d->create_date)->format('H:i:s') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->customer_name }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->selected_table_no }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->total_pax }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->payment_by }}</td>
                                              @endif
                                              <td>{{ $menu->description ?? 'N/A' }}</td> 
                                              <td>{{ $menu->total_qty ?? 'N/A' }}</td> 
                                              <td>Rp {{ number_format($menu->unit_price, 0, ',', '.') }}</td> 
                                              @php
                                                  $tp = $menu->unit_price * $menu->total_qty;
                                                  $subTotal = $tp - ($menu->disc ?? 0);
                                                  $subTotalTransaction = App\Models\Details::hitungSubTotal($d->trstoreid);
                                                  $sc = 0.05 * ($subTotalTransaction - $d->bill_discount);
                                                  $tax = 0.1 * ($subTotalTransaction - $d->bill_discount);
                                                  $GP = $subTotalTransaction - $d->bill_discount + $d->sc_amount + $d->tax_amount - $d->down_payment;
                                              @endphp
                                              <!-- $GP = $subTotalTransaction - $d->bill_discount + $sc + $tax - $d->down_payment; -->
                                              <td>Rp {{ number_format($tp, 0, ',', '.') }}</td> 
                                              <td>{{ $menu->disc ?? 'N/A' }}</td> 
                                              @if($index == 0)
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($subTotalTransaction, 0, ',', '.') }}</td> 
                                                  <td rowspan="{{ count($getDetails) }}">-</td> 
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($d->bill_discount, 0, ',', '.') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($d->sc_amount, 0, ',', '.') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($d->tax_amount, 0, ',', '.') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($d->down_payment, 0, ',', '.') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">-</td>
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($GP, 0, ',', '.') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">{{ $d->payment_type }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($d->payment_amount, 0, ',', '.') }}</td>
                                                  <td rowspan="{{ count($getDetails) }}">Rp {{ number_format($d->kembalian, 0, ',', '.') }}</td>
                                              @endif
                                          </tr>
                                      @endforeach
                                  @endforeach
                              @else
                                  <tr>
                                      <td colspan="6"><strong>Data Not Found</strong></td>
                                  </tr>
                              @endif

                            </tbody>
                            @if($data)
                            <tfoot>
                              <tr>
                                  <td colspan="25">
                                      <div class="d-flex justify-content-end">
                                          <ul class="pagination" style="margin: 0; font-size: 1rem;">
                                              @foreach ($details->getUrlRange(1, $details->lastPage()) as $page => $url)
                                                  <li class="page-item {{ $page == $details->currentPage() ? 'active' : '' }}">
                                                      <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                  </li>
                                              @endforeach
                                          </ul>
                                      </div>
                                  </td>
                              </tr>
                          </tfoot>
                          @endif
                          </table>
                        </div>
                      </div>
                      

                  </div>
              </div>
          </div>
      </div>
  </main>

  <script>
    const ctx = document.getElementById('topMenuChart').getContext('2d');
    const labels = <?php echo $labels; ?>;
    const datasets = {!! $datasets !!};
    const topMenuChart = new Chart(ctx, {
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
            text: 'Top Menu Items Ordered ( {{ $startDate }} - {{ $endDate }} )', // Judul grafik
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
        },
      },
    });
  </script>
  @include('layouts.footer')