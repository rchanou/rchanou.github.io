<?php

namespace ClubSpeed\Documentation\API;

abstract class DocAPIBase {

    public $id;
    public $header;
    public $calls;

    public function __construct() {
        $this->id       = '';
        $this->header   = '';
        $this->calls    = array();
        $this->examples = array();
        $this->usage    = '';
    }

    protected function expand() {
        $rootUrl = '/api/index.php/' . $this->url;
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
        foreach($calls as $key => $call) {
            if (!isset($call['info']))
                $call['info'] = array('access' => 'Private');
            $call['info']['access'] = strtolower($call['info']['access'] === 'public') ? 'Public' : 'Private';
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