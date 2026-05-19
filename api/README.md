# PHP API

REST API on **http://localhost:9999**.

## Run

```bash
docker compose up -d --build
```

## Endpoints

| Method | Path        | Description              |
|--------|-------------|--------------------------|
| GET    | `/`         | API info                 |
| GET    | `/health`   | Database health check    |
| POST   | `/productos`| Create a product         |

```bash
curl http://localhost:9999/

curl http://localhost:9999/health

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
```
