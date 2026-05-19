function fillSelect(selectId, result, labelKey = 'nombre') {
    const select = document.getElementById(selectId);
    if (!result.success) {
        console.error(selectId, result.errors);
        return;
    }
    result.data.forEach((item) => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item[labelKey];
        select.appendChild(option);
    });
}

function fillMateriales(result) {
    const container = document.getElementById('producto-materiales');
    if (!result.success) {
        console.error('materiales', result.errors);
        return;
    }
    result.data.forEach((material) => {
        const label = document.createElement('label');
        const input = document.createElement('input');
        input.type = 'checkbox';
        input.name = 'producto-materiales';
        input.value = material.id;
        label.appendChild(input);
        label.appendChild(document.createTextNode(' ' + material.nombre));
        container.appendChild(label);
    });
}

function resetSelect(selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value=""></option>';
}

async function loadCatalogs() {
    resetSelect('producto-bodega');
    resetSelect('producto-moneda');
    document.getElementById('producto-materiales').innerHTML = '';

    const [bodegas, monedas, materiales] = await Promise.all([
        getBodegas(),
        getMonedas(),
        getMateriales(),
    ]);
    fillSelect('producto-bodega', bodegas);
    fillSelect('producto-moneda', monedas);
    fillMateriales(materiales);
}

async function loadSucursales(bodegaId) {
    const select = document.getElementById('producto-sucursal');
    select.innerHTML = '<option value=""></option>';
    select.disabled = true;
    if (!bodegaId) return;

    const result = await getSucursales(bodegaId);
    if (!result.success) {
        console.error('sucursales', result.errors);
        return;
    }
    result.data.forEach((sucursal) => {
        const option = document.createElement('option');
        option.value = sucursal.id;
        option.textContent = sucursal.nombre;
        select.appendChild(option);
    });
    select.disabled = false;
}

function buildPayload(form) {
    const materiales = [...form.querySelectorAll('input[name="producto-materiales"]:checked')]
        .map((input) => Number(input.value));

    return {
        codigo: form['producto-codigo'].value.trim(),
        nombre: form['producto-nombre'].value.trim(),
        descripcion: form['producto-descripcion'].value.trim(),
        precio: parseFloat(form['producto-precio'].value.trim()),
        moneda_id: Number(form['producto-moneda'].value),
        bodega_id: Number(form['producto-bodega'].value),
        sucursal_id: Number(form['producto-sucursal'].value),
        materiales,
    };
}

async function handleSubmit(event) {
    event.preventDefault();
    const form = event.target;

    if (!validateForm(form)) {
        return;
    }

    const codigo = form['producto-codigo'].value.trim();
    try {
        if (await codigoExists(codigo)) {
            alert('El código del producto ya está registrado.');
            return;
        }

        const result = await createProducto(buildPayload(form));
        if (result.success) {
            alert('Producto guardado correctamente.');
            form.reset();
            resetSelect('producto-sucursal');
            document.getElementById('producto-sucursal').disabled = true;
            await loadCatalogs();
            return;
        }

        const errors = result.errors || ['No se pudo guardar el producto.'];
        if (errors.some((e) => typeof e === 'string' && e.toLowerCase().includes('unique'))) {
            alert('El código del producto ya está registrado.');
            return;
        }
        alert(errors.join('\n'));
    } catch (err) {
        console.error(err);
        alert('Error de conexión con el servidor.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('product-form').addEventListener('submit', handleSubmit);

    loadCatalogs().catch((err) => console.error(err));

    document.getElementById('producto-bodega').addEventListener('change', (e) => {
        loadSucursales(e.target.value).catch((err) => console.error(err));
    });
});
