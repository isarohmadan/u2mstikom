@csrf


<!-- check is there any errors -->
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<input type="hidden" name="user_id" value="{{ old('user_id', auth()->id()) }}">

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                value="{{ old('title', $topic->title ?? '') }}" required>
            @error('title')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                value="{{ old('slug', $topic->slug ?? '') }}" placeholder="opsional">
            @error('slug')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="content" class="form-label">Konten <span class="text-danger">*</span></label>
    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6"
        required>{{ old('content', $topic->content ?? '') }}</textarea>
    @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="attachments" class="form-label">Lampiran (Gambar/PDF)</label>

            {{-- Display existing file attachments --}}
            @if(isset($file_attachments) && $file_attachments->count() > 0)
                <div class="existing-attachments mb-3">
                    <small class="text-muted d-block mb-2">Lampiran saat ini:</small>
                    @foreach($file_attachments as $attachment)
                        @php
                            $ext = strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                            $isPdf = $ext === 'pdf';
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-2 p-2 border rounded existing-attachment-item"
                            id="attachment-{{ $attachment->id }}">
                            @if($isImage)
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="Attachment" class="rounded"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                            @elseif($isPdf)
                                <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-file-pdf fs-4"></i>
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-secondary text-white rounded"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-file-earmark fs-4"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <small class="text-truncate d-block" style="max-width: 200px;">
                                    {{ basename($attachment->file_path) }}
                                </small>
                                <small class="text-muted">
                                    {{ $isPdf ? 'PDF' : ($isImage ? 'Gambar' : strtoupper($ext)) }}
                                    @if($attachment->file_size > 0)
                                        ({{ number_format($attachment->file_size / 1024, 1) }} KB)
                                    @endif
                                </small>
                            </div>
                            <div class="ms-auto">
                                <input type="checkbox" class="form-check-input remove-attachment-checkbox"
                                    name="remove_attachments[]" value="{{ $attachment->id }}"
                                    id="remove-attachment-{{ $attachment->id }}"
                                    style="cursor: pointer; width: 18px; height: 18px;">
                                <label class="form-check-label text-danger ms-1" for="remove-attachment-{{ $attachment->id }}"
                                    style="cursor: pointer;">
                                    <i class="bi bi-trash"></i> Hapus
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file"
                class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
                id="attachments" name="attachments[]" accept="image/*,.pdf" multiple>
            <small class="text-muted">Format yang diizinkan: JPG, PNG, GIF, PDF. Maksimal beberapa file.</small>
            @error('attachments')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @error('attachments.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Tautan Lampiran</label>

            {{-- Display existing link attachments --}}
            @if(isset($link_attachments) && $link_attachments->count() > 0)
                <div class="existing-links mb-3">
                    <small class="text-muted d-block mb-2">Tautan saat ini:</small>
                    @foreach($link_attachments as $attachment)
                        <div class="d-flex align-items-center gap-2 mb-2 p-2 border rounded existing-link-item"
                            id="link-attachment-{{ $attachment->id }}">
                            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-link-45deg fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <a href="{{ $attachment->file_path }}" target="_blank" class="text-truncate d-block"
                                    style="max-width: 200px;">
                                    {{ $attachment->file_path }}
                                </a>
                            </div>
                            <div class="ms-auto">
                                <input type="checkbox" class="form-check-input remove-attachment-checkbox"
                                    name="remove_attachments[]" value="{{ $attachment->id }}"
                                    id="remove-link-attachment-{{ $attachment->id }}"
                                    style="cursor: pointer; width: 18px; height: 18px;">
                                <label class="form-check-label text-danger ms-1"
                                    for="remove-link-attachment-{{ $attachment->id }}" style="cursor: pointer;">
                                    <i class="bi bi-trash"></i> Hapus
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add new links section --}}
            <small class="text-muted d-block mb-2">Tambah tautan baru:</small>
            <div id="link-list">
                <div class="input-group mb-2 attachment-link-row">
                    <input type="url" class="form-control" name="attachment_links[]"
                        placeholder="https://contoh.com/resource" pattern="https?://.*">
                    <button type="button" class="btn btn-outline-danger remove-link">&times;</button>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-link">Tambah Tautan</button>
            <small class="text-muted d-block mt-1">Hanya tautan dengan skema http/https.</small>
        </div>
    </div>

</div>

