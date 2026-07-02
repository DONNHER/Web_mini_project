<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TransformApiResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // 1. Automatic snake_case to camelCase transformation
            $data = $this->arrayKeysToCamelCase($data);

            // 2. Add standard metadata
            $data['meta'] = array_merge($data['meta'] ?? [], [
                'version' => '1.1',
                'timestamp' => now()->toIso8601String(),
            ]);

            $response->setData($data);

            // 3. ETag support for conditional requests
            $content = $response->getContent();
            $etag = md5($content);
            $response->header('ETag', $etag);

            if ($request->header('If-None-Match') === $etag) {
                return response()->json(null, 304)->header('ETag', $etag);
            }
        }

        return $response;
    }

    protected function arrayKeysToCamelCase(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $camelKey = Str::camel($key);
            if (is_array($value)) {
                $result[$camelKey] = $this->arrayKeysToCamelCase($value);
            } else {
                $result[$camelKey] = $value;
            }
        }
        return $result;
    }
}
