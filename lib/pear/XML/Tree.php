<?php
//
// +----------------------------------------------------------------------+
// | PEAR :: XML_Tree                                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Bernd Römer <berndr@bonn.edu>                               |
// |          Sebastian Bergmann <sb@sebastian-bergmann.de>               |
// |          Tomas V.V.Cox <cox@idecnet.com>                             |
// |          Michele Manzato <michele.manzato@verona.miz.it>             |
// +----------------------------------------------------------------------+
//
// $Id: Tree.php,v 1.1 2004-01-10 12:55:32 gongo Exp $
//

require_once 'XML/Parser.php';
require_once 'XML/Tree/Node.php';

/**
* PEAR::XML_Tree
*
* Purpose
*
*    Allows for the building of XML data structures
*    using a tree representation, without the need
*    for an extension like DOMXML.
*
* Example
*
*    $tree  = new XML_Tree;
*    $root =& $tree->addRoot('root');
*    $foo  =& $root->addChild('foo');
*
*    $tree->dump(true);
*
* @author  Bernd Römer <berndr@bonn.edu>
* @package XML
* @version $Version$ - 1.0
*/
class XML_Tree extends XML_Parser
{
    /**
    * File Handle
    *
    * @var  resource
    */
    var $file = NULL;

    /**
    * Filename from which the XML_Tree was read
    *
    * @var  string
    */
    var $filename = '';

    /**
    * Namespace
    *
    * @var  array
    */
    var $namespace = array();

    /**
    * Root node of the XML tree
    *
    * @var  object XML_Tree_Node
    */
    var $root = NULL;

    /**
    * XML Version
    *
    * @var  string
    */
    var $version = '1.0';

    /**
    * Constructor
    *
    * @param  string  filename  Filename where to read the XML
    * @param  string  version   XML Version to apply
    */
    function XML_Tree($filename = '', $version = '1.0')
    {
        $this->filename = $filename;
        $this->version  = $version;
    }

    /**
    * Gets the root node
    *
    * @return object    Root XML_Tree_Node, or PEAR_Error if there isn't any root node.
    *
    * @access public
    */
    function &getRoot()
    {
        if (!is_null($this->root)) {
            return $this->root;
        }
        return $this->raiseError("No root");
    }

    /**
    * Sets the root node of the XML tree.
    *
    * @param  string    name        Name of root element
    *
    * @return object XML_Tree_Node   Reference to the newly created root node
    * @access public
    */
    function &addRoot($name, $content = '', $attributes = array(), $lineno = null)
    {
        $this->root = new XML_Tree_Node($name, $content, $attributes, $lineno);
        return $this->root;
    }

    /**
    * Inserts a child/tree (child) into tree ($path,$pos) and maintains
    * namespace integrity
    *
    * @param mixed      path            Path to parent node to add child (see
    *                                   getNodeAt() for format)
    * @param integer    pos             Position where to insert the new child.
    *                                   0 < means |$pos| elements before the end,
    *                                   e.g. -1 appends as last child.
    * @param mixed      child           Child to insert (XML_Tree or XML_Tree_Node),
    *                                   or name of child node
    * @param string     content         Content (text) for the new node (only if
    *                                   $child is the node name)
    * @param array      attributes      Attribute-hash for new node
    *
    * @return object Reference to the inserted child (node), or PEAR_Error upon error
    * @access public
    * @see getNodeAt()
    */
    function &insertChild($path, $pos, $child, $content = '', $attributes = array())
    {
        $parent =& $this->getNodeAt($path);
        if (PEAR::isError($parent)) {
            return $parent;
        }

        $x =& $parent->insertChild(null, $pos, $child, $content, $attributes);

        if (!PEAR::isError($x)) {
        // update namespace to maintain namespace integrity
            $count = count($path);
            foreach ($this->namespace as $key => $val) {
                if ((array_slice($val,0,$count)==$path) && ($val[$count]>=$pos)) {
                    $this->namespace[$key][$count]++;
                }
            }
        }
        return $x;
    }

