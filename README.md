# tapao/custom-landing-page

A Flarum extension that serves a **fully custom HTML page** at the root path `/` with zero Flarum chrome — no header, footer, or SPA shell.

> Unlike `datlechin/flarum-landing-page` which injects content inside Flarum's layout, this extension intercepts the request at the PHP middleware layer and returns raw HTML before the Flarum SPA ever loads.

## Features

- Full `<html>` document freedom — your HTML is the whole page
- No Flarum JS, CSS, or layout loaded at all
- Guest-only mode — bypass the landing page for logged-in users
- Template variables for dynamic values (title, URL, login/register links, year)
- Auto-detects `fof/direct-links` for clean `/login` and `/register` URLs
- Compatible with Flarum **1.8.x** and **2.x**

## Installation

```bash
composer require tapao/custom-landing-page
php flarum migrate
php flarum cache:clear
```

Then enable the extension in your Flarum admin panel.

## Configuration

Go to **Admin → Extensions → Custom Landing Page** and configure:

| Setting | Description |
|---|---|
| **Enable** | Master on/off toggle |
| **Show to guests only** | When on, logged-in users see the normal forum instead |
| **Landing Page HTML** | Paste your full HTML document here |

## Template Variables

These placeholders are replaced server-side before the page is sent:

| Variable | Resolves to |
|---|---|
| `{{ forum_title }}` | Your forum's title |
| `{{ forum_url }}` | Forum base URL |
| `{{ login_url }}` | `/login` (with fof/direct-links) or `/?modal=login` |
| `{{ register_url }}` | `/register` (with fof/direct-links) or `/?modal=register` |
| `{{ year }}` | Current year (e.g. `2026`) |

### Example HTML

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ forum_title }}</title>
</head>
<body>
    <h1>Welcome to {{ forum_title }}</h1>
    <nav>
        <a href="{{ login_url }}">Login</a>
        <a href="{{ register_url }}">Register</a>
        <a href="{{ forum_url }}/discussions">Browse Forum</a>
    </nav>
    <footer>&copy; {{ year }} {{ forum_title }}</footer>
</body>
</html>
```

## Login / Register Links

### With `fof/direct-links` (recommended)

Install `fof/direct-links` for clean `/login` and `/register` routes. This extension auto-detects it and resolves `{{ login_url }}` to `/login` and `{{ register_url }}` to `/register`.

```bash
composer require fof/direct-links
```

### Without `fof/direct-links`

The `{{ login_url }}` and `{{ register_url }}` variables fall back to `/?modal=login` and `/?modal=register`. Flarum's frontend listens for these query params on boot and auto-opens the login/register modal.

> **Note:** The middleware only intercepts bare `GET /` — the `?modal=login` query param does not affect the path, so the fallback URLs correctly bypass the landing page and load the Flarum SPA.

## How It Works

A PSR-15 middleware is registered in Flarum's `web` middleware stack. On every request it checks:

1. Is the extension enabled?
2. Is this a `GET /` request?
3. Is the visitor a guest? (when guest-only mode is on)

If all conditions pass, it returns an `HtmlResponse` with your stored HTML — the rest of Flarum's request pipeline never runs.

## Building JS (for developers)

The admin settings panel requires the compiled JS asset:

```bash
cd js
npm install
npm run build
```

For development with watch mode:

```bash
npm run dev
```

## Compatibility

| Flarum | PHP | Status |
|---|---|---|
| 1.8.x | 8.1+ | Supported |
| 2.x | 8.1+ | Supported |

## License

MIT
