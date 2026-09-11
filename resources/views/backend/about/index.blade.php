@extends('backend.layout.main')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header & Action Bar -->
    <div class="page-header-container mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold d-flex align-items-center">
                <i class="fas fa-images text-primary mr-2"></i>
                Hero Banner Lists
            </h1>
        </div>
        <div>
            <a href="{{ route('about.create') }}" class="btn btn-gradient-primary shadow-sm">
                <i class="fas fa-plus fa-sm mr-1"></i> Add New About Us
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="custom-card mb-4">
        <div class="custom-card-header">
            <h6><i class="fas fa-table mr-1"></i> Hero Banners</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-gray-800">
                            <th style="width: 60px;" class="text-center align-middle">S.No</th>
                            <th style="width: 140px;" class="text-center align-middle">Image</th>
                            <th class="align-middle">Title</th>
                            <th style="width: 100px;" class="text-center align-middle">Order</th>
                            <th style="width: 110px;" class="text-center align-middle">Status</th>
                            <th style="width: 160px;" class="text-center align-middle">Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-light text-gray-800">
                            <th class="text-center align-middle">S.No</th>
                            <th class="text-center align-middle">Image</th>
                            <th class="align-middle">Title</th>
                            <th class="text-center align-middle">Order</th>
                            <th class="text-center align-middle">Status</th>
                            <th class="text-center align-middle">Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @forelse ($posts as $list)
                            <tr>
                                <td class="text-center align-middle font-weight-bold">{{ $loop->iteration }}</td>
                                <td class="text-center align-middle">
                                    @if(!empty($list->image))
                                        <img src="{{ asset('storage/' . $list->image) }}" 
                                             alt="Hero Banner" 
                                             class="table-banner-thumbnail rounded border shadow-xs"
                                             onerror="this.onerror=null;this.parentElement.innerHTML='<span class=\'badge badge-light text-muted p-2\'><i class=\'fas fa-image mr-1\'></i>No Image</span>';">
                                    @else
                                        <span class="badge badge-light text-muted p-2">
                                            <i class="fas fa-image mr-1"></i> No Image
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="mb-1">
                                        <span class="badge badge-primary mr-1" style="font-size: 0.7rem;">EN</span>
                                        <span class="font-weight-bold text-gray-900">{{ $list->translations->firstWhere('locale', 'en')?->title ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="badge badge-info mr-1" style="font-size: 0.7rem;">NE</span>
                                        <span class="text-gray-700">{{ $list->translations->firstWhere('locale', 'ne')?->title ?? 'उपलब्ध छैन' }}</span>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light border text-dark font-weight-bold px-2 py-1">
                                        {{ $list->position ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    @if($list->is_active)
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle fa-sm mr-1"></i> Published
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-clock fa-sm mr-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('hero-banners.edit', $list->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Edit">
                                        <i class="fas fa-edit fa-sm mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('hero-banners.destroy', $list->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this hero banner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Delete">
                                            <i class="fas fa-trash-alt fa-sm mr-1"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-images fa-2x mb-2 text-gray-300 d-block"></i>
                                    <span>No hero banners found.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
