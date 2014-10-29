<?php

namespace ClubSpeed\Documentation\API;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;

abstract class DocAPIBase {

    public $id;
    public $header;
    public $route;
    public $calls;
    protected $root;

    public function __construct() {
        $this->id       = '';
        $this->header   = '';
        $this->calls    = array();
        $this->examples = array();
        $this->usage    = '';
        $this->root = '/api/index.php/';
    }

    private function parseJson($file) {
        return json_decode(file_get_contents($file), true);
    }

    protected function parseModel($modelId) {
        $model = $this->parseJson(__DIR__ . "/models/" . ucfirst($modelId) . "Model.json");
        // foreach($model as $column) {
        //     print_r($column);
        // }
        return $model; // we really need model to be a lookup/hash/service/something
    }

    protected function parseCalls($callId) {
        $callsJson = $this->parseJson(__DIR__ . "/calls/" . ucfirst($callId) . "Calls.json");
        $calls =& $callsJson['calls'];
        foreach($calls as &$call) {
            if (!isset($call['access']) || empty($call['access']))
                $call['access'] = 'private';
            $callData = array_merge(
                (isset($call['required']) ? is_array($call['required']) ? $call['required'] : array($call['required']) : array())
                , (isset($call['available']) ? is_array($call['available']) ? $call['available'] : array($call['available']) : array())
            );
            $call['request'] = array();
            foreach($callData as $data) {
                // $this->model should be a lookup eventually so models could potentially be shared/embedded/extended
                $property = Arrays::first($this->model, function($val) use ($data) {
                    return strtolower($data) === strtolower($val['name']);
                });
                $call['request'][$property['name']] = Convert::convert(
                    isset($property['default']) ? $property['default'] : ""
                    , $property['type']
                );
            }
            // ksort($call);
        }
        $this->calls = $callsJson;
        // return $callsJson;
        // print_r($calls);
        // print_r(json_encode($calls));
        // die();
    }

    protected function expand() {
        $rootUrl = $this->root . $this->url;
        $calls =& $this->calls;

        $create = array(
            'required'      => array()
            , 'available'   => array()
            , 'unavailable' => array()
        );
        $update = array(
            'required'      => array()
            , 'available'   => array()
            , 'unavailable' => array()
        );

        if (isset($this->info) && !empty($this->info)) {
            foreach($this->info as $info) {
                if (isset($info['create'])) {
                    if ($info['create'] == 'required')
                        $create['required'][] = $info['name'];
                    else if ($info['create'] == 'available')
                        $create['available'][] = $info['name'];
                    else
                        $create['unavailable'][] = $info['name'];
                }
                else
                    $create['unavailable'][] = $info['name'];

                if (isset($info['update'])) {
                    if ($info['update'] == 'required')
                        $update['required'][] = $info['name'];
                    else if ($info['update'] == 'available')
                        $update['available'][] = $info['name'];
                    else
                        $update['unavailable'][] = $info['name'];
                }
                else
                    $update['unavailable'][] = $info['name'];
            }
        }
        
        foreach($calls as $key => $call) {
            if (!isset($call['info']))
                $call['info'] = array('access' => 'Private');
            $call['info']['access'] = isset($call['info']['access']) && strtolower($call['info']['access'] === 'public') ? 'Public' : 'Private';
            $call['info']['access_icon'] = $call['info']['access'] === 'Public' ? '' : 'lock';
            $expanded = array();
            switch($key) {
                case 'create':
                    $expanded = array(
                          'header'      => 'Create'
                        , 'header_icon' => 'plus'
                        , 'id'          => 'create'
                        , 'type'        => 'create'
                        , 'info' => array(
                              'url'         => $rootUrl
                            , 'verb'        => 'POST'
                            , 'verb_icon'   => 'export'
                            , 'required'    => $create['required']
                            , 'available'   => $create['available']
                            , 'unavailable' => $create['unavailable']
                        )
                    );
                    break;
                case 'list':
                    $expanded = array(
                          'header'      => 'List'
                        , 'header_icon' => 'th-list'
                        , 'id'          => 'list'
                        , 'type'        => 'get'
                        , 'info' => array(
                              'url'         => $rootUrl
                            , 'verb'        => 'GET'
                            , 'verb_icon'   => 'save'
                        )
                    );
                    break;
                case 'single':
                    $expanded = array(
                          'header'      => 'Single'
                        , 'header_icon' => 'save'
                        , 'id'          => 'single'
                        , 'type'        => 'get'
                        , 'info' => array(
                              'url'         => $rootUrl . '/:id'
                            , 'verb'        => 'GET'
                            , 'verb_icon'   => 'save'
                        )
                    );
                    break;
                case 'match':
                    $expanded = array(
                          'header'      => 'Match'
                        , 'header_icon' => 'random'
                        , 'id'          => 'match'
                        , 'type'        => 'get'
                        , 'info' => array(
                              'url'         => $rootUrl . '?column1=value1&column2=value2'
                            , 'verb'        => 'GET'
                            , 'verb_icon'   => 'save'
                        )
                        , 'usage' => <<<EOS
<p>
    See <a href="#query-operations-property-matching">Property Matching</a> for documentation on matching.
</p>
EOS
                    );
                    break;
                case 'search':
                    $expanded = array(
                          'header'      => 'Search'
                        , 'header_icon' => 'search'
                        , 'id'          => 'search'
                        , 'type'        => 'get'
                        , 'info' => array(
                              'url'         => $rootUrl . '?filter=column1 {comparison operator} value1'
                            , 'verb'        => 'GET'
                            , 'verb_icon'   => 'save'
                        )
                        , 'usage' => <<<EOS
<p>
    See <a href="#query-operations-record-filtering">Record Filtering</a> for documentation on filters.
</p>
EOS
                    );
                    break;
                case 'update':
                    $expanded = array(
                          'header'      => 'Update'
                        , 'header_icon' => 'pencil'
                        , 'id'          => 'update'
                        , 'type'        => 'update'
                        , 'info' => array(
                              'url'         => $rootUrl . '/:id'
                            , 'verb'        => 'PUT'
                            , 'verb_icon'   => 'pencil'
                            , 'required'    => $update['required']
                            , 'available'   => $update['available']
                            , 'unavailable' => $update['unavailable']
                        )
                    );
                    break;
                case 'delete':
                    $expanded = array(
                          'header'      => 'Delete'
                        , 'header_icon' => 'remove'
                        , 'id'          => 'delete'
                        , 'type'        => 'delete'
                        , 'info' => array(
                              'url'         => $rootUrl . '/:id'
                            , 'verb'        => 'DELETE'
                            , 'verb_icon'   => 'remove'
                        )
                    );
                    break;
            }
            $calls[$key] = array_merge_recursive($call, $expanded);
        }
    }
}