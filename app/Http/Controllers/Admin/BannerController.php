<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('created_at')->paginate(20);

        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'image_path' => $path,
            'link_url' => $request->link_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', "Banner \"{$request->title}\" berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'title' => $request->title,
            'link_url' => $request->link_url,
            'sort_order' => $request->sort_order ?? $banner->sort_order,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return back()->with('success', "Banner \"{$banner->title}\" berhasil diperbarui.");
    }

    public function toggleActive($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        $label = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Banner \"{$banner->title}\" berhasil {$label}.");
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();
        return back()->with('success', "Banner \"{$banner->title}\" berhasil dihapus.");
    }
}
