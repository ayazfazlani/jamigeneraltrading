@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Items List</h1>
        <a href="{{ route('items.create') }}" class="btn btn-primary">Add New Item</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Cost</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->cost }}</td>
                        <td>{{ $item->price }}</td>
                        <td>
                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" width="100" alt="Item Image">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
