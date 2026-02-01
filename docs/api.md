# API Documentation

## Authentication

API requests use token-based authentication. The token must be sent in the `Authorization` header in the format `Bearer {token}`.

```http
Authorization: Bearer your-api-token-here
```

## Endpoints

### Users

#### Get User List

```http
GET /api/users
```

Query parameters:
- `filters`: Filters
- `order_by`: Sorting
- `limit`: Result count limit

Example response:
```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2024-03-20T12:00:00Z",
        "created_at": "2024-03-20T12:00:00Z",
        "updated_at": "2024-03-20T12:00:00Z"
    }
]
```

#### Get User by ID

```http
GET /api/users/{id}
```

Example response:
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": "2024-03-20T12:00:00Z",
    "created_at": "2024-03-20T12:00:00Z",
    "updated_at": "2024-03-20T12:00:0Z"
}
```

#### Create New User

```http
POST /api/users
```

Request parameters:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password"
}
```

#### Update User

```http
PUT /api/users/{id}
```

Request parameters:
```json
{
    "name": "John Doe",
    "email": "john@example.com"
}
```

#### Delete User

```http
DELETE /api/users/{id}
```

#### Count Users

```http
GET /api/users/count
```

Query parameters:
- `filters`: Filters

Example response:
```json
{
    "count": 10
}
```

## Status Codes

- `200`: Success
- `201`: Created successfully
- `204`: Deleted successfully
- `400`: Bad request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not found
- `422`: Validation error
- `500`: Server error

## Errors

In case of an error, the response will be as follows:

```json
{
    "error": "Error message"
}
```

## Filters

Filters are sent as an array of conditions:

```json
{
    "filters": [
        {
            "column": "name",
            "operator": "like",
            "value": "%John%"
        }
    ]
}
```

Allowed operators:
- `=`: Equal
- `!=`: Not equal
- `>`: Greater than
- `>=`: Greater than or equal
- `<`: Less than
- `<=`: Less than or equal
- `like`: Like
- `not like`: Not like
- `in`: In
- `not in`: Not in
- `null`: Null
- `not null`: Not null

## Sorting

Sorting is sent as an object with two properties:

```json
{
    "order_by": {
        "column": "created_at",
        "direction": "desc"
    }
}
```

Allowed directions:
- `asc`: Ascending
- `desc`: Descending

## Result Limit

The result limit is sent as an integer:

```json
{
    "limit": 10
}
```
