@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Item</h1>
        <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $item->name }}" required>
            </div>
            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" value="{{ $item->sku }}" required>
            </div>
            <div class="form-group">
                <label for="cost">Cost</label>
                <input type="number" name="cost" id="cost" class="form-control" value="{{ $item->cost }}" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ $item->price }}" required>
            </div>
            <div class="form-group">
                <label for="image">Item Image</label>
                <input type="file" name="image" id="image" class="form-control">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" width="100" alt="Item Image">
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
    </div>
@endsection
