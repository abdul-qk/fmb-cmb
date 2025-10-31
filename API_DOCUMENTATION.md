# ERP FMB API Documentation

## Overview

This API provides read-only access to the ERP FMB database. All endpoints return JSON responses and follow RESTful conventions.

## Base URL

All API endpoints are prefixed with `/api`:

```
http://your-domain.com/api
```

## Authentication

Since the API is used by the same domain, it uses session-based authentication. Ensure you are authenticated via the main application before making API requests.

## Response Format

### Success Response

```json
{
  "success": true,
  "data": { ... },
  "message": "Optional message"
}
```

### Paginated Response

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://domain.com/api/endpoint?page=1",
    "last": "http://domain.com/api/endpoint?page=10",
    "prev": null,
    "next": "http://domain.com/api/endpoint?page=2"
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

## Common Query Parameters

### Pagination

- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

Example: `/api/users?page=2&per_page=20`

### Filtering

Each endpoint supports specific filters. Check the endpoint documentation for available filters.

Example: `/api/events?status=Approved&place_id=1`

### Sorting

- `sort`: Field to sort by (default varies by endpoint)
- `order`: Sort order - `asc` or `desc` (default: `desc`)

Example: `/api/users?sort=name&order=asc`

### Including Relationships

- `include`: Comma-separated list of relationships to include

Example: `/api/users/1?include=place,roles,profile`

## API Endpoints

### Core Entities

#### Users
- `GET /api/users` - List all users
- `GET /api/users/{id}` - Get user by ID

**Filters**: `place_id`, `email`  
**Includes**: `place`, `roles`, `profile`

#### Roles
- `GET /api/roles` - List all roles
- `GET /api/roles/{id}` - Get role by ID

**Filters**: `name`, `guard_name`  
**Includes**: `permissions`

#### Modules
- `GET /api/modules` - List all modules
- `GET /api/modules/{id}` - Get module by ID

**Filters**: `is_active`, `parent_id`, `slug`  
**Includes**: `parent`, `children`, `permissions`

### Location Entities

#### Countries
- `GET /api/countries` - List all countries
- `GET /api/countries/{id}` - Get country by ID

**Filters**: `code`, `name`

#### Cities
- `GET /api/cities` - List all cities
- `GET /api/cities/{id}` - Get city by ID

**Filters**: `country_id`, `name`  
**Includes**: `country`

#### Places
- `GET /api/places` - List all places
- `GET /api/places/{id}` - Get place by ID

**Filters**: `location_id`, `incharge_id`, `default`  
**Includes**: `location`, `incharge`

#### Stores
- `GET /api/stores` - List all stores
- `GET /api/stores/{id}` - Get store by ID

**Filters**: `place_id`, `default`  
**Includes**: `place`

#### Kitchens
- `GET /api/kitchens` - List all kitchens
- `GET /api/kitchens/{id}` - Get kitchen by ID

**Filters**: `place_id`, `default`  
**Includes**: `place`

### Event Entities

#### Events
- `GET /api/events` - List all events
- `GET /api/events/{id}` - Get event by ID

**Filters**: `place_id`, `status`, `date`, `date_from`, `date_to`  
**Includes**: `place`, `menus`, `menu`, `purchaseOrder`

#### Menus
- `GET /api/menus` - List all menus
- `GET /api/menus/{id}` - Get menu by ID

**Filters**: `event_id`  
**Includes**: `event`

#### Recipes
- `GET /api/recipes` - List all recipes
- `GET /api/recipes/{id}` - Get recipe by ID

**Filters**: `place_id`, `dish_id`  
**Includes**: `dish`, `place`, `recipeItems`

### Procurement Entities

#### Purchase Orders
- `GET /api/purchase-orders` - List all purchase orders
- `GET /api/purchase-orders/{id}` - Get purchase order by ID

**Filters**: `vendor_id`, `place_id`, `store_id`, `status`, `type`, `menu_id`  
**Includes**: `vendor`, `place`, `store`, `menu`, `currency`, `detail`, `events`

#### Vendors
- `GET /api/vendors` - List all vendors
- `GET /api/vendors/{id}` - Get vendor by ID

**Filters**: `city_id`, `name`, `email`  
**Includes**: `city`, `contactPerson`, `bank`, `contactPersons`, `banks`, `items`

### Inventory Entities

#### Inventories
- `GET /api/inventories` - List all inventories
- `GET /api/inventories/{id}` - Get inventory by ID

**Filters**: `store_id`, `inventory_status`, `approved_purchase_order_detail_id`  
**Includes**: `store`

#### Items
- `GET /api/items` - List all items
- `GET /api/items/{id}` - Get item by ID

**Filters**: `category_id`, `name`, `zoho_id`  
**Includes**: `itemCategory`, `vendors`, `itemBase`, `detail`

#### Item Categories
- `GET /api/item-categories` - List all item categories
- `GET /api/item-categories/{id}` - Get item category by ID

**Filters**: `name`, `division`, `zoho_id`

## Rate Limiting

API endpoints are rate-limited to prevent abuse. The default rate limit is configured in Laravel's throttle middleware.

## CORS

CORS is configured for API endpoints. Since the API is used by the same domain, CORS is handled automatically.

## Swagger Documentation

Interactive API documentation is available at:

```
http://your-domain.com/api/documentation
```

To generate/update the Swagger documentation, run:

```bash
php artisan l5-swagger:generate
```

## Database Configuration

The API reads from the `erp_fmb` database. Ensure your `.env` file is configured:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_fmb
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Example Requests

### Get all users with pagination

```bash
GET /api/users?page=1&per_page=20
```

### Get user by ID with relationships

```bash
GET /api/users/1?include=place,roles
```

### Filter events by status and place

```bash
GET /api/events?status=Approved&place_id=1
```

### Get purchase orders sorted by creation date

```bash
GET /api/purchase-orders?sort=created_at&order=desc
```

## Error Codes

- `400` - Bad Request (validation errors)
- `404` - Not Found (resource doesn't exist)
- `429` - Too Many Requests (rate limit exceeded)
- `500` - Internal Server Error

## Notes

- All endpoints are read-only (GET requests only)
- Soft-deleted records are automatically excluded
- Dates are returned in ISO 8601 format
- All timestamps are in UTC