    /*
    * Removes a child node from tree and maintains namespace integrity
    *
    * @param array      path        Path to the parent of child to remove (see
    *                               getNodeAt() for format)
    * @param integer    pos         Position of child in parent children-list
    *                               0 < means |$pos| elements before the end,
    *                               e.g. -1 removes the last child.
    *
    * @return object    Parent XML_Tree_Node whose child was removed, or PEAR_Error upon error
    * @access public
    * @see getNodeAt()
    */
    function &removeChild($path, $pos)
    {
        $parent =& $this->getNodeAt($path);
        if (PEAR::isError($parent)) {
            return $parent;
        }

        $x =& $parent->removeChild($pos);

        if (!PEAR::isError($x)) {
            // Update namespace to maintain namespace integrity
            $count=count($path);
            foreach($this->namespace as $key => $val) {
                if (array_slice($val,0,$count)==$path) {
                    if ($val[$count]==$pos) {
                        unset($this->namespace[$key]); break;
                    }
                    if ($val[$count]>$pos) {
                        $this->namespace[$key][$count]--;
                    }
                }
            }
        }

        return $x;
    }

    /*
    * Maps a XML file to a XML_Tree
    *
    * @return mixed The XML tree root (an XML_Tree_Node), or PEAR_Error upon error.
    * @access public
    */
    function &getTreeFromFile ()
    {
        $this->folding = false;
        $this->XML_Parser(null, 'event');
        $err = $this->setInputFile($this->filename);
        if (PEAR::isError($err)) {
            return $err;
        }
        $this->cdata = null;
        $err = $this->parse();
        if (PEAR::isError($err)) {
            return $err;
        }
        return $this->root;
    }

    /*
    * Maps an XML string to an XML_Tree.
    *
    * @return mixed The XML tree root (an XML_Tree_Node), or PEAR_Error upon error.
    * @access public
    */
    function &getTreeFromString($str)
    {
        $this->folding = false;
        $this->XML_Parser(null, 'event');
        $this->cdata = null;
        $err = $this->parseString($str);
        if (PEAR::isError($err)) {
            return $err;
        }
        return $this->root;
    }

    /**
    * Handler for the xml-data
    * Used by XML_Parser::XML_Parser() when parsing an XML stream.
    *
    * @param mixed  xp          ignored
    * @param string elem        name of the element
    * @param array  attribs     attributes for the generated node
    *
    * @access private
    */
    function startHandler($xp, $elem, &$attribs)
    {
        $lineno = xml_get_current_line_number($xp);
        // root elem
        if (!isset($this->i)) {
            $this->obj1 =& $this->addRoot($elem, null, $attribs, $lineno);
            $this->i = 2;
        } else {
            // mixed contents
            if (!empty($this->cdata)) {
                $parent_id = 'obj' . ($this->i - 1);
                $parent    =& $this->$parent_id;
                $parent->children[] = &new XML_Tree_Node(null, $this->cdata, null, $lineno);
            }
            $obj_id = 'obj' . $this->i++;
            $this->$obj_id = &new XML_Tree_Node($elem, null, $attribs, $lineno);
        }
        $this->cdata = null;
        return null;
    }

    /**
    * Handler for the xml-data
    * Used by XML_Parser::XML_Parser() when parsing an XML stream.
    *
    * @param mixed  xp          ignored
    * @param string elem        name of the element
    *
    * @access private
    */
    function endHandler($xp, $elem)
    {
        $this->i--;
        if ($this->i > 1) {
            $obj_id = 'obj' . $this->i;
            // recover the node created in StartHandler
            $node   =& $this->$obj_id;
            // mixed contents
            if (count($node->children) > 0) {
                if (trim($this->cdata)) {
                    $node->children[] = &new XML_Tree_Node(null, $this->cdata);
                }
            } else {
                $node->setContent($this->cdata);
            }
            $parent_id = 'obj' . ($this->i - 1);
            $parent    =& $this->$parent_id;
            // attach the node to its parent node children array
            $parent->children[] = $node;
        }
        $this->cdata = null;
        return null;
    }

