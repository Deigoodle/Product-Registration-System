# PHP API

REST API on **http://localhost:9999**.

## Run

```bash
docker compose up -d --build
```

## Endpoints

| Method | Path               | Description        |
|--------|--------------------|--------------------|
| GET    | `/`                | API info           |
| GET    | `/health`          | DB health check    |
| GET    | `/productos`       | List all products  |
| GET    | `/productos/{id}`  | Get one product    |
| POST   | `/productos`       | Create product     |
| PUT    | `/productos/{id}`  | Update product     |
| DELETE | `/productos/{id}`  | Delete product     |

```bash
curl http://localhost:9999/productos
curl http://localhost:9999/productos/1

curl -X POST http://localhost:9999/productos \
  -H 'Content-Type: application/json' \
  -d '{
    "codigo": "PROD-001",
    "nombre": "Mouse inalámbrico",
    "descripcion": "Mouse ergonómico inalámbrico con receptor USB",
    "precio": 25.50,
    "moneda_id": 1,
    "bodega_id": 1,
    "sucursal_id": 1,
    "materiales": [1, 2]
  }'

curl -X PUT http://localhost:9999/productos/1 \
  -H 'Content-Type: application/json' \
  -d '{
    "codigo": "PROD-001",
    "nombre": "Mouse actualizado",
    "descripcion": "Mouse ergonómico actualizado con receptor USB",
    "precio": 29.99,
    "moneda_id": 1,
    "bodega_id": 1,
    "sucursal_id": 1,
    "materiales": [1, 3]
  }'

curl -X DELETE http://localhost:9999/productos/1
```
