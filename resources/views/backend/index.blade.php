@extends('backend.layout.main')

 
@section('content')
<div class="container-fluid px-0">

    <!-- Page Header & Action Bar -->
    <div class="page-header-container">
        <div>
            
            <h1 class="h4 mb-0 text-gray-800 font-weight-bold d-flex align-items-center">
                <i class="fas fa-images text-primary mr-2"></i>
                {{ isset($banner) ? 'Edit Hero Banner' : 'Add New Hero Banner' }}
                @if(isset($banner))
                    <span class="badge badge-info ml-2" style="font-size: 0.75rem;">ID: #{{ $banner->id }}</span>
                @endif
            </h1>
        </div>
        <div>
            <a href="{{ route('hero-banners.index') }}" class="btn btn-secondary-custom shadow-sm">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Global Alert  -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                <div>
                    <strong>Please resolve the following errors:</strong>
                    <ul class="mb-0 mt-1 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Main Form Starts -->
    <form id="heroBannerForm" action="{{ isset($banner) ? route('hero-banners.update', $banner->id) : route('hero-banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($banner))
            @method('PUT')
        @endif

        <div class="row">
          
            <div class="col-xl-5 col-lg-6 mb-4">
                
                <!-- Card 1: Banner Image Upload -->
                <div class="custom-card mb-4">
                    <div class="custom-card-header">
                        <h6>Banner Media</h6>
                        <span class="badge badge-pill badge-primary">Required</span>
                    </div>
                    <div class="card-body p-3">
                        <!-- Hidden File Input -->
                        <input type="file" name="image" id="imageInput" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp">
                        <input type="hidden" name="remove_image" id="removeImageInput" value="0">

                        @php
                            $hasImage = isset($banner) && $banner->image;
                            $imageUrl = $hasImage ? asset('storage/' . $banner->image) : '';
                        @endphp

                        <!-- Upload / Preview Box -->
                        <div id="dropzoneArea" class="banner-upload-area mb-3 {{ $hasImage ? 'p-0' : '' }}" onclick="document.getElementById('imageInput').click()">
                            
                            <!-- State A: Empty Prompt -->
                            <div id="uploadPrompt" class="upload-prompt {{ $hasImage ? 'd-none' : '' }}">
                                <div class="upload-icon-circle">
                                    <i class="fas fa-image"></i>
                                </div>
                                <h6 class="font-weight-bold text-gray-800 mb-1">Click to upload banner image</h6>
                                <p class="text-muted small mb-0">or drag and drop your image file here</p>
                            </div>

                            <!-- State B: Image Preview Container -->
                            <div id="previewContainer" class="preview-container {{ $hasImage ? '' : 'd-none' }}">
                                <div class="preview-aspect-ratio">
                                    <img id="imagePreview" src="{{ $imageUrl }}" alt="Banner Preview" class="preview-image">
                                </div>
                                <div class="preview-overlay-actions">
                                    <button type="button" class="btn btn-light btn-sm" onclick="event.stopPropagation(); document.getElementById('imageInput').click()">
                                        <i class="fas fa-sync-alt mr-1"></i> Change
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="event.stopPropagation(); openImageModal()">
                                        <i class="fas fa-search-plus mr-1"></i> Zoom
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="event.stopPropagation(); removeImage()">
                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Selected File Metadata Display -->
                        <div id="fileMetaPill" class="d-none alert alert-light py-2 px-3 mb-3 border d-flex align-items-center justify-content-between">
                            <span class="small text-truncate mr-2"><i class="fas fa-file-image text-primary mr-1"></i> <span id="fileName">image.jpg</span></span>
                            <span class="badge badge-secondary" id="fileSize">0 KB</span>
                        </div>

                        <!-- Recommended Specs Box -->
                        <div class="specs-box">
                            <div class="font-weight-bold text-gray-700 mb-1 d-flex align-items-center">
                                <i class="fas fa-info-circle text-info mr-1"></i> Image Guidelines
                            </div>
                            <ul class="mb-0">
                                <li><strong>Recommended Dimension:</strong> 1920 × 800 px</li>
                                <li><strong>Max file size:</strong> 3 MB</li>
                                <li><strong>Supported Formats:</strong> JPG, JPEG, PNG, WEBP</li>
                            </ul>
                        </div>

                        @error('image')
                            <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>

               

            </div>

            <!-- Right Column: Banner Content & Configuration -->
            <div class="col-xl-7 col-lg-6 mb-4">
                
                <!-- Card 3: Multilingual Content -->
                <div class="custom-card mb-4">
                    <div class="custom-card-header">
                        <h6>Hero Banner Content</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Language Navigation Tabs -->
                        <ul class="nav custom-nav-tabs mb-4" id="languageTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="en-tab" data-toggle="tab" href="#en" role="tab" aria-controls="en" aria-selected="true">
                                    <span class="mr-1">🇺🇸</span> English <span class="badge badge-primary badge-pill ml-1" style="font-size: 0.65rem;">Primary</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="ne-tab" data-toggle="tab" href="#ne" role="tab" aria-controls="ne" aria-selected="false">
                                    <span class="mr-1">🇳🇵</span> नेपाली (Nepali)
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="languageTabContent">
                            <!-- English Fields -->
                            <div class="tab-pane fade show active" id="en" role="tabpanel" aria-labelledby="en-tab">
                                <div class="form-group mb-3">
                                    <label for="title_en" class="form-label-custom">
                                        <span>Banner Title (English) <span class="text-danger">*</span></span>
                                        <span class="small text-muted font-weight-normal" id="enCharCount">0 / 255</span>
                                    </label>
                                    <div class="input-group input-group-custom">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        </div>
                                        <input type="text" 
                                               name="title_en" 
                                               id="title_en" 
                                               class="form-control form-control-custom @error('title_en') is-invalid @enderror" 
                                               placeholder="e.g. Empowering National Digital Innovation & Progress" 
                                               value="{{ old('title_en', isset($banner) ? $banner->title_en : '') }}" 
                                               maxlength="255"
                                               required>
                                    </div>
                                    @error('title_en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nepali Fields -->
                            <div class="tab-pane fade" id="ne" role="tabpanel" aria-labelledby="ne-tab">
                                <div class="form-group mb-3">
                                    <label for="title_ne" class="form-label-custom">
                                        <span>Banner Title (नेपाली)</span>
                                        <span class="small text-muted font-weight-normal" id="neCharCount">0 / 255</span>
                                    </label>
                                    <div class="input-group input-group-custom">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        </div>
                                        <input type="text" 
                                               name="title_ne" 
                                               id="title_ne" 
                                               class="form-control form-control-custom @error('title_ne') is-invalid @enderror" 
                                               placeholder="उदा. राष्ट्रिय डिजिटल प्रविधि र विकासको प्रवर्द्धन" 
                                               value="{{ old('title_ne', isset($banner) ? $banner->title_ne : '') }}"
                                               maxlength="255">
                                    </div>
                                    @error('title_ne')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Card 4: Publication & Display Settings -->
                <div class="custom-card mb-4">
                    <div class="custom-card-header">
                        <h6> Sort Order & Publishing Configuration</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <!-- Display Order / Position -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="position" class="form-label-custom">
                                    <span>Display Order Position</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                    </div>
                                    <input type="number" 
                                           name="position" 
                                           id="position" 
                                           class="form-control form-control-custom @error('position') is-invalid @enderror" 
                                           placeholder="1" 
                                           min="0"
                                           value="{{ old('position', isset($banner) ? $banner->position : '1') }}">
                                </div>
                                @error('position')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Publishing Status Sliding Toggle -->
                            <div class="col-md-6">
                                <label class="form-label-custom">
                                    <span>Publishing Status</span>
                                </label>
                                @php
                                    $isActive = old('is_active', isset($banner) ? $banner->is_active : 1);
                                @endphp
                                <div class="d-flex align-items-center justify-content-between" style="height: 42px;">
                                    <div class="custom-control custom-switch custom-switch-lg m-0">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               class="custom-control-input" 
                                               id="isActiveSwitch" 
                                               value="1" 
                                               {{ $isActive == 1 ? 'checked' : '' }}
                                               onchange="updateStatusLabel(this)">
                                        <label class="custom-control-label mb-0" for="isActiveSwitch" id="statusLabel">
                                            {{ $isActive == 1 ? 'Published' : 'Draft / Hidden' }}
                                        </label>
                                    </div>
                                    <span class="badge {{ $isActive == 1 ? 'badge-success' : 'badge-secondary' }}" id="statusBadge">
                                        {{ $isActive == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @error('is_active')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Submit Bar -->
                <div class="custom-card">
                    <div class="card-body p-3 d-flex align-items-center">
                        <button type="submit" id="submitBtn" class="btn btn-gradient-primary mr-2">
                            <i class="fas fa-save mr-1"></i> {{ isset($banner) ? 'Update Banner' : 'Publish Banner' }}
                        </button>
                        <a href="{{ route('hero-banners.index') }}" class="btn btn-secondary-custom">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

<!-- Zoom Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title font-weight-bold text-gray-800" id="imageModalLabel">
                    <i class="fas fa-image text-primary mr-2"></i> Banner Image Preview
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 text-center bg-dark">
                <img id="modalPreviewImg" src="" alt="Full Preview" class="img-fluid" style="max-height: 75vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

 
