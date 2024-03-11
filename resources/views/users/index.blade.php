@extends('layouts.master')

@section('title', 'Users')

@section('content')
<div class="row g-4">
  <div class="col-12">
    <div class="bg-light rounded h-100 p-4">
      <!-- <h6 class="mb-4">Responsive Table</h6> -->
      <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary rounded-pill mb-2" id="addUser" data-toggle="modal"
          data-target="#myModal">Add User</button>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Role</th>
              <th scope="col" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
            <tr data-user-id="{{ $user->id }}">
              <th scope="row">{{ $loop->iteration }}</th>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              
              @foreach($user->getRoleNames() as $role)
              <td>{{ $role }}</td>
              @endforeach

              @if($user->getRoleNames()->isEmpty())
              <td>Not Assigned</td>
              @endif

              <td>
                <button class="btn btn-primary btn-sm" onclick="updateOrDelete({{ $user->id }}, 'edit')"><i class="fas fa-edit"
                    title="Edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="updateOrDelete({{ $user->id }}, 'delete')"><i class="fas fa-trash-alt" title="Delete"></i></button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="12" class="text-center">No records found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
  $(document).ready(function () {
    $('tbody').on('dblclick', 'tr', function () {
      var userId = $(this).data('user-id');
      if (userId) {
        getAllPermissions(userId);
      }
    });
  });

  $('#addUser').on('click', function () {
    window.location.href = "{{ route('user.create') }}";
  });
  function updateOrDelete(userId, action) {
    if(action == 'edit')
    {
      var url = "/user/" + userId + "/edit";
    }
    else {
      var url = "/user/" + userId + "/destroy";
    }
    $.ajax({
      url: url,
      type: "GET",
      success: function (response) {
        if (response.status) {
          var user = response.user || {};
          if (user) {
            var encodedData = btoa(JSON.stringify(user));
            var data = encodeURIComponent(encodedData);
          }
          if(action == 'edit') {
            window.location.href = "{{ route('user.create') }}?data=" + data;
          } else {
            toastr.success(response.message);
            window.location.reload();
          }
        }
      },
      error: function (xhr, status, error) {
        toastr.error(response.errors);
        console.error("Error:", error);
      }
    });
  }

  function getAllPermissions(userId) {
    $.ajax({
      url: "{{ route('getPermission') }}",
      type: "POST",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (res) {
        if (res.status) {
          var permissions = res.permission;
          var data = {
                        userId: userId,
                        permissions: permissions
                    };
                var permissionsData = btoa(JSON.stringify(data));
                window.location.href = "{{ route('show.permission') }}?data=" + encodeURIComponent(permissionsData);
        }
      }
    });
  }

  function savePermissions() {
    $.ajax({
      url: "{{ route('assignPermissions') }}",
      type: "POST",
      data: $('#permissionForm').serialize(),
      success: function (response) {
        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: response.message,
          });
        }
      },
      error: function (xhr, status, error) {
          console.error("Unexpected error:", xhr.responseText);
        }
    })
  }
</script>

@endsection