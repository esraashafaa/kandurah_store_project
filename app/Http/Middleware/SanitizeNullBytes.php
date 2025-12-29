<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeNullBytes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تنظيف جميع البيانات من null bytes
        $this->sanitizeRequest($request);
        
        return $next($request);
    }

    /**
     * تنظيف Request من null bytes
     */
    protected function sanitizeRequest(Request $request): void
    {
        // تنظيف Query Parameters
        $query = $request->query();
        foreach ($query as $key => $value) {
            if (is_string($value)) {
                $query[$key] = str_replace("\0", '', $value);
            } elseif (is_array($value)) {
                $query[$key] = $this->sanitizeArray($value);
            }
        }
        $request->query->replace($query);

        // تنظيف Request Data
        $input = $request->input();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = str_replace("\0", '', $value);
            } elseif (is_array($value)) {
                $input[$key] = $this->sanitizeArray($value);
            }
        }
        $request->merge($input);

        // تنظيف Route Parameters
        $route = $request->route();
        if ($route) {
            $parameters = $route->parameters();
            foreach ($parameters as $key => $value) {
                if (is_string($value)) {
                    $parameters[$key] = str_replace("\0", '', $value);
                }
            }
            $route->setParameters($parameters);
        }

        // تنظيف URI
        $uri = $request->getRequestUri();
        if ($uri) {
            $uri = str_replace("\0", '', $uri);
            $request->server->set('REQUEST_URI', $uri);
        }

        // تنظيف Path
        $path = $request->path();
        if ($path) {
            $path = str_replace("\0", '', $path);
            $request->server->set('PATH_INFO', $path);
        }
    }

    /**
     * تنظيف Array بشكل recursive
     */
    protected function sanitizeArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = str_replace("\0", '', $value);
            } elseif (is_array($value)) {
                $array[$key] = $this->sanitizeArray($value);
            }
        }
        return $array;
    }
}

