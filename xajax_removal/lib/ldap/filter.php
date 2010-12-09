<?php

/**
 * Object representation of a part of a LDAP filter.
 *
 * Inspired by PEAR Net_LDAP_Filter class
 * {@link http://pear.php.net/package/Net_LDAP/docs/latest/Net_LDAP/Net_LDAP_Filter.html}
 *
 * LDAP filters are defined in RFC-2254 and can be found under
 * {@link http://www.ietf.org/rfc/rfc2254.txt}
 */
class LDAPFilter
{
    var $_subfilters = array();
    var $_match;
    var $_filter;

   /**
    * Combine two or more filter objects using a logical operator
    *
    * This static method combines two or more filter objects and returns one single
    * filter object that contains all the others.
    * Call this method statically: $filter =& LDAPFilter('or', array($filter1, $filter2))
    * If the array contains filter strings instead of filter objects, we will try to parse them.
    *
    * @param string                $log_op  The locicall operator. May be "and", "or", "not" or the subsequent logical equivalents "&", "|", "!"
    * @param array|LDAPFilter$filters array with LDAPFilter objects
    *
    * @return LDAPFilter|Exception
    * @static
    */
    function &combine($log_op, $filters)
    {
        // substitude named operators to logical operators
        if ($log_op == 'and') $log_op = '&';
        if ($log_op == 'or')  $log_op = '|';
        if ($log_op == 'not') $log_op = '!';

        // tests for sane operation
        if ($log_op == '!') {
            // Not-combination, here we also accept one filter object or filter string
            if (!is_array($filters) && is_a($filters, 'LDAPFilter')) {
                $filters = array($filters); // force array
            } elseif (is_string($filters)) {
                $filter_o = LDAPFilter::parse($filters);
                if (!$filter_o) {
                    throw new Exception('LDAPFilter combine error');
                } else {
                    $filters = array($filter_o);
                }
            } else {
                throw new Exception('LDAPFilter combine error: operator is "not" but $filter is not a valid LDAPFilter nor an array nor a filter string');
            }
        } elseif ($log_op == '&' || $log_op == '|') {
            if (!is_array($filters) || count($filters) < 2) {
                throw new Exception('LDAPFilter combine error: parameter $filters is not an array or contains less than two LDAPFilter objects');
            }
        } else {
            throw new Exception('LDAPFilter combine error: logical operator is not known');
        }

        $combined_filter = new LDAPFilter();
        foreach ($filters as $key => $testfilter) {
            if (is_string($testfilter)) {
                // string found, try to parse into an filter object
                $filters[$key] = LDAPFilter::parse($testfilter);

            } elseif (!is_a($testfilter, 'LDAPFilter')) {
                throw new Exception('LDAPFilter combine error: invalid object passed in array $filters');
            }
        }

        $combined_filter->_subfilters = $filters;
        $combined_filter->_match      = $log_op;
        return $combined_filter;
    }

    /**
    * Parse FILTER into a LDAPFilter object
    *
    * This parses an filter string into LDAPFilter objects.
    *
    * @param string $FILTER The filter string
    *
    * @access static
    * @return LDAPFilter|Net_LDAP_Error
    */
    function parse($FILTER)
    {
        if (preg_match('/^\((.+?)\)$/', $FILTER, $matches)) {
            if (in_array(substr($matches[1], 0, 1), array('!', '|', '&'))) {
                // extract logical operator and subfilters
                $log_op              = substr($matches[1], 0, 1);
                $remaining_component = substr($matches[1], 1);

                // bite off the next filter part and parse
                $subfilters = array();
                while (preg_match('/^(\(.+?\))(.*)/', $remaining_component, $matches)) {
                    $remaining_component = $matches[2];
                    array_push($subfilters, LDAPFilter::parse($matches[1]));
                }

                // combine subfilters using the logical operator
                $filter_o = LDAPFilter::combine($log_op, $subfilters);
                return $filter_o;
            } else {
                // This is one leaf filter component, do some syntax checks, then escape and build filter_o
                // $matches[1] should be now something like "foo=bar"

                // detect multiple leaf components
                if (stristr($matches[1], ')(')) {
                    throw new Exception('Filter parsing error: invalid filter syntax - multiple leaf components detected');
                } else {
                    $filter_parts = preg_split('/(?<!\\\\)(=|=~|>|<|>=|<=)/', $matches[1], 2, PREG_SPLIT_DELIM_CAPTURE);
                    if (count($filter_parts) != 3) {
                        throw new Exception('Filter parsing error: invalid filter syntax - unknown matching rule used');
                    } else {
                        $filter_o          = new LDAPFilter();
                        $value             = $filter_parts[2];
                        $filter_o->_filter = '('.$filter_parts[0].$filter_parts[1].$value.')';
                        return $filter_o;
                    }
                }
            }
        } else {
               throw new Exception('Filter parsing error: invalid filter syntax - filter components must be enclosed in round brackets');
        }
    }
}
