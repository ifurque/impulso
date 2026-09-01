@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Administración</p>
      <h1>Base de datos del emprendimiento</h1>
      <p class="muted">Registra y consulta productos o servicios en formato de tabla.</p>
    </div>
    <div class="page-actions">
      <a class="button secondary" href="{{ route('management.index', $business) }}">Gestion operativa</a>
      <a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a>
    </div>
  </div>

  <section class="panel" style="margin-bottom: 18px;">
    <p class="eyebrow">Nuevo registro</p>
    <form method="POST" action="{{ route('products.store', $business) }}" class="db-form">
      @csrf
      <label>
        Tipo
        <select name="type" id="db-product-type" required>
          <option value="product">Producto</option>
          <option value="service">Servicio</option>
        </select>
      </label>
      <label>
        Nombre
        <input name="name" maxlength="120" required>
      </label>
      <label>
        Categoria
        <input name="category" maxlength="100" placeholder="Ej. Panificados">
      </label>
      <label>
        Unidad
        <select name="unit" id="db-product-unit" required>
          <option value="unidad">Unidad</option>
          <option value="kilo">Kilo</option>
          <option value="litro">Litro</option>
        </select>
      </label>
      <label>
        Precio
        <input type="number" name="price" min="0" step="0.01" required>
      </label>
      <label class="check">
        <input type="checkbox" name="is_active" value="1" checked>
        <span>Activo</span>
      </label>
      <button class="button">Guardar</button>
    </form>
  </section>

  <section class="panel">
    <div class="db-table-head">
      <p class="eyebrow">Registros</p>
      <label>
        Buscar
        <input id="db-search" class="search-input" placeholder="Nombre, categoria o tipo">
      </label>
    </div>

    <div class="db-table-wrap">
      <table class="db-table">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Nombre</th>
            <th>Categoria</th>
            <th>Unidad</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($business->products as $product)
            <tr data-db-row data-filter-text="{{ Str::lower($product->type.' '.$product->name.' '.($product->category ?? '').' '.($product->unit ?? '')) }}">
              <td>{{ $product->type === 'service' ? 'Servicio' : 'Producto' }}</td>
              <td>{{ $product->name }}</td>
              <td>{{ $product->category ?: '-' }}</td>
              <td>{{ ucfirst($product->unit ?? 'unidad') }}</td>
              <td>$ {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
              <td>{{ $product->is_active ? 'Activo' : 'Inactivo' }}</td>
              <td>
                <form method="POST" action="{{ route('products.destroy', [$business, $product]) }}" onsubmit="return confirm('¿Eliminar este registro?')">
                  @csrf
                  @method('DELETE')
                  <button class="plain-button">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="muted">No hay registros cargados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</section>

<style>
  .db-form {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 12px;
    align-items: end;
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
  }
  .db-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    min-width: 780px;
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
  @media (max-width: 980px) {
    .db-form {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
  }
  @media (max-width: 640px) {
    .db-form {
      grid-template-columns: 1fr;
    }
    .db-form .button {
      width: 100%;
    }
    .db-table-head {
      flex-direction: column;
      align-items: stretch;
    }
    .db-table-head label {
      min-width: 0;
    }
  }
</style>

<script>
  const dbProductType = document.querySelector('#db-product-type');
  const dbProductUnit = document.querySelector('#db-product-unit');
  const dbSearchInput = document.querySelector('#db-search');
  const dbRows = document.querySelectorAll('[data-db-row]');

  const syncUnit = () => {
    if (!dbProductType || !dbProductUnit) return;
    if (dbProductType.value === 'service') {
      dbProductUnit.value = 'unidad';
      dbProductUnit.disabled = true;
      return;
    }
    dbProductUnit.disabled = false;
  };

  dbProductType?.addEventListener('change', syncUnit);
  syncUnit();

  dbSearchInput?.addEventListener('input', () => {
    const search = dbSearchInput.value.toLowerCase().trim();
    dbRows.forEach((row) => {
      const text = row.dataset.filterText || '';
      row.style.display = text.includes(search) ? '' : 'none';
    });
  });
</script>
@endsection
