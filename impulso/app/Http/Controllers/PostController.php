<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Business $business)
    {
        $this->ownerOnly($business);
        return view('posts.index', ['business' => $business, 'posts' => $business->posts()->latest()->paginate(10), 'socialLinks' => $business->socialLinks()->get()]);
    }

    public function create(Business $business)
    {
        $this->ownerOnly($business);

        return view('posts.create', ['business' => $business, 'socialLinks' => $business->socialLinks()->get()]);
    }

    public function store(Request $request, Business $business)
    {
        $this->ownerOnly($business);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'body' => ['required', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:4'],
            'photos.*' => ['image', 'max:5120'],
            'type' => ['required', 'in:product,offer,news'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_published' => ['nullable', 'boolean'],
            'share_on_social' => ['nullable', 'boolean'],
        ]);
        if ($request->hasFile('photos')) {
            $data['photos'] = collect($request->file('photos'))->map(fn ($photo) => $photo->store('posts', 'public'))->all();
            $data['photo'] = $data['photos'][0];
        }
        $business->posts()->create($data + ['created_by' => Auth::id(), 'is_published' => $request->boolean('is_published'), 'share_on_social' => $request->boolean('share_on_social')]);
        return back()->with('success', 'Publicación creada.');
    }

    public function update(Request $request, Business $business, Post $post)
    {
        $this->ownerOnly($business);
        abort_unless($post->business_id === $business->id, 404);
        $data = $request->validate(['title' => ['required', 'string', 'max:140'], 'body' => ['required', 'string', 'max:2000'], 'photos' => ['nullable', 'array', 'max:4'], 'photos.*' => ['image', 'max:5120'], 'type' => ['required', 'in:product,offer,news'], 'price' => ['required', 'numeric', 'min:0'], 'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'], 'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'], 'is_published' => ['nullable', 'boolean'], 'share_on_social' => ['nullable', 'boolean']]);
        if ($request->hasFile('photos')) {
            $data['photos'] = collect($request->file('photos'))->map(fn ($photo) => $photo->store('posts', 'public'))->all();
            $data['photo'] = $data['photos'][0];
        }
        $post->update($data + ['is_published' => $request->boolean('is_published'), 'share_on_social' => $request->boolean('share_on_social')]);
        return back()->with('success', 'Publicación actualizada.');
    }

    public function destroy(Business $business, Post $post)
    {
        $this->ownerOnly($business);
        abort_unless($post->business_id === $business->id, 404);
        $post->delete();
        return back()->with('success', 'Publicación eliminada.');
    }

    public function social(Request $request, Business $business)
    {
        $this->ownerOnly($business);
        $data = $request->validate(['platform' => ['required', 'in:instagram,facebook,whatsapp,tiktok'], 'url' => ['required', 'url', 'max:255']]);
        $business->socialLinks()->updateOrCreate(['platform' => $data['platform']], $data);

        return back()->with('success', 'Red social guardada.');
    }

    private function ownerOnly(Business $business): void
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
    }
}