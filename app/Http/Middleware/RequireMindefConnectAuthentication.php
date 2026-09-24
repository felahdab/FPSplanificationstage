<?php

namespace Modules\FPSplanificationstage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMindefConnectAuthentication
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (
            $request->user()
            && filled(
                $request->user()->sub
            )
        ) {
            return $next(
                $request
            );
        }

        if ($request->isMethod('GET')) {
            $request->session()->put(
                'url.intended',
                $request->fullUrl()
            );
        }

        return redirect()->route(
            $this->mindefConnectIsConfigured()
                ? 'keycloak.login.redirect'
                : 'login'
        );
    }

    private function mindefConnectIsConfigured(): bool
    {
        foreach (
            [
                'client_id',
                'client_secret',
                'redirect',
                'base_url',
                'realms',
            ] as $configurationKey
        ) {
            if (
                blank(
                    config(
                        'services.keycloak.' . $configurationKey
                    )
                )
            ) {
                return false;
            }
        }

        return true;
    }
}
