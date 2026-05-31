<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentTemplateResource;
use App\Http\Resources\DocumentTemplateVersionResource;
use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateVersion;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct(private TemplateService $service)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', DocumentTemplate::class);
        $list = DocumentTemplate::with('latestVersion')->orderBy('name')->paginate(15);
        return DocumentTemplateResource::collection($list);
    }

    public function show(DocumentTemplate $template)
    {
        $this->authorize('view', $template);
        $template->load(['latestVersion', 'versions']);
        return new DocumentTemplateResource($template);
    }

    public function store(Request $request)
    {
        $this->authorize('create', DocumentTemplate::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ]);
        $template = $this->service->createTemplate($validated['name'], $validated['description'] ?? null, $validated['file'], auth()->id());
        return (new DocumentTemplateResource($template->load('latestVersion')))->response()->setStatusCode(201);
    }

    public function uploadVersion(Request $request, DocumentTemplate $template)
    {
        $this->authorize('uploadVersion', $template);
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ]);
        $version = $this->service->storeNewVersion($template, $validated['file'], auth()->id());
        return new DocumentTemplateVersionResource($version);
    }
}


