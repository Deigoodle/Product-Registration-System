const CODIGO_REGEX = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z0-9]{5,15}$/;
const PRECIO_REGEX = /^(?:0*[1-9]\d*)(?:\.\d{1,2})?$/;

function validateForm(form) {
    const codigo = form['producto-codigo'].value.trim();
    const nombre = form['producto-nombre'].value.trim();
    const bodega = form['producto-bodega'].value.trim();
    const sucursal = form['producto-sucursal'].value.trim();
    const moneda = form['producto-moneda'].value.trim();
    const precio = form['producto-precio'].value.trim();
    const materiales = form.querySelectorAll('input[name="producto-materiales"]:checked');
    const descripcion = form['producto-descripcion'].value.trim();

    const [codigoValido, codigoMensaje] = validateCodigo(codigo);
    if (!codigoValido) {
        alert(codigoMensaje);
        return false;
    }

    const [nombreValido, nombreMensaje] = validateNombre(nombre);
    if (!nombreValido) {
        alert(nombreMensaje);
        return false;
    }

    const [bodegaValida, bodegaMensaje] = validateBodega(bodega);
    if (!bodegaValida) {
        alert(bodegaMensaje);
        return false;
    }

    const [sucursalValida, sucursalMensaje] = validateSucursal(sucursal);
    if (!sucursalValida) {
        alert(sucursalMensaje);
        return false;
    }

    const [monedaValida, monedaMensaje] = validateMoneda(moneda);
    if (!monedaValida) {
        alert(monedaMensaje);
        return false;
    }

    const [precioValido, precioMensaje] = validatePrecio(precio);
    if (!precioValido) {
        alert(precioMensaje);
        return false;
    }

    const [materialesValidos, materialesMensaje] = validateMateriales(materiales);
    if (!materialesValidos) {
        alert(materialesMensaje);
        return false;
    }

    const [descripcionValida, descripcionMensaje] = validateDescripcion(descripcion);
    if (!descripcionValida) {
        alert(descripcionMensaje);
        return false;
    }

    return true;
}

function validateCodigo(codigo) {
    if (codigo.length === 0) {
        return [false, 'El código del producto no puede estar en blanco.'];
    }
    if (codigo.length < 5 || codigo.length > 15) {
        return [false, 'El código del producto debe tener entre 5 y 15 caracteres.'];
    }
    if (!CODIGO_REGEX.test(codigo)) {
        return [false, 'El código del producto debe contener letras y números'];
    }
    return [true, ''];
}

function validateNombre(nombre) {
    if (nombre.length === 0) {
        return [false, 'El nombre del producto no puede estar en blanco.'];
    }
    if (nombre.length < 2 || nombre.length > 50) {
        return [false, 'El nombre del producto debe tener entre 2 y 50 caracteres.'];
    }
    return [true, ''];
}

function validatePrecio(precio) {
    if (precio.length === 0) {
        return [false, 'El precio del producto no puede estar en blanco.'];
    }
    if (!PRECIO_REGEX.test(precio)) {
        return [false, 'El precio del producto debe ser un número positivo con hasta dos decimales.'];
    }
    return [true, ''];
}

function validateMateriales(materiales) {
    if (materiales.length < 2) {
        return [false, 'Debe seleccionar al menos dos materiales para el producto.'];
    }
    return [true, ''];
}

function validateBodega(bodega) {
    if (bodega.length === 0) {
        return [false, 'Debe seleccionar una bodega.'];
    }
    return [true, ''];
}

function validateSucursal(sucursal) {
    if (sucursal.length === 0) {
        return [false, 'Debe seleccionar una sucursal para la bodega seleccionada.'];
    }
    return [true, ''];
}

function validateMoneda(moneda) {
    if (moneda.length === 0) {
        return [false, 'Debe seleccionar una moneda para el producto.'];
    }
    return [true, ''];
}

function validateDescripcion(descripcion) {
    if (descripcion.length === 0) {
        return [false, 'La descripción del producto no puede estar en blanco.'];
    }
    if (descripcion.length < 10 || descripcion.length > 1000) {
        return [false, 'La descripción del producto debe tener entre 10 y 1000 caracteres.'];
    }
    return [true, ''];
}
