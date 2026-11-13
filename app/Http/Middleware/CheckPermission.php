<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $menu, ?string $action = null): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role === 'admin') {
            return $next($request);
        }

        // Jika action tidak ditentukan secara eksplisit, tentukan otomatis berdasarkan HTTP Method / Route Name
        if ($action === null) {
            $method = strtoupper($request->method());
            $routeName = (string) optional($request->route())->getName();

            if ($method === 'DELETE' || str_ends_with($routeName, '.destroy') || str_contains($routeName, 'destroy') || str_contains($routeName, 'delete')) {
                $action = 'delete';
            } elseif (in_array($method, ['PUT', 'PATCH'], true) || str_ends_with($routeName, '.edit') || str_ends_with($routeName, '.update') || str_contains($routeName, 'update')) {
                $action = 'edit';
            } elseif ($method === 'POST' || str_ends_with($routeName, '.create') || str_ends_with($routeName, '.store') || str_contains($routeName, 'store') || str_contains($routeName, 'import') || str_contains($routeName, 'sync')) {
                $action = 'create';
            } else {
                $action = 'view';
            }
        }

        if (!$user->hasPermission($menu, $action)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => "Anda tidak memiliki izin akses ({$action}) pada menu ini."
                ], 403);
            }

            // Jika gagal pada akses 'view' ke dashboard utama setelah login, arahkan ke halaman pertama yang diizinkan
            if ($action === 'view' && $menu === 'utama_dashboard') {
                $fallback = $user->firstAccessibleRoute();
                if ($fallback !== 'utama.dashboard') {
                    return redirect()->route($fallback);
                }
            }

            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES (' . strtoupper($action) . ') UNTUK FITUR / MENU INI.');
        }

        return $next($request);
    }
}
