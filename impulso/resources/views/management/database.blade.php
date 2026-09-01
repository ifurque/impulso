@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
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

  <section class="panel" style="margin-bottom: 18px;">
    <p class="eyebrow">Componentes de la base</p>
    <form method="POST" action="{{ route('management.database.components', $business) }}" class="db-components-form" style="margin-bottom: 18px;">
      @csrf
      <label class="check db-fixed-check">
        <input type="checkbox" checked disabled>
        <span>ID <small>(fijo)</small></span>
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

    <p class="eyebrow">Nuevo registro</p>
    <form method="POST" action="{{ route('products.store', $business) }}" class="db-form">
      @csrf
      @if(in_array('producto', $selectedComponents, true))
        <label>
          Producto
          <input name="name" maxlength="120" required>
        </label>
      @else
        <input type="hidden" name="name" value="Registro {{ now()->format('His') }}">
      @endif

      @if(in_array('marca', $selectedComponents, true))
        <label>
          Marca
          <input name="brand" maxlength="120" placeholder="Ej. Philips">
        </label>
      @else
        <input type="hidden" name="brand" value="">
      @endif

      @if(in_array('modelo', $selectedComponents, true))
        <label>
          Modelo
          <input name="model" maxlength="120" placeholder="Ej. X200">
        </label>
      @else
        <input type="hidden" name="model" value="">
      @endif

      @if(in_array('unidad', $selectedComponents, true))
        <label>
          Unidad
          <select name="unit" required>
            <option value="kilo">Kg</option>
            <option value="litro">Lt</option>
            <option value="unidad">Unidad</option>
          </select>
        </label>
      @else
        <input type="hidden" name="unit" value="unidad">
      @endif

      @if(in_array('cantidad', $selectedComponents, true))
        <label>
          Cantidad (por unidad)
          <input type="number" name="quantity" min="0.01" step="0.01" value="1" required>
        </label>
      @else
        <input type="hidden" name="quantity" value="1">
      @endif

      @if(in_array('precio', $selectedComponents, true))
        <label>
          Precio (por unidad)
          <input type="number" name="price" min="0" step="0.01" required>
        </label>
      @else
        <input type="hidden" name="price" value="0">
      @endif

      @if(in_array('categoria', $selectedComponents, true))
        <label>
          Categoria
          <input name="category" maxlength="100" placeholder="Ej. Panificados">
        </label>
      @else
        <input type="hidden" name="category" value="">
      @endif
      <button class="button">Guardar</button>
    </form>

    <hr style="margin: 18px 0;">

    <p class="eyebrow">Ingreso de mercaderia</p>
    <form method="POST" action="{{ route('products.add-stock', $business) }}" class="stock-entry-form">
      @csrf
      <label>
        Selecciona producto
        <select name="product_id" required>
          <option value="">Elegir producto</option>
          @foreach($business->products as $product)
            <option value="{{ $product->id }}">
              #{{ $product->id }} - {{ $product->name }} (stock actual: {{ number_format((float) ($product->quantity ?? 1), 2, ',', '.') }} {{ $product->unit === 'kilo' ? 'Kg' : ($product->unit === 'litro' ? 'Lt' : 'Unidad') }})
            </option>
          @endforeach
        </select>
      </label>
      <label>
        Cantidad a ingresar
        <input type="number" name="incoming_quantity" min="0.01" step="0.01" placeholder="Ej. 10" required>
      </label>
      <button class="button">Sumar mercaderia</button>
    </form>
  </section>

  <section class="panel">
    <div class="db-table-head">
      <p class="eyebrow">Registros</p>
      <label>
        Buscar
        <input id="db-search" class="search-input" placeholder="Producto, categoria o unidad">
      </label>
    </div>

    <div class="db-table-wrap">
      <table class="db-table">
        <thead>
          <tr>
            <th>ID</th>
            @if(in_array('producto', $selectedComponents, true))<th>Producto</th>@endif
            @if(in_array('marca', $selectedComponents, true))<th>Marca</th>@endif
            @if(in_array('modelo', $selectedComponents, true))<th>Modelo</th>@endif
            @if(in_array('unidad', $selectedComponents, true))<th>Unidad</th>@endif
            @if(in_array('cantidad', $selectedComponents, true))<th>Cantidad (por unidad)</th>@endif
            @if(in_array('precio', $selectedComponents, true))<th>Precio (por unidad)</th>@endif
            @if(in_array('categoria', $selectedComponents, true))<th>Categoria</th>@endif
            <th>Accion</th>
          </tr>
        </thead>
        <tbody>
          @forelse($business->products as $product)
            <tr data-db-row data-filter-text="{{ Str::lower(($product->name ?? '').' '.($product->brand ?? '').' '.($product->model ?? '').' '.($product->category ?? '').' '.($product->unit ?? '')) }}">
              <td>{{ $product->id }}</td>
              @if(in_array('producto', $selectedComponents, true))<td>{{ $product->name }}</td>@endif
              @if(in_array('marca', $selectedComponents, true))<td>{{ $product->brand ?: '-' }}</td>@endif
              @if(in_array('modelo', $selectedComponents, true))<td>{{ $product->model ?: '-' }}</td>@endif
              @if(in_array('unidad', $selectedComponents, true))<td>{{ $product->unit === 'kilo' ? 'Kg' : ($product->unit === 'litro' ? 'Lt' : 'Unidad') }}</td>@endif
              @if(in_array('cantidad', $selectedComponents, true))<td>{{ number_format((float) ($product->quantity ?? 1), 2, ',', '.') }}</td>@endif
              @if(in_array('precio', $selectedComponents, true))<td>$ {{ number_format((float) ($product->price ?? 0), 2, ',', '.') }}</td>@endif
              @if(in_array('categoria', $selectedComponents, true))<td>{{ $product->category ?: '-' }}</td>@endif
              <td>
                <div class="db-actions">
                  <button type="button" class="plain-button" data-edit-toggle="row-{{ $product->id }}">Modificar</button>
                  <form method="POST" action="{{ route('products.destroy', [$business, $product]) }}" onsubmit="return confirm('¿Eliminar este registro?')">
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
                    ID
                    <input value="{{ $product->id }}" disabled>
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
                      <input type="number" name="quantity" min="0.01" step="0.01" value="{{ $product->quantity ?? 1 }}" required>
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
                    <button type="button" class="button secondary button-small" data-edit-toggle="row-{{ $product->id }}">Cancelar</button>
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
  @media (max-width: 640px) {
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
</script>
@endsection
