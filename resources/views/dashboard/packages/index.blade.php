@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Packages') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Packages') }}</h1>
                </div>
                <a href="{{ route('packages.create') }}" class="btn btn-success">{{ __('Create Package') }}</a>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name (EN)') }}</th>
                            <th>{{ __('Name (AR)') }}</th>
                            <th>{{ __('Monthly Price') }}</th>
                            <th>{{ __('Annual Price') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($packages as $index => $package)
                            <tr>
                                <td>{{ $packages->firstItem() + $index }}</td>
                                <td>{{ $package->name_en }}</td>
                                <td>{{ $package->name_ar }}</td>
                                <td>{{ number_format($package->price_monthly, 2) }}</td>
                                <td>{{ number_format($package->price_annual, 2) }}</td>
                                <td>{{ $package->statusText }}</td>
                                <td>
                                    <a href="{{ route('packages.edit', $package->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-danger delete">
                                            <i class="fa fa-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No packages found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $packages->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
@endsection
