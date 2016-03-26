<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * minimum configuration, maximal flexibility
 * 
 * new logunForm (
 *      name = name of form. If there is no id attribute, id is equal to name
 *      type
 *          - reload: standard html form
 *          - ajax: results sent over ajax request
 *          - static: submit is disabled
 *      action = address of submit action. If null, current url is applied. If type of form is ajax, ajax support is set to default, and action is null, form sends request to constructed url address. 
 *      attributes [optional]
 *          -   method: default is POST
 *          -   class: class or classes (separated by commas) describing this form
 *          -   input_defaults: array of default assigned to all children inputs (additionaly to attributes set in optional attributes of input). eg. [class=>'input'] means that all of inputs will have assigned the input class to their defined classes
 *          -   id: if id is not to be the same as form name
 *          -   attr:  an array of array pairs - attribute name => attribute value
 *          -   js: an array of array pairs - event name => js function
 *      config
 *          -   ajax_support
 *                  -   default (===null):    logun calls, via default js function, supplied with serialized form, to page URL/FORM_NAME to verify form via ajax
 *                  -   name of js function: logun calls js function, supplying it with serialized form
 *          -   ajax_url_template: sprintf-ready string containing prepared address for ajax validation, overriding default url. Only parameter for replacement if formName (eg. ajax/%s.formcheck.ajax is parsed to ajax/FORM_NAME.formcheck.ajax)
 *          -   ajax_js_caller: name of js function to be used instead of default login function. Overwrites only when ajax_support is set to default
 *          -   render_type
 *                  -   default (===null): logun  prepares input fields and forms as constructed html
 *                  -   template_manager: login uses supplied template manager to construct form
 *          -   render_lib = is render_type is set to template_manager, reference to object responsible for constructing templates
 *          -   template_dir = if render_type is set to template_manager, this variable sets path under witch logun is supposed to find templates for inputs and forms, to later use in template rendering ()
 *          -   render_fetch
 *          -   render_assign
 *          -   js_lib
 *          -   js_support
 *          -   i18n_type
 *          -   i18n_lib
 *          -   captcha_type
 *          -   captcha_lib
 *          -   upload_lib
 * )
 * 
 * ->construct(
 *      form setup in array form, eg.:
 *      [
 *          'form'      =>  [
 *              'name'  =>  string
 *              'type'  =>  string (constant)
 *              'action'    =>  string
 *              'attributes'    =>  array
 *              'config'    =>  array
 *          ]
 *          ,'inputs'   =>  [
 *              input_name  =>  [
 *      type =>  string
 *      name =>  string
 *      requirement  =>  string (constant)
 *      attr =>  array
 *              ]
 *          ]
 *          ,'validate' =>  []
 *      ]
 * )
 * 
 * public functions handeling 
 * ->email([...])
 * ->url([...])
 * ->textfield([...])
 * ->password([...])
 * ->textarea([...])
 * ->select(
 *      data = dataset from where to get information. Prefferably array. 
 *          Default construction:
 *          'default'   =>  [key]
 *          [key]   =>  [value]
 *          [key]   =>  [value]
 *          ...
 *          [key]   =>  [   //optgroup
 *              [key]   =>  [value]
 *              [key]   =>  [value]
 *              ...
 *          ]
 *          Hierarchy construction:
 *          'default'   =>  [key]
 *          [parrent_key]   =>  [
 *              [key]   =>  [value]
 *              [key]   =>  [value]
 *              ...
 *          ]
 *          [parrent_key]   =>  [
 *              [key]   =>  [value]
 *              [key]   =>  [value]
 *              ...
 *          ]
 *          ...
 *          [parrent_key]   =>  [
 *              [key]   =>  [   //optgroup
 *                  [key]   =>  [value]
 *                  [key]   =>  [value]
 *                  ...
 *          ]
 *      parent = reference to another LogunSelect object
 *      [...])
 * ->button([...]) 
 * 
 * [currently not supported] ->number(
 *      start = starting value of input
 *      stop  = final value of input
 *      step  = incrementation of value
 *      [...])
 * [currently not supported] ->range(
 *      start = starting value of input
 *      stop  = final value of input
 *      step  = incrementation of value
 *      [...])
 * [currently not supported] ->slider( 
 *      data    =   array of values to be selected in slider. Slider is sliced into count(data) segments
 *      [...])
 * [currently not supported] ->search(
 *      target_type =   type of supplied target. Possible types are: array, url, js, object
 *      target  = reference to an url, a JavaScript function, a PHP callable, or a JSON object. All of targets must return result as a JSON object:
 *          {
 *              'results':[
 *                  [
 *                      'title':title
 *                      'content':content
 *                      'icon':url to icon
 *                      'link':link to result
 *                  ]
 *              ]
 *              'all':number of all results
 *              'more':link to list of all results
 *          }
 *      display = number of results to display
 *      [...])
 * [currently not supported] ->reference(lib, [...])    \
 * [currently not supported] ->rte(lib, [...])           |
 * [currently not supported] ->color(lib, [...])          }lib = js library handeling custom selector
 * [currently not supported] ->date(lib, [...])          |
 * [currently not supported] ->time(lib, [...])         /
 * 
 * 
 * private function handeling all basic input requests
 * 
 * ->input(
 *      type = type of input field
 *      name = id (if is an array, id increments)
 *      attributes [optional]
 *          -   value: default value of field
 *          -   data: string, integer or an array of available values to select
 *          -   class: string containing class, or classes (separated by commas) assigned to this field
 *          -   id: if id is not to be the same as the field name
 *          -   attr: an array of array pairs - attribute name => attribute value
 *          -   js: an array of array pairs - event name => js function
 *          -   overwrite_defaults: true/false - if default form values are not to be attached
 * )
 *
 * ->*(*)->check / ->input(*)->check(
 *      type = predefined or callable function which check for field validity, eg.:
 *          -   required
 *          -   optional_if: mandatory quantifier array, indicating if some, or all fields, referenced by name, or object, are filled, this field IS NOT madatory
 *          -   required_if: mandatory quantifier array, indicating if some, or all fields, referenced by name, or object, are filled, this field IS madatory
 *          -   optional_if_all: mandatory quantifier array, indicating IF ALL fields, referenced by name, or object, are filled, this field IS NOT madatory
 *          -   required_if_all: mandatory quantifier array, indicating IF ALL fields, referenced by name, or object, are filled, this field IS madatory
 *      valid = message
 *      invalid = message
 *      quantifiers [optional] = array of optional params specyfying validity of check
 * )
 * 
 * @author loup
 */
