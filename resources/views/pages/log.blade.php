@include('layouts.header')
<style type="text/css">
    .row span {
        color: white;
    }
</style>
    <div class="container-fluid py-4">
      <div class="row mt-4">
        <form action="searchlog" method="POST">
                    @csrf
                    <div class="row">
                      <div class="col-3">
                        <span>Start Date</span>
                        <input type="date" class="form-control" value="{{ $startDate }}" name="startDate" id="startDatePicker">
                      </div>
                      <div class="col-3">
                        <span>End Date</span>
                        <input type="date" class="form-control" value="{{ $endDate }}" name="endDate" id="endDatePicker">
                      </div>
                      <div class="col-2">
                        <span>Outlet</span>
                        <select name="cabang" id="cabang" class="form-control">
                            <option value="all">All</option>
                              @foreach($cabang as $c)
                                <option value="{{ $c['id'] }}" >
                                    {{ $c['cabang_name'] }}
                                </option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-2">
                        <span>Status</span>
                        <select name="status" id="status" class="form-control">
                            <option value="all">All</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                          </select>
                      </div>
                      <div class="col-2 mt-4">
                        <button type="submit" class="btn btn-primary" style="color:white;">Search</button>
                      </div>
                    </div>
                  </form>
        <div class="col-lg-12 mb-lg-0 mb-12">
          <div class="card ">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between">
                <form action="updateserver" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="startDate" id="start">
                        <input type="hidden" name="endDate" id="end">
                      <div class="col-12 mt-1">
                        <button type="submit" class="btn btn-warning" style="color:white;">Update Server</button>
                      </div>
                    </div>
                  </form>
              </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Cabang</th>
                            <th scope="col">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col">Insert Date</th>
                            <th scope="col">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = ($log->currentPage() - 1) * $log->perPage() + 1; // Nomor awal per halaman
                        @endphp
                        @foreach($log as $index => $l)
                            <tr>
                                <td scope="col">{{ $no++ }}</td> <!-- Gunakan $no untuk nomor urut -->
                                <td>{{ $l->cabang_name }}</td>
                                <td>{{ $l->type }}</td>
                                <td>{{ $l->status }}</td>
                                <td>{{ $l->tgl_insert }}</td>
                                <td>{{ $l->deskripsi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <div class="d-flex justify-content-end">
                                    <!-- Gunakan links() untuk pagination -->
                                    {{ $log->links('pagination::bootstrap-4') }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>

            </div>

          </div>
        </div>
        
      </div>
    </div>
  </main>
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
  @include('layouts.footer')