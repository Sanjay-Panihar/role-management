@extends('layouts.master')

@section('title', 'Permission')

@section('content')
<div class="row g-4">
  <div class="col-12 p-4">
    <div class="bg-light rounded h-100 p-4">
      <!-- <h6 class="mb-4">Responsive Table</h6> -->
      <div class="d-flex justify-content-end">
        <!-- <button type="button" class="btn btn-primary rounded-pill mb-2" onclick="permissionAction()">Add Permission</button> -->
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Guard</th>
              <!-- <th scope="col">Action</th> -->
            </tr>
          </thead>
          <tbody>
            @forelse($permissions as $permission)
            <tr>
              <th scope="row">{{ $loop->iteration }}</th>
              <td>{{ $permission->name ?? '' }}</td>
              <td>{{ $permission->guard_name ??'' }}</td>
              <td>
                <!-- <button class="btn btn-primary btn-sm" onclick="permissionAction({{ $permission->id }})"><i class="fas fa-edit" title="Edit"></i></button>
                <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt" onclick= "deletePermission({{ $permission->id }})" title="Delete"></i></button> -->
              </td>
            </tr>
            @empty
            <tr>
              <td class="text-center" colspan="12">No records found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        {{ $permissions->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
function permissionAction(permissionId) {
      var url = "/permissions/"+ permissionId + "/edit";
      $.ajax({
        url: url,
        type: "GET",
        success:function(response){
         if (response.status) {
          var permission = response.permission|| {};
          if (permission) {
            var encodedData = btoa(JSON.stringify(permission));
            var data = encodeURIComponent(encodedData);
          }
          window.location.href = "{{ route('permission.create') }}?permission=" + data;
         }
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
      });
    }
    function deletePermission(id) {
      showLoader();
      var url = "permissionsDelete";
        $.ajax({
          url: url,
          type: "POST",
          data: {"id" : id},
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
          success: function (response) {
            if (response.status) {
                toastr.success(response.message);
          }
        },
          error: function (xhr, status, error) {
            hideLoader();

            if (xhr.responseJSON.message) {
              var errors = xhr.responseJSON.message;
              $.each(errors, function (key, value) {
                $("#" + key).next().html(value[0]);
                $("#" + key).next().removeClass('d-none');
              });
            } else if (xhr.responseJSON.message) {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: xhr.responseJSON.message,
              });
            } else {
              console.error("Unexpected error:", xhr.responseText);
            }
          }
        })
      }
</script>
@endsection