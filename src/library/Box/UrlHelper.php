<?php
/**
 * Copyright 2022-2023 FOSSBilling
 * Copyright 2011-2021 BoxBilling, Inc.
 * SPDX-License-Identifier: Apache-2.0
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

class Box_UrlHelper {
    public $params = array();
    public $match = false;

    /**
     * @param string $requestUri
     */
    public function __construct(private $method, $url, private $conditions, $requestUri) {

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if (strtoupper($method) == $requestMethod) {

            $paramNames = array();
            $paramValues = array();

            preg_match_all('@:([a-zA-Z]+)@', $url, $paramNames, PREG_PATTERN_ORDER);                    // get param names
            $paramNames = $paramNames[1];                                                               // we want the set of matches
            $regexedUrl = preg_replace_callback('@:[a-zA-Z_\-]+@', array($this, 'regexValue'), $url);   // replace param with regex capture
            if (preg_match('@^' . $regexedUrl . '$@', $requestUri, $paramValues)) {                            // determine match and get param values
                array_shift($paramValues);                                                              // remove the complete text match
                $counted = count($paramNames);
                for ($i=0; $i < $counted; $i++) {
                    $this->params[$paramNames[$i]] = $paramValues[$i];
                }
                $this->match = true;
            }
        }
    }

    private function regexValue($matches) {
        $key = str_replace(':', '', $matches[0]);
        if (array_key_exists($key, $this->conditions)) {
            return '(' . $this->conditions[$key] . ')';
        } else {
            return '([a-zA-Z0-9_\-]+)';
        }
    }

}