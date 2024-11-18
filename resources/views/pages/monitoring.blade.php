@include('layouts.header')
    <div class="container-fluid">
      <form action="">
        <div class="row">
          <div class="col-3">
            <span>Start Date</span>
            <input type="date" class="form-control" value="<?= date('Y-m-d');  ?>">
          </div>
          <div class="col-3">
            <span>End Date</span>
            <input type="date" class="form-control" value="<?= date('Y-m-d');  ?>">
          </div>
          <div class="col-3">
            <span>Outlet</span>
            <select name="" id="" class="form-control">
              @foreach($cabang as $c)
                <option value="{{ $c['id'] }}">{{ $c['cabang_name'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-3 mt-4">
            <button type="submit" class="btn btn-primary" style="color:white;">Search</button>
          </div>
        </div>
      </form>
    </div>
    <div class="container mt-2 mb-2">
      <div class="row">
        <div class="col-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Today's Money</p>
                      <h5 class="font-weight-bolder">
                        $53,000
                      </h5>
                      <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">+55%</span>
                          since yesterday
                      </p>
                    </div>
                  </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                          <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row mt-4">
            <div class="col-lg-12 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="text-center">Warehouse Database</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th rowspan="2">Outlet</th>
                                    <th colspan="2">10-11-2024</th>
                                    <th colspan="2">11-11-2024</th>
                                    <th colspan="2">12-11-2024</th>
                                </tr>
                                <tr>
                                    <th>Main</th>
                                    <th>Outlet</th>
                                    <th>Man</th>
                                    <th>Outlet</th>
                                    <th>Man</th>
                                    <th>Outlet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>HG Ampera</td>
                                    <td>180</td>
                                    <td>180</td>
                                    <td>350</td>
                                    <td>350</td>
                                    <td>45</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td>HG Alsut</td>
                                    <td>250</td>
                                    <td>250</td>
                                    <td>285</td>
                                    <td>285</td>
                                    <td>60</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td>SH Depok</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

  </main>
  @include('layouts.footer')