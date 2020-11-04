@extends('layouts.app')

@php
    $view_keys = ['id', 'first_name', 'last_name', 'country', 'phone', 'company'];
@endphp

@section('content')
    <form id="form-search" action="{{ route('contact.index') }}" method="GET">
        @csrf
    </form>

    <div class="container">
        <div class="row w-100 mx-auto mt-5 align-items-center">
            <div class="col-md-2">
                <input type="submit" value="Search" form="form-search" class="btn btn-block btn-primary">
            </div>
            @foreach (array_slice($view_keys, 1) as $key)
                <div class="col-md-2">
                    <input type="text" name="{{ $key }}" form="form-search" value="{{ request()->input($key) }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="container mt-3">
        <div class="bg-white shadow-sm">
            <div class="row w-100 mx-auto bg-dark text-white py-3 text-center">
                <div class="col-md-2">id</div>
                <div class="col-md-2">First Name</div>
                <div class="col-md-2">Last Name</div>
                <div class="col-md-2">Country</div>
                <div class="col-md-2">Phone</div>
                <div class="col-md-2">Company</div>
            </div>
            @if ($data->count())
                @foreach ($data as $d)
                    <div class="row w-100 mx-auto py-2 align-items-center text-center text-break border border-top-0 bg-white">
                        @foreach ($view_keys as $key)
                            <div class="col-md-2">{{ $d->{$key} }}</div>
                        @endforeach
                    </div>
                @endforeach
            @else
                Records not found
            @endif
        </div>
        @if ($data->count())
            <div class="row w-100 mx-auto py-3 py-5 mt-5">
                <div class="col d-flex justify-content-center">
                    {{ $data->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