class LogunForm {

    /**
     * 
     * @param string $sName name of form. If there is no id attribute, id is equal to name
     * @param string $sType - reload: standard html form <br /> - ajax: results sent over ajax request <br /> - static: submit is disabled
     * @param string $sAction   address of submit action. If null, current url is applied. If type of form is ajax, ajax support is set to default, and action is null, form sends request to constructed url address. 
     * @param array $aAttributes    [optional]  <br />-   method: default is POST<br />-   class: class or classes (separated by commas) describing this form<br />-   input_defaults: array of default assigned to all children inputs (additionaly to attributes set in optional attributes of input). eg. [class=>'input'] means that all of inputs will have assigned the input class to their defined classes<br />-   id: if id is not to be the same as form name<br />-   attr:  an array of array pairs - attribute name => attribute value<br />-   js: an array of array pairs - event name => js function
     * @param array $aConfig    [optional]  <br />-   ajax_support<br />*   default (===null):    logun calls, via default js function, supplied with serialized form, to page URL/FORM_NAME to verify form via ajax<br />*   name of js function: logun calls js function, supplying it with serialized form<br />-   ajax_url_template: sprintf-ready string containing prepared address for ajax validation, overriding default url. Only parameter for replacement if formName (eg. ajax/%s.formcheck.ajax is parsed to ajax/FORM_NAME.formcheck.ajax)<br />-   ajax_js_caller: name of js function to be used instead of default login function. Overwrites only when ajax_support is set to default<br />-   render_type<br />*   default (===null): logun  prepares input fields and forms as constructed html<br />*   template_manager: login uses supplied template manager to construct form<br />-   render_lib = is render_type is set to template_manager, reference to object responsible for constructing templates<br />-   template_dir = if render_type is set to template_manager, this variable sets path under witch logun is supposed to find templates for inputs and forms, to later use in template rendering ()<br />-   render_fetch<br />-   render_assign<br />-   js_lib<br />-   js_support<br />-   i18n_type<br />-   i18n_lib<br />-   captcha_type<br />-   captcha_lib<br />-   upload_lib
     * @return object LogunForm instance
     */
    public function __construct ($sName, $sType, $sAction, $aAttributes = [], $aConfig = []){
        
    }
    
    public function constructFromArray(){}
    
    public function constructFromTemplate(){}
    
    private function construct(){}
    
    private function input(){}
    
    public function __call($name, $arguments) {
        ;
    }
    
// * ->construct(
// *      form setup in array form, eg.:
// *      [
// *          'form'      =>  [
// *              'name'  =>  string
// *              'type'  =>  string (constant)
// *              'action'    =>  string
// *              'attributes'    =>  array
// *              'config'    =>  array
// *          ]
// *          ,'inputs'   =>  [
// *              input_name  =>  [
// *      type =>  string
// *      name =>  string
// *      requirement  =>  string (constant)
// *      attr =>  array
// *              ]
// *          ]
// *          ,'validate' =>  []
// *      ]
// * )

}
