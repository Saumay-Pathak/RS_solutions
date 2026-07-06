# Get all Products

Fetches products with pagination.

# OpenAPI definition

```json
{
  "openapi": "3.0.1",
  "info": {
    "title": "Realtime Biometrics Public API",
    "version": "v1"
  },
  "servers": [
    {
      "url": "https://app.realtimebiometrics.net/api",
      "description": "Production API server"
    }
  ],
  "paths": {
    "/content/products": {
      "get": {
        "tags": [
          "Products"
        ],
        "summary": "Get all Products",
        "description": "Fetches products with pagination. Use per_page and page query parameters to page through results.",
        "parameters": [
          {
            "name": "page",
            "in": "query",
            "schema": {
              "type": "integer",
              "format": "int32"
            }
          },
          {
            "name": "per_page",
            "in": "query",
            "description": "Represents how many results you'd like to retrieve per request (page). Common values: 20, 50, 100.",
            "schema": {
              "type": "integer",
              "format": "int32"
            }
          },
          {
            "name": "slug",
            "in": "query",
            "description": "Optional filter by product slug.",
            "schema": {
              "type": "string"
            }
          },
          {
            "name": "category_id",
            "in": "query",
            "description": "Optional filter by category id.",
            "schema": {
              "type": "string"
            }
          }
        ],
        "responses": {
          "200": {
            "description": "Returns the product list successfully",
            "content": {
              "application/json": {
                "schema": {
                  "$ref": "#/components/schemas/ProductsResponse"
                }
              }
            }
          },
          "429": {
            "description": "Too Many Requests"
          }
        }
      }
    }
  },
  "components": {
    "schemas": {
      "Product": {
        "type": "object",
        "properties": {
          "id": { "type": "string" },
          "title": { "type": "string" },
          "slug": { "type": "string" },
          "description": { "type": "string", "nullable": true },
          "image": { "type": "string", "nullable": true },
          "updated_at": { "type": "string", "nullable": true },
          "created_at": { "type": "string", "nullable": true }
        },
        "additionalProperties": true
      },
      "ProductsResponse": {
        "type": "object",
        "properties": {
          "success": { "type": "boolean" },
          "data": {
            "type": "array",
            "items": { "$ref": "#/components/schemas/Product" }
          }
        },
        "additionalProperties": true
      }
    }
  }
}
```
