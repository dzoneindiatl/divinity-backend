<?php

namespace App\Traits;

use Spatie\Permission\Models\Permission;

trait HasPermissions
{
    /**
     * Check if the current user has permission for the given action.
     *
     * @param string $permission
     * @return bool
     */
    public function checkPermission(string $permission)
    {
        $user = auth()->user();
        return $user && $user->can($permission);
    }

    /**
     * Automatically apply permissions to controller methods.
     */
    public function applyPermissions()
    {
        if ($this->shouldSkipPermissions() || (auth()->check() && auth()->user()->hasRole('Super_Admin'))) {
            \Log::info('Permissions skipped for Super_Admin or excluded route.');
            return;
        }

        $permissions = $this->mapPermissions();

        // Log mapped permissions for debugging
        \Log::info('Mapped Permissions:', $permissions);

        // Apply middleware dynamically based on permissions
        foreach ($permissions as $action => $permission) {
            $methods = $this->getControllerMethodsForAction($action);
            if ($methods) {
                \Log::info("Applying middleware for action: {$action} with permission: {$permission} on methods:", $methods);
                $this->middleware("permission:{$permission}")->only($methods);
            }
        }
    }

    /**
     * Map permissions based on menu_name and predefined actions.
     *
     * @return array
     */
    protected function mapPermissions()
    {
        $permissions = [];
        $controllerName = class_basename(request()->route()->getController());

        // Log the current controller name for debugging
        \Log::info('Current Controller:', [$controllerName]);

        $permissionsGrouped = Permission::groupBy('menu_name')->get();

        $actionKeywords = ['view', 'create', 'edit', 'delete'];

        foreach ($permissionsGrouped as $permissionGroup) {
            if ($permissionGroup->menu_name !== strtolower($controllerName)) {
                continue;
            }

            $allPermissions = Permission::where('menu_name', $permissionGroup->menu_name)->get();

            foreach ($allPermissions as $permission) {
                foreach ($actionKeywords as $action) {
                    if (strpos($permission->name, $action) !== false) {
                        $permissions[$action] = $permission->name;
                        break;
                    }
                }
            }
        }

        return $permissions;
    }

    /**
     * Get controller methods corresponding to a specific action.
     *
     * @param string $action
     * @return array
     */
    protected function getControllerMethodsForAction(string $action)
    {
        $methodMapping = [
            'view' => ['index'],
            'create' => ['create', 'store'],
            'edit' => ['edit', 'update'],
            'delete' => ['destroy'],
        ];

        return $methodMapping[$action] ?? [];
    }

    /**
     * Determine if permissions should be skipped.
     *
     * @return bool
     */
    protected function shouldSkipPermissions()
    {
        $excludedRoutes = [
            'login',
            'register',
            'password.request',
            'password.reset',
        ];

        $currentRouteName = request()->route()->getName();
        return in_array($currentRouteName, $excludedRoutes);
    }
}
