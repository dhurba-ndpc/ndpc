<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function PHPSTORM_META\type;

abstract class BasePostController extends Controller
{
  protected string $postType;
  protected string $viewPath;
  protected string $routePrefix;
  protected array $locales = ['en', 'ne'];
  protected ?string $requestClass = null;
  protected string $uploadDisk = 'public';
  protected string $uploadPath = 'uploads';

  public function index()
  {

    $posts = Post::ofType($this->postType)
      ->with(['translations'])
      ->orderBy('position', 'Desc')
      ->latest()
      ->paginate(10);
    return view("{$this->viewPath}.index", compact('posts'));
  }

  public function create()
  {
    return view("{$this->viewPath}.form");
  }

  public function edit(Post $post)
  {

    $post = Post::ofType($this->postType)
      ->with(['translations', 'categories'])
      ->findOrFail($post->id);



    return view("{$this->viewPath}.form", compact('post'));
  }

  public function store(Request $request)
  {
    $validated = $this->resolveValidation($request);

    DB::beginTransaction();
    try {
      $imagePath = $this->handleFileUpload($request, 'image');
      $filePath = $this->handleFileUpload($request, 'file');

      $post = Post::create([
        'user_id' => auth()->id(),
        'type' => $this->postType,
        'slug' => isset($validated['slug']) ? Str::slug($validated['slug']) : null,
        'image' => $imagePath,
        'file' => $filePath,
        'position' => $validated['position'] ?? 0,
        'is_active' => $request->boolean('is_active', true),
        'meta_json' => $this->extractMetaJson($validated),
      ]);


      $this->saveTranslations($post, $validated);

      if (!empty($validated['category_ids'])) {
        $post->categories()->sync($validated['category_ids']);
      }

      DB::commit();
      return redirect()
        ->route("{$this->routePrefix}.index")
        ->with('success', ucfirst($this->postType) . ' created successfully.');
    } catch (\Throwable $e) {
      DB::rollBack();
      dd($e->getMessage());
      return back()->withInput()->with('error', 'Error creating record: ' . $e->getMessage());
    }
  }



  public function update(Request $request, Post $post)
  {
    $post = Post::ofType($this->postType)->findOrFail($post->id);
    $validated = $this->resolveValidation($request, $post->id);



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
    } catch (\Throwable $e) {
      dd($e->getMessage());
      DB::rollBack();
      return back()->withInput()->with('error', 'Error updating record: ' . $e->getMessage());
    }
  }

  /**
   * Soft delete record
   */
  public function destroy(Post $post)
  {
    $post = Post::ofType($this->postType)->findOrFail($post->id);
    $post->delete();

    return redirect()->route("{$this->routePrefix}.index")
      ->with('success', ucfirst($this->postType) . ' deleted successfully.');
  }

  protected function resolveValidation(Request $request, $id = null): array
  {
    if ($this->requestClass && class_exists($this->requestClass)) {
      return app($this->requestClass)->validated();
    }
    return $request->all();
  }


  protected function extractMetaJson(array $validated, ?array $existingMeta = null): ?array
  {
    return $validated['meta_json'] ?? $existingMeta;
  }



  protected function saveTranslations(Post $post, array $validated): void
  {
    $locales = ['en', 'ne'];

    foreach ($locales as $locale) {
      $title = $validated['translations'][$locale]['title'] ?? $validated["title_{$locale}"] ?? null;
      $badge = $validated['translations'][$locale]['badge_title'] ?? $validated["badge_title_{$locale}"] ?? null;
      $short = $validated['translations'][$locale]['short_description'] ?? $validated["short_description_{$locale}"] ?? null;
      $desc  = $validated['translations'][$locale]['description'] ?? $validated["description_{$locale}"] ?? null;

      if ($title || $desc || $short || $badge) {
        PostTranslation::where('post_id', $post->id)
          ->where('locale', $locale)
          ->updateOrInsert(
            [
              'post_id' => $post->id,
              'locale'  => $locale,
            ],
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
   * File upload handler
   */
  protected function handleFileUpload(Request $request, string $key): ?string
  {
    if ($request->hasFile($key)) {
      return $request->file($key)->store("{$this->uploadPath}/{$this->postType}", $this->uploadDisk);
    }
    return null;
  }


  protected function deleteFile(?string $path): void
  {
    if ($path && Storage::disk($this->uploadDisk)->exists($path)) {
      Storage::disk($this->uploadDisk)->delete($path);
    }
  }
}
