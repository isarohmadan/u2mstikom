@extends('layouts.app')

@section('title', 'Detail Topik')

@section('role', 'Manajemen Topik')

@section('navigation')
    @include('fragments.navigation')
@endsection

@push('styles')
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-container {
            min-height: 200px;
        }

        .ql-editor {
            min-height: 200px;
        }

        /* Style for displaying Quill content */
        .answer-content .ql-editor {
            min-height: auto !important;
            padding: 0 !important;
        }

        .answer-content .ql-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 10px 0;
        }

        /* Comment styling */
        .avatar-circle {
            font-weight: 600;
        }

        .comment-item {
            transition: all 0.2s ease;
        }

        .comment-item:hover {
            background-color: #f8f9fa !important;
            transform: translateX(2px);
        }

        .comment-form {
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-content {
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        .answer-content .ql-editor p {
            margin-bottom: 1rem;
        }

        .answer-content .ql-editor ul,
        .answer-content .ql-editor ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .answer-content .ql-editor blockquote {
            border-left: 4px solid #ccc;
            margin: 1rem 0;
            padding-left: 1rem;
            font-style: italic;
            color: #666;
        }

        .answer-content .ql-editor code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }

        .answer-content .ql-editor pre {
            background: #f4f4f4;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
        }

        /* File attachment link styling */
        .answer-content .ql-editor a.file-attachment,
        .ql-editor a[href*=".pdf"],
        .ql-editor a[href*=".doc"],
        .ql-editor a[href*=".xls"],
        .ql-editor a[href*=".ppt"] {
            display: inline-block;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px 14px;
            margin: 4px 2px;
            text-decoration: none;
            color: #495057;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .answer-content .ql-editor a.file-attachment:hover,
        .ql-editor a[href*=".pdf"]:hover,
        .ql-editor a[href*=".doc"]:hover,
        .ql-editor a[href*=".xls"]:hover,
        .ql-editor a[href*=".ppt"]:hover {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        /* Custom file button in toolbar */
        .ql-file {
            width: 28px !important;
        }

        .ql-file svg {
            width: 18px;
            height: 18px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Detail Topik</h4>
        <div>
            <!-- Bookmark Button -->
            <form action="{{ route('topics.bookmark', $topic) }}" method="POST" class="d-inline me-1">
                @csrf
                <button type="submit"
                    class="btn btn-sm {{ auth()->user()->hasBookmarked($topic->id) ? 'btn-warning text-white' : 'btn-outline-secondary' }}"
                    data-bs-toggle="tooltip"
                    title="{{ auth()->user()->hasBookmarked($topic->id) ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}">
                    <i
                        class="bi {{ auth()->user()->hasBookmarked($topic->id) ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                    {{ auth()->user()->hasBookmarked($topic->id) ? 'Disimpan' : 'Simpan' }}
                </button>
            </form>

            @if(($topic->user_id == auth()->id() && auth()->user()->can('topics.my.edit')) || auth()->user()->can('topics.edit'))
                <a href="{{ route('topics.edit', $topic) }}" class="btn btn-sm btn-warning"><i
                        class="bi bi-pencil me-1"></i>Ubah</a>
            @endif
            <a href="{{ route('topics.index') }}" class="btn btn-sm btn-secondary"><i
                    class="bi bi-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-2">
                @php
                    $statusLabels = [
                        'approved' => 'Disetujui',
                        'submitted' => 'Menunggu',
                        'rejected' => 'Ditolak',
                        'archived' => 'Diarsipkan',
                    ];
                    $statusLabel = $statusLabels[$topic->status] ?? ucfirst($topic->status);
                @endphp
                <span
                    class="badge bg-{{ $topic->status === 'approved' ? 'success' : ($topic->status === 'rejected' ? 'danger' : ($topic->status === 'submitted' ? 'warning' : 'secondary')) }}">{{ $statusLabel }}</span>
                @if($topic->is_locked)
                    <span class="badge bg-dark ms-2">Terkunci</span>
                @endif
                @if($topic->is_edited)
                    <span class="badge bg-secondary ms-2">Diedit</span>
                @endif
            </div>
            <h3 class="mb-1">{{ $topic->title }}</h3>
            <div class="text-muted mb-3">
                <span class="me-2">Slug: <code>{{ $topic->slug }}</code></span>
                @if($topic->categories && $topic->categories->count() > 0)
                    <span class="me-2">• Kategori:
                        @foreach($topic->categories as $category)
                            <a href="{{ route('topics.index', ['category_id' => $category->id]) }}"
                                class="text-decoration-none badge bg-primary me-1">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </span>
                @elseif($topic->category)
                    {{-- Fallback to single category for backward compatibility --}}
                    <span class="me-2">• Kategori:
                        <a href="{{ route('topics.index', ['category_id' => $topic->category->id]) }}"
                            class="text-decoration-none badge bg-primary">
                            {{ $topic->category->name }}
                        </a>
                    </span>
                @endif
                <span>• Dibuat: {{ optional($topic->created_at)->format('d M Y H:i') }}</span>
            </div>
            <div class="mb-3">
                <strong>Penulis:</strong> {{ optional($topic->user)->name ?? '-' }}
                @if($topic->approved_by && $topic->approver)
                    <span class="ms-3"><strong>Disetujui oleh:</strong> {{ $topic->approver->name }}</span>
                @endif
                @if($topic->is_edited && $topic->editor)
                    <span class="ms-3"><strong>Diedit oleh:</strong> {{ $topic->editor->name }}</span>
                @endif
            </div>
            <div class="mb-3">
                @php($tags = is_array($topic->tags) ? $topic->tags : [])
                @if(count($tags))
                    <div class="mb-2"><strong>Tag:</strong></div>
                    <div>
                        @foreach($tags as $tg)
                            <a href="{{ route('topics.index', ['tag' => $tg]) }}" class="text-decoration-none">
                                <span class="badge bg-info text-dark me-1">#{{ $tg }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <hr>
            <div class="mt-3">
                <div class="mb-2"><strong>Konten</strong></div>
                <div class="white-space-pre-wrap">{!! nl2br(e($topic->content)) !!}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Lampiran</h6>
        </div>
        <div class="card-body">
            @if($imageAttachments->count() || $pdfAttachments->count() || $otherAttachments->count())
                {{-- Display Image Attachments Inline --}}
                @foreach($imageAttachments as $att)
                    <div class="mb-3">
                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ basename($att->file_path) }}"
                                class="img-fluid rounded shadow-sm" style="max-height: 400px; cursor: pointer;">
                        </a>
                        <div class="mt-1">
                            <small class="text-muted">
                                <i class="bi bi-image me-1"></i>{{ basename($att->file_path) }}
                                ({{ number_format($att->file_size / 1024, 1) }} KB)
                            </small>
                        </div>
                    </div>
                @endforeach

                {{-- Display PDF Attachments with Embedded Viewer --}}
                @foreach($pdfAttachments as $att)
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-file-earmark-pdf text-danger fs-4 me-2"></i>
                            <div>
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" rel="noopener"
                                    class="fw-bold text-decoration-none">
                                    {{ basename($att->file_path) }}
                                </a>
                                <small class="text-muted ms-2">({{ number_format($att->file_size / 1024, 1) }} KB)</small>
                                <a href="{{ asset('storage/' . $att->file_path) }}" download
                                    class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="bi bi-download me-1"></i>Unduh
                                </a>
                            </div>
                        </div>
                        <div class="ratio ratio-16x9 border rounded shadow-sm" style="max-height: 600px;">
                            <iframe src="{{ asset('storage/' . $att->file_path) }}" title="{{ basename($att->file_path) }}"
                                class="rounded" style="border: none;">
                            </iframe>
                        </div>
                    </div>
                @endforeach

                {{-- Display Other Attachments as Download Links --}}
                @foreach($otherAttachments as $att)
                    <div class="mb-2">
                        @if($att->file_type === 'link')
                            <a href="{{ $att->file_path }}" target="_blank" rel="noopener">
                                <i class="bi bi-link-45deg me-1"></i>
                                {{ $att->file_path }}
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" rel="noopener">
                                <i class="bi bi-file-earmark me-1"></i>
                                {{ basename($att->file_path) }}
                            </a>
                            <small class="text-muted ms-2">({{ $att->file_type }}, {{ number_format($att->file_size / 1024, 1) }}
                                KB)</small>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-muted">Belum ada lampiran</div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Jawaban</h6>
        </div>
        <div class="card-body">
            @if($topic->answers->count())
                @foreach($topic->answers as $ans)
                    <div class="mb-4 p-3 border rounded shadow-sm">
                        {{-- Answer Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px; font-size: 0.875rem;">
                                        {{ strtoupper(substr(optional($ans->user)->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <small
                                                class="text-primary fw-bold d-block">{{ optional($ans->user)->name ?? '-' }}</small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $ans->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    @if($ans->is_verified)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                        </span>
                                    @endif
                                    @if($ans->verifier)
                                        <small class="text-muted">
                                            oleh {{ $ans->verifier->name }}
                                        </small>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $ans->created_at->format('d M Y, H:i') }}
                                    @if($ans->updated_at != $ans->created_at)
                                        <span class="ms-2">
                                            <i class="bi bi-pencil me-1"></i>
                                            Diperbarui {{ $ans->updated_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </small>
                            </div>
                            <div class="text-end">
                                {{-- Vote Buttons --}}
                                <div class="d-flex flex-column align-items-end gap-2 mb-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button"
                                            class="btn btn-outline-success vote-btn {{ optional($ans->userVote)->vote == 1 ? 'active' : '' }}"
                                            data-answer-id="{{ $ans->id }}" data-vote="1" data-bs-toggle="tooltip"
                                            title="Vote Positif">
                                            <i
                                                class="bi bi-arrow-up-circle{{ optional($ans->userVote)->vote == 1 ? '-fill' : '' }}"></i>
                                        </button>
                                        <span class="btn btn-outline-secondary vote-count-{{ $ans->id }}"
                                            style="pointer-events: none;">
                                            {{ $ans->vote_count }}
                                        </span>
                                        <button type="button"
                                            class="btn btn-outline-danger vote-btn {{ optional($ans->userVote)->vote == -1 ? 'active' : '' }}"
                                            data-answer-id="{{ $ans->id }}" data-vote="-1" data-bs-toggle="tooltip"
                                            title="Vote Negatif">
                                            <i
                                                class="bi bi-arrow-down-circle{{ optional($ans->userVote)->vote == -1 ? '-fill' : '' }}"></i>
                                        </button>
                                    </div>
                                    {{-- Verify Button (Staff/Admin only) --}}
                                    @can('answers.verify')
                                        <form action="{{ route('answers.verify', $ans->id) }}" method="POST"
                                            class="verify-form d-inline" data-answer-id="{{ $ans->id }}">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $ans->is_verified ? 'btn-success' : 'btn-outline-success' }} verify-btn"
                                                data-bs-toggle="tooltip"
                                                title="{{ $ans->is_verified ? 'Hapus Verifikasi' : 'Verifikasi Jawaban' }}">
                                                <i class="bi bi-check-circle{{ $ans->is_verified ? '-fill' : '' }} me-1"></i>
                                                {{ $ans->is_verified ? 'Terverifikasi' : 'Verifikasi' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        {{-- Answer Content (Rich HTML from Quill) --}}
                        <div class="answer-content mb-3">
                            <div class="ql-snow">
                                <div class="ql-editor" style="min-height: auto; padding: 0;">
                                    {!! $ans->content !!}
                                </div>
                            </div>
                        </div>

                        {{-- Comments Section --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="bi bi-chat-left-text me-1"></i>
                                    Komentar
                                    @if($ans->comments->count() > 0)
                                        <span class="badge bg-secondary">{{ $ans->comments->count() }}</span>
                                    @endif
                                </h6>
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0"
                                    onclick="toggleCommentForm({{ $ans->id }})" id="toggleBtn{{ $ans->id }}">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Komentar
                                </button>
                            </div>

                            {{-- Existing Comments --}}
                            @if($ans->comments->count())
                                <div class="comments-list mb-3" id="commentsList{{ $ans->id }}">
                                    @foreach($ans->comments as $comment)
                                        <div class="mb-2 p-3 bg-light rounded border-start border-primary border-3 comment-item"
                                            data-comment-id="{{ $comment->id }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                            style="width: 32px; height: 32px; font-size: 0.875rem;">
                                                            {{ strtoupper(substr(optional($comment->user)->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <small
                                                                class="text-primary fw-bold d-block">{{ optional($comment->user)->name ?? '-' }}</small>
                                                            <small class="text-muted">
                                                                <i class="bi bi-clock me-1"></i>
                                                                {{ $comment->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 comment-content">{{ $comment->content }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-3 bg-light rounded" id="noComments{{ $ans->id }}">
                                    <i class="bi bi-chat-quote fs-4 d-block mb-2"></i>
                                    <small>Belum ada komentar. Jadilah yang pertama!</small>
                                </div>
                            @endif

                            {{-- Comment Form (Hidden by default) --}}
                            <div class="comment-form mt-3" id="commentForm{{ $ans->id }}" style="display: none;">
                                <form action="{{ route('answers.comments.store', $ans->id) }}" method="POST"
                                    class="comment-submit-form" data-answer-id="{{ $ans->id }}">
                                    @csrf
                                    <div class="mb-2">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px; flex-shrink: 0;">
                                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea name="content"
                                                    class="form-control @error('content.' . $ans->id) is-invalid @enderror"
                                                    rows="3" placeholder="Tulis komentar Anda di sini..." required
                                                    id="commentTextarea{{ $ans->id }}"></textarea>
                                                @error('content.' . $ans->id)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Tekan Enter untuk kirim, Shift+Enter untuk baris
                                                    baru</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="cancelCommentForm({{ $ans->id }})">
                                            <i class="bi bi-x me-1"></i>Batal
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-send me-1"></i>Kirim
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Action Buttons (if needed) --}}
                        @can('update', $ans)
                            <div class="mt-3 pt-2 border-top">
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i>Ubah
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </div>
                        @endcan
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-info-circle fs-2 mb-2 d-block"></i>
                    <div>Belum ada jawaban untuk topik ini.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Answer form (rich text) --}}
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0">Beri Jawaban</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('topics.answers.store', $topic) }}" method="POST" id="answerForm">
                @csrf
                <div class="mb-3">
                    <div id="editor">{{ old('content') }}</div>
                    <input type="hidden" name="content" id="content">
                    @error('content')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Register custom file blot for Quill
            var Link = Quill.import('formats/link');
            class FileBlot extends Link {
                static create(value) {
                    let node = super.create(value.url || value);
                    node.setAttribute('href', value.url || value);
                    node.setAttribute('target', '_blank');
                    node.setAttribute('rel', 'noopener');
                    if (value.filename) {
                        node.textContent = '📎 ' + value.filename;
                    }
                    node.classList.add('file-attachment');
                    return node;
                }
            }
            FileBlot.blotName = 'file';
            FileBlot.tagName = 'a';
            Quill.register(FileBlot);

            var toolbarOptions = {
                container: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'script': 'sub' }, { 'script': 'super' }],
                    ['clean'],
                    ['link', 'image', 'file']],
                handlers: {
                    'image': imageHandler,
                    'file': fileHandler
                }
            };

            let images = [];
            let files = [];

            var quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Tulis jawaban Anda di sini...'
            });

            // Add custom icon for file button
            var fileButton = document.querySelector('.ql-file');
            if (fileButton) {
                fileButton.innerHTML = '<svg viewBox="0 0 18 18"><path d="M9 1v12M5 9l4 4 4-4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M1 13v2a2 2 0 002 2h12a2 2 0 002-2v-2" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
                fileButton.title = 'Upload File (PDF, DOC, etc.)';
            }

            function imageHandler() {
                // console.log('imageHandler triggered by user');
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = async () => {
                    let file = input.files[0];
                    let formData = new FormData();
                    formData.append('image', file);

                    // get CSRF token from meta tag
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    let res;
                    try {
                        res = await fetch('/upload-image', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                    } catch (err) {
                        console.error('Network error uploading image', err);
                        alert('Gagal mengunggah gambar: jaringan bermasalah');
                        return;
                    }

                    // if not JSON, show helpful diagnostic
                    const ct = res.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        const text = await res.text().catch(() => '');
                        console.error('Expected JSON but server returned HTML/text:', text);
                        alert('Upload gagal: server mengembalikan halaman HTML (cek console/network). Mungkin sesi Anda kadaluwarsa atau route tidak ditemukan.');
                        return;
                    }

                    const data = await res.json();
                    if (!res.ok) {
                        console.error('Upload error response', data);
                        alert(data.error || data.message || 'Gagal mengunggah gambar');
                        return;
                    }

                    // insert url image into editor
                    let range = quill.getSelection(true) || { index: quill.getLength() };
                    quill.insertEmbed(range.index, 'image', data.url);
                    images.push(data.url)
                    quill.setSelection(range.index + 1);
                };
            }

            function fileHandler() {
                // console.log('fileHandler triggered by user');
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx');
                input.click();

                input.onchange = async () => {
                    let file = input.files[0];
                    if (!file) return;

                    let formData = new FormData();
                    formData.append('file', file);

                    // get CSRF token from meta tag
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

                    // Show loading indicator
                    let range = quill.getSelection(true) || { index: quill.getLength() };
                    quill.insertText(range.index, '⏳ Uploading ' + file.name + '...', { 'italic': true, 'color': '#999' });
                    let loadingLength = ('⏳ Uploading ' + file.name + '...').length;

                    let res;
                    try {
                        res = await fetch('/upload-file', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                    } catch (err) {
                        console.error('Network error uploading file', err);
                        quill.deleteText(range.index, loadingLength);
                        alert('Gagal mengunggah file: jaringan bermasalah');
                        return;
                    }

                    // Remove loading text
                    quill.deleteText(range.index, loadingLength);

                    // if not JSON, show helpful diagnostic
                    const ct = res.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        const text = await res.text().catch(() => '');
                        console.error('Expected JSON but server returned HTML/text:', text);
                        alert('Upload gagal: server mengembalikan halaman HTML (cek console/network). Mungkin sesi Anda kadaluwarsa atau route tidak ditemukan.');
                        return;
                    }

                    const data = await res.json();
                    if (!res.ok) {
                        console.error('Upload error response', data);
                        alert(data.error || data.message || 'Gagal mengunggah file');
                        return;
                    }

                    // Insert file link with icon
                    let fileIcon = data.extension === 'pdf' ? '📄' : '📎';
                    let linkText = fileIcon + ' ' + data.filename;

                    quill.insertText(range.index, linkText, { 'link': data.url });
                    files.push({ url: data.url, filename: data.filename });
                    quill.insertText(range.index + linkText.length, ' ');
                    quill.setSelection(range.index + linkText.length + 1);
                };
            }

            // When form is submitted, copy HTML to hidden input
            var form = document.getElementById('answerForm');
            form.onsubmit = function (ev) {
                ev.preventDefault();

                // Basic validation
                if (quill.getText().trim().length === 0) {
                    alert('Jawaban tidak boleh kosong');
                    return false;
                }

                // Extract images from Quill content
                let imagesNew = [];
                quill.getContents().ops.forEach(op => {
                    if (op.insert && op.insert.image) {
                        imagesNew.push(op.insert.image);
                    }
                });

                // Set content value
                var content = document.getElementById('content');
                content.value = quill.root.innerHTML;

                // Remove existing hidden inputs if they exist (to avoid duplicates)
                var existingNew = form.querySelector('input[name="images-new"]');
                var existingOld = form.querySelector('input[name="images-old"]');
                if (existingNew) existingNew.remove();
                if (existingOld) existingOld.remove();

                // Create and append hidden inputs for images
                var imagesNewInput = document.createElement('input');
                imagesNewInput.setAttribute('type', 'hidden');
                imagesNewInput.setAttribute('name', 'images-new');
                imagesNewInput.setAttribute('value', JSON.stringify(imagesNew));
                form.appendChild(imagesNewInput);

                var imagesOldInput = document.createElement('input');
                imagesOldInput.setAttribute('type', 'hidden');
                imagesOldInput.setAttribute('name', 'images-old');
                imagesOldInput.setAttribute('value', JSON.stringify(images));
                form.appendChild(imagesOldInput);

                // Now submit the form properly
                form.submit();
            };

            // If there are validation errors, show the editor as invalid
            @error('content')
                quill.root.classList.add('is-invalid');
                quill.root.style.borderColor = '#dc3545';
            @enderror

            // Comment form functionality
            window.toggleCommentForm = function(answerId) {
                const form = document.getElementById('commentForm' + answerId);
                const btn = document.getElementById('toggleBtn' + answerId);
                const textarea = document.getElementById('commentTextarea' + answerId);

                if (form.style.display === 'none' || form.style.display === '') {
                    form.style.display = 'block';
                    btn.style.display = 'none';
                    textarea.focus();
                } else {
                    form.style.display = 'none';
                    btn.style.display = 'block';
                }
            };

            window.cancelCommentForm = function (answerId) {
                const form = document.getElementById('commentForm' + answerId);
                const btn = document.getElementById('toggleBtn' + answerId);
                const textarea = document.getElementById('commentTextarea' + answerId);

                form.style.display = 'none';
                btn.style.display = 'block';
                textarea.value = '';
            };

            // Handle Enter key for comment textareas (Shift+Enter for new line, Enter to submit)
            document.querySelectorAll('textarea[id^="commentTextarea"]').forEach(textarea => {
                textarea.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        const form = this.closest('form');
                        if (form && this.value.trim() !== '') {
                            form.submit();
                        }
                    }
                });
            });

            // Handle comment form submission with AJAX (optional enhancement)
            document.querySelectorAll('.comment-submit-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
                });
            });

            // Vote functionality
            document.querySelectorAll('.vote-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const answerId = this.dataset.answerId;
                    const voteValue = parseInt(this.dataset.vote);
                    const btnGroup = this.closest('.btn-group');
                    const upvoteBtn = btnGroup.querySelector('[data-vote="1"]');
                    const downvoteBtn = btnGroup.querySelector('[data-vote="-1"]');
                    const countSpan = btnGroup.querySelector('.vote-count-' + answerId);

                    // Disable buttons during request
                    upvoteBtn.disabled = true;
                    downvoteBtn.disabled = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/answers/${answerId}/vote`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ vote: voteValue })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update vote count
                            countSpan.textContent = data.vote_count;

                            // Update button states
                            const userVote = data.user_vote;
                            if (userVote === 1) {
                                upvoteBtn.classList.add('active');
                                upvoteBtn.classList.remove('btn-outline-success');
                                upvoteBtn.classList.add('btn-success');
                                upvoteBtn.querySelector('i').className = 'bi bi-arrow-up-circle-fill';

                                downvoteBtn.classList.remove('active');
                                downvoteBtn.classList.remove('btn-danger');
                                downvoteBtn.classList.add('btn-outline-danger');
                                downvoteBtn.querySelector('i').className = 'bi bi-arrow-down-circle';
                            } else if (userVote === -1) {
                                downvoteBtn.classList.add('active');
                                downvoteBtn.classList.remove('btn-outline-danger');
                                downvoteBtn.classList.add('btn-danger');
                                downvoteBtn.querySelector('i').className = 'bi bi-arrow-down-circle-fill';

                                upvoteBtn.classList.remove('active');
                                upvoteBtn.classList.remove('btn-success');
                                upvoteBtn.classList.add('btn-outline-success');
                                upvoteBtn.querySelector('i').className = 'bi bi-arrow-up-circle';
                            } else {
                                // No vote
                                upvoteBtn.classList.remove('active');
                                upvoteBtn.classList.remove('btn-success');
                                upvoteBtn.classList.add('btn-outline-success');
                                upvoteBtn.querySelector('i').className = 'bi bi-arrow-up-circle';

                                downvoteBtn.classList.remove('active');
                                downvoteBtn.classList.remove('btn-danger');
                                downvoteBtn.classList.add('btn-outline-danger');
                                downvoteBtn.querySelector('i').className = 'bi bi-arrow-down-circle';
                            }
                        } else {
                            alert(data.message || 'Gagal melakukan vote');
                        }
                    } catch (error) {
                        console.error('Vote error:', error);
                        alert('Terjadi kesalahan saat melakukan vote');
                    } finally {
                        upvoteBtn.disabled = false;
                        downvoteBtn.disabled = false;
                    }
                });
            });

            // Verify functionality
            document.querySelectorAll('.verify-form').forEach(form => {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const answerId = this.dataset.answerId;
                    const verifyBtn = this.querySelector('.verify-btn');
                    const originalHtml = verifyBtn.innerHTML;

                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const formData = new FormData(this);

                        const response = await fetch(`/answers/${answerId}/verify`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update button state
                            if (data.is_verified) {
                                verifyBtn.classList.remove('btn-outline-success');
                                verifyBtn.classList.add('btn-success');
                                verifyBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Verified';
                                verifyBtn.title = 'Hapus Verifikasi';

                                // Add verified badge to header if not exists
                                const answerCard = this.closest('.mb-4');
                                const header = answerCard.querySelector('.d-flex.align-items-center.gap-2');
                                if (header && !header.querySelector('.badge.bg-success')) {
                                    const badge = document.createElement('span');
                                    badge.className = 'badge bg-success';
                                    badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Terverifikasi';
                                    header.appendChild(badge);
                                }
                            } else {
                                verifyBtn.classList.remove('btn-success');
                                verifyBtn.classList.add('btn-outline-success');
                                verifyBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Verify';
                                verifyBtn.title = 'Verifikasi Jawaban';

                                // Remove verified badge from header
                                const answerCard = this.closest('.mb-4');
                                if (answerCard) {
                                    const header = answerCard.querySelector('.d-flex.align-items-center.gap-2');
                                    if (header) {
                                        const verifiedBadge = header.querySelector('.badge.bg-success');
                                        if (verifiedBadge && verifiedBadge.textContent.includes('Terverifikasi')) {
                                            verifiedBadge.remove();
                                        }
                                    }
                                }
                            }

                            // Reload page to update sorting
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            alert(data.message || 'Gagal memverifikasi jawaban');
                            verifyBtn.innerHTML = originalHtml;
                        }
                    } catch (error) {
                        console.error('Verify error:', error);
                        alert('Terjadi kesalahan saat memverifikasi jawaban');
                        verifyBtn.innerHTML = originalHtml;
                    } finally {
                        verifyBtn.disabled = false;
                    }
                });
            });
        });
    </script>
@endpush
@endsection