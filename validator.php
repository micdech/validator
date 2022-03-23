<?php

/*
 * validator.php
 *
 * Copyright 2015 Michele <micdech@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 *
 *  required, date, minlength, maxlength, exactlength, greaterthan,
 *  lessthan, alpha, alphanumeric, integer, float, numeric,
 *  email, url, phone, zipcode, startswith, endswith, contains, regex, inlist
 *
 *
 */

class Validator {

    private $fields;
    private $errors;
    private $messages;
    private $data;
    private $rules;
    private $classes;

    public function __construct(){
        $this->messages = $this->messages();
        $this->rules = $this->rules();
        $this->classes = "error";
    }

    /* Rules Methods */
    public function required($input = null) {
        return empty($input) ? false : true;
    }

    // Datetime validation from http://www.phpro.org/examples/Validate-Date-Using-PHP.html
    public function date($input = null, $format = 'MM/DD/YYYY') {
        if (empty($input)) {
            return true;
        }

        switch($format) {
            case 'YYYY/MM/DD':
            case 'YYYY-MM-DD':
            list($y, $m, $d) = preg_split('/[-\.\/ ]/', $input);
            break;

            case 'YYYY/DD/MM':
            case 'YYYY-DD-MM':
            list($y, $d, $m) = preg_split('/[-\.\/ ]/', $input);
            break;

            case 'DD-MM-YYYY':
            case 'DD/MM/YYYY':
            list($d, $m, $y) = preg_split('/[-\.\/ ]/', $input);
            break;

            case 'MM-DD-YYYY':
            case 'MM/DD/YYYY':
            list($m, $d, $y) = preg_split('/[-\.\/ ]/', $input);
            break;

            case 'YYYYMMDD':
            $y = substr($input, 0, 4);
            $m = substr($input, 4, 2);
            $d = substr($input, 6, 2);
            break;

            case 'YYYYDDMM':
            $y = substr($input, 0, 4);
            $d = substr($input, 4, 2);
            $m = substr($input, 6, 2);
            break;

            default:
            throw new \InvalidArgumentException("Invalid Date Format");
        }
        return checkdate($m, $d, $y);
    }

    public function minlength($input = null, $length = 0) {
        if (empty($input)) {
            return true;
        }

        return strlen(trim($input)) >= (int) $length ? true : false;
    }

    public function maxlength($input = null, $length = 0) {
        if (empty($input)) {
            return true;
        }

        return strlen(trim($input)) <= (int) $length ? true : false;
    }

    public function exactlength($input = null, $length = 0) {
        if (empty($input)) {
            return true;
        }

        return in_array(strlen(trim($input)), array_map('intval', $length)) ? true : false;
    }

    public function greaterthan($input = null, $min = 0) {
        if (empty($input)) {
            return true;
        }

        return (float) $input > (float) $min ? true : false;
    }

    public function lessthan($input = null, $max = 0) {
        if (empty($input)) {
            return true;
        }

        return (float) $input < (float) $max ? true : false;
    }

    public function alpha($input = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/^([a-z])+$/i', $input);
    }

    public function alphanumeric($input = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/^([a-z0-9])+$/i', $input);

    }

    public function integer($input = null) {
        if (empty($input)) {
            return true;
        }

        if (filter_var($input, FILTER_VALIDATE_INT) !== false) {
            return true;
        }

        return false;
    }

    public function float($input = null) {
        if (empty($input)) {
            return true;
        }

        if (filter_var($input, FILTER_VALIDATE_FLOAT) !== false) {
            return true;
        }

        return false;
    }

    public function numeric($input = null) {
        if (empty($input)) {
            return true;
        }

        return is_numeric($input) ? true : false;
    }

    public function email($input = null) {
        if (empty($input)) {
            return true;
        }

        if (filter_var($input, FILTER_VALIDATE_EMAIL) !== false) {
            return true;
        }

        return false;
    }

    public function url($input = null) {
        if (empty($input)) {
            return true;
        }

        if (filter_var($input, FILTER_VALIDATE_URL) !== false) {
            return true;
        }

        return false;
    }

    public function phone($input = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/^\(?([0-9]{3})\)?[- ]?([0-9]{3})[- ]?([0-9]{4})$/', $input);
    }

    public function zipcode($input = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/^\d{5}(-\d{4})?$/', $input);
    }

    public function startswith($input = null, $match = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/^' . preg_quote($match) . '/', $input);
    }

    public function endswith($input = null, $match = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/' . preg_quote($match) . '$/', $input);
    }

