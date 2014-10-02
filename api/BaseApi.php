<?php

use ClubSpeed\Enums\Enums as Enums;

abstract class BaseApi {

    public $restler;
    protected $interface;
    protected $mapper;
    protected $access;
    protected $logic;

    function __construct() {
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = $GLOBALS['logic'];
        $this->access = array(
            'post'   => Enums::API_PRIVATE_ACCESS,
            'get'    => Enums::API_PRIVATE_ACCESS,
            'put'    => Enums::API_PRIVATE_ACCESS,
            'delete' => Enums::API_PRIVATE_ACCESS,
            'all'    => Enums::API_PRIVATE_ACCESS
        );
    }

    protected function validate($call) {
        if (isset($call) && isset($this->access[$call]) && !empty($this->access[$call])) {
            switch ($this->access[$call]) {
                case Enums::API_NO_ACCESS:
                    throw new RestException(404);
                case Enums::API_PUBLIC_ACCESS:
                    if (!\ClubSpeed\Security\Authenticate::publicAccess())
                        throw new RestException(401, "Invalid authorization!");
                    break;
                case Enums::API_PRIVATE_ACCESS:
                default:
                    if (!\ClubSpeed\Security\Authenticate::privateAccess())
                        throw new RestException(401, "Invalid authorization!");
                    break;
            }
        }
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) // or throw 404?
            throw new RestException(401, "Invalid authorization!");
    }

    public function post($id, $request_data = null) {
        $this->validate('post');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
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

    public function get($id, $request_data = null) {
        $this->validate('get');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            if (isset($id)) {
                return $this->mapper->mutate($id, $request_data, function($id) use (&$interface) {
                    return $interface->get($id);
                });
            }
            else {
                if (\ClubSpeed\Utility\Params::hasNonReservedData($request_data)) {
                    if (\ClubSpeed\Utility\Params::isFilter($request_data)) {
                        return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
                            return $interface->find($mapped);
                        });
                    }
                    else {
                        return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
                            return $interface->match($mapped);
                        });
                    }
                }
                else {
                    $this->validate('all'); // special case, sometimes we want to deny this separate from the other gets
                    return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
                        return $interface->all();
                    });
                }
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

    public function put($id, $request_data = null) {
        $this->validate('put');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            return $this->mapper->mutate($id, $request_data, function($id, $mapped) use (&$interface) {
                return $interface->update($id, $mapped);
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

    public function delete($id) {
        $this->validate('delete');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            return $this->mapper->mutate($id, function($id) use (&$interface) {
                return $interface->delete($id);
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

    // public function index($request_data = null) {
    //     // if (!$this->validateAccess(__FUNCTION__))
    //     //     throw new RestException(401, "Invalid authorization!");
    //     try {
    //         $interface =& $this->interface; // PHP 5.3 hack
    //         if (\ClubSpeed\Utility\Params::hasNonReservedData($request_data)) {
    //             if (\ClubSpeed\Utility\Params::isFilter($request_data)) {
    //                 return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
    //                     return $interface->find($mapped);
    //                 });
    //             }
    //             else {
    //                 return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
    //                     return $interface->match($mapped);
    //                 });
    //             }
    //         }
    //         else {
    //             return $this->mapper->mutate(function() use (&$interface) {
    //                 return $interface->all();
    //             });
    //         }
    //     }
    //     catch (RestException $e) {
    //         throw $e;
    //     }
    //     catch (CSException $e) {
    //         throw new RestException($e->getCode() ?: 412, $e->getMessage());
    //     }
    //     catch (Exception $e) {
    //         throw new RestException(500, $e->getMessage());
    //     }
    // }
}