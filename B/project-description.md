# Test Project Outline – Module B – Social Media REST API

## Competition Time

4 hours

---

# Introduction

Module B focuses on the implementation of a secure REST API backend for a social media platform.

The API will allow users to create accounts, authenticate, manage their profiles, publish posts, interact with other users' posts, and discover content through search, hashtags, and trending topics.

### Scenario

You are building the backend of a visual social media platform.

Users can create accounts and publish posts containing an image, a description, and hashtags. Authenticated users can manage their own posts, like or unlike posts, view user profiles, and explore content published by other creators.

The frontend application will communicate with your backend through a REST API.

## General Description of Project and Tasks

In this module, you must develop a REST API backend that provides the main features of a social media platform.

You must build a backend that:

* Allows users to sign up and log in
* Protects private endpoints using token-based authentication
* Allows authenticated users to view and update their profiles
* Allows creators to create, read, update, and delete their own posts
* Supports image uploads for posts
* Allows users to like and unlike posts
* Provides a paginated home feed
* Provides paginated creator profile posts
* Supports searching and filtering posts
* Calculates trending hashtags
* Uses a relational database for persistence
* Implements request validation and consistent error responses
* Exposes a clean, frontend-ready REST API

### Competitor Information

* The backend will be tested using an HTTP client and automated tests.
* The API must reject unauthorized requests to protected endpoints.
* A relational database must be used.
* Uploaded images must be stored correctly and accessible through a public URL.
* Pagination metadata must be included in paginated responses.
* A user must only be allowed to update or delete their own posts.
* The backend must return appropriate HTTP status codes.
* The API should follow REST principles.
* The competitor is free to organize the project structure as they see fit.

## Requirements

### 1. Authentication

The API must support user registration and login.

#### Registration

Endpoint:

`POST /auth/register`

Example input:

```json
{
    "name": "John Smith",
    "username": "john_smith",
    "email": "john@example.com",
    "password": "password123",
    "passwordConfirmation": "password123"
}
```

Requirements:

* `name` is required.
* `username` is required and must be unique.
* `email` is required, valid, and unique.
* `password` is required and must contain at least 8 characters.
* `passwordConfirmation` must match the password.
* The password must be securely hashed before being stored.
* The username should not contain spaces.

Example output:

```json
{
    "message": "Account created successfully",
    "user": {
        "id": 1,
        "name": "John Smith",
        "username": "john_smith",
        "email": "john@example.com"
    },
    "token": "generated-authentication-token"
}
```

#### Login

Endpoint:

`POST /auth/login`

Example input:

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

Example output:

```json
{
    "message": "Login successful",
    "token": "generated-authentication-token",
    "user": {
        "id": 1,
        "name": "John Smith",
        "username": "john_smith"
    }
}
```

The token must be sent in subsequent protected requests using the following header:

```http
Authorization: Bearer <token>
```

#### Logout

Endpoint:

`POST /auth/logout`

Requirements:

* Authentication is required.
* The current authentication token must be invalidated.

---

### 2. User Profile

Each user must have a public profile.

A user profile includes:

| Property        | Type     | Description                              |
| --------------- | -------- | ---------------------------------------- |
| `id`            | integer  | Unique user identifier                   |
| `name`          | string   | User's display name                      |
| `username`      | string   | Unique username                          |
| `email`         | string   | User's email, visible only to the owner  |
| `bio`           | string   | Optional short biography                 |
| `profileImage`  | string   | Optional profile image URL               |
| `postsCount`    | integer  | Number of posts published by the user    |
| `likesReceived` | integer  | Total likes received on the user's posts |
| `createdAt`     | datetime | Account creation date                    |

#### Get the Authenticated User Profile

Endpoint:

`GET /profile`

Requirements:

* Authentication is required.
* The response must include the authenticated user's complete profile.
* The email may be included because the user is accessing their own profile.

#### Update the Authenticated User Profile

Endpoint:

`PUT /profile`

Possible input:

