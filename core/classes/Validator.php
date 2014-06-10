<?php
  /**
   * A class for validating and filtering user input
   * 
   * @package Pos
   * @author David Powers
   * @copyright David Powers 2008
   * @version 1.0.1
   */
   
  class Validator {
    
      /**
       * Stores the source of input variables as a filter constant (INPUT_POST or INPUT_GET).
       * @var int
       */
      protected $_inputType;
      /**#@+
       *
       * @var array
       */
      /**
       * Stores the raw input array.
       */
      protected $_submitted;
      /**
       * Stores an array of required fields.
       */
      protected $_required;
      /**
       * Stores the filtered output.
       */
      protected $_missing;
      /**
       * Stores an associative array of error messages.
       * 
       * The key (index) of each array element is the name of the field that failed validation.
       */
      protected $_errors;
      /**
       * Stores a multidimensional array of filter constants and flags to be applied to each field.
       */
      protected $_filterArgs;
      /**
      

      /**
       * Constructs a validator object for $_POST or $_GET input.
       * 
       * The constructor checks the availability of the PHP filter functions for which it
       * acts as a wrapper. If the optional first argument (an array of required fields) is 
       * supplied, it checks that each one contains at least some input. Any missing items are
       * stored as an array in the $missing property.
       * 
       * By default, the constructor sets the input type to "post". To create a validator for
       * the $_GET array, set the optional second argument to "get".
       * 
       * @param array  $required   Optional array containing the names of required fields or URL variables.
       * @param string $inputType  Type of input; valid options: "post" and "get" (case-insensitive); defaults to "post"
       * @return Pos_Validator object
       */
      public function __construct($required = array(), $inputType = 'post')  {
          if (!is_null($required) && !is_array($required)) {
            throw new Exception('The names of required fields must be an array, even if only one field is required.');
          }
          $this->_required = $required;
          $this->setInputType($inputType);
          
          if ($this->_required) {
            $this->checkRequired();   // This method will pass the required fields found to the missing array.
          }
          $this->_filterArgs = array();
          $this->_errors = array();
      }


      public function displayInputErrors($errors = null) {      
        //echo ' <div class=" well ">;
        echo '<div class="alert alert-danger"> <b>Please review the following errors:</b>';
        echo '<p>';
        foreach ($errors as $field) {
          if(!empty($field) ) { echo '- ' ; }
          echo ucfirst($field) ."<br/>";
        }
        echo '</p></div>';
        //echo '</div>';          
      }   


      
      public function displayInputErrors2($required = array(), $error_array = array() ) {
        if (!empty($error_array) ) {
        $errors = array_merge($error_array, $this->requiredFields($required, $_POST)) ;
        }
        else {
            $errors = $this->requiredFields($required, $_POST) ;
        }
        echo "<b>Please review the following fields:</b><br />";
        foreach($errors as $error) {
          echo "- " . $error . "<br/>";
        }
      }



      public function formatDate($date) {
        $date = DateTime::createFromFormat("D F d, Y H:i a", 'Wed January 9, 2013 01:43 pm');
        echo $date->format("Y-m-d H:i:s");
      }

    
      /**
       * Checks whether the submitted value is an integer.
       * 
       * @param string  $fieldName  Name of submitted value to be checked.
       * @param int     $min        Optional minimum acceptable number.
       * @param int     $max        Optional maximum acceptable number.
       */
      public function isInt($fieldName, $min = null, $max = null){
        // Set the filter options
        $this->_filterArgs[$fieldName] = array('filter' => FILTER_VALIDATE_INT);
        // Add filter options to test for integers within a specified range
        if (is_int($min)) {
          $this->_filterArgs[$fieldName]['options']['min_range'] = $min;
        }
        if (is_int($max)) {
          $this->_filterArgs[$fieldName]['options']['max_range'] = $max;
        }
      }
       
      

      /**
       * Checks whether the input conforms to the format of an email address.
       * 
       * It does not check whether the address is genuine. Multiple addresses are
       * rejected, guarding against email header injection attacks.
       * 
       * @ param string $fieldName Name of submitted value to be checked.
       */
        public function isEmail($fieldName)   {
          // Set the filter options 
          $this->_filterArgs[$fieldName] = FILTER_VALIDATE_EMAIL;
        }



      /**
       * Matches the submitted value against a Perl-compatible regular expression.
       * 
       * @param string  $fieldName  Name of submitted value to be checked.
       * @param string  $pattern    Perl-compatible regular expression.
       */
      public function matches($fieldName, $pattern)  {
        // Set the filter options 
        $this->_filterArgs[$fieldName] = array('filter' => FILTER_VALIDATE_REGEXP,
                               'options' => array('regexp' => $pattern));
      }

        
      public function sanitize($input) {
        if (is_array($input)) {
          foreach($input as $var => $val) {
          $output[$var] = trim(htmlentities(strip_tags( $val,",")));
          }
            return $output;
        }
        else {
          $input = trim(htmlentities(strip_tags($input,",")));
          return $input;
        }     
      }
        

      
      /**
       * Sanitizes a string by removing completely all tags (including PHP and HTML).
       * 
       * @param string  $fieldName       Name of submitted value to be checked.
       * @param boolean $encodeAmp       Optional; converts & to &#38; if set to true; defaults to false.
       * @param boolean $preserveQuotes  Optional; preserves double and single quotes if true; defaults to false.
       */
      public function removeTags($fieldName, $encodeAmp = false, $preserveQuotes = false)   {

        $this->_filterArgs[$fieldName]['filter'] = FILTER_SANITIZE_STRING;
        // Multiple flags are set using the "binary or" operator
        $this->_filterArgs[$fieldName]['flags'] = 0;
        if ($encodeAmp) {
          $this->_filterArgs[$fieldName]['flags'] |= FILTER_FLAG_ENCODE_AMP;
        }
        if ($preserveQuotes) {
          $this->_filterArgs[$fieldName]['flags'] |= FILTER_FLAG_NO_ENCODE_QUOTES;
        }

      }

      
                
      public function sanitizeInput($input) {
        if (is_array($input)) {
          foreach($input as $var => $val) {
          $output[$var] = trim(htmlentities(strip_tags( $val,",")));
          }
            return $output;
        }
        else {
          $input = trim(htmlentities(strip_tags($input,",")));
          return $input;
        }     
      }
      

    /**
     * Sanitizes array by removing completely all tags (including PHP and HTML).
     * 
     * Arguments four to seven determine whether characters with an ASCII value less than 32 or
     * greater than 127 are encoded or stripped. By default, they are left untouched.  
     * 
     * @param string  $fieldName              Name of submitted value to be checked.
     * @param boolean $encodeAmp       Optional; converts & to &#38; if set to true; defaults to false.
     * @param boolean $preserveQuotes  Optional; preserves double and single quotes if true; defaults to false.
     */
      public function removeTagsFromArray($fieldName, $encodeAmp = false, $preserveQuotes = false)  {

        // Set the filter options 
        $this->_filterArgs[$fieldName]['filter'] = FILTER_SANITIZE_STRING;
        // Multiple flags are set using the "binary or" operator
        $this->_filterArgs[$fieldName]['flags'] = FILTER_REQUIRE_ARRAY;
        if ($encodeAmp) {
          $this->_filterArgs[$fieldName]['flags'] |= FILTER_FLAG_ENCODE_AMP;
        }
        if ($preserveQuotes) {
          $this->_filterArgs[$fieldName]['flags'] |= FILTER_FLAG_NO_ENCODE_QUOTES;
        }
     
      }

      
      
      /**
       * Sanitizes input by converting to numeric entities single and double quotes, <, >, &, and
       * characters with an ASCII value of less than 32.
       * 
       * Optional arguments accept an array, convert characters with an ASCII value greater than
       * 127, or strip characters with an ASCII value less than 32 or greater than 127.
       * 
       * @param string  $fieldName  Name of submitted value to be checked. 
       * @param boolean $isArray    Optional; validates an array of strings if true; defaults to false.
       */
      public function useEntities($fieldName, $isArray = false) {

        // Set the filter options 
        $this->_filterArgs[$fieldName]['filter'] = FILTER_SANITIZE_SPECIAL_CHARS;
        $this->_filterArgs[$fieldName]['flags'] = 0;
        if ($isArray) {
          $this->_filterArgs[$fieldName]['flags'] |= FILTER_REQUIRE_ARRAY;
        }
      }
        
      
      
      

    /**
     * Checks the number of characters in the submitted value.
     * 
     * If the submitted data falls outside the specified range, an error message is added to
     * the validator's $_errors property.
     * 
     * @param string  $fieldName  Name of submitted value to be checked
     * @param int     $min        Minimum number of characters expected
     * @param int     $max        Optional; sets the maximum number of characters permitted
     */
      public function checkTextLength($fieldName, $min, $max = null)  {
        // Get the submitted value
        $text = trim($this->_submitted[$fieldName]);
        // Make sure it's a string
        if (!is_string($text)) {
           throw new Exception("The checkTextLength() method can only be applied to strings; $fieldName is the wrong data type.");
        }
        // Make sure the second argument is a number
        if (!is_numeric($min)) {
           throw new Exception("The checkTextLength() method expects a number as the second argument (field name: $fieldName)");
        }
        // If the string is shorter than the minimum, create error message
        if (strlen($text) < $min) {
          // Check whether a valid maximum value has been set
          if (is_numeric($max)) {
            $this->_errors[$fieldName] = ucfirst($fieldName) . " must be between $min and $max characters.";
          } else {
            $this->_errors[$fieldName] = ucfirst($fieldName) . " must be a minimum of $min characters.";
          }
        }
        // If a maximum has been set, and the string is too long, create error message
        if (is_numeric($max) && strlen($text) > $max) {
          if ($min == 0) {
            $this->_errors[$fieldName] = ucfirst($fieldName) . " must be no more than $max characters.";
          } else {
            $this->_errors[$fieldName] = ucfirst($fieldName) . " must be between $min and $max characters.";
          }
        }
      }


      /**
       * Returns an array of required items that have not been filled in
       * 
       * @return array Indexed array of names of missing fields or variables.
       */
      public function getMissing(){
        return $this->_missing;
      }


      /**
       * Returns an array containing the names of fields or variables that failed
       * the validation test.
       * 
       * @return array  Indexed array of fields (variables) that failed validation
       */
      public function getErrors(){
        return $this->_errors;
      }

                                                  
      /**
       * Checks the input type, and assigns the appropriate superglobal array to the submitted property.
       * 
       * Uses the PHP constants defined by the filter functions. 
       * 
       * @param string  $type  Specifies the input type to be processed; valid values: "post" and "get" (case-insensitive)     
       */
      protected function setInputType($type){
        switch (strtolower($type)) {
          case 'post':
            $this->_inputType = INPUT_POST;
            $this->_submitted = $_POST;
            break;
          case 'get':
            $this->_inputType = INPUT_GET;
            $this->_submitted = $_GET;
            break;
          default:
            throw new Exception('Invalid input type. Valid types are "post" and "get".');
        }
      }


      /**
       * Checks the submitted value of all required items to ensure that the field isn't 
       * blank.
       * 
       * If the item is a scalar (single) value, whitespace is stripped from both
       * ends to prevent users from entering a series of spaces. Populates the $_missing
       * property with the names of missing fields or variables. 
       */
      protected function checkRequired(){
        $OK = array();
        foreach ($this->_submitted as $name => $value) {
          $value = is_array($value) ? $value : trim($value);
          if (!empty($value)) {
            $OK[] = $name;
          }
        }
        // pass the values that are not OK to the missing array .    // http://www.developphp.com/view_lesson.php?v=460
        $this->_missing = array_diff($this->_required, $OK);
      }


      
      
  } // end class



