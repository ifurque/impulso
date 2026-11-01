<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Publicaciones (ofertas) del emprendimiento y enlaces a sus redes sociales. */
class PostController extends Controller
{
    /** Listado paginado de publicaciones y redes sociales cargadas. */
    public function index(Business $business)
    {
        $this->ownerOnly($business);
        return view('posts.index', ['business' => $business, 'posts' => $business->posts()->latest()->paginate(10), 'socialLinks' => $business->socialLinks()->get()]);
    }

    /** Formulario de nueva publicación, con los productos activos para vincular. */
    public function create(Business $business)
    {
        $this->ownerOnly($business);

        return view('posts.create', ['business' => $business, 'socialLinks' => $business->socialLinks()->get(), 'products' => $business->products()->where('is_active', true)->orderBy('name')->get()]);
    }

    /** Crea una oferta con hasta 4 fotos y los productos (con cantidad) que incluye. */
    public function store(Request $request, Business $business)
    {
        $this->ownerOnly($business);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'body' => ['required', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:4'],
            'photos.*' => ['image', 'max:5120'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_published' => ['nullable', 'boolean'],
            'share_on_social' => ['nullable', 'boolean'],
            'selected_products' => ['nullable', 'array'],
            'selected_products.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'selected_products.*.quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);
        $data['type'] = 'offer';

        $selectedProducts = $this->normalizeSelectedProducts($request, $business);

        if ($request->hasFile('photos')) {
            $data['photos'] = collect($request->file('photos'))->map(fn ($photo) => $photo->store('posts', 'public'))->all();
            $data['photo'] = $data['photos'][0];
        }

        $post = DB::transaction(function () use ($business, $data, $request, $selectedProducts) {
            $post = $business->posts()->create($data + ['created_by' => Auth::id(), 'is_published' => $request->boolean('is_published'), 'share_on_social' => $request->boolean('share_on_social')]);

            foreach ($selectedProducts as $productId => $quantity) {
                $product = $business->products()->whereKey($productId)->firstOrFail();
                if ((float) $product->quantity < $quantity && $product->quantity !== null) {
                    throw new \RuntimeException('No hay stock suficiente para '. $product->name .'.');
                }

                $post->productItems()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $post;
        });

        return back()->with('success', 'Publicación creada.');
    }

    /** Edita una publicación: si se suben fotos nuevas reemplaza y borra las anteriores, y rearma sus productos. */
    public function update(Request $request, Business $business, Post $post)
    {
        $this->ownerOnly($business);
        abort_unless($post->business_id === $business->id, 404);
        $data = $request->validate(['title' => ['required', 'string', 'max:140'], 'body' => ['required', 'string', 'max:2000'], 'photos' => ['nullable', 'array', 'max:4'], 'photos.*' => ['image', 'max:5120'], 'price' => ['required', 'numeric', 'min:0'], 'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'], 'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'], 'is_published' => ['nullable', 'boolean'], 'share_on_social' => ['nullable', 'boolean'], 'selected_products' => ['nullable', 'array'], 'selected_products.*.product_id' => ['nullable', 'integer', 'exists:products,id'], 'selected_products.*.quantity' => ['nullable', 'integer', 'min:1', 'max:999']]);
        $data['type'] = 'offer';
        $oldPhotos = [];
        if ($request->hasFile('photos')) {
            $oldPhotos = array_merge($post->photos ?? [], [$post->photo]);
            $data['photos'] = collect($request->file('photos'))->map(fn ($photo) => $photo->store('posts', 'public'))->all();
            $data['photo'] = $data['photos'][0];
        }
        $post->update($data + ['is_published' => $request->boolean('is_published'), 'share_on_social' => $request->boolean('share_on_social')]);
        $selectedProducts = $this->normalizeSelectedProducts($request, $business);
        $post->productItems()->delete();
        foreach ($selectedProducts as $productId => $quantity) {
            $post->productItems()->create(['product_id' => $productId, 'quantity' => $quantity]);
        }
        Storage::disk('public')->delete(array_filter($oldPhotos));
        return back()->with('success', 'Publicación actualizada.');
    }

    /** Elimina la publicación y sus fotos. */
    public function destroy(Business $business, Post $post)
    {
        $this->ownerOnly($business);
        abort_unless($post->business_id === $business->id, 404);
        Storage::disk('public')->delete(array_filter(array_merge($post->photos ?? [], [$post->photo])));
        $post->delete();
        return back()->with('success', 'Publicación eliminada.');
    }

    /** Guarda o actualiza el enlace a una red social del emprendimiento (uno por plataforma). */
    public function social(Request $request, Business $business)
    {
        $this->ownerOnly($business);
        $data = $request->validate(['platform' => ['required', 'in:instagram,facebook,whatsapp,tiktok'], 'url' => ['required', 'url', 'max:255']]);
        $business->socialLinks()->updateOrCreate(['platform' => $data['platform']], $data);

        return back()->with('success', 'Red social guardada.');
    }

    /** Devuelve [id de producto => cantidad] descartando entradas inválidas o de productos ajenos al emprendimiento. */
    private function normalizeSelectedProducts(Request $request, Business $business): array
    {
        $selectedProducts = [];
        $rawProducts = $request->input('selected_products', []);

        foreach ($rawProducts as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $productId = (int) ($entry['product_id'] ?? 0);
            $quantity = (int) ($entry['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $product = $business->products()->whereKey($productId)->first();
            if (!$product) {
                continue;
            }

            $selectedProducts[$productId] = min($quantity, 999);
        }

        return $selectedProducts;
    }

    /** Corta con 403 si el usuario no puede gestionar el emprendimiento. */
    private function ownerOnly(Business $business): void
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
    }
}