```json
{
    "name": "John Smith",
    "username": "john_dev",
    "bio": "Web developer and technology enthusiast"
}
```

Requirements:

* Authentication is required.
* The user may update their name, username, bio, and profile image.
* The username must remain unique.
* If a profile image is uploaded, it must be validated and stored correctly.

#### Get a Public Creator Profile

Endpoint:

`GET /users/:username`

Example output:

```json
{
    "id": 1,
    "name": "John Smith",
    "username": "john_dev",
    "bio": "Web developer and technology enthusiast",
    "profileImage": "/storage/profiles/john.jpg",
    "postsCount": 12,
    "likesReceived": 87,
    "createdAt": "2026-07-21T10:00:00Z"
}
```

The public profile must not expose sensitive information such as the user's email or password.

---

### 3. Posts

Authenticated users can publish posts.

Each post must contain the following information:

| Property      | Type     | Description                              |
| ------------- | -------- | ---------------------------------------- |
| `id`          | integer  | Unique post identifier                   |
| `description` | string   | Post description or caption              |
| `image`       | string   | Public URL of the uploaded image         |
| `hashtags`    | array    | Hashtags associated with the post        |
| `likesCount`  | integer  | Total number of likes                    |
| `isLiked`     | boolean  | Whether the authenticated user liked it  |
| `creator`     | object   | Basic information about the post creator |
| `createdAt`   | datetime | Post creation date                       |
| `updatedAt`   | datetime | Last post update date                    |

The creator information must include:

```json
{
    "id": 1,
    "name": "John Smith",
    "username": "john_dev",
    "profileImage": "/storage/profiles/john.jpg"
}
```

#### Create a Post

Endpoint:

`POST /posts`

Authentication is required.

The request must use `multipart/form-data`.

Possible fields:

| Field         | Type  | Required | Description                    |
| ------------- | ----- | -------- | ------------------------------ |
| `description` | text  | Yes      | Description of the post        |
| `image`       | file  | Yes      | Image associated with the post |
| `hashtags`    | array | No       | List of hashtags               |

Example hashtags:

```json
["technology", "webdevelopment", "laravel"]
```

Requirements:

* The description must not be empty.
* The image must be a valid image file.
* The image size must respect the configured maximum size.
* Hashtags must be normalized before storage.
* Hashtags must be stored without the `#` character.
* Duplicate hashtags in the same post must not be stored.
* Hashtags should be converted to lowercase.
* The authenticated user becomes the creator of the post.

Example output:

```json
{
    "message": "Post created successfully",
    "post": {
        "id": 15,
        "description": "Building a new Laravel API",
        "image": "/storage/posts/post-15.jpg",
        "hashtags": [
            "technology",
            "laravel"
        ],
        "likesCount": 0,
        "isLiked": false,
        "creator": {
            "id": 1,
            "name": "John Smith",
            "username": "john_dev",
            "profileImage": null
        },
        "createdAt": "2026-07-21T10:30:00Z",
        "updatedAt": "2026-07-21T10:30:00Z"
    }
}
```

#### Get One Post

Endpoint:

`GET /posts/:id`

The response must include:

* Post information
* Creator information
* Hashtags
* Number of likes
* Whether the authenticated user liked the post

The endpoint may be public, but when an authentication token is provided, the `isLiked` property must be calculated for the current user.

#### Update a Post

Endpoint:

`PUT /posts/:id`

Authentication is required.

The creator may update:

* Description
* Image
* Hashtags

Requirements:

* Only the creator of the post may update it.
* If a new image is uploaded, the old image must be removed.
* Hashtags must be synchronized with the new hashtag list.
* Updating a post must not remove its existing likes.

#### Delete a Post

Endpoint:

`DELETE /posts/:id`

Authentication is required.

Requirements:

* Only the creator of the post may delete it.
* The associated image must be removed from storage.
* Associated likes and hashtag relationships must also be removed.
* The operation must return an appropriate success response.

---

### 4. Creator Posts

The API must provide the posts published by a specific creator.

Endpoint:

