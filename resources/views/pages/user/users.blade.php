@include('layouts.header')
    <div class="container-fluid py-4">
      <div class="row mt-4">
        <div class="col-lg-12 mb-lg-0 mb-4">
          <div class="card ">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between">
                @if(!empty($PermissionAdd))
                  <a href="/adduser" class="btn btn-primary">Add New User</a>
                @endif
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
                                  @if(!empty($PermissionEdit))
                                    <a href="{{ route('edituser', ['id' => $user->id]) }}" class="btn btn-warning">Edit</a>
                                  @endif
                                  @if(!empty($PermissionDelete))
                                    <form action="{{ route('users.hapus_users', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" class="btn btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                  @endif
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