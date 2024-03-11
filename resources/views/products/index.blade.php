@extends('layouts.master')

@section('title', 'Product')

@section('content')
<div class="col-sm-12 p-4">
    <div class="bg-light rounded h-100 p-4">
    <div class="d-flex justify-content-end">
        @can('add product')<button class="btn btn-primary rounded-pill m-2 float-right" onclick="addProduct()">Add Product</button>@endcan
    </div>
    <div class="row">
    <div class="col-md-4">
    <form id="filterForm" action="{{ route('product.index') }}" method="GET">

            <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Product Code">
    </div>
    <div class="col-md-4">
            <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Product Name">
    </div>
    <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </div>
</div>

       
    <table class="table table-hover" id="productTable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Product Code</th>
                        <th scope="col">Description</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Price</th>
                        <th scope="col">Images</th>
                        @if (Gate::allows('edit product') || Gate::allows('delete product'))
                            <th scope="col">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="productTableBody">
                @forelse($products as $product)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->product_code }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->price}}</td>
                        <td>
                        @if ($product['image'])
                        @php
                            $imageNames = json_decode($product['image']);
                        @endphp

                        @if (is_array($imageNames))
                            <div class="image-gallery">
                                @foreach ($imageNames as $image)
                                    <img src="{{ asset('storage/images/' . $image) }}" alt="Product Image" style="width: 50px; height: 50px;">
                                @endforeach
                            </div>
                        @else
                            No Images
                        @endif
                    @else
                        No Images
                    @endif

                        <td>
                        @can('edit product') <button class="btn btn-sm btn-primary  rounded-pill" onclick="editOrDeleteProduct({{$product->id}}, 'edit')">Edit</button>@endcan 
                        @can('delete product')<button class="btn btn-sm btn-danger  rounded-pill" onclick="editOrDeleteProduct({{$product->id}}, 'delete')">Delete</button>@endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No records found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $products->links() }}
    </div>
</div>

@endsection

@section('script')
<script>
function addProduct() {
    window.location.href = "{{ route('product.create') }}";
}

function editOrDeleteProduct(id, action) {
    if (action === 'delete') {
        if (!confirm("Are you sure you want to delete this product?")) {
            return;
        }
    }

    $.ajax({
        url: action === 'edit' ? "{{ route('product.edit') }}" : "{{ route('product.destroy') }}",
        type: "POST",
        data: { id: id },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            if (res.status) {
                if (action === 'edit' && res.product) {
                    var encodedData = btoa(JSON.stringify(res.product));
                    var data = encodeURIComponent(encodedData);
                    window.location.href = "{{ route('product.create') }}?data=" + data;
                } else {
                    toastr.success(res.message);
                    window.location.reload();
                }
            } else {
                console.log(res.errors);
                displayValidationErrors(res.errors);
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

</script>
@endsection
