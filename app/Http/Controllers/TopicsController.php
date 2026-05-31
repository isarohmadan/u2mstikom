<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topics;
use App\Models\User;
use App\Models\Category;
use App\Models\Announcement;
use App\Models\TopicAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Helpers\FileUploadHelper;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Topics::with(['user', 'approver', 'editor', 'category'])
            ->latest();

        // For non-admin users, only show approved topics or their own topics
        if (!auth()->user()->can('topics.approve')) {
            $query->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhere('user_id', auth()->id());
            });
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && in_array($request->status, ['submitted', 'approved', 'rejected', 'archived'])) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Tag filter
        if ($request->has('tag') && !empty($request->tag)) {
            $tag = $request->tag;
            $query->whereJsonContains('tags', $tag);
        }

        // Sorting
        switch ($request->sort) {
            case 'terlama':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('title', 'asc');
                break;
            case 'z-a':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $query = $query->paginate(10);

        // Get stats for dashboard
        $stats = [
            'total' => Topics::count(),
            'approved' => Topics::where('status', 'approved')->count(),
            'submitted' => Topics::where('status', 'submitted')->count(),
            'rejected' => Topics::where('status', 'rejected')->count(),
            'archived' => Topics::where('status', 'archived')->count(),
        ];

        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        // Get all unique tags for filter dropdown
        $allTags = Topics::whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('topics.index', compact('query', 'stats', 'categories', 'allTags'));
    }

    /**
     * Display user's own topics
     */
    public function my(Request $request)
    {
        $query = Topics::with(['user', 'approver', 'editor', 'category'])
            ->where('user_id', auth()->id());

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && in_array($request->status, ['submitted', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Sorting
        switch ($request->get('sort', 'terbaru')) {
            case 'terlama':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('title', 'asc');
                break;
            case 'z-a':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $query = $query->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        return view('topics.my', compact('query', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $topic = new Topics();
        $categories = Category::orderBy('name')->get();
        return view('topics.create', compact('topic', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:topics,slug'],
            'content' => ['required', 'string'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'string'],
            'attachments' => ['nullable'],
            'attachments.*' => ['file', 'mimetypes:image/jpeg,image/png,image/gif,application/pdf', 'max:5120'],
            'attachment_links' => ['nullable', 'array'],
            'attachment_links.*' => ['nullable', 'url', 'regex:/^https?:\/\//i'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Validasi gagal. Periksa input Anda.');
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $slug = $validated['slug'] ?? Str::slug($validated['title']);
            if (empty($slug)) {
                $slug = Str::random(8);
            }
            $baseSlug = $slug;
            $counter = 1;
            while (Topics::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $tagsArray = [];
            if (!empty($validated['tags'])) {
                $tagsArray = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
            }

            $topic = Topics::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'slug' => $slug,
                'content' => $validated['content'],
                'status' => 'submitted',
                'category_id' => !empty($validated['category_ids']) ? $validated['category_ids'][0] : null, // Keep first category for backward compatibility
                'tags' => $tagsArray,
                'is_locked' => false,
                'is_edited' => false,
                'view_count' => 0,
            ]);

            // Sync multiple categories
            if (!empty($validated['category_ids'])) {
                $topic->categories()->sync($validated['category_ids']);
            }

            if ($request->hasFile('attachments')) {
                foreach ((array) $request->file('attachments') as $file) {
                    if (!$file || !$file->isValid())
                        continue;
                    $path = FileUploadHelper::upload($file, "topics/{$topic->id}/attachments");
                    TopicAttachment::create([
                        'topic_id' => $topic->id,
                        'uploaded_by' => auth()->id(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            $links = (array) ($validated['attachment_links'] ?? []);
            foreach ($links as $link) {
                if (!empty($link)) {
                    TopicAttachment::create([
                        'topic_id' => $topic->id,
                        'uploaded_by' => auth()->id(),
                        'file_path' => $link,
                        'file_type' => 'link',
                        'file_size' => 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('topics.index')->with('success', 'Topik berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat topik: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $topic = Topics::with([
            'user',
            'approver',
            'category',
            'categories', // Load many-to-many categories relationship
            'attachments.uploader',
            'answers' => function ($query) {
                $query->withCount('comments')
                    ->with(['user.roles', 'verifier', 'comments.user', 'userVote'])
                    ->orderByRaw('is_verified DESC, vote_count DESC, comments_count DESC');
            },
        ])->findOrFail($id);

        // Increment view count
        $topic->increment('view_count');

        // Separate attachments into images, PDFs, and others
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
        $pdfExtensions = ['pdf'];

        $imageAttachments = $topic->attachments->filter(function ($att) use ($imageExtensions) {
            if ($att->file_type === 'link')
                return false;
            $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
            return in_array($ext, $imageExtensions);
        });

        $pdfAttachments = $topic->attachments->filter(function ($att) use ($pdfExtensions) {
            if ($att->file_type === 'link')
                return false;
            $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
            return in_array($ext, $pdfExtensions);
        });

        $otherAttachments = $topic->attachments->filter(function ($att) use ($imageExtensions, $pdfExtensions) {
            if ($att->file_type === 'link')
                return true;
            $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
            return !in_array($ext, $imageExtensions) && !in_array($ext, $pdfExtensions);
        });

        return view('topics.show', compact('topic', 'imageAttachments', 'pdfAttachments', 'otherAttachments'));
    }

    /**
     * Approve a topic (requires topics.approve permission)
     */
    public function approve($id)
    {
        if (!auth()->user()->can('topics.approve')) {
            abort(403);
        }

        $topic = Topics::findOrFail($id);

        if ($topic->status === 'approved') {
            return back()->with('info', 'Topik sudah disetujui sebelumnya.');
        }

        try {
            DB::beginTransaction();
            $topic->status = 'approved';
            $topic->approved_by = auth()->id();
            $topic->save();
            DB::commit();
            return back()->with('success', 'Topik berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui topik: ' . $e->getMessage());
        }
    }

    /**
     * Reject a topic (requires topics.approve permission)
     */
    public function reject($id)
    {
        if (!auth()->user()->can('topics.approve')) {
            abort(403);
        }

        $topic = Topics::findOrFail($id);

        if ($topic->status === 'rejected') {
            return back()->with('info', 'Topik sudah ditolak sebelumnya.');
        }

        try {
            DB::beginTransaction();
            $topic->status = 'rejected';
            $topic->approved_by = auth()->id();
            $topic->save();
            DB::commit();
            return back()->with('success', 'Topik berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak topik: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $topic = Topics::with('categories')->findOrFail($id);

        // Owner with topics.my.edit OR users with topics.edit (for all) can edit
        $isOwner = $topic->user_id === auth()->id();
        $canEditOwn = $isOwner && auth()->user()->can('topics.my.edit');
        $canEditAll = auth()->user()->can('topics.edit');

        if (!$canEditOwn && !$canEditAll) {
            abort(403);
        }

        $categories = Category::orderBy('name')->get();
        $file_attachments = $topic->attachments->where('file_type', '!=', 'link');
        $link_attachments = $topic->attachments->where('file_type', 'link');

        return view('topics.edit', compact('topic', 'categories', 'file_attachments', 'link_attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $topic = Topics::findOrFail($id);

        // Owner with topics.my.edit OR users with topics.edit (for all) can update
        $isOwner = $topic->user_id === auth()->id();
        $canEditOwn = $isOwner && auth()->user()->can('topics.my.edit');
        $canEditAll = auth()->user()->can('topics.edit');

        if (!$canEditOwn && !$canEditAll) {
            abort(403);
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('topics', 'slug')->ignore($topic->id)],
            'content' => ['required', 'string'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'string'],
            'attachments' => ['nullable'],
            'attachments.*' => ['file', 'mimetypes:image/jpeg,image/png,image/gif,application/pdf', 'max:5120'],
            'attachment_links' => ['nullable', 'array'],
            'attachment_links.*' => ['nullable', 'url', 'regex:/^https?:\/\//i'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Validasi gagal. Periksa input Anda.');
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $slug = $validated['slug'] ?? Str::slug($validated['title']);
            if (empty($slug)) {
                $slug = Str::random(8);
            }
            $baseSlug = $slug;
            $counter = 1;
            while (Topics::where('slug', $slug)->where('id', '!=', $topic->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $tagsArray = [];
            if (!empty($validated['tags'])) {
                $tagsArray = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
            }

            // Process removals
            $removeIds = (array) $request->input('remove_attachments', []);
            if (!empty($removeIds)) {
                $attachmentsToRemove = TopicAttachment::whereIn('id', $removeIds)->where('topic_id', $topic->id)->get();
                foreach ($attachmentsToRemove as $att) {
                    if (($att->file_type ?? '') !== 'link' && !empty($att->file_path)) {
                        try {
                            FileUploadHelper::delete($att->file_path);
                        } catch (\Exception $e) {
                        }
                    }
                    $att->delete();
                }
            }

            // Update topic
            $topic->update([
                'title' => $validated['title'],
                'slug' => $slug,
                'content' => $validated['content'],
                'category_id' => !empty($validated['category_ids']) ? $validated['category_ids'][0] : $topic->category_id, // Keep first category for backward compatibility
                'tags' => $tagsArray,
                'is_edited' => true,
                'edited_by' => auth()->id(),
            ]);

            // Sync multiple categories
            if (isset($validated['category_ids'])) {
                $topic->categories()->sync($validated['category_ids']);
            } else {
                $topic->categories()->sync([]);
            }

            // Store new files
            if ($request->hasFile('attachments')) {
                foreach ((array) $request->file('attachments') as $file) {
                    if (!$file || !$file->isValid())
                        continue;
                    $path = FileUploadHelper::upload($file, "topics/{$topic->id}/attachments");
                    TopicAttachment::create([
                        'topic_id' => $topic->id,
                        'uploaded_by' => auth()->id(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Store new link attachments
            $newLinks = array_filter((array) ($validated['attachment_links'] ?? []));
            foreach ($newLinks as $link) {
                if (!empty($link)) {
                    TopicAttachment::create([
                        'topic_id' => $topic->id,
                        'uploaded_by' => auth()->id(),
                        'file_path' => $link,
                        'file_type' => 'link',
                        'file_size' => 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('topics.show', $topic)->with('success', 'Topik berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui topik: ' . $e->getMessage());
        }
    }

    /**
     * Toggle bookmark for a topic
     */
    /**
     * Toggle bookmark for a topic
     */
    public function toggleBookmark($id)
    {
        $topic = Topics::findOrFail($id);

        $isBookmarked = auth()->user()->toggleBookmark($id); // Uses the JSON method we added to User model

        $message = $isBookmarked ? 'Topik berhasil disimpan ke favorit.' : 'Topik dihapus dari favorit.';

        return back()->with('success', $message);
    }

    /**
     * Display user's bookmarked topics (Favorites)
     */
    public function favorites(Request $request)
    {
        $topics = auth()->user()->getBookmarkedTopics();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower($request->search);
            $topics = $topics->filter(function ($topic) use ($search) {
                return str_contains(strtolower($topic->title ?? ''), $search) ||
                       str_contains(strtolower(strip_tags($topic->content ?? '')), $search);
            })->values();
        }

        // Status filter
        if ($request->has('status') && in_array($request->status, ['submitted', 'approved', 'rejected'])) {
            $topics = $topics->filter(function ($topic) use ($request) {
                return $topic->status === $request->status;
            })->values();
        }

        // Category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $topics = $topics->filter(function ($topic) use ($request) {
                return $topic->category_id == $request->category_id;
            })->values();
        }

        // Sorting
        $sort = $request->get('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $topics = $topics->sortBy('created_at')->values();
                break;
            case 'a-z':
                $topics = $topics->sortBy('title')->values();
                break;
            case 'z-a':
                $topics = $topics->sortByDesc('title')->values();
                break;
            default:
                $topics = $topics->sortByDesc('created_at')->values();
                break;
        }

        // Paginate manually since it returns a collection
        $page = $request->get('page', 1);
        $perPage = 10;
        $sliced = $topics->slice(($page - 1) * $perPage, $perPage)->values();

        $query = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $topics->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        return view('topics.favorites', compact('query', 'categories'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $topic = Topics::with('attachments')->findOrFail($id);

        // Owner with topics.my.delete OR users with topics.delete (for all) can delete
        $isOwner = $topic->user_id === auth()->id();
        $canDeleteOwn = $isOwner && auth()->user()->can('topics.my.delete');
        $canDeleteAll = auth()->user()->can('topics.delete');

        if (!$canDeleteOwn && !$canDeleteAll) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            foreach ($topic->attachments as $att) {
                if (($att->file_type ?? '') !== 'link' && !empty($att->file_path)) {
                    try {
                        FileUploadHelper::delete($att->file_path);
                    } catch (\Exception $e) {
                    }
                }
                try {
                    $att->delete();
                } catch (\Exception $e) {
                }
            }

            $topic->delete();

            DB::commit();
            return redirect()->route('topics.index')->with('success', 'Topik berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus topik: ' . $e->getMessage());
        }
    }
}