<div class="row">
    {{-- Status field - only visible to users who can approve topics --}}
    @if(auth()->check() && auth()->user()->can('topics.approve'))
        <div class="col-md-4">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @php
                        $currentStatus = old('status', isset($topic) && $topic ? $topic->status : 'submitted');
                    @endphp
                    <option value="submitted" {{ $currentStatus == 'submitted' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ $currentStatus == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @else
        {{-- Regular users always submit with 'submitted' status --}}
        <input type="hidden" name="status" value="submitted">
    @endif
    <div class="col-md-4">
        <div class="mb-3">
            <label for="category_ids" class="form-label">Kategori</label>
            <select class="form-select @error('category_ids') is-invalid @enderror" id="category_ids" name="category_ids[]" multiple>
                @php
                    $selectedCategories = [];
                    if (old('category_ids')) {
                        $selectedCategories = old('category_ids');
                    } elseif (isset($topic) && $topic->relationLoaded('categories') && $topic->categories) {
                        $selectedCategories = $topic->categories->pluck('id')->toArray();
                    } elseif (isset($topic) && $topic->categories) {
                        $selectedCategories = $topic->categories->pluck('id')->toArray();
                    }
                @endphp
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategories) ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                @endif
            </select>
            @error('category_ids')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Pilih satu atau lebih kategori</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="tags" class="form-label">Tag</label>
            <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags"
                placeholder="tag1, tag2"
                value="{{ old('tags', isset($topic) && isset($topic->tags) && is_array($topic->tags) ? implode(', ', $topic->tags) : '') }}">
            @error('tags')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="is_locked" name="is_locked" {{ old('is_locked', isset($topic) && $topic ? ($topic->is_locked ?? false) : false) ? 'checked' : '' }} style="width: 20px; height: 20px;">
            <label class="form-check-label" for="is_locked" style="color:red">
                Kunci Diskusi
            </label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="{{ route('topics.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-2"></i> Simpan
    </button>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tom Select for Category (Multiple Selection with Create)
            var categorySelect = new TomSelect('#category_ids', {
                plugins: ['remove_button'],
                maxItems: null,
                create: function(input, callback) {
                    // Show loading state
                    callback({loading: true});
                    
                    // Create category via AJAX
                    fetch('{{ route("categories.store.ajax") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: input
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Add the new category to the options
                            categorySelect.addOption({
                                value: data.category.id,
                                text: data.category.name
                            });
                            // Select the newly created category
                            callback({value: data.category.id, text: data.category.name});
                        } else {
                            // Show error message
                            alert(data.message || 'Gagal membuat kategori baru');
                            callback();
                        }
                    })
                    .catch(error => {
                        console.error('Error creating category:', error);
                        alert('Terjadi kesalahan saat membuat kategori baru');
                        callback();
                    });
                },
                createOnBlur: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Cari & Pilih Kategori...',
                plugins: ['dropdown_input', 'remove_button'],
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results">Tekan enter untuk menambahkan kategori baru "' + escape(data.input) + '"</div>';
                    },
                    option_create: function(data, escape) {
                        return '<div class="create">Tambahkan kategori baru <strong>' + escape(data.input) + '</strong></div>';
                    }
                }
            });

            // Tom Select for Tags (Searchable + Tagging)
            var tagInput = new TomSelect('#tags', {
                create: true,
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                delimiter: ',',
                placeholder: 'Ketik tag lalu tekan enter...',
                render: {
                    no_results: function (data, escape) {
                        return '<div class="no-results">Tekan enter untuk menambahkan tag "' + escape(data.input) + '"</div>';
                    }
                }
            });

            // Existing Attachment Logic
            var addBtn = document.getElementById('add-link');
            var list = document.getElementById('link-list');
            if (addBtn && list) {
                addBtn.addEventListener('click', function () {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'input-group mb-2 attachment-link-row';
                    var input = document.createElement('input');
                    input.type = 'url';
                    input.className = 'form-control';
                    input.name = 'attachment_links[]';
                    input.placeholder = 'https://contoh.com/resource';
                    input.pattern = 'https?://.*';
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-danger remove-link';
                    button.innerHTML = '&times;';
                    wrapper.appendChild(input);
                    wrapper.appendChild(button);
                    list.appendChild(wrapper);
                });
                list.addEventListener('click', function (e) {
                    if (e.target && e.target.classList.contains('remove-link')) {
                        var row = e.target.closest('.attachment-link-row');
                        if (row) row.remove();
                    }
                });
            }
        });
    </script>
@endpush