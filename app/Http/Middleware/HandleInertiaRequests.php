<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'auth.user' => fn () => $request->user()
            ? $request->user()->only('id', 'name', 'email')
            : null,
            'auth.roles' => fn () => $request->user()
            ? $request->user()->roles->map(function ($role) {
                return $role['name'];
            })->all()
            : [],
            'auth.permissions' => fn () => $request->user()
            ? $request->user()->roles->flatMap(function ($role) {
                return $role->permissions;
            })->map(function ($permission) {
                return $permission['name'];
            })->all()
            : []
        ]);
    }
}