`GET /users/:username/posts`

The response must be paginated.

Supported query parameters:

| Parameter | Type    | Description                      |
| --------- | ------- | -------------------------------- |
| `page`    | integer | Requested page number            |
| `limit`   | integer | Number of posts per page         |
| `sort`    | string  | Sort order: `latest` or `oldest` |

Example:

```http
GET /users/john_dev/posts?page=1&limit=10&sort=latest
```

Example response:

```json
{
    "data": [
        {
            "id": 15,
            "description": "Building a new Laravel API",
            "image": "/storage/posts/post-15.jpg",
            "hashtags": [
                "technology",
                "laravel"
            ],
            "likesCount": 12,
            "isLiked": false,
            "createdAt": "2026-07-21T10:30:00Z"
        }
    ],
    "pagination": {
        "currentPage": 1,
        "perPage": 10,
        "totalItems": 24,
        "totalPages": 3,
        "hasNextPage": true,
        "hasPreviousPage": false
    }
}
```

---

### 5. Home Feed

The API must provide a paginated home feed containing posts from all creators.

Endpoint:

`GET /posts`

The posts must be sorted from newest to oldest by default.

Supported query parameters:

| Parameter | Type    | Description                      |
| --------- | ------- | -------------------------------- |
| `page`    | integer | Requested page number            |
| `limit`   | integer | Number of posts per page         |
| `sort`    | string  | `latest`, `oldest`, or `popular` |
| `search`  | string  | Search inside post descriptions  |
| `hashtag` | string  | Filter posts by one hashtag      |

Example requests:

```http
GET /posts?page=1&limit=12
```

```http
GET /posts?search=laravel
```

```http
GET /posts?hashtag=technology
```

```http
GET /posts?hashtag=technology&sort=popular&page=1&limit=10
```

The `popular` sort option must sort posts by their number of likes.

The response must include pagination metadata.

Example response:

```json
{
    "data": [
        {
            "id": 15,
            "description": "Building a new Laravel API",
            "image": "/storage/posts/post-15.jpg",
            "hashtags": [
                "technology",
                "laravel"
            ],
            "likesCount": 12,
            "isLiked": true,
            "creator": {
                "id": 1,
                "name": "John Smith",
                "username": "john_dev",
                "profileImage": null
            },
            "createdAt": "2026-07-21T10:30:00Z"
        }
    ],
    "pagination": {
        "currentPage": 1,
        "perPage": 12,
        "totalItems": 45,
        "totalPages": 4,
        "hasNextPage": true,
        "hasPreviousPage": false
    }
}
```

---

### 6. Search and Filtering

The API must support searching and filtering posts.

Search must be performed through the home feed endpoint:

`GET /posts`

#### Search by Description

Example:

```http
GET /posts?search=javascript
```

Requirements:

* The search must be case-insensitive.
* Posts containing the provided text in their description must be returned.
* Empty search values must be ignored.

#### Filter by Hashtag

Example:

```http
GET /posts?hashtag=webdevelopment
```

Requirements:

* The hashtag filter must be case-insensitive.
* The API must accept the hashtag with or without the `#` character.
* Only posts associated with the selected hashtag must be returned.

The following requests must therefore produce the same result:

```http
GET /posts?hashtag=technology
```

```http
GET /posts?hashtag=%23technology
```

#### Combined Search and Filter

Search, hashtag filtering, sorting, and pagination must work together.

Example:

```http
GET /posts?search=api&hashtag=laravel&sort=popular&page=1&limit=10
```

This request must return popular Laravel posts whose description contains the term `api`.

---

### 7. Likes

Authenticated users can like or unlike posts.

A user must not be able to like the same post more than once.

#### Like a Post

Endpoint:

`POST /posts/:id/like`

Authentication is required.

Example response:

```json
{
    "message": "Post liked successfully",
    "likesCount": 13,
    "isLiked": true
}
```

Requirements:

* The post must exist.
* The authenticated user must not have already liked the post.
* A like relationship must be created between the user and the post.
* The updated number of likes must be returned.