    /*
    * The xml character data handler
    * Used by XML_Parser::XML_Parser() when parsing an XML stream.
    *
    * @param mixed  xp          ignored
    * @param string data        PCDATA between tags
    *
    * @access private
    */
    function cdataHandler($xp, $data)
    {
        if (trim($data) != '') {
            $this->cdata .= $data;
        }
    }

    /**
    * Get a copy of this tree by cloning and all of its nodes, recursively.
    *
    * @return object XML_Tree copy of this node.
    * @access public
    */
    function clone()
    {
        $clone = new XML_Tree($this->filename, $this->version);
        if (!is_null($this->root)) {
            $clone->root = $this->root->clone();
        }

        // clone all other vars
        $temp = get_object_vars($this);
        foreach($temp as $varname => $value) {
            if (!in_array($varname,array('filename','version','root'))) {
                $clone->$varname=$value;
            }
        }
        return $clone;
    }

    /**
    * Print text representation of XML tree.
    *
    * @param bool xmlHeader     if true then generate also the leading XML
    *                           'Content-type' header directive, e.g. for
    *                           direct output to a web page.
    *
    * @access public
    */
    function dump($xmlHeader = false)
    {
        if ($xmlHeader) {
            header('Content-type: text/xml');
        }
        echo $this->get();
    }

    /**
    * Get text representation of XML tree.
    *
    * @return string  Text (XML) representation of the tree
    * @access public
    */
    function &get()
    {
        $out = '<?xml version="' . $this->version . "\"?>\n";
        if (!is_null($this->root))
            {
            if(!is_object($this->root) || (get_class($this->root) != 'xml_tree_node'))
                return $this->raiseError("Bad XML root node");
            $out .= $this->root->get();
        }
        return $out;
    }

    /**
    * Get current namespace.
    *
    * @param  string  name  namespace
    * @return string
    *
    * @access public
    */
    function &getName($name) {
        return $this->root->getElement($this->namespace[$name]);
    }

    /**
    * Register a namespace.
    *
    * @param  string  $name namespace
    * @param  string  $path path
    *
    * @access public
    */
    function registerName($name, $path) {
        $this->namespace[$name] = $path;
    }

    /**
    * Get a reference to a node. Node is searched by its 'path'.
    *
    * @param mixed  path  Path to node. Can be either a string (slash-separated
    *                     children names) or an array (sequence of children names) both
    *                     of them starting from node. Note that the first name in sequence
    *                     must be the name of the document root.
    * @return object    Reference to the XML_Tree_Node found, or PEAR_Error if
    *                   the path does not exist. If more than one element matches
    *                   then only the first match is returned.
    * @access public
    */
    function &getNodeAt($path)
    {
        if (is_null($this->root)){
            return $this->raiseError("XML_Tree hasn't a root node");
        }
        if (is_string($path))
            $path = explode("/", $path);
        if (sizeof($path) == 0) {
            return $this->raiseError("Path to node is empty");
        }
        $path1 = $path;
        $rootName = array_shift($path1);
        if ($this->root->name != $rootName) {
            return $this->raiseError("Path does not match the document root");
        }
        $x =& $this->root->getNodeAt($path1);
        if (!PEAR::isError($x)) {
            return $x;
        }
        // No node with that name found
        return $this->raiseError("Bad path to node: [".implode('/', $path)."]");
    }

    /**
    * Gets all children that match a given tag name.
    *
    * @param  string    Tag name
    *
    * @return array     An array of Node objects of the children found,
    *                   an empty array if none
    * @access public
    * @author Pierre-Alain Joye <paj@pearfr.org>
    */
    function &getElementsByTagName($tagName)
    {
        if (empty($tagName)) {
            return $this->raiseError('Empty tag name');
        }
        if (sizeof($this->children)==0) {
            return null;
        }
        $result = array();
        foreach ($this->children as $child) {
            if ($child->name == $tagName) {
                $result[] = $child;
            }
        }
        return $result;
    }
}
?>
