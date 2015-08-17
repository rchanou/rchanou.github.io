<?php

namespace ClubSpeed\Structures;

class TreeNode {

    public $parent;
    public $value;
    public $children;

    public function __construct($value) {
        $this->parent = null;
        $this->value = $value;
        $this->children = array();
    }

    public function add($node) {
        if (!$node instanceof TreeNode)
            $node = new TreeNode($node);
        $node->parent = $this;
        $this->children[] = $node;
        return $this;
    }

    public function root() {
        $node = $this;
        while ($parent = $node->parent)
            $node = $parent;
        return $node;
    }

    public function isLeaf() {
        return count($this->children) === 0;
    }

    public function isRoot() {
        return is_null($this->parent);
    }

    public function isChild() {
        return !is_null($this->parent);
    }

    public function getValue() {
        return $this->value;
    }

    public function setParent(TreeNode $node) {
        $this->parent = $node;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }
}
