# Social Media REST API — Module B

A REST API backend for a visual social media platform: accounts, token
auth, profiles, posts with images, likes, search/filtering, hashtags, and
trending-hashtag calculation.

---

## 1. Database configuration

Edit `.env` and set `DB_CONNECTION` to whichever engine you're using:

**MySQL / MariaDB** (default in `.env.example`):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=social_media_api
DB_USERNAME=root
DB_PASSWORD=
```

## 2. Migrate and seed

```bash
php artisan migrate
php artisan db:seed
```

This creates:
- 5 users (all with password `password123`)
- 24 posts, spread across the last 14 days (so `GET /hashtags/trending`
  has meaningful data at various `days` windows), including one user
  (`aymen_rachdi`) with several posts
- 10 hashtags, attached to posts with 1–3 tags each
- Likes distributed across different users and posts
- A real placeholder image file written to the `public` disk for every
  seeded post, so seeded `image` URLs actually resolve

Run both in one step instead with:
```bash
php artisan migrate --seed
```

## 3. Public storage configuration

Uploaded images (posts and profile pictures) are stored on the `public`
disk (`storage/app/public`) and served through `storage/posts/...` and
`storage/profiles/...` public URLs. Laravel needs the symlink from
`public/storage` to `storage/app/public` to exist:

```bash
php artisan storage:link
```

Without this step, uploaded/seeded images will return 404 even though the
API reports a URL for them.

## 4. Run the application

```bash
php artisan serve
```

The API is now available at `http://localhost:8000/api`.

## 5. Authentication

1. `POST /api/auth/register` or `POST /api/auth/login` returns a `token`.
2. Send it on every protected request:
   ```http
   Authorization: Bearer <token>
   ```
3. `POST /api/auth/logout` invalidates that specific token.

Tokens are plain random strings; only their SHA-256 hash is stored
(`personal_access_tokens.token`). They expire after
`AUTH_TOKEN_TTL_MINUTES` (default 30 days — see `.env`).

## 6. Test account

```
email:    youness@gmail.com
username: aymen_rachdi
password: password123
```