    public function contains($input = null, $match = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match('/' . preg_quote($match) . '/', $input);
    }

    public function regex($input = null, $regex = null) {
        if (empty($input)) {
            return true;
        }

        return (bool) preg_match($regex, $input);
    }

    public function inlist($input = null, $list = array()) {
        if (empty($input)) {
            return true;
        }

        return in_array($input, $list);
    }

    /* Engine methods */
    public function messages() {

        // required, date, minlength, maxlength, exactlength, greaterthan,
        // lessthan, alpha, alphanumeric, integer, float, numeric,
        // email, url, phone, zipcode, startswith, endswith, contains, regex, inlist
        $messages = array(
            'required'      => 'il campo &egrave; richiesto',
            'date'          => 'il campo &egrave; di tipo data',
            'minlength'     => 'inserire almeno ',
            'maxlength'     => 'inserire massimo ',
            'exactlength'   => '',
            'greaterthan'   => '',
            'lessthan'      => '',
            'alpha'         => 'sono consentiti solo caratteri alfa',
            'alphanumeric'  => 'sono consentiti solo caratteri alfa numerici',
            'integer'       => 'inserire solo numero',
            'float'         => 'campo di tipo float',
            'numeric'       => 'campo di tipo numerico',
            'email'         => 'la mail deve essere indicata',
            'url'           => 'inserire un url corretto',
            'phone'         => 'indicare un numero di telefono valido',
            'zipcode'       => 'specificare un cap valido',
            'startswith'    => 'il campo deve iniziare con',
            'endswith'      => 'il campo deve finire con',
            'contains'      => 'il campo deve contenere',
            'regex'         => 'regex non trovata',
            'inlist'        => 'il valore deve essere contenuta nella lista',
        );

        return $messages;
    }

    public function addMessages($messages){
        $m = array_push($this->messages, $messages);

        return $m;
    }

    public function rules($id = null) {

        $rules = array(
            'first_name'    => 'required',
            'last_name'     => 'required',
            'user_name'     => 'required',
            'email'         => 'required|email',
            'country'       => 'required',
            'address'       => 'required',
            'alt_address'   => '',
            'city'          => 'required',
            'state'         => 'required',
            'cap'           => 'required|zipcode',
            'phone'         => 'required|phone'
        );

        return $rules;
    }

    public function addRules($rules){

        foreach($rules as $k => $v){
            $this->rules[$k] = $v;
        }

        return $this->rules;

    }

    function getRules($input){

        $rules = explode("|", $input);

        foreach ($rules as $r) {

            $rule_name = $r;
            $rule_params = array();

            // For each rule in the list, see if it has any parameters. Example: minlength[5].
            if (preg_match('/\[(.*?)\]/', $r, $matches)) {

                // This one has parameters. Split out the rule name from it's parameters.
                $rule_name = substr($r, 0, strpos($r, '['));

                // There may be more than one parameters.
                $rule_params = explode(',', $matches[1]);
            } elseif (preg_match('/\{(.*?)\}/', $r, $matches)) {
                // This one has an array parameter. Split out the rule name from it's parameters.
                $rule_name = substr($r, 0, strpos($r, '{'));

                // There may be more than one parameter.
                $rule_params = array(explode(',', $matches[1]));
            }

            $return[$rule_name] = $rule_params;
        }

        return $return;
    }

    public function validate($inputs = array(), $rules = array(), $errors = array()){
        if(!is_array($inputs)) return;

        foreach($inputs as $inputName => $inputValue){
            if(array_key_exists($inputName, $this->rules)){
                $getRules = $this->getRules($this->rules[$inputName]);
            }

            foreach($getRules as $rule => $args){
                $execRule = call_user_func_array(array($this, $rule), array($inputValue, $args)); //return true or false

                $this->data[$inputName][$rule]['status'] = $execRule;
                $this->data[$inputName][$rule]['messages'] = $this->messages[$rule];
                $this->data[$inputName][$rule]['classes'] = $this->classes;

                if($execRule === false){
                    $this->fields[] = $inputName;
                    $this->errors[] = $execRule;
                }
            }
        }

        return $this->data;
    }

    public function getFields(){
        return $this->fields;
    }

    public function hasErrors() {
        if(!empty($this->errors))
            return true;
    }

    public function getErrors(){
        return $this->errors;
    }

    public function reset() {
        $this->fields = array();
        $this->errors = array();
        $this->messages = array();
        $this->data = array();
        $this->classes = array();
    }
}

$vldtr = new Validator();
