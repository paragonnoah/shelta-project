<?php

namespace Botble\Optimize\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use OptimizerHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class PageSpeed
{
    /**
     * Apply rules.
     */
    abstract public function apply(string $buffer): string;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! OptimizerHelper::isEnabled()
            || $request->segment(1) == '_debugbar'
            || $request->expectsJson()
            || in_array($response->headers->get('Content-Type'), ['application/json', 'application/pdf'])
            || $response instanceof BinaryFileResponse
        ) {
            return $response;
        }

        if ($response instanceof Response) {
            if (! $this->shouldProcessPageSpeed($request)) {
                return $response;
            }

            $html = $response->getContent();
            $newContent = $this->apply($html);

            return $response->setContent($newContent);
        }

        return $response;
    }

    /**
     * Replace content response.
     */
    protected function replace(array $replace, string $buffer): string
    {
        return preg_replace(array_keys($replace), array_values($replace), $buffer);
    }

    /**
     * Check Page Speed is enabled or not
     */
    protected function isEnable(): bool
    {
        return (bool)setting('optimize_page_speed_enable', false);
    }

    /**
     * Should Process
     */
    protected function shouldProcessPageSpeed(Request $request): bool
    {
        if (! $this->isEnable()) {
            return false;
        }

        $patterns = config('packages.optimize.general.skip', []);
        $patterns = empty($patterns) ? [] : $patterns;

        foreach ($patterns as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }
}
