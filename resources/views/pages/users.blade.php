@include('layouts.header')
    <div class="container-fluid py-4">
      <a href="/adduser" class="btn btn-primary">Add New User</a>

      <div class="row mt-4">
        <div class="col-lg-12 mb-lg-0 mb-4">
          <div class="card ">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between">
              </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Created Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                            <tr>
                                <td scope="col">{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                  <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubahuser{{ $user->id }}">
                                    Edit
                                  </button>
                                  <form action="{{ route('users.hapus_users', $user->id) }}" method="POST">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" class="btn btn-danger">
                                          Delete
                                      </button>
                                  </form>
                                </td>
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

  @include('layouts.footer')