#### Unlike a Post

Endpoint:

`DELETE /posts/:id/like`

Authentication is required.

Example response:

```json
{
    "message": "Post unliked successfully",
    "likesCount": 12,
    "isLiked": false
}
```

Requirements:

* The post must exist.
* The authenticated user must have previously liked the post.
* The existing like relationship must be removed.
* The updated number of likes must be returned.

#### Get Users Who Liked a Post

Endpoint:

`GET /posts/:id/likes`

The response must be paginated.

Example response:

```json
{
    "data": [
        {
            "id": 4,
            "name": "Emma Brown",
            "username": "emma_b",
            "profileImage": "/storage/profiles/emma.jpg"
        }
    ],
    "pagination": {
        "currentPage": 1,
        "perPage": 10,
        "totalItems": 13,
        "totalPages": 2
    }
}
```

---

### 8. Hashtags

Hashtags must be extracted from the request and associated with posts.

A hashtag includes:

| Property     | Type    | Description                       |
| ------------ | ------- | --------------------------------- |
| `id`         | integer | Unique hashtag identifier         |
| `name`       | string  | Normalized hashtag name           |
| `postsCount` | integer | Number of posts using the hashtag |

Hashtag names must:

* Be stored in lowercase
* Be stored without the `#` character
* Not contain spaces
* Be unique in the database
* Be reused when the same hashtag is used in different posts

#### Get Posts by Hashtag

Endpoint:

`GET /hashtags/:name/posts`

Supported query parameters:

* `page`
* `limit`
* `sort`

Example:

```http
GET /hashtags/technology/posts?page=1&limit=10&sort=latest
```

The response must include:

* Hashtag information
* Paginated posts
* Pagination metadata

---

### 9. Trending Hashtags

The API must provide a list of trending hashtags.

Endpoint:

`GET /hashtags/trending`

Trending hashtags must be calculated based on their usage in posts created during a recent period.

By default, the API must calculate trends using posts created during the last 7 days.

Supported query parameters:

| Parameter | Type    | Description                                |
| --------- | ------- | ------------------------------------------ |
| `limit`   | integer | Maximum number of hashtags returned        |
| `days`    | integer | Number of recent days used for calculation |

Example:

```http
GET /hashtags/trending?limit=10&days=7
```

Example output:

```json
{
    "period": {
        "days": 7,
        "from": "2026-07-14T00:00:00Z",
        "to": "2026-07-21T23:59:59Z"
    },
    "data": [
        {
            "name": "technology",
            "postsCount": 38,
            "rank": 1
        },
        {
            "name": "laravel",
            "postsCount": 25,
            "rank": 2
        },
        {
            "name": "webdevelopment",
            "postsCount": 19,
            "rank": 3
        }
    ]
}
```

Requirements:

* Only posts created during the selected period must be counted.
* Hashtags must be ordered from most used to least used.
* The default limit must be 10.
* The maximum allowed limit must be validated.
* Hashtags with the same usage count may be sorted alphabetically.

---

### 10. Image Uploads

The API must support image uploads for:

* Post images
* Profile images

Requirements:

* Only valid image types must be accepted.
* The maximum image size must be validated.
* Uploaded files must use generated unique filenames.
* File paths must not expose private server directories.
* Images must be publicly accessible through URLs.
* Replaced images must be removed when no longer used.
* Post images must be removed when their post is deleted.
* Invalid or failed requests must not leave unused files in storage.

Supported image formats may include:

* JPEG
* PNG
* WebP

---

### 11. Authorization Rules

The following authorization rules must be implemented:

