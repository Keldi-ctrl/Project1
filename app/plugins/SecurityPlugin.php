<?php
namespace Growave2\Plugins;

use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;
use Growave2\Models\Roles;
use Growave2\Models\Permissions;



class SecurityPlugin extends Component
{

    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * The file path of the ACL cache file.
     *
     * @var string
     */
    private $filePath;

    /**
     * Define the resources that are considered "private". These controller => actions require authentication.
     *
     * @var array
     */
    private $privateResources = array();

    /**
     * Checks if a controller is private or not
     *
     * @param string $controllerName
     * @return boolean
     */
    public function isPrivate($controllerName)
    {
        $controllerName = strtolower($controllerName);
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     *
     * @param string $role
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($role, $controller, $action)
    {
        return $this->getAcl()->isAllowed($role, $controller, $action);
    }

    /**
     * Returns the ACL list
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }
        // Check if the ACL is in APC
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch('vokuro-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }

        $filePath = $this->getFilePath();

        // Check if the ACL is already generated
        if (!file_exists($filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Get the ACL from the data file
        $data = file_get_contents($filePath);
        $this->acl = unserialize($data);

        // Store the ACL in APC
        if (function_exists('apc_store')) {
            apc_store('vokuro-acl', $this->acl);
        }

        return $this->acl;
    }

    /**
     * Returns the permissions assigned to a profile
     *
     * @param Roles $role
     * @return array
     */
    public function getPermissions(Roles $role)
    {
        $permissions = [];
        foreach ($role->permissions as $permission) {
            $permissions[$permission->resource . '.' . $permission->action] = true;
        }
        return $permissions;
    }

    /**
     * Returns all the resources and their actions available in the application
     *
     * @return array
     */
    public function getResources()
    {
        return $this->privateResources;
    }

    /**
     * Returns the action description according to its simplified name
     *
     * @param string $action
     * @return string
     */
    public function getActionDescription($action)
    {
        if (isset($this->actionDescriptions[$action])) {
            return $this->actionDescriptions[$action];
        } else {
            return $action;
        }
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $acl = new AclMemory();

        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        // Register roles

        $roles = Roles::find();

        foreach ($roles as $role) {
            $acl->addRole(new AclRole($role->name));
        }

        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
        }

        // Grant access to private area to role Users
        foreach ($roles as $role) {
            // Grant permissions in "permissions" model
            foreach ($role->permissions as $permission) {
                $acl->allow('admin', 'admin',
                    array(
                        'create',
                        'showAllUsers',
                        'panel',
                        'particularUser',
                        'searchDataByDate',
                        'updateTime',
                        'deleteUser',
                        'delete',
                        'editUserData',
                        'update',
                        'logout',
                        'getHolidays',
                        'manipulateHolidays',
                        'setHolidays',
                ));
                $acl->allow('admin', 'register',
                    array(
                       'index',
                    ));
                $acl->allow('user', 'time-tracker',
                    array(
                        'index',
                        'startTracking',
                        'stopTracking',
                        'show',
                        'team',
                    ));
            }
        }
        $filePath = $this->getFilePath();

        if (touch($filePath) && is_writable($filePath)) {
            file_put_contents($filePath, serialize($acl));

            // Store the ACL in APC
            if (function_exists('apc_store')) {
                apc_store('vokuro-acl', $acl);
            }
        } else {

            $this->flash->error(
                'The user does not have write permissions to create the ACL list at ' . $filePath
            );
        }

        return $acl;
    }


    /**
     * Set the acl cache file path
     *
     * @return string
     */
    protected function getFilePath()
    {
        if (!isset($this->filePath)) {
            $this->filePath = rtrim(APP_PATH . '/cache/', '\\/') . '/acl/data.txt';
        }
        return $this->filePath;
    }

    /**
     * Adds an array of private resources to the ACL object.
     *
     * @param array $resources
     */
    public function addPrivateResources(array $resources) {
        if (count($resources) > 0) {
            $this->privateResources = array_merge($this->privateResources, $resources);
            if (is_object($this->acl)) {
                $this->acl = $this->rebuild();
            }
        }
    }
}
