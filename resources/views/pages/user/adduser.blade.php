@include('layouts.header')
    <div class="container-fluid py-4">
      <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
          <div class="card ">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between">
              </div>
            </div>
            <form action="/createuser" method="POST">
              @csrf
              <div class="container-fluid py-4">
                <div class="mb-3">
                  <strong>Username</strong>
                  <input type="text" name="name" class="form-control" required="">
                </div>
                <div class="mb-3">
                  <strong>Email</strong>
                  <input type="email" name="email" class="form-control" required="">
                </div>
                <div class="mb-3">
                  <strong>Password</strong>
                  <input type="password" name="password" class="form-control" required="">
                </div>
                <div class="mb-3">
                  <strong>Permission</strong>
                  @foreach($permission as $p)
                      <div class="row" style="margin-bottom: 20px;">
                          <div class="col-md-3">
                              {{ $p['name'] }}
                          </div>
                          <div class="col-9">
                              <div class="row">
                                  @foreach($p['group'] as $group)
                                      <div class="col-md-3">
                                          <input 
                                              type="checkbox" 
                                              id="checkbox-{{ $group['id'] }}" 
                                              class="checkbox {{ strtolower($p['name']) }} {{ strtolower(str_replace(' ', '-', $group['name'])) }}" 
                                              value="{{ $group['id'] }}" 
                                              name="permission_id[]" 
                                              style="width: 20px; transform: scale(2);"
                                          > 
                                          {{ $group['name'] }}
                                      </div>
                                  @endforeach
                              </div>
                          </div>
                      </div>
                      <hr>
                  @endforeach
                </div>     
              </div>
                <button type="submit" class="btn btn-primary" style="float: right;margin-right: 30px;padding-right: 50px;padding-left: 50px;">Save</button>
            </form>
          </div>
        </div>
        
      </div>
    </div>
  </main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select all relevant checkboxes
        const userCheckbox = document.querySelector('.users');
        const addCheckbox = document.querySelector('.add-users');
        const editCheckbox = document.querySelector('.edit-users');
        const deleteCheckbox = document.querySelector('.delete-users');

        // Function to enable/disable and uncheck child checkboxes
        function toggleUserGroup(state) {
            [addCheckbox, editCheckbox, deleteCheckbox].forEach((checkbox) => {
                checkbox.disabled = !state;
                if (!state) checkbox.checked = false; // Uncheck if disabled
            });
        }

        // Initialize: ensure child checkboxes start as enabled
        toggleUserGroup(userCheckbox.checked);

        // Event listener for the main "Users" checkbox
        userCheckbox.addEventListener('change', function () {
            toggleUserGroup(userCheckbox.checked);
        });

        // Event listener for "Delete Users" checkbox
        deleteCheckbox.addEventListener('change', function () {
            if (deleteCheckbox.checked) {
                addCheckbox.checked = true;
                editCheckbox.checked = true;
            }
        });

        // Event listener for "Add Users" checkbox
        addCheckbox.addEventListener('change', function () {
            if (addCheckbox.checked) {
                editCheckbox.checked = true;
            }
        });

        // Event listener for "Edit Users" checkbox
        editCheckbox.addEventListener('change', function () {
            if (editCheckbox.checked) {
                addCheckbox.checked = true;
            }
        });
    });
</script>
  @include('layouts.footer')