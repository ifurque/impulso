@extends('layouts.app')

@section('body_class', 'inventory-database-page')

@section('content')
  <section class="dashboard wrap">
      @include('partials.help-tour-trigger', ['label' => 'Guía de inventario', 'steps' => [
        ['target' => '[data-inventory-toggle="nuevo-registro"]', 'title' => 'Crear un producto', 'body' => 'Completa nombre, precio, cantidad y los datos opcionales que quieres guardar en tu catálogo.'],
        ['target' => '[data-inventory-toggle="registros"]', 'title' => 'Revisar el listado', 'body' => 'Busca, modifica o elimina productos. Los campos visibles dependen de la estructura que configures.'],
        ['target' => '[data-inventory-toggle="ingreso-mercaderia"]', 'title' => 'Ingresar mercadería', 'body' => 'Selecciona un producto y suma las unidades recibidas; el stock actual no se reemplaza.'],
      ]])
    <header class="inventory-mobile-header">
      <p class="inventory-mobile-kicker">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 9 5v10l-9 5-9-5V7l9-5Z"/><path d="m3.5 7.2 8.5 4.7 8.5-4.7M12 12v10M7.5 4.5l9 5"/></svg>
        Inventario
      </p>
      <h1>Dashboard</h1>
      <p class="inventory-mobile-description">Gestiona el stock de {{ $business->name }}.</p>
    </header>

    <nav id="inventory-actions" class="inventory-mobile-actions" aria-label="Acciones de inventario"
      data-initial-panel="{{ $errors->has('incoming_quantity') || $errors->has('product_id') ? 'ingreso-mercaderia' : ($errors->any() ? 'nuevo-registro' : '') }}">
      <button type="button" aria-controls="inventory-disclosure-panel" aria-expanded="false" data-inventory-toggle="nuevo-registro">
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 8v8M8 12h8"/></svg>
        <span>Crear producto</span>
        <svg class="inventory-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
      </button>
      <button type="button" aria-controls="inventory-disclosure-panel" aria-expanded="false" data-inventory-toggle="registros">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6h12M9 12h12M9 18h12"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
        <span>Listado</span>
        <svg class="inventory-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
      </button>
      <button type="button" aria-controls="inventory-disclosure-panel" aria-expanded="false" data-inventory-toggle="ingreso-mercaderia">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="M4.5 7.8 12 12l7.5-4.2M12 12v9M12 8V2m-3 3 3-3 3 3"/></svg>
        <span>Ingresar producto</span>
        <svg class="inventory-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
      </button>
    </nav>
    <div id="inventory-disclosure-panel" class="inventory-disclosure-panel" hidden></div>

    <div class="dashboard-top inventory-desktop-top">
      <div>
        <p class="eyebrow">Administración</p>
        <h1>Base de datos del emprendimiento</h1>
        <p class="muted">Formato tradicional para registrar y administrar tu catalogo.</p>
      </div>
      <div class="page-actions">
        <a class="button secondary" href="{{ route('management.index', $business) }}">Gestion operativa</a>
        <a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a>
      </div>
    </div>

    <div id="inventory-panel-storage" hidden>
    <section id="nuevo-registro" class="inventory-create-view" hidden>
      <header class="inventory-view-heading">
        <button class="inventory-back-link" type="button" aria-label="Cerrar apartado">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12"/></svg>
        </button>
        <div>
          <p class="inventory-section-kicker">Inventario</p>
          <h2>Crear producto</h2>
        </div>
      </header>

      <details class="inventory-components">
        <summary>Personalizar campos de la tabla</summary>
      <form method="POST" action="{{ route('management.database.components', $business) }}" class="db-components-form"
        style="margin-bottom: 18px;">
        @csrf
        <label class="check db-fixed-check">
          <input type="checkbox" checked disabled>
          <span>N° <small>(fijo)</small></span>
        </label>
        @foreach($availableComponents as $component)
          <label class="check">
            <input type="checkbox" name="components[]" value="{{ $component }}" @checked(in_array($component, $selectedComponents, true))>
            <span>{{ ucfirst($component) }}</span>
          </label>
        @endforeach
        <label class="check db-fixed-check">
          <input type="checkbox" checked disabled>
          <span>Accion <small>(fijo)</small></span>
        </label>
        <button class="button button-small" type="submit">Guardar estructura</button>
      </form>
      </details>

      <form method="POST" enctype="multipart/form-data" action="{{ route('products.store', $business) }}" class="inventory-product-form">
        @csrf
        <div class="inventory-product-main">
          <div class="inventory-product-media-fields">
            <label class="inventory-photo-picker" for="inventory-product-photo">
              <img id="inventory-photo-preview" alt="Vista previa del producto" hidden>
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m-5 5 5-5 5 5M4 15v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4"/></svg>
              <span>Subir imagen</span>
              <small>PNG o JPG, hasta 5 MB</small>
              <input id="inventory-product-photo" type="file" name="photo" accept="image/*">
            </label>
            @error('photo') <span class="error">{{ $message }}</span> @enderror

            <label class="inventory-field">
              Descripción
              <textarea name="description" maxlength="2000" rows="4" placeholder="Describe brevemente el producto">{{ old('description') }}</textarea>
            </label>
          </div>

          <div class="inventory-product-core">
            @if(in_array('producto', $selectedComponents, true))
              <label class="inventory-field">
                Nombre
                <input name="name" maxlength="120" value="{{ old('name') }}" required>
              </label>
            @else
              <input type="hidden" name="name" value="Registro {{ now()->format('His') }}">
            @endif

            @if(in_array('marca', $selectedComponents, true))
              <label class="inventory-field">
                Marca
                <input name="brand" maxlength="120" value="{{ old('brand') }}" placeholder="Ej. Philips">
              </label>
            @else
              <input type="hidden" name="brand" value="">
            @endif

            @if(in_array('precio', $selectedComponents, true))
              <label class="inventory-field">
                Precio
                <span class="inventory-input-prefix"><span>$</span><input type="number" name="price" min="0" step="0.01" value="{{ old('price') }}" required></span>
              </label>
            @else
              <input type="hidden" name="price" value="0">
            @endif

            @if(in_array('cantidad', $selectedComponents, true))
              <label class="inventory-field">
                Stock
                <span class="inventory-input-stepper">
                  <input type="number" name="quantity" min="0.01" step="0.01" value="{{ old('quantity', 1) }}" required>
                  <button type="button" data-step="-1" aria-label="Restar stock">−</button>
                  <button type="button" data-step="1" aria-label="Sumar stock">+</button>
                </span>
              </label>
            @else
              <input type="hidden" name="quantity" value="1">
            @endif

            @if(in_array('unidad', $selectedComponents, true))
              <label class="inventory-field">
                Unidad
                <select name="unit" required>
                  <option value="unidad" @selected(old('unit', 'unidad') === 'unidad')>Unidad</option>
                  <option value="kilo" @selected(old('unit') === 'kilo')>Kg</option>
                  <option value="litro" @selected(old('unit') === 'litro')>Lt</option>
                </select>
              </label>
            @else
              <input type="hidden" name="unit" value="unidad">
            @endif
          </div>
        </div>

        <div class="inventory-product-optional">
          <h3>Datos privados y opcionales</h3>
          <div class="inventory-product-core">
            @if(in_array('modelo', $selectedComponents, true))
              <label class="inventory-field">
                Modelo
                <input name="model" maxlength="120" value="{{ old('model') }}" placeholder="Ej. X200">
              </label>
            @else
              <input type="hidden" name="model" value="">
            @endif

            @if(in_array('categoria', $selectedComponents, true))
              <label class="inventory-field">
                Categoría
                <input name="category" maxlength="100" value="{{ old('category') }}" placeholder="Ej. Panificados">
              </label>
            @else
              <input type="hidden" name="category" value="">
            @endif
          </div>
        </div>

        <div class="inventory-form-actions">
          <button class="inventory-cancel-button" type="button" data-inventory-close>Cancelar</button>
          <button class="inventory-submit-button" type="submit">Crear producto</button>
        </div>
      </form>
    </section>

    <section id="ingreso-mercaderia" class="inventory-stock-view" hidden>
      <header class="inventory-view-heading">
        <button class="inventory-back-link" type="button" aria-label="Cerrar apartado">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12"/></svg>
        </button>
        <div>
          <p class="inventory-section-kicker">Inventario</p>
          <h2>Ingresar producto</h2>
        </div>
      </header>

      <form method="POST" action="{{ route('products.add-stock', $business) }}" class="inventory-stock-form">
        @csrf
        <label class="inventory-stock-product">
          Seleccionar producto
          <select name="product_id" required>
            <option value="">Producto</option>
            @foreach($business->products as $product)
              <option value="{{ $product->id }}">
                #{{ $product->id }} - {{ $product->name }} (stock actual:
                {{ number_format((float) ($product->quantity ?? 1), 2, ',', '.') }}
                {{ $product->unit === 'kilo' ? 'Kg' : ($product->unit === 'litro' ? 'Lt' : 'Unidad') }})
              </option>
            @endforeach
          </select>
        </label>
        </label>
        <label class="inventory-field inventory-stock-quantity">
          Cantidad
          <span class="inventory-input-stepper inventory-stepper-control">
            <button type="button" data-step="-1" aria-label="Restar cantidad">−</button>
            <input id="inventory-incoming-quantity" type="number" name="incoming_quantity" min="0.01" step="0.01" value="1" required>
            <button type="button" data-step="1" aria-label="Sumar cantidad">+</button>
          </span>
        </label>

        <div class="inventory-stock-summary">
          <h3>Cambios extras</h3>
          <p>El ingreso se suma al stock actual. Para modificar precio o datos del producto, usa la opción “Modificar” en el listado.</p>
        </div>

        <div class="inventory-form-actions">
          <button class="inventory-cancel-button" type="button" data-inventory-close>Cancelar</button>
          <button class="inventory-submit-button" type="submit">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 17v3h16v-3"/></svg>
            Ingresar producto
          </button>
        </div>
      </form>
    </section>

    <section id="registros" class="inventory-list-view" hidden>
      <div class="db-table-head">
        <div class="inventory-list-title">
          <p class="inventory-section-kicker">Inventario</p>
          <h2>Lista de productos</h2>
        </div>
        <label class="inventory-search-field">
          <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
          <input id="db-search" class="search-input" placeholder="Buscar producto">
        </label>
      </div>

      <div class="db-table-wrap">
        <table class="db-table">
          <thead>
            <tr>
              <th>ID</th>
              @if(in_array('producto', $selectedComponents, true))
              <th>Producto</th>@endif
              @if(in_array('marca', $selectedComponents, true))
              <th>Marca</th>@endif
              @if(in_array('modelo', $selectedComponents, true))
              <th>Modelo</th>@endif
              @if(in_array('unidad', $selectedComponents, true))
              <th>Unidad</th>@endif
              @if(in_array('cantidad', $selectedComponents, true))
              <th>Cantidad (por unidad)</th>@endif
              @if(in_array('precio', $selectedComponents, true))
              <th>Precio (por unidad)</th>@endif
              @if(in_array('categoria', $selectedComponents, true))
              <th>Categoria</th>@endif
              <th>Accion</th>
            </tr>
          </thead>
          <tbody>
            @forelse($business->products as $product)
              <tr data-db-row
                data-filter-text="{{ Str::lower(($product->name ?? '') . ' ' . ($product->brand ?? '') . ' ' . ($product->model ?? '') . ' ' . ($product->category ?? '') . ' ' . ($product->unit ?? '')) }}">
                <td>{{ $product->catalog_number }}</td>
                @if(in_array('producto', $selectedComponents, true))
                <td>{{ $product->name }}</td>@endif
                @if(in_array('marca', $selectedComponents, true))
                <td>{{ $product->brand ?: '-' }}</td>@endif
                @if(in_array('modelo', $selectedComponents, true))
                <td>{{ $product->model ?: '-' }}</td>@endif
                @if(in_array('unidad', $selectedComponents, true))
                <td>{{ $product->unit === 'kilo' ? 'Kg' : ($product->unit === 'litro' ? 'Lt' : 'Unidad') }}</td>@endif
                @if(in_array('cantidad', $selectedComponents, true))
                <td>{{ number_format((float) ($product->quantity ?? 1), 2, ',', '.') }}</td>@endif
                @if(in_array('precio', $selectedComponents, true))
                <td>$ {{ number_format((float) ($product->price ?? 0), 2, ',', '.') }}</td>@endif
                @if(in_array('categoria', $selectedComponents, true))
                <td>{{ $product->category ?: '-' }}</td>@endif
                <td>
                  <div class="db-actions">
                    <button type="button" class="plain-button" data-edit-toggle="row-{{ $product->id }}">Modificar</button>
                    <form method="POST" action="{{ route('products.destroy', [$business, $product]) }}"
                      onsubmit="return confirm('¿Eliminar este registro?')">
                      @csrf
                      @method('DELETE')
                      <button class="plain-button">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
              <tr id="row-{{ $product->id }}" class="db-edit-row" hidden>
                <td colspan="{{ 2 + count($selectedComponents) }}">
                  <form method="POST" action="{{ route('products.update', [$business, $product]) }}" class="db-edit-form">
                    @csrf
                    @method('PUT')
                    <label>
                      N°
                      <input value="{{ $product->catalog_number }}" disabled>
                    </label>

                    @if(in_array('producto', $selectedComponents, true))
                      <label>
                        Producto
                        <input name="name" maxlength="120" value="{{ $product->name }}" required>
                      </label>
                    @else
                      <input type="hidden" name="name" value="{{ $product->name }}">
                    @endif

                    @if(in_array('marca', $selectedComponents, true))
                      <label>
                        Marca
                        <input name="brand" maxlength="120" value="{{ $product->brand }}">
                      </label>
                    @else
                      <input type="hidden" name="brand" value="{{ $product->brand }}">
                    @endif

                    @if(in_array('modelo', $selectedComponents, true))
                      <label>
                        Modelo
                        <input name="model" maxlength="120" value="{{ $product->model }}">
                      </label>
                    @else
                      <input type="hidden" name="model" value="{{ $product->model }}">
                    @endif

                    @if(in_array('unidad', $selectedComponents, true))
                      <label>
                        Unidad
                        <select name="unit" required>
                          <option value="kilo" @selected($product->unit === 'kilo')>Kg</option>
                          <option value="litro" @selected($product->unit === 'litro')>Lt</option>
                          <option value="unidad" @selected(($product->unit ?? 'unidad') === 'unidad')>Unidad</option>
                        </select>
                      </label>
                    @else
                      <input type="hidden" name="unit" value="{{ $product->unit ?? 'unidad' }}">
                    @endif

                    @if(in_array('cantidad', $selectedComponents, true))
                      <label>
                        Cantidad
                        <input type="number" name="quantity" min="0.01" step="0.01" value="{{ $product->quantity ?? 1 }}"
                          required>
                      </label>
                    @else
                      <input type="hidden" name="quantity" value="{{ $product->quantity ?? 1 }}">
                    @endif

                    @if(in_array('precio', $selectedComponents, true))
                      <label>
                        Precio
                        <input type="number" name="price" min="0" step="0.01" value="{{ $product->price ?? 0 }}" required>
                      </label>
                    @else
                      <input type="hidden" name="price" value="{{ $product->price ?? 0 }}">
                    @endif

                    @if(in_array('categoria', $selectedComponents, true))
                      <label>
                        Categoria
                        <input name="category" maxlength="100" value="{{ $product->category }}">
                      </label>
                    @else
                      <input type="hidden" name="category" value="{{ $product->category }}">
                    @endif

                    <div class="db-edit-actions">
                      <button class="button button-small">Guardar cambios</button>
                      <button type="button" class="button secondary button-small"
                        data-edit-toggle="row-{{ $product->id }}">Cancelar</button>
                    </div>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="{{ 2 + count($selectedComponents) }}" class="muted">No hay registros cargados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
    </div>

    <nav class="inventory-mobile-bottom-nav" aria-label="Navegación del emprendimiento">
      <a href="{{ route('user.profile') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
        <span>{{ auth()->user()->name }}</span>
      </a>
      <a href="{{ route('dashboard', $business) }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7M5 9v12h14V9M9 21v-7h6v7"/></svg>
        <span>Inicio</span>
      </a>
    </nav>
  </section>

  <style>
    .db-components-form {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      padding: 12px;
      border: 1px solid var(--line);
      background: #f8fbf6;
      border-radius: 4px;
    }

    .db-components-form .check {
      margin: 0;
    }

    .db-fixed-check {
      opacity: 0.85;
    }

    .db-form {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 12px;
      align-items: end;
    }

    .stock-entry-form {
      display: grid;
      grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr) auto;
      gap: 12px;
      align-items: end;
    }

    .stock-entry-form .button {
      height: 44px;
      white-space: nowrap;
    }

    .db-form .button {
      height: 44px;
    }

    .db-table-head {
      display: flex;
      justify-content: space-between;
      align-items: end;
      gap: 14px;
      margin-bottom: 14px;
    }

    .db-table-head label {
      min-width: 260px;
    }

    .db-table-wrap {
      overflow-x: auto;
      border: 1px solid var(--line);
      border-radius: 4px;
    }

    .db-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      min-width: 860px;
    }

    .db-table th,
    .db-table td {
      border: 1px solid var(--line);
      padding: 10px;
      text-align: left;
      font-size: 0.86rem;
      white-space: nowrap;
    }

    .db-table th {
      background: #f0f3ee;
      font-weight: 700;
    }

    .db-table tbody tr:nth-child(even) {
      background: #fafcf8;
    }

    .db-actions {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .db-edit-row {
      background: #f9fbf7;
    }

    .db-edit-form {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 10px;
      align-items: end;
    }

    .db-edit-actions {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    body.inventory-database-page {
      min-height: 100vh;
      background: #eef3f5;
      color: #202a2e;
    }

    body.inventory-database-page .nav,
    body.inventory-database-page footer,
    body.inventory-database-page .business-inbox,
    body.inventory-database-page .inventory-desktop-top {
      display: none;
    }

    body.inventory-database-page .dashboard {
      max-width: 1040px;
      padding-top: 42px;
      padding-bottom: 116px;
    }

    .inventory-mobile-header {
      display: grid;
      gap: 10px;
      margin-bottom: 20px;
    }

    .inventory-mobile-kicker {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0;
      color: var(--teal);
      font-size: 1rem;
      font-weight: 600;
    }

    .inventory-mobile-kicker svg {
      width: 27px;
      height: 27px;
      fill: none;
      stroke: currentColor;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 1.7;
    }

    body.inventory-database-page .inventory-mobile-header h1 {
      margin: 0;
      color: #202a2e;
      font-size: 2rem;
      line-height: 1.15;
    }

    .inventory-mobile-description {
      margin: 0;
      color: #69777d;
      font-size: 0.92rem;
    }

    .inventory-mobile-actions {
      display: grid;
      gap: 8px;
      max-width: 760px;
      margin-bottom: 28px;
    }

    .inventory-mobile-actions button {
      display: grid;
      grid-template-columns: 30px minmax(0, 1fr) 20px;
      align-items: center;
      gap: 12px;
      min-height: 64px;
      padding: 0 16px;
      border: 1px solid #cbd2d2;
      border-radius: 8px;
      background: #fff;
      color: #202a2e;
      font-size: 1rem;
      font-weight: 500;
      font-family: var(--sans);
      text-align: left;
      transition: border-color 160ms ease, background 160ms ease, transform 160ms ease;
    }

    .inventory-mobile-actions button:hover,
    .inventory-mobile-actions button[aria-expanded="true"] {
      transform: translateY(-1px);
      border-color: #8fb9af;
      background: #fbfdfd;
    }

    .inventory-mobile-actions button:focus-visible,
    .inventory-mobile-bottom-nav a:focus-visible {
      outline: 3px solid #69a999;
      outline-offset: 2px;
    }

    .inventory-mobile-actions svg {
      width: 27px;
      height: 27px;
      fill: none;
      stroke: var(--ink);
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 1.8;
    }

    .inventory-mobile-actions button:first-child > svg:first-child,
    .inventory-mobile-actions button:nth-child(2) > svg:first-child {
      color: var(--teal);
      stroke: currentColor;
    }

    .inventory-mobile-actions .inventory-chevron {
      width: 20px;
      height: 20px;
      justify-self: end;
    }

    .inventory-mobile-actions button[aria-expanded="true"] .inventory-chevron {
      transform: rotate(90deg);
    }

    .inventory-disclosure-panel:not([hidden]) {
      display: block;
      animation: inventory-panel-enter 180ms ease-out;
    }

    @keyframes inventory-panel-enter {
      from { opacity: 0; transform: translateY(-7px); }
      to { opacity: 1; transform: translateY(0); }
    }

    body.inventory-database-page .panel {
      border-color: #d6dfe1;
      border-radius: 8px;
      box-shadow: 0 3px 12px #253a4010;
    }

    body.inventory-database-page .inventory-mobile-bottom-nav {
      position: fixed;
      z-index: 30;
      right: 0;
      bottom: 0;
      left: 0;
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 76px;
      padding: 8px 14px calc(8px + env(safe-area-inset-bottom));
      border-top: 1px solid #d9dfdf;
      background: #fff;
      box-shadow: 0 -4px 18px #253a400c;
    }

    .inventory-mobile-bottom-nav a {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-width: 0;
      color: #263136;
      font-size: 0.92rem;
    }

    .inventory-mobile-bottom-nav svg {
      flex: 0 0 auto;
      width: 27px;
      height: 27px;
      fill: none;
      stroke: currentColor;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 1.7;
    }

    .inventory-mobile-bottom-nav a:first-child span {
      overflow: hidden;
      max-width: 220px;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .inventory-create-view,
    .inventory-stock-view,
    .inventory-list-view {
      margin-bottom: 22px;
      padding: 28px;
      border: 1px solid #d6dfe1;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 3px 12px #253a4010;
      scroll-margin-top: 24px;
    }

    .inventory-view-heading {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 22px;
    }

    .inventory-back-link {
      display: grid;
      flex: 0 0 30px;
      place-items: center;
      width: 30px;
      height: 36px;
      padding: 0;
      border: 0;
      background: transparent;
      color: #283337;
      cursor: pointer;
    }

    .inventory-back-link svg {
      width: 25px;
      height: 25px;
      fill: none;
      stroke: currentColor;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 2;
    }

    .inventory-section-kicker {
      margin: 0 0 2px;
      color: #42545a;
      font-size: 0.83rem;
      font-weight: 500;
    }

    .inventory-view-heading h2,
    .inventory-list-title h2 {
      margin: 0;
      color: #202a2e;
      font-size: 1.55rem;
      line-height: 1.2;
    }

    .inventory-components {
      margin: -4px 0 20px;
      border-bottom: 1px solid #dbe2e3;
      color: #344247;
    }

    .inventory-components summary {
      padding: 10px 0;
      color: #287a62;
      cursor: pointer;
      font-size: 0.84rem;
      font-weight: 600;
    }

    .inventory-components .db-components-form {
      margin-bottom: 16px !important;
      background: #f5f8f8;
    }

    .inventory-product-form {
      display: grid;
      gap: 22px;
      max-width: 850px;
    }

    .inventory-product-main {
      display: grid;
      grid-template-columns: minmax(180px, 0.8fr) minmax(0, 1.2fr);
      gap: 24px;
    }

    .inventory-product-media-fields {
      display: grid;
      align-content: start;
      gap: 16px;
    }

    .inventory-photo-picker {
      position: relative;
      display: grid;
      align-content: center;
      justify-items: center;
      gap: 8px;
      min-height: 170px;
      overflow: hidden;
      border: 1px solid #cbd4d5;
      border-radius: 7px;
      background: #f4f7f8;
      color: #278c68;
      cursor: pointer;
      text-align: center;
    }

    .inventory-photo-picker svg {
      width: 35px;
      height: 35px;
      fill: none;
      stroke: currentColor;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 1.8;
    }

    .inventory-photo-picker span {
      color: #263438;
      font-size: 0.92rem;
      font-weight: 500;
    }

    .inventory-photo-picker small {
      color: #718087;
      font-size: 0.72rem;
    }

    .inventory-photo-picker input {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
    }

    .inventory-photo-picker:has(img:not([hidden])) svg,
    .inventory-photo-picker:has(img:not([hidden])) span,
    .inventory-photo-picker:has(img:not([hidden])) small {
      display: none;
    }

    .inventory-photo-picker img:not([hidden]) {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .inventory-product-core {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      align-content: start;
      gap: 14px 18px;
    }

    .inventory-field {
      display: grid;
      align-content: start;
      gap: 6px;
      min-width: 0;
      color: #263438;
      font-size: 0.86rem;
      font-weight: 500;
    }

    .inventory-field input,
    .inventory-field select,
    .inventory-field textarea {
      width: 100%;
      min-width: 0;
      min-height: 42px;
      padding: 9px 11px;
      border: 1px solid #bdc9cb;
      border-radius: 6px;
      background: #fff;
      color: #263438;
      font: inherit;
      outline-color: #36a879;
    }

    .inventory-field textarea {
      min-height: 108px;
      resize: vertical;
    }

    .inventory-input-prefix,
    .inventory-input-stepper {
      display: flex;
      align-items: center;
      min-height: 42px;
      overflow: hidden;
      border: 1px solid #bdc9cb;
      border-radius: 6px;
      background: #fff;
    }

    .inventory-input-prefix > span {
      padding-left: 11px;
      color: #7b878c;
      font-size: 0.95rem;
      font-weight: 400;
    }

    .inventory-field .inventory-input-prefix input,
    .inventory-field .inventory-input-stepper input {
      min-height: 40px;
      border: 0;
      border-radius: 0;
      outline: none;
    }

    .inventory-input-stepper > button {
      align-self: stretch;
      width: 34px;
      border: 0;
      background: transparent;
      color: #25845f;
      cursor: pointer;
      font: 500 1.05rem var(--sans);
    }

    .inventory-input-stepper > button:hover {
      background: #eef7f2;
    }

    .inventory-product-optional {
      padding-top: 18px;
      border-top: 1px solid #dbe2e3;
    }

    .inventory-product-optional h3,
    .inventory-stock-summary h3 {
      margin: 0 0 12px;
      color: #263438;
      font-size: 1rem;
    }

    .inventory-form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      padding-top: 4px;
    }

    .inventory-cancel-button,
    .inventory-submit-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-width: 150px;
      min-height: 44px;
      padding: 10px 18px;
      border: 1px solid #36a879;
      border-radius: 6px;
      background: transparent;
      color: #278c68;
      cursor: pointer;
      font: 600 0.88rem var(--sans);
      text-align: center;
    }

    .inventory-submit-button {
      border-color: #2ba873;
      background: #2ba873;
      color: #fff;
    }

    .inventory-submit-button:hover {
      border-color: #18865b;
      background: #18865b;
    }

    .inventory-submit-button svg {
      width: 18px;
      height: 18px;
      fill: none;
      stroke: currentColor;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-width: 1.8;
    }

    .inventory-stock-form {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(140px, 0.55fr);
      gap: 20px 26px;
      align-items: end;
    }

    .inventory-stock-product {
      display: grid;
      grid-template-columns: minmax(130px, 0.7fr) minmax(0, 1fr);
      align-items: center;
      gap: 12px;
      color: #263438;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .inventory-stock-product select {
      min-height: 44px;
      padding: 9px 12px;
      border: 1px solid #bdc9cb;
      border-radius: 6px;
      background: #fff;
      color: #66747a;
      font: inherit;
    }

    .inventory-stepper-control {
      justify-content: space-between;
    }

    .inventory-stepper-control button {
      align-self: stretch;
      width: 40px;
      border: 0;
      background: transparent;
      color: #263438;
      cursor: pointer;
      font: 500 1.35rem var(--sans);
    }

    .inventory-stepper-control button:hover {
      background: #eef7f2;
      color: #21835d;
    }

    .inventory-stepper-control input {
      text-align: center;
    }

    .inventory-stock-summary {
      grid-column: 1 / -1;
      padding-top: 18px;
      border-top: 1px solid #dbe2e3;
    }

    .inventory-stock-summary h3 {
      margin-bottom: 5px;
    }

    .inventory-stock-summary p {
      max-width: 600px;
      margin: 0;
      color: #718087;
      font-size: 0.84rem;
    }

    .inventory-stock-form .inventory-form-actions {
      grid-column: 1 / -1;
    }

    .inventory-list-view .db-table-head {
      align-items: center;
    }

    .inventory-list-title h2 {
      font-size: 1.55rem;
    }

    .inventory-search-field {
      position: relative;
      display: block;
      width: min(100%, 280px);
    }

    .inventory-search-field svg {
      position: absolute;
      top: 50%;
      left: 11px;
      width: 17px;
      height: 17px;
      transform: translateY(-50%);
      fill: none;
      stroke: #287a62;
      stroke-linecap: round;
      stroke-width: 1.8;
      pointer-events: none;
    }

    .inventory-search-field .search-input {
      width: 100%;
      min-height: 40px;
      padding: 8px 11px 8px 36px;
      border-color: #d0dadd;
      border-radius: 6px;
      font-size: 0.84rem;
    }

    .inventory-list-view .db-table-wrap {
      border-color: #d7dfe1;
      border-radius: 6px;
    }

    .inventory-list-view .db-table {
      min-width: 700px;
    }

    .inventory-list-view .db-table th {
      border-color: #dce4e5;
      background: #e6edef;
      color: #287a62;
      font-size: 0.8rem;
      font-weight: 700;
    }

    .inventory-list-view .db-table td {
      height: 52px;
      border-color: #e1e7e8;
      color: #344247;
    }

    .inventory-list-view .db-table tbody tr:nth-child(even) {
      background: #f8fafb;
    }

    @media (max-width: 980px) {
      .db-components-form {
        gap: 10px;
      }

      .db-form {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }

      .stock-entry-form {
        grid-template-columns: 1fr 1fr;
      }

      .stock-entry-form .button {
        grid-column: 1 / -1;
      }

      .db-edit-form {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 760px) {
      body.inventory-database-page .dashboard {
        padding-top: 26px;
        padding-bottom: 108px;
      }

      .inventory-create-view,
      .inventory-stock-view,
      .inventory-list-view {
        padding: 18px 16px;
      }

      body.inventory-database-page .inventory-mobile-header h1 {
        font-size: 1.85rem;
      }

      body.inventory-database-page .inventory-mobile-actions {
        margin-bottom: 22px;
      }

      .db-components-form {
        display: grid;
        grid-template-columns: 1fr;
      }

      .db-form {
        grid-template-columns: 1fr;
      }

      .stock-entry-form {
        grid-template-columns: 1fr;
      }

      .db-edit-form {
        grid-template-columns: 1fr;
      }

      .inventory-product-main {
        grid-template-columns: minmax(105px, 0.82fr) minmax(0, 1.18fr);
        gap: 12px;
      }

      .inventory-product-core {
        grid-template-columns: 1fr;
        gap: 11px;
      }

      .inventory-photo-picker {
        min-height: 145px;
      }

      .inventory-photo-picker small {
        padding: 0 5px;
      }

      .inventory-field {
        font-size: 0.82rem;
      }

      .inventory-field input,
      .inventory-field select {
        padding: 8px;
      }

      .inventory-field textarea {
        min-height: 94px;
        padding: 8px;
      }

      .inventory-product-optional .inventory-product-core {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .inventory-stock-form {
        grid-template-columns: 1fr;
        gap: 18px;
      }

      .inventory-stock-product {
        grid-column: 1;
        grid-template-columns: minmax(108px, 0.8fr) minmax(0, 1.2fr);
        gap: 9px;
        font-size: 0.82rem;
      }

      .inventory-stock-quantity {
        max-width: 220px;
      }

      .inventory-stock-summary,
      .inventory-stock-form .inventory-form-actions {
        grid-column: 1;
      }

      .inventory-form-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
      }

      .inventory-cancel-button,
      .inventory-submit-button {
        min-width: 0;
        padding: 9px 8px;
        font-size: 0.8rem;
      }

      .inventory-list-view .db-table-head {
        align-items: stretch;
      }

      .inventory-search-field {
        width: 100%;
      }

      .db-form .button,
      .stock-entry-form .button {
        width: 100%;
      }

      .db-table-head {
        flex-direction: column;
        align-items: stretch;
      }

      .db-table-head label {
        min-width: 0;
      }

      .db-actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
      }

      .db-edit-actions {
        flex-direction: column;
        align-items: stretch;
      }
    }

    @media (max-width: 360px) {
      .inventory-product-main {
        grid-template-columns: minmax(92px, 0.75fr) minmax(0, 1.25fr);
        gap: 9px;
      }

      .inventory-photo-picker {
        min-height: 132px;
      }

      .inventory-product-optional .inventory-product-core {
        grid-template-columns: 1fr;
      }
    }

    @media (min-width: 761px) {
      body.inventory-database-page .inventory-mobile-actions {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        max-width: none;
      }

      .inventory-mobile-actions button {
        min-height: 72px;
      }
    }
  </style>

  <script>
    const dbSearchInput = document.querySelector('#db-search');
    const dbRows = document.querySelectorAll('[data-db-row]');

    dbSearchInput?.addEventListener('input', () => {
      const search = dbSearchInput.value.toLowerCase().trim();
      dbRows.forEach((row) => {
        const text = row.dataset.filterText || '';
        row.style.display = text.includes(search) ? '' : 'none';
      });
    });

    document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const target = document.getElementById(button.dataset.editToggle);
        if (!target) return;
        target.hidden = !target.hidden;
      });
    });

    const photoInput = document.querySelector('#inventory-product-photo');
    const photoPreview = document.querySelector('#inventory-photo-preview');

    photoInput?.addEventListener('change', () => {
      const [photo] = photoInput.files || [];
      if (!photo || !photoPreview) return;
      photoPreview.src = URL.createObjectURL(photo);
      photoPreview.hidden = false;
    });

    document.querySelectorAll('.inventory-input-stepper [data-step]').forEach((button) => {
      button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('input');
        if (!input) return;
        const minimum = Number(input.min || 0);
        const current = Number(input.value || minimum || 0);
        input.value = Math.max(minimum, current + Number(button.dataset.step)).toString();
        input.dispatchEvent(new Event('input', { bubbles: true }));
      });
    });

    const inventoryActions = document.querySelector('#inventory-actions');
    const inventoryPanelStorage = document.querySelector('#inventory-panel-storage');
    const inventoryDisclosurePanel = document.querySelector('#inventory-disclosure-panel');
    const inventoryToggles = document.querySelectorAll('[data-inventory-toggle]');

    const closeInventoryPanel = () => {
      const openToggle = document.querySelector('[data-inventory-toggle][aria-expanded="true"]');
      const openPanel = openToggle && document.getElementById(openToggle.dataset.inventoryToggle);

      if (openPanel && inventoryPanelStorage) {
        openPanel.hidden = true;
        inventoryPanelStorage.append(openPanel);
      }

      inventoryToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
      if (inventoryDisclosurePanel) inventoryDisclosurePanel.hidden = true;
    };

    const openInventoryPanel = (toggle) => {
      const panelId = toggle.dataset.inventoryToggle;
      const panel = inventoryPanelStorage?.querySelector(`#${panelId}`);
      if (!panel || !inventoryDisclosurePanel) return;

      closeInventoryPanel();
      panel.hidden = false;
      inventoryDisclosurePanel.append(panel);
      inventoryDisclosurePanel.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
    };

    inventoryToggles.forEach((toggle) => {
      toggle.addEventListener('click', () => {
        if (toggle.getAttribute('aria-expanded') === 'true') {
          closeInventoryPanel();
          return;
        }

        openInventoryPanel(toggle);
      });
    });

    document.querySelectorAll('.inventory-back-link').forEach((button) => {
      button.addEventListener('click', closeInventoryPanel);
    });

    document.querySelectorAll('[data-inventory-close]').forEach((button) => {
      button.addEventListener('click', closeInventoryPanel);
    });

    const initialPanelId = inventoryActions?.dataset.initialPanel;
    const initialToggle = initialPanelId && document.querySelector(`[data-inventory-toggle="${initialPanelId}"]`);
    if (initialToggle) openInventoryPanel(initialToggle);
  </script>
@endsection