@include('layouts.header')
<div class="container-fluid py-4">
  <div class="row mt-4">
    <div class="col-lg-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header pb-0 p-3">
          <div class="d-flex justify-content-between">
            <h5>Change Password</h5>
          </div>
        </div>
        <form action="/changePassword" method="POST">
          @csrf
          <div class="container-fluid py-4">
            <div class="mb-3">
              <label for="current-password" class="form-label"><strong>Current Password</strong></label>
              <input type="password" name="current_password" id="current-password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="new-password" class="form-label"><strong>New Password</strong></label>
              <input type="password" name="new_password" id="new-password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="retype-password" class="form-label"><strong>Re-type Password</strong></label>
              <input type="password" name="retype_password" id="retype-password" class="form-control" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="float: right; margin-right: 30px; padding: 10px 50px;">Save</button>
        </form>
      </div>
    </div>
    <div class="col-lg-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header pb-0 p-3">
          <div class="d-flex justify-content-between">
            <h5>Change Email</h5>
          </div>
        </div>
        <form action="/changeEmail" method="POST">
          @csrf
          <div class="container-fluid py-4">
            <div class="mb-3">
              <label for="current-password" class="form-label"><strong>Email</strong></label>
              <input type="email" name="email" id="current-password" class="form-control" value="{{ $users->email }}" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="float: right; margin-right: 30px; padding: 10px 50px;">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>
@include('layouts.footer')
