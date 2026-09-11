<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class BaseCrudController extends Controller
{
    /**
     * Post discriminator type (e.g. 'banner', 'blog', 'service')
     */
    protected string $postType;

    /**
     * Fully-qualified FormRequest class name for Store/Update validation
     */
    protected ?string $requestClass = null;

    /**
     * Storage folder for image/file uploads
     */
    protected string $uploadDisk = 'public';
    protected string $uploadPath = 'uploads';

    /**
     * View path prefix (e.g. 'admin.banners')
     */
    protected string $viewPath;

    /**
     * Redirect route prefix (e.g. 'admin.banners.index')
     */
    protected string $routePrefix;

    /**
     * List all records of this post type
     */
    public function index(Request $request)
    {
        $posts = Post::ofType($this->postType)
            ->with(['translations'])
            ->orderBy('position', 'asc')
            ->latest()
            ->paginate(15);

        return view("{$this->viewPath}.index", compact('posts'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view("{$this->viewPath}.create");
    }

    /**
     * Store record using the validated FormRequest
     */
    public function store(Request $request)
    {
        $validated = $this->resolveValidation($request);

        DB::beginTransaction();
        try {
            // Handle file/image uploads
            $imagePath = $this->handleFileUpload($request, 'image');
            $filePath  = $this->handleFileUpload($request, 'file');

            // 1. Create base post
            $post = Post::create([
                'user_id'    => auth()->id(),
                'type'       => $this->postType,
                'slug'       => isset($validated['slug']) ? Str::slug($validated['slug']) : null,
                'image'      => $imagePath,
                'file'       => $filePath,
                'position'   => $validated['position'] ?? 0,
                'is_active'  => $request->boolean('is_active', true),
                'meta_json'  => $this->extractMetaJson($validated),
            ]);

            // 2. Save multilingual translations (EN, NE)
            $this->saveTranslations($post, $validated);

            // 3. Attach categories if provided
            if (!empty($validated['category_ids'])) {
                $post->categories()->sync($validated['category_ids']);
            }

            DB::commit();
            return redirect()->route("{$this->routePrefix}.index")
                ->with('success', ucfirst($this->postType) . ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating record: ' . $th->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $post = Post::ofType($this->postType)
            ->with(['translations', 'categories'])
            ->findOrFail($id);

        return view("{$this->viewPath}.edit", compact('post'));
    }

    /**
     * Update existing record
     */
    public function update(Request $request, $id)
    {
        $post = Post::ofType($this->postType)->findOrFail($id);
        $validated = $this->resolveValidation($request, $id);

        DB::beginTransaction();
        try {
            // Check for replacement uploads
            if ($request->hasFile('image')) {
                $this->deleteFile($post->image);
                $post->image = $this->handleFileUpload($request, 'image');
            }

            if ($request->hasFile('file')) {
                $this->deleteFile($post->file);
                $post->file = $this->handleFileUpload($request, 'file');
            }

            // Update base post fields
            $post->update([
                'slug'      => isset($validated['slug']) ? Str::slug($validated['slug']) : $post->slug,
                'position'  => $validated['position'] ?? $post->position,
                'is_active' => $request->boolean('is_active', true),
                'meta_json' => $this->extractMetaJson($validated, $post->meta_json),
            ]);

            // Sync translations
            $this->saveTranslations($post, $validated);

            // Sync categories if present
            if (isset($validated['category_ids'])) {
                $post->categories()->sync($validated['category_ids']);
            }

            DB::commit();
            return redirect()->route("{$this->routePrefix}.index")
                ->with('success', ucfirst($this->postType) . ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating record: ' . $th->getMessage());
        }
    }

    /**
     * Soft delete record
     */
    public function destroy($id)
    {
        $post = Post::ofType($this->postType)->findOrFail($id);
        $post->delete();

        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', ucfirst($this->postType) . ' deleted successfully.');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Resolve FormRequest validation if provided; fallback to standard $request->all()
     */
    protected function resolveValidation(Request $request, $id = null): array
    {
        if ($this->requestClass && class_exists($this->requestClass)) {
            return app($this->requestClass)->validated();
        }
        return $request->all();
    }

    /**
     * Save/Update rows in `post_translations` for multiple locales
     */
    protected function saveTranslations(Post $post, array $validated): void
    {
        $locales = ['en', 'ne'];

        foreach ($locales as $locale) {
            // Checks for locale keys (e.g. translations.en.title or title_en)
            $title = $validated['translations'][$locale]['title'] ?? $validated["title_{$locale}"] ?? null;
            $badge = $validated['translations'][$locale]['badge_title'] ?? $validated["badge_title_{$locale}"] ?? null;
            $short = $validated['translations'][$locale]['short_description'] ?? $validated["short_description_{$locale}"] ?? null;
            $desc  = $validated['translations'][$locale]['description'] ?? $validated["description_{$locale}"] ?? null;

            if ($title || $desc || $short || $badge) {
                $post->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title'             => $title,
                        'badge_title'       => $badge,
                        'short_description' => $short,
                        'description'       => $desc,
                    ]
                );
            }
        }
    }

    /**
     * Override in child controller to package custom dynamic attributes into `meta_json`
     */
    protected function extractMetaJson(array $validated, ?array $existingMeta = null): ?array
    {
        return $validated['meta_json'] ?? $existingMeta;
    }

    /**
     * File upload handler
     */
    protected function handleFileUpload(Request $request, string $key): ?string
    {
        if ($request->hasFile($key)) {
            return $request->file($key)->store("{$this->uploadPath}/{$this->postType}", $this->uploadDisk);
        }
        return null;
    }

    /**
     * Storage file remover
     */
    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk($this->uploadDisk)->exists($path)) {
            Storage::disk($this->uploadDisk)->delete($path);
        }
    }
}