| Action                  | Public | Authenticated User | Post Creator |
| ----------------------- | ------ | ------------------ | ------------ |
| Register                | Yes    | Yes                | Yes          |
| Login                   | Yes    | Yes                | Yes          |
| View home feed          | Yes    | Yes                | Yes          |
| View one post           | Yes    | Yes                | Yes          |
| View public profile     | Yes    | Yes                | Yes          |
| View creator posts      | Yes    | Yes                | Yes          |
| Search and filter posts | Yes    | Yes                | Yes          |
| View trending hashtags  | Yes    | Yes                | Yes          |
| Create a post           | No     | Yes                | Yes          |
| Like or unlike a post   | No     | Yes                | Yes          |
| Update a post           | No     | No                 | Yes          |
| Delete a post           | No     | No                 | Yes          |
| Update own profile      | No     | Yes                | Yes          |

When a user attempts to update or delete another creator's post, the API must return:

```http
403 Forbidden
```

---

### 12. Validation

The API must validate all incoming data.

Validation must include, where applicable:

* Required fields
* Unique email
* Unique username
* Password length
* Password confirmation
* Valid image type
* Maximum image size
* Description length
* Valid page and limit values
* Existing post identifiers
* Existing user identifiers
* Valid sorting options
* Valid hashtag format
* Maximum number of hashtags per post

Validation errors must use a consistent format.

Example:

```json
{
    "message": "Validation failed",
    "errors": {
        "description": [
            "The description field is required."
        ],
        "image": [
            "The image must be a valid image file."
        ]
    }
}
```

---

### 13. Error Handling

The API must return appropriate HTTP status codes.

| Status Code | Usage                                    |
| ----------- | ---------------------------------------- |
| `200`       | Successful request                       |
| `201`       | Resource successfully created            |
| `204`       | Successful request without response body |
| `400`       | Invalid request                          |
| `401`       | Missing or invalid authentication        |
| `403`       | Authenticated but not authorized         |
| `404`       | Resource not found                       |
| `409`       | Resource conflict                        |
| `422`       | Validation error                         |
| `500`       | Unexpected server error                  |

Example not-found response:

```json
{
    "message": "Post not found"
}
```

Example unauthorized response:

```json
{
    "message": "Authentication is required"
}
```

Example forbidden response:

```json
{
    "message": "You are not allowed to modify this post"
}
```

The API must not expose:

* Database queries
* Server file paths
* Stack traces
* Passwords
* Authentication tokens belonging to other users
* Internal exception details

---

### 14. Database

You are free to design the database schema as you see fit.

The database must support:

* Users
* Posts
* Hashtags
* Post-hashtag relationships
* Likes
* Authentication tokens, when required by the selected authentication system

The database relationships must support the following rules:

* One user can create many posts.
* One post belongs to one creator.
* One post can contain many hashtags.
* One hashtag can belong to many posts.
* One user can like many posts.
* One post can be liked by many users.
* One user can like a specific post only once.

The database must prevent duplicate likes through an appropriate database constraint.

The database must also prevent duplicate hashtag names.

You must provide the necessary database migrations.

You must also provide seed data containing:

* At least 5 users
* At least 20 posts
* At least 10 hashtags
* Likes distributed between different users and posts
* Posts created on different dates to test trending hashtags
* At least one user with multiple posts

Seeded passwords must be securely hashed.

---

### 15. Pagination

Pagination must be implemented for:

* Home feed
* Creator posts
* Posts filtered by hashtag
* Users who liked a post

Every paginated response must include:

```json
{
    "pagination": {
        "currentPage": 1,
        "perPage": 10,
        "totalItems": 50,
        "totalPages": 5,
        "hasNextPage": true,
        "hasPreviousPage": false
    }
}
```

Requirements:

* Invalid page numbers must be handled correctly.
* The API must define a default page size.
* The API must define a maximum page size.
* Filtering must be applied before pagination.
* Sorting must be applied before pagination.

---

### 16. API Documentation

The project must include API documentation.

The documentation must describe:

* Available endpoints
* HTTP methods
* Authentication requirements
* Request parameters
* Request body formats
* Validation rules
* Response formats
* Possible error responses

The API may be documented using:

* OpenAPI
* Swagger
* Postman collection
* A structured Markdown document

The documentation must include example requests and responses.

---

### 17. Required Endpoints Summary

#### Authentication

