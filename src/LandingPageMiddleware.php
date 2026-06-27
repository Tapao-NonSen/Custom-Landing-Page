<?php

namespace Tapao\CustomLandingPage;

use Flarum\Foundation\Config;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LandingPageMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SettingsRepositoryInterface $settings,
        private Config $config
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Extension enabled?
        if (!$this->settings->get('tapao-custom-landing-page.enabled', false)) {
            return $handler->handle($request);
        }

        // 2. Only intercept GET /
        $path = rtrim($request->getUri()->getPath(), '/') ?: '/';
        if ($path !== '/' || $request->getMethod() !== 'GET') {
            return $handler->handle($request);
        }

        // 3. Guest-only mode: pass through for authenticated users
        if ($this->settings->get('tapao-custom-landing-page.guests_only', true)) {
            $actor = RequestUtil::getActor($request);
            if ($actor->exists) {
                return $handler->handle($request);
            }
        }

        // 4. Render and return
        $html = $this->settings->get('tapao-custom-landing-page.html', '');
        $html = $this->substituteVariables($html);

        return new HtmlResponse($html ?: $this->defaultPage());
    }

    private function substituteVariables(string $html): string
    {
        $baseUrl = rtrim((string) $this->config->url(), '/');
        $hasDirect = class_exists(\FoF\DirectLinks\DirectLinksServiceProvider::class);

        return str_replace(
            ['{{ forum_title }}', '{{ forum_url }}', '{{ login_url }}', '{{ register_url }}', '{{ year }}'],
            [
                $this->settings->get('forum_title', 'Forum'),
                $baseUrl,
                $hasDirect ? $baseUrl . '/login' : $baseUrl . '/?modal=login',
                $hasDirect ? $baseUrl . '/register' : $baseUrl . '/?modal=register',
                date('Y'),
            ],
            $html
        );
    }

    private function defaultPage(): string
    {
        $title = htmlspecialchars($this->settings->get('forum_title', 'Forum'));
        $baseUrl = htmlspecialchars(rtrim((string) $this->config->url(), '/'));
        $hasDirect = class_exists(\FoF\DirectLinks\DirectLinksServiceProvider::class);
        $loginUrl  = $hasDirect ? $baseUrl . '/login'    : $baseUrl . '/?modal=login';
        $registerUrl = $hasDirect ? $baseUrl . '/register' : $baseUrl . '/?modal=register';

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$title}</title>
            <style>
                body { font-family: sans-serif; text-align: center; padding: 4rem 1rem; }
                a { color: #1a73e8; text-decoration: none; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <h1>Welcome to {$title}</h1>
            <p>
                <a href="{$loginUrl}">Login</a> ·
                <a href="{$registerUrl}">Register</a> ·
                <a href="{$baseUrl}/discussions">Enter the forum</a>
            </p>
        </body>
        </html>
        HTML;
    }
}
