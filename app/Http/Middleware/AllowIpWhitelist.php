<?php

namespace App\Http\Middleware;

use App\Models\AllowedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class AllowIpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        // dd(config('security.ip_whitelist_enabled'));
        if (! config('security.ip_whitelist_enabled', false)) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->role == 1) {
            return $next($request);
        }

        $allowedIps = $this->getAllowedIps();
        $clientIp = $this->getClientIp($request);

        // When list is empty and whitelist is on: allow no one (except localhost in local env below)
        if (empty($allowedIps)) {
            
            if (app()->environment('local') && in_array($clientIp, ['127.0.0.1', '::1'], true)) {
                return $next($request);
            }
            return response()->view('errors.ip-not-allowed', ['ip' => $clientIp], 403);
        }

        if (app()->environment('local') && in_array($clientIp, ['127.0.0.1', '::1'], true)) {
            return $next($request);
        }
        
        if (! $this->isIpAllowed($clientIp, $allowedIps)) {
            return response()->view('errors.ip-not-allowed', ['ip' => $clientIp], 403);
        }

        return $next($request);
    }

    protected function isIpAllowed(string $clientIp, array $allowedIps): bool
    {
        foreach ($allowedIps as $entry) {
            $entry = trim($entry);
            if ($entry === '') continue;
            if (str_contains($entry, '/')) {
                if ($this->ipInCidr($clientIp, $entry)) return true;
            } elseif ($clientIp === $entry) {
                return true;
            }
        }
        return false;
    }

    protected function ipInCidr(string $ip, string $cidr): bool
    {
        if (! str_contains($cidr, '/')) return $ip === trim($cidr);
        [$subnet, $bits] = explode('/', $cidr, 2);
        $subnet = trim($subnet);
        $bits = (int) $bits;
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && str_contains($subnet, '.') && $bits >= 0 && $bits <= 32) {
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            if ($ipLong === false || $subnetLong === false) return false;
            $mask = -1 << (32 - $bits);
            $subnetLong &= $mask;
            return ($ipLong & $mask) === $subnetLong;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $this->ipv6InCidr($ip, $cidr);
        }
        return false;
    }

    protected function ipv6InCidr(string $ip, string $cidr): bool
    {
        $ipBin = inet_pton($ip);
        [$subnet, $bits] = explode('/', $cidr, 2);
        $subnetBin = @inet_pton(trim($subnet));
        if ($ipBin === false || $subnetBin === false || strlen($ipBin) !== 16) return false;
        $bits = (int) $bits;
        if ($bits < 0 || $bits > 128) return false;
        $bytes = (int) ceil($bits / 8);
        for ($i = 0; $i < $bytes; $i++) {
            $mask = $i === $bytes - 1 ? 0xFF << (8 - $bits % 8) : 0xFF;
            if ((ord($ipBin[$i]) & $mask) !== (ord($subnetBin[$i]) & $mask)) return false;
        }
        return true;
    }

    protected function getAllowedIps(): array
    {
        $fromConfig = config('security.allowed_ips', []);
        $fromDb = [];
        if (Schema::hasTable('allowed_ips')) {
            try {
                $fromDb = AllowedIp::pluck('ip_address')->map(fn ($ip) => trim($ip))->filter()->values()->all();
            } catch (\Throwable $e) {}
        }
        return array_values(array_unique(array_merge($fromConfig, $fromDb)));
    }

    protected function getClientIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            $ips = array_map('trim', explode(',', $forwarded));
            $clientIp = $ips[0] ?? '';
            if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP)) return $clientIp;
        }
        $realIp = $request->header('X-Real-IP');
        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP)) return $realIp;
        return $request->ip() ?? '';
    }
}