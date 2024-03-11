@extends('layouts.master')

@section('title', 'Create')

@section('content')
<div class="col-sm-12  p-4">
    <div class="bg-light rounded h-100 p-4">
        <!-- <div class="d-flex justify-content-end">
            <button class="btn btn-primary rounded-pill m-2 float-right" onclick="back()">Back</button>
        </div> -->
        <h6 class="mb-4">Add User</h6>
        <form method="POST" action="{{ route ('user.store') }}" enctype="multipart/form-data" id="userForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" aria-describedby="name" name="name">
                <input type="hidden" name="id" id="id" value="">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
            <div class="mb-3">
            <label class="form-check-label mb-2" for="exampleCheck1">Select Role</label>
              <select class="form-select mb-3" aria-label="Default select example" name="role_id" id="role_id">
                <option selected disabled="">Select Role</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitUserForm()">Save</button>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
  $(document).ready(function () {
    getAllRoles();
    decryptAndPopulateFields();
  });
  function getAllRoles() {
    $.ajax({
      url: "/roles",
      type: 'GET',
      success: function (response) {
        if (response.status) {
          var roles = response.roles;

          var select = $('#role_id');
          select.empty().append('<option value="" selected disabled>Select Role</option>');

          for (var i = 0; i < roles.length; i++) {
            var role = roles[i];
            select.append('<option value="' + role.id + '">' + role.name + '</option>');
            $('#role_id').val(role.id);
          }
        }
      },
      error: function (xhr, status, error) {
        var errors = JSON.parse(xhr.responseText);
        $.each(errors.errors, function (key, value) {
          $("#" + key).next().html(value[0]);
          $("#" + key).next().removeClass('d-none');
        });
      }
    });
  }
  function decryptAndPopulateFields() {
    var encodedData = getParameterByName('data');
    if (encodedData != null) {
      var decodedDataString = decodeURIComponent(atob(encodedData));
      if (decodedDataString) {
        var decodedData = JSON.parse(decodedDataString);
        $('#name').val(decodedData.name);
        $('#email').val(decodedData.email);
        $('#role_id').val(decodedData.role_id);
        $('#id').val(decodedData.id);
      }
    }
  }
  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }
  function submitUserForm() {

    var userId = $('#id').val();
    var formData = $('#userForm').serialize();

    var url, method;

    if (userId) {
      url = "{{ url('user') }}/" + userId;
      method = 'PUT';
      formData += '&_method=PUT';
    } else {
      url = "{{ route('user.store') }}";
      method = 'POST';
    }

    $.ajax({
      url: url,
      type: method,
      data: formData,
      success: function (response) {
        if (response.status) {
            toastr.success(response.message);
        }

        if (!userId) {
          $('#userForm')[0].reset();
          $('.text-danger').html('');
        }
      },
      error: function (xhr, status, error) {
        console.log(xhr);

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
    });
  }
  </script>
  @endsection