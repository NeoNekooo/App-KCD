<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('checkRouteActive')) {
    /**
     * Helper function to check if a route is active (considering parameters)
     *
     * @param string|null $route
     * @param array $params
     * @return bool
     */
    function checkRouteActive($route, $params)
    {
        if (!$route || !Route::has($route)) {
            return false;
        }
        if (!request()->routeIs($route)) {
            return false;
        }

        if (!empty($params)) {
            $allParamsMatch = true;
            foreach ($params as $key => $value) {
                if (request()->query($key) != $value) {
                    $allParamsMatch = false;
                    break;
                }
            }
            return $allParamsMatch;
        } else {
            // If no specific params, ensure URL also clean (no extra query strings)
            return count(request()->query()) == 0;
        }
    }
}

if (!function_exists('checkMenuStatusRecursive')) {
    /**
     * Check for active state recursively for current menu item or any of its descendants.
     *
     * @param \App\Models\Menu $menu
     * @param bool $currentRouteIsActive
     * @return array ['isActive' => bool, 'isOpen' => bool]
     */
    function checkMenuStatusRecursive($menu, $currentRouteIsActive = false)
    {
        if ($currentRouteIsActive) {
            return ['isActive' => true, 'isOpen' => true];
        }
        
        $hasActiveChild = false;
        if ($menu->childrenRecursive->isNotEmpty()) {
            foreach ($menu->childrenRecursive as $child) {
                $childRouteActive = checkRouteActive($child->route, $child->params);
                $status = checkMenuStatusRecursive($child, $childRouteActive);
                if ($status['isActive'] || $status['isOpen']) {
                    $hasActiveChild = true;
                    break;
                }
            }
        }
        return ['isActive' => $currentRouteIsActive, 'isOpen' => $hasActiveChild];
    }
}
