# WordPress REST API

---

## Table of Contents

- [Authentication](#authentication)
- [Fetching Posts](#1-fetching-posts-get)
- [Creating a Post](#2-creating-a-post-post)
- [Updating a Post](#3-updating-a-post-put)
- [Deleting a Post](#4-deleting-a-post-delete)
- [Other Endpoints](#5-working-with-other-endpoints)
- [Uploading Media](#6-uploading-media-post)
- [Custom Endpoints (PHP)](#7-custom-endpoint-php--functionsphp)
- [Query Parameters](#common-query-parameters)

---

## Authentication

Most write operations require authentication. WordPress supports **Application Passwords** (built-in since WP 5.6).

Go to **WordPress Admin → Users → Profile → Application Passwords** to generate one.

```javascript
// Base64 encode "username:app_password"
const credentials = btoa('admin:xxxx xxxx xxxx xxxx xxxx xxxx');

const headers = {
  'Authorization': `Basic ${credentials}`,
  'Content-Type': 'application/json'
};
```

---

## 1. Fetching Posts (GET)

```javascript
// Get all posts
fetch('https://yoursite.com/wp-json/wp/v2/posts')
  .then(res => res.json())
  .then(posts => console.log(posts));

// Get a single post by ID
fetch('https://yoursite.com/wp-json/wp/v2/posts/42')
  .then(res => res.json())
  .then(post => console.log(post.title.rendered));

// Get posts with query parameters
fetch('https://yoursite.com/wp-json/wp/v2/posts?per_page=5&page=1&search=hello')
  .then(res => res.json())
  .then(posts => posts.forEach(p => console.log(p.title.rendered)));
```

---

## 2. Creating a Post (POST)

```javascript
const newPost = {
  title: 'My New Post',
  content: '<p>Hello from the REST API!</p>',
  status: 'publish',   // draft | publish | private
  categories: [3],     // category IDs
  tags: [5, 8]
};

fetch('https://yoursite.com/wp-json/wp/v2/posts', {
  method: 'POST',
  headers: {
    'Authorization': `Basic ${btoa('admin:your_app_password')}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(newPost)
})
  .then(res => res.json())
  .then(post => console.log('Created post ID:', post.id));
```

---

## 3. Updating a Post (PUT)

```javascript
fetch('https://yoursite.com/wp-json/wp/v2/posts/42', {
  method: 'PUT',
  headers: {
    'Authorization': `Basic ${btoa('admin:your_app_password')}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: 'Updated Title',
    content: '<p>Updated content here.</p>'
  })
})
  .then(res => res.json())
  .then(post => console.log('Updated:', post.title.rendered));
```

---

## 4. Deleting a Post (DELETE)

```javascript
// Move to trash
fetch('https://yoursite.com/wp-json/wp/v2/posts/42', {
  method: 'DELETE',
  headers: {
    'Authorization': `Basic ${btoa('admin:your_app_password')}`,
    'Content-Type': 'application/json'
  },
})
  .then(res => res.json())
  .then(data => console.log('Deleted:', data.id));

// Force permanent delete (skip trash)
fetch('https://yoursite.com/wp-json/wp/v2/posts/42?force=true', {
  method: 'DELETE',
  headers: {
    'Authorization': `Basic ${btoa('admin:your_app_password')}`,
    'Content-Type': 'application/json'
  }
});
```

---

## 5. Working with Other Endpoints

```javascript
const base = 'https://yoursite.com/wp-json/wp/v2';

// Pages
fetch(`${base}/pages`);

// Categories
fetch(`${base}/categories`);

// Tags
fetch(`${base}/tags`);

// Users (requires auth)
fetch(`${base}/users`, { headers });

// Media
fetch(`${base}/media`);

// Comments on a specific post
fetch(`${base}/comments?post=42`);
```

---

## 6. Uploading Media (POST)

```javascript
const fileInput = document.querySelector('#file');
const file = fileInput.files[0];
const formData = new FormData();
formData.append('file', file);

fetch('https://yoursite.com/wp-json/wp/v2/media', {
  method: 'POST',
  headers: {
    'Authorization': `Basic ${btoa('admin:your_app_password')}`,
    'Content-Disposition': `attachment; filename="${file.name}"`
  },
  body: formData
})
  .then(res => res.json())
  .then(media => console.log('Uploaded media ID:', media.id));
```

---

## 7. Custom Endpoint (PHP – functions.php)

Register your own REST route inside WordPress:

```php
// In functions.php or a plugin
add_action('rest_api_init', function () {
  register_rest_route('myplugin/v1', '/greet/(?P<name>[a-zA-Z0-9-]+)', [
    'methods'             => 'GET',
    'callback'            => 'my_greet_handler',
    'permission_callback' => '__return_true' // public endpoint
  ]);
});

function my_greet_handler(WP_REST_Request $request) {
  $name = $request->get_param('name');
  return new WP_REST_Response([
    'message' => "Hello, $name!"
  ], 200);
}
```

Call it from JavaScript:

```javascript
fetch('https://yoursite.com/wp-json/myplugin/v1/greet/John')
  .then(res => res.json())
  .then(data => console.log(data.message)); // "Hello, John!"
```

---

## Common Query Parameters

| Parameter | Description | Example |
|-----------|-------------|---------|
| `per_page` | Results per page (max 100) | `?per_page=10` |
| `page` | Page number for pagination | `?page=2` |
| `search` | Keyword search | `?search=wordpress` |
| `orderby` | Field to sort by | `?orderby=date` |
| `order` | Sort direction (`asc` or `desc`) | `?order=asc` |
| `categories` | Filter by category ID | `?categories=5` |
| `status` | Filter by post status | `?status=draft` |
| `_fields` | Return only specific fields | `?_fields=id,title,slug` |

---

## Resources

- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)
- [WP REST API Reference](https://developer.wordpress.org/rest-api/reference/)
- [Application Passwords](https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/)
