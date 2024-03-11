@extends('layouts.master')

@section('title', 'Create')

@section('content')
<div class="col-sm-12  p-4">
    <div class="bg-light rounded h-100 p-4">
        <h6 class="mb-4">Add Product</h6>
        <form method="POST" action="{{ route ('product.store') }}" enctype="multipart/form-data" id="productForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" aria-describedby="name" name="name">
                <input type="hidden" class="form-control" id="id" name="id" value="" />
            </div>
            <div class="mb-3">
                <label for="product_code" class="form-label">Product Code</label>
                <input type="text" class="form-control" id="product_code" name="product_code">
            </div>
            <div class="mb-3">
                <label for="description">Description</label>
                <textarea class="form-control" placeholder="Write Description" id="description" style="height: 150px;" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price">
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="text" class="form-control" id="stock" name="stock">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image[]" multiple accept="image/gif, image/jpeg, image/png" />
                <br />
                <div id="image-holder"></div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    decryptAndPopulateFields();

    $("#productForm").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var files = $("input[type='file']")[0].files;
        formData.append('total_images', files.length);
        $.each(files, function(i, file) {
            formData.append('images[]', file);
        });

        $.ajax({
            url: $(this).attr('action'),
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.status) {
                    $('#productForm')[0].reset();
                    $('#image-holder').html('');
                    toastr.success("Pr uploaded successfully.");
                    decryptAndPopulateFields();
                } else {
                    console.log(res.errors);
                }
            },
            error: function(xhr, status, error) {
                var errorResponse = JSON.parse(xhr.responseText);
                toastr.error(errorResponse.errors);
            }
        });
    });

    $("#image").on('change', function() {
        var image_holder = $("#image-holder");
        image_holder.empty();
        var countFiles = $(this)[0].files.length;

        for (var i = 0; i < countFiles; i++) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("<img />", {
                    "src": e.target.result,
                    "class": "thumb-image"
                }).appendTo(image_holder);
            }

            reader.readAsDataURL($(this)[0].files[i]);
        }
    });
});

function decryptAndPopulateFields() {
    var baseUrl = "{{ asset('storage/images') }}";
    var encodedData = getParameterByName('data');
    if (!encodedData) return;
    
    try {
        var decodedData = JSON.parse(decodeURIComponent(atob(encodedData)));
    } catch (error) {
        return console.error('Error decoding or parsing data:', error);
    }

    $('#id').val(decodedData.id);
    $('#name').val(decodedData.name);
    $('#product_code').val(decodedData.product_code);
    $('#description').val(decodedData.description);
    $('#price').val(decodedData.price);
    $('#stock').val(decodedData.stock);
    
    $('#image-holder').empty();
    (JSON.parse(decodedData.image) || []).forEach(function(relativePath) {
        var imageUrl = baseUrl + '/' + relativePath;
        $('#image-holder').append($('<img>').attr({src: imageUrl, width: '50', height: '50'}));
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


</script>
@endsection
