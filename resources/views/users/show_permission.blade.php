@extends('layouts.master')

@section('title', 'Create')

@section('content')
<div class="col-sm-12  p-4">
    <div class="bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-end">
            <a class="btn btn-primary rounded-pill m-2 float-right" href="{{ route('user.index') }}">Back</a>
        </div>
        <h6 class="mb-4">Permissions</h6>

        <div id="permissionsContainer"></div>
        <input type="hidden" name="userId" id="userId" value="">

        <button id="saveButton" class="btn btn-sm btn-primary  rounded-pill mt-3" onclick="savePermissions()">Save Permissions</button>

        
    </div>
</div>

@endsection

@section('script')
    <script>
       $(document).ready(function() {
    decryptAndPopulateFields();
});
function decryptAndPopulateFields() {
    var encodedData = getParameterByName('data');
    if (!encodedData) return;
    
    try {
        var decodedData = JSON.parse(decodeURIComponent(atob(encodedData)));
    } catch (error) {
        return console.error('Error decoding or parsing data:', error);
    }
    $('#userId').val(decodedData.userId);
    var permissionsContainer = document.getElementById('permissionsContainer');

    Object.values(decodedData.permissions).forEach(function(permission) {
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'permissions[]';
        checkbox.value = permission.id;
        checkbox.id = 'permission_' + permission.id;

        var label = document.createElement('label');
        label.htmlFor = 'permission_' + permission.id;
        label.appendChild(document.createTextNode(permission.name));

        var div = document.createElement('div');
        div.appendChild(checkbox);
        div.appendChild(label);

        permissionsContainer.appendChild(div);
    });
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
function savePermissions() {
        var selectedPermissions = [];
        $('input[name="permissions[]"]:checked').each(function() {
            selectedPermissions.push($(this).val());
        });

        var userId = $('#userId').val();

        $.ajax({
            url: '{{ route('assignPermissions')}}',
            type: 'POST',
            data: {
                permissions: selectedPermissions,
                id: userId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    alert('Permissions saved successfully.');
                } else {
                    alert('Failed to save permissions.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    </script>
@endsection