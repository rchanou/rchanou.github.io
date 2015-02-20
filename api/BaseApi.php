<?php

use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Security\Authenticate as Authenticate;
use ClubSpeed\Database\Helpers\UnitOfWork as UnitOfWork;

abstract class BaseApi {

    public $restler;
    protected $interface;   // Logic instance
    protected $mapper;      // Mapper instance
    protected $access;
    protected $logic;       // LogicService
    protected $mappers;     // MapperService

    function __construct() {
        $this->logic = $GLOBALS['logic'];       // these should really be injected,
        $this->mappers = $GLOBALS['mappers'];   // but we don't have a choice with restler v2.
        $this->access = array(
            'all'    => Enums::API_PRIVATE_ACCESS,
            'delete' => Enums::API_PRIVATE_ACCESS,
            'filter' => Enums::API_PRIVATE_ACCESS,
            'get'    => Enums::API_PRIVATE_ACCESS,
            'match'  => Enums::API_PRIVATE_ACCESS,
            'post'   => Enums::API_PRIVATE_ACCESS,
            'put'    => Enums::API_PRIVATE_ACCESS
        );
    }

    protected function validate(/* $call, $id */) {
        $args = func_get_args();
        $call = array_shift($args);
        if (isset($call) && isset($this->access[$call]) && !empty($this->access[$call])) {
            switch ($this->access[$call]) {
                case Enums::API_NO_ACCESS:
                    throw new RestException(404);
                case Enums::API_FREE_ACCESS:
                    break;
                case Enums::API_PUBLIC_ACCESS:
                    if (!Authenticate::publicAccess())
                        throw new RestException(403, "Invalid authorization!");
                    break;
                case Enums::API_CUSTOMER_ACCESS:
                    if (!Authenticate::customerAccess(array_shift($args)))
                        throw new RestException(403, "Invalid authorization");
                    break;
                case Enums::API_PRIVATE_ACCESS: // fall-through to default
                default:
                    if (!Authenticate::privateAccess())
                        throw new RestException(403, "Invalid authorization!");
                    break;
            }
        }
        else {
            if (!\ClubSpeed\Security\Authenticate::privateAccess())
                throw new RestException(403, "Invalid authorization!");
        }
    }

    /**
     * @url POST /
     */
    public function post($request_data = null) {
        $this->validate('post');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            return $this->mapper->mutate($request_data, function($mapped = array()) use (&$interface) {
                return $interface->create($mapped);
            });
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * @url GET /
     */
    public function get($request_data = null) {
        // no routing id was passed
        // figure out which call the user actually wants - all, match, or filter
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            if (\ClubSpeed\Utility\Params::hasNonReservedData($request_data)) {
                if (\ClubSpeed\Utility\Params::isFilter($request_data)) {
                    $this->validate('filter');
                    return $this->mapper->mutate($request_data, function($mapped = array()) use (&$interface) {
                        return $interface->find($mapped);
                    });
                }
                else {
                    $this->validate('match');
                    return $this->mapper->mutate($request_data, function($mapped = array()) use (&$interface) {
                        return $interface->match($mapped);
                    });
                }
            }
            else {
                $this->validate('all');
                return $this->mapper->mutate($request_data, function() use (&$interface) {
                    return $interface->all();
                });
            }
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * The function to be used by all route extensions of get, 
     * and works regardless of the number of routing ids passed.
     */
    protected function _get() {
        $getArgs = func_get_args();
        $validateId = @$getArgs[0] ?: null; // only look for the first one, for the bandaid customer authentication
        $this->validate('get', $validateId);
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            $callback = function() use (&$interface) {
                return call_user_func_array(array($interface, 'get'), func_get_args());
            };
            $mutateArgs = func_get_args();
            array_push($mutateArgs, $callback);
            return call_user_func_array(array($this->mapper, 'mutate'), $mutateArgs);
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * @url GET /:id
     *
     * Note: Restler expects route params to be explicitly set in the parameter list.
     *       For this reason, we are using get1 and mapping it to _get,
     *       which can be used by any number of ids.
     *
     *       This convention can be used to interface with multiple ids
     *       following get1, get2, get3, etc.
     */
    public function get1($id, $request_data) {
        return call_user_func_array(array($this, '_get'), func_get_args()); // ~ 9ms
    }

    protected function _put() {
        $putArgs = func_get_args();
        $validateId = @$putArgs[0] ?: null; // only look for the first one, for the bandaid customer authentication
        $this->validate('put', $validateId);
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            $callback = function() use (&$interface) {
                $closureArgs = func_get_args();
                return call_user_func_array(array($interface, 'update'), $closureArgs);
            };
            $mutateArgs = func_get_args();
            array_push($mutateArgs, $callback);
            return call_user_func_array(array($this->mapper, 'mutate'), $mutateArgs);
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * @url PUT /:id1
     *
     * Note: Restler expects route params to be explicitly set in the parameter list.
     *       For this reason, we are using put and mapping it to _put,
     *       which can be used by any number of ids.
     */
    public function put1($id1, $request_data) {
        return call_user_func_array(array($this, '_put'), func_get_args());
    }

    protected function _delete() {
        $this->validate('delete');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            $callback = function() use (&$interface) {
                $closureArgs = func_get_args();
                return call_user_func_array(array($interface, 'delete'), $closureArgs);
            };
            $mutateArgs = func_get_args();
            array_push($mutateArgs, $callback);
            return call_user_func_array(array($this->mapper, 'mutate'), $mutateArgs);
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:id1
     */
    public function delete1($id1) {
        $this->validate('delete');
        return call_user_func_array(array($this, '_delete'), func_get_args());
    }
}