const API_URL = 'http://localhost:9999';

async function getBodegas() {
    const response = await fetch(`${API_URL}/bodegas`);
    return response.json();
}

async function getSucursales(bodegaId) {
    const response = await fetch(`${API_URL}/sucursales/bodega/${bodegaId}`);
    return response.json();
}

async function getMateriales() {
    const response = await fetch(`${API_URL}/materiales`);
    return response.json();
}

async function getMonedas() {
    const response = await fetch(`${API_URL}/monedas`);
    return response.json();
}

async function codigoExists(codigo) {
    const response = await fetch(
        `${API_URL}/productos/codigo/${encodeURIComponent(codigo)}`
    );
    const data = await response.json();
    return data.success === true;
}

async function createProducto(payload) {
    const response = await fetch(`${API_URL}/productos`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
    return response.json();
}