| Method | Endpoint         | Authentication | Description                  |
| ------ | ---------------- | -------------- | ---------------------------- |
| POST   | `/auth/register` | Public         | Create a user account        |
| POST   | `/auth/login`    | Public         | Authenticate a user          |
| POST   | `/auth/logout`   | Required       | Invalidate the current token |

#### Profiles

| Method | Endpoint                 | Authentication | Description                    |
| ------ | ------------------------ | -------------- | ------------------------------ |
| GET    | `/profile`               | Required       | Get authenticated user profile |
| PUT    | `/profile`               | Required       | Update authenticated profile   |
| GET    | `/users/:username`       | Public         | Get public creator profile     |
| GET    | `/users/:username/posts` | Public         | Get paginated creator posts    |

#### Posts

| Method | Endpoint     | Authentication | Description                 |
| ------ | ------------ | -------------- | --------------------------- |
| GET    | `/posts`     | Public         | Get the paginated home feed |
| POST   | `/posts`     | Required       | Create a post               |
| GET    | `/posts/:id` | Public         | Get one post                |
| PUT    | `/posts/:id` | Creator only   | Update a post               |
| DELETE | `/posts/:id` | Creator only   | Delete a post               |

#### Likes

| Method | Endpoint           | Authentication | Description                |
| ------ | ------------------ | -------------- | -------------------------- |
| POST   | `/posts/:id/like`  | Required       | Like a post                |
| DELETE | `/posts/:id/like`  | Required       | Unlike a post              |
| GET    | `/posts/:id/likes` | Public         | Get users who liked a post |

#### Hashtags

| Method | Endpoint                | Authentication | Description               |
| ------ | ----------------------- | -------------- | ------------------------- |
| GET    | `/hashtags/trending`    | Public         | Get trending hashtags     |
| GET    | `/hashtags/:name/posts` | Public         | Get posts using a hashtag |

---

## Technical Requirements

The project must:

* Use a backend framework that supports REST API development
* Use a relational database
* Use migrations
* Use seeders or fixtures
* Use secure password hashing
* Use token-based authentication
* Use authorization middleware or policies
* Validate all incoming requests
* Support multipart image uploads
* Use appropriate database relationships
* Avoid duplicated database queries where possible
* Return JSON responses
* Use correct HTTP methods and status codes
* Include clear setup instructions
* Include API documentation
* Include meaningful Git commits

The following practices are recommended:

* Separate controllers, services, models, and validation logic
* Use resource or serializer classes for API responses
* Use transactions when several database operations must succeed together
* Use eager loading to avoid unnecessary queries
* Use reusable pagination response structures
* Use environment variables for database and application configuration

---

## Deliverables

The competitor must submit:

1. The complete source code
2. Database migrations
3. Database seeders
4. API documentation
5. A database export if required
6. A `.env.example` file
7. A README file containing:

   * Installation instructions
   * Database configuration instructions
   * Migration and seeding commands
   * Application startup command
   * Authentication instructions
   * Public storage configuration
   * Test account credentials
8. A Git repository containing meaningful commits

The submitted project must run using the instructions provided in the README.

---

## Assessment

The module is assessed by directly interacting with the API using automated tests and HTTP clients.

Assessment focuses on:

* Authentication security
* Authorization and ownership rules
* Correct CRUD behavior
* Profile management
* Image upload handling
* Like and unlike behavior
* Search and hashtag filtering
* Pagination
* Trending hashtag calculation
* Database relationships
* Validation
* Error handling
* API response consistency
* Code structure and maintainability
* API documentation
* Git usage

## Mark Distribution

| WSOS Section | Description                            | Points |
| ------------ | -------------------------------------- | -----: |
| 1            | Work Organization and Self-Management  |      3 |
| 2            | Communication and Interpersonal Skills |      0 |
| 3            | Design Implementation                  |      0 |
| 4            | Front-End Development                  |      0 |
| 5            | Back-End Development                   |     22 |
| **Total**    |                                        | **25** |
