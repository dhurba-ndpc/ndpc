 // State references
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const dropzoneArea = document.getElementById('dropzoneArea');
    const removeImageInput = document.getElementById('removeImageInput');
    const fileMetaPill = document.getElementById('fileMetaPill');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    const titleEnInput = document.getElementById('title_en');
    const titleNeInput = document.getElementById('title_ne');
    const enCharCount = document.getElementById('enCharCount');
    const neCharCount = document.getElementById('neCharCount');

    // Status Toggle Handler
    function updateStatusLabel(checkbox) {
        const label = document.getElementById('statusLabel');
        const badge = document.getElementById('statusBadge');
        if (checkbox.checked) {
            if (label) label.textContent = 'Published';
            if (badge) {
                badge.textContent = 'Active';
                badge.className = 'badge badge-success';
            }
        } else {
            if (label) label.textContent = 'Draft / Hidden';
            if (badge) {
                badge.textContent = 'Inactive';
                badge.className = 'badge badge-danger';
            }
        }
    }

    // Character counter updates
    function updateCharCounters() {
        if (titleEnInput && enCharCount) {
            enCharCount.textContent = `${titleEnInput.value.length} / 255`;
        }
        if (titleNeInput && neCharCount) {
            neCharCount.textContent = `${titleNeInput.value.length} / 255`;
        }
    }

    if (titleEnInput) {
        titleEnInput.addEventListener('input', updateCharCounters);
    }

    if (titleNeInput) {
        titleNeInput.addEventListener('input', updateCharCounters);
    }

    // File selection & preview
    function handleFile(file) {
        if (!file) return;

        // Size check (3MB max)
        if (file.size > 3 * 1024 * 1024) {
            alert('File size exceeds 3MB limit! Please upload an optimized banner image.');
            imageInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            previewContainer.classList.remove('d-none');
            uploadPrompt.classList.add('d-none');
            dropzoneArea.classList.add('p-0');

            // File meta pill
            if (fileName && fileSize && fileMetaPill) {
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                fileMetaPill.classList.remove('d-none');
            }

            if (removeImageInput) removeImageInput.value = '0';
        }
        reader.readAsDataURL(file);
    }

    imageInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            handleFile(e.target.files[0]);
        }
    });

    // Drag & Drop Handlers
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneArea.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneArea.classList.remove('dragover');
        }, false);
    });

    dropzoneArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            imageInput.files = files;
            handleFile(files[0]);
        }
    });

    // Remove Image
    function removeImage() {
        imagePreview.src = '';
        previewContainer.classList.add('d-none');
        uploadPrompt.classList.remove('d-none');
        dropzoneArea.classList.remove('p-0');
        imageInput.value = '';
        
        if (removeImageInput) removeImageInput.value = '1';
        if (fileMetaPill) fileMetaPill.classList.add('d-none');
    }

    // Zoom Image Modal
    function openImageModal() {
        const modalImg = document.getElementById('modalPreviewImg');
        if (imagePreview.src) {
            modalImg.src = imagePreview.src;
            $('#imageModal').modal('show');
        }
    }

    // Initialize state
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCounters();
    });