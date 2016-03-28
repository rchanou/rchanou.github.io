<?php

use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Security\Authenticate as Authenticate;
use ClubSpeed\Database\Helpers\UnitOfWork as UnitOfWork;
use ClubSpeed\Database\Helpers\Comparator as Comparator;
use ClubSpeed\Utility\Params;
use ClubSpeed\Containers\ParamsContainer;
use ClubSpeed\Utility\Arrays;

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
            if (!Authenticate::privateAccess())
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
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url GET /count
     */
    public function getCount($request_data = null) {
        try {
            $this->validate('get'); // same access permissions as get (?)
            $uow = UnitOfWork::build($request_data)->action('count');

            // patch backwards so we can call /count on old style methods.
            $mapper =& $this->mapper;
            $interface =& $this->interface;
            $uow = $mapper->uow($uow, function($mapped) use (&$interface) {
                $interface->uow($mapped);
            });
            return $uow->data;
        }
        catch (Exception $e) {
            $this->_error($e);
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
            if (
                   (!isset($request_data['limit']) || is_null($request_data['limit']))
                && (!isset($request_data['take'])  || is_null($request_data['take']))
            ) {
                // if limit is null, set it to something very high,
                // so we don't inadvertantly introduce limits for old calls
                // which didn't previously have limits.
                // this is done for backwards compatibility
                // with existing 3rd party integrations.
                $MAX_INT = 2147483647;
                $request_data['limit'] = $MAX_INT; // or max 32 bit int, anyways.
            }
            if (Params::hasNonReservedData($request_data)) {
                if (Params::isFilter($request_data)) {
                    // hijack filter syntax, convert to JSON object query syntax,
                    // then use unitofwork for backwards compatibility.
                    $this->validate('filter');

                    $operators = Comparator::$operators;
                    $pattern = '/ (AND|OR) /i';
                    $string = $request_data['filter'];
                    if (!$string)
                        throw new \CSException('Received a get by filter request with an empty filter! Received: ' . $request_data['filter']);

                    $groups = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    $c = count($groups);
                    if ($c === 0)
                        throw new \CSException('Received a get by filter request with an unparsable filter! Received: ' . $request_data['filter']);

                    $json = null;
                    if ($c === 1) {
                        // shortcut - no grouping required.
                        $group = $groups[0];
                        $comparator = new Comparator($group);
                        $json = $comparator->toJSON();
                    }
                    else {
                        $comparators = array();
                        $connectors = array();
                        for ($i = 0; $i < $c; $i += 2) {
                            $comparator = new Comparator($groups[$i]);
                            $comparators[] = $comparator->toJSON();
                        }
                        for ($i = 1; $i < $c - 1; $i += 2) {
                            $connector = $groups[$i];
                            $connectors[] = $connector;
                        }
                        while ($connector = array_pop($connectors)) {
                            $connector = strtolower($connector);
                            if ($connector === 'and')
                                $connector = '$and';
                            else if ($connector === 'or')
                                $connector = '$or';
                            $right = array_pop($comparators);
                            $left = array_pop($comparators);
                            $_json = array(
                                $connector => array(
                                    $left,
                                    $right
                                )
                            );
                            array_push($comparators, $_json);
                        }
                        $json = $comparators[0];
                    }
                    $request_data['where'] = json_encode($json);
                    $uow = UnitOfWork::build($request_data)->action('all');
                    $mapper =& $this->mapper;
                    $interface =& $this->interface;
                    $mapper->uowIn($uow);
                    $interface->uow($uow);
                    $data = $mapper->out($uow->data);
                    return $data;
                }
                else if (Params::isWhere($request_data)) {
                    // allow where syntax with old methods,
                    // but still use $mapper->out()
                    // in order to hoist the json data into a property
                    // this is done for backwards compatibility and
                    // consistency with the expectation for the other calls.

                    $this->validate('all');
                    $uow = UnitOfWork::build($request_data)->action('all');
                    $mapper =& $this->mapper;
                    $interface =& $this->interface;
                    $mapper->uowIn($uow);
                    $interface->uow($uow);
                    $data = $mapper->out($uow->data);
                    return $data;
                }
                else {
                    // hijack match syntax, convert to JSON object query syntax,
                    // then use unitofwork for backwards compatibility.
                    $this->validate('match');
                    $container = new ParamsContainer($request_data);
                    $params = $container->params;
                    $where = Arrays::select($params, function($val, $key, $arr) {
                        return array( $key => array ( '$eq' => $val ) );
                    });
                    if (!empty($where)) {
                        $json = array( '$and' => $where );
                        $request_data['where'] = json_encode($json);
                    }
                    $uow = UnitOfWork::build($request_data)->action('all');
                    $mapper =& $this->mapper;
                    $interface =& $this->interface;
                    $mapper->uowIn($uow);
                    $interface->uow($uow);
                    $data = $mapper->out($uow->data);
                    return $data;
                }
            }
            else {
                // just use unitofwork with all, as well.
                $this->validate('all');
                $mapper =& $this->mapper;
                $interface =& $this->interface;
                $uow = UnitOfWork::build($request_data)->action('all');
                $mapper->uowIn($uow);
                $interface->uow($uow);
                $data = $mapper->out($uow->data);
                return $data;
            }
        }
        catch (Exception $e) {
            $this->_error($e);
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
        catch (Exception $e) {
            $this->_error($e);
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

    /**
     * @url PUT /:id1
     *
     * Note: Restler expects route params to be explicitly set in the parameter list.
     *       For this reason, we are using put and mapping it to _put,
     *       which can be used by any number of ids.
     */
    public function put1($id1, $request_data) {
        try {
            $this->validate('put', $id1); // pass the id along -- we need to be able to do this for customers to own their own data
            $uow = UnitOfWork::build($request_data)
                ->action('update')
                ->table_id($id1);
            $mapper =& $this->mapper;
            $interface =& $this->interface;
            $mapper->uowIn($uow);
            $interface->uow($uow);
            return; // empty body return
        }
        catch (Exception $e) {
            $this->_error($e);
        }
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
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url DELETE /:id1
     */
    public function delete1($id1) {
        $this->validate('delete');
        return call_user_func_array(array($this, '_delete'), func_get_args());
    }

    /**
     * @access private
     *
     * Abstracted error handler.
     * Use to convert internal exceptions to RestExceptions as necessary.
     *
     * Note, @access doesn't actually work with Restler 2.0.
     * Holding on to it for documentation purposes.
     * The function is named with an underscore to keep it out of the exposed API calls.
     */
    protected final function _error($e) {
        if ($e instanceof RestException)
            throw $e;
        if ($e instanceof CSException)
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        throw new RestException(500, $e->getMessage());
    }
}
