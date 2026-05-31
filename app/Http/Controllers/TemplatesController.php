<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateVersion;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    public function __construct(private TemplateService $service)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', DocumentTemplate::class);
        
        $query = DocumentTemplate::with('latestVersion');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by version number
        if ($request->filled('version_min')) {
            $query->where('latest_version_number', '>=', (int) $request->version_min);
        }
        if ($request->filled('version_max')) {
            $query->where('latest_version_number', '<=', (int) $request->version_max);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['name', 'slug', 'latest_version_number', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }

        $templates = $query->paginate(15)->withQueryString();
        
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        $this->authorize('create', DocumentTemplate::class);
        return view('templates.create');
    }

    public function show(DocumentTemplate $template){  
        $versions = $template->versions; 
        return view('templates.show', compact('template','versions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', DocumentTemplate::class);

        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ]);

        $this->service->createTemplate($validated['name'], $validated['description'] ?? null, $validated['file'], auth()->id());

        return redirect()->route('templates.index')->with('success', 'Template dibuat.');
    }

    public function uploadVersion(Request $request, DocumentTemplate $template)
    {
        $this->authorize('uploadVersion', $template);

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // maksimal 10240KB = 10MB
                'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ],
        ]);

        try {
            $this->service->storeNewVersion($template, $validated['file'], auth()->id());
            return back()->with('success', 'Versi baru ditambahkan.');
        } catch (\Throwable $e) {
            \Log::error('Gagal upload versi baru template', [
                'template_id' => $template->id ?? null,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Gagal upload versi baru. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function download(DocumentTemplate $template, DocumentTemplateVersion $version)
    {
        $this->authorize('download', $template);
        abort_unless($version->template_id === $template->id, 404);

        $this->service->logDownload($template, $version, auth()->id(), request()->ip());
        $absolute = $this->service->getDownloadAbsolutePath($version);
        return response()->download($absolute, $version->original_filename);
    }

    public function destroy(DocumentTemplate $template)
    {
        $this->authorize('delete', $template);

        $this->service->delete($template);

        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus.');
    }
}


