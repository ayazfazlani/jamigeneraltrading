@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Item</h1>
        
        <!-- Display any success or error messages -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Item Name -->
            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <div class="alert alert-danger">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <!-- SKU -->
            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" required value="{{ old('sku') }}">
                @if ($errors->has('sku'))
                    <div class="alert alert-danger">{{ $errors->first('sku') }}</div>
                @endif
            </div>

            <!-- Cost -->
            <div class="form-group">
                <label for="cost">Cost</label>
                <input type="number" name="cost" id="cost" class="form-control" required value="{{ old('cost') }}">
                @if ($errors->has('cost'))
                    <div class="alert alert-danger">{{ $errors->first('cost') }}</div>
                @endif
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" required value="{{ old('price') }}">
                @if ($errors->has('price'))
                    <div class="alert alert-danger">{{ $errors->first('price') }}</div>
                @endif
            </div>

            <!-- Image Upload with Preview -->
            <div class="form-group">
                <label for="image">Item Image</label>
                <input type="file" name="image" id="image" class="form-control" onchange="previewImage(event)">
                @if ($errors->has('image'))
                    <div class="alert alert-danger">{{ $errors->first('image') }}</div>
                @endif
                <!-- Image Preview -->
                <img id="imagePreview" style="max-width: 200px; margin-top: 10px;" />
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Save Item</button>
        </form>
    </div>

    <script>
        // Image Preview Functionality
        function previewImage(event) {
            const output = document.getElementById('imagePreview');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src); // Free memory after image loads
            }
        }
    </script>
@endsection
