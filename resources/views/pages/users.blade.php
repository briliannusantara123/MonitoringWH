@include('layouts.header')
    <div class="container-fluid py-4">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add New User
      </button>

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
                            <th scope="col">Role</th>
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
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                  <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubahuser{{ $user->id }}">
                                    <i class="fas fa-pen"></i>
                                  </button>
                                  <form action="{{ route('users.hapus_users', $user->id) }}" method="POST">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" class="btn btn-danger">
                                          <i class="fas fa-trash"></i>
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
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add New User</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/createuser" method="POST">
            <div class="modal-body">
                @csrf
                <div class="mb-3">
                  <input type="text" class="form-control" placeholder="Username" aria-label="Name" name="name">
                </div>
                <div class="mb-3">
                  <input type="email" class="form-control" placeholder="Email" aria-label="Name" name="email">
                </div>
                <div class="mb-3">
                  <input type="password" class="form-control" placeholder="Password" aria-label="Email" name="password">
                </div>
                <div class="mb-3">
                  <select name="role" class="form-control">
                    <option selected="" disabled="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="operation">Operation</option>
                  </select>
                </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @foreach($users as $index => $user)
    <div class="modal fade" id="ubahuser{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit User</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('users.update', $user->id) }}" method="POST">
              <div class="modal-body">
                  @csrf
                  @method('PUT') <!-- Gunakan method PUT untuk update -->
                  <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Username" aria-label="Name" name="name" value="{{ $user->name }}">
                  </div>
                  <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Email" aria-label="Email" name="email" value="{{ $user->email }}">
                  </div>
                  <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Password" aria-label="Password" name="password">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                  </div>
                  <div class="mb-3">
                    <select name="role" class="form-control">
                      <option disabled="">Select Role</option>
                      <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                      <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                      <option value="operation" {{ $user->role == 'operation' ? 'selected' : '' }}>Operation</option>
                    </select>
                  </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach

  @include('layouts.footer')