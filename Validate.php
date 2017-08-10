<?php

    class Validate
    {       
        /*
        // used to validate Validate
        @param array $input
        @param array $rules
        @return Array
        */
        public static function check($input,$rules)
        {           
            $input = Validate::MysqlEscapeString($input);
            $input = Validate::htmlSpecialChars($input);

            $output = array();
            $error = 0;
            foreach($input as $inputKey => $inputValue)
            {
                foreach($rules as $rulesKey => $rulesValue)
                {
                    if($inputKey == $rulesKey)
                    {
                        $status = Validate::ExplodeRules($rulesValue);
                        foreach ($status as $value)
                        {
                            $functionName = $value;
                            
                            if(preg_match('/min/',$functionName) || preg_match('/max/',$functionName) || preg_match('/custom/',$functionName))
                            {
                                $status = Validate::ExplodeSize($functionName);
                                $functionName =$status[0];
                                $size = $status[1];
                                $result = Validate::$functionName($inputKey,$inputValue,$size);
                            }
                            else
                            {
                                 $result = Validate::$functionName($inputKey,$inputValue);
                            }
                           
                            if($result != "TRUE" )
                            {
                                $error++;
                                $output[$inputKey]=$result;
                                //array_push($output,$result);
                            }
                        }
                    }
                }
            }
            if(empty($output))
            {
                $associativeArray = Validate::inputArray($input);
                $output = array('success' => "true",'error_message' => null);
                $associativeArray = $associativeArray+$output;                
            }
            else
            {
                $associativeArray = Validate::inputArray($input);
                $output = array('success' => "false",'error_message' => $output);
                $associativeArray = $associativeArray+$output;
            } 
            return (object) $associativeArray;           
        } 

        /*
        // used to create a temp array to store a input value
        @param array $input
        @return Array
        */
        public static function inputArray($input)
        {
            $associativeArray = array();
                
            foreach($input as $key=>$value)
            {
                $tempArray = array($key=>$value);
                //array_merge($associativeArray,$tempArray);
                $associativeArray = $associativeArray+$tempArray;
            }
            return $associativeArray;
        }

        /*
        // used to explode the string using |
        @param string $rule
        */
        public static function ExplodeRules($rule)
        {
                $rule = explode("|",$rule);           
                return $rule;
        }

        /*
        // used to explode the string using ;
        @param string $rule
        */
        public static function ExplodeSize($rule)
        {
                $rule = explode(":",$rule);           
                return $rule;
        }  

        /*
        // used to check the string is empty ;
        @param string $inputKey
        @param string $input
        @return string
        */
        public static function Required($inputKey,$input)
        {
            if (empty($input))
            {
                return "the $inputKey must required";
            }
            else
            {
                return "TRUE";
            }
        }

        /*
        // used to check the value is alphabets  ;
        @param string $inputKey
        @param string $input
        @return string
        */
        public static function alpha($inputKey,$input)
        {
            //if(is_string($input))
            if(preg_match("/^[a-zA-Z]$/", $input))
            {
                return "TRUE";
            }
            else
            {
              return "the $inputKey is not  alphabets";
               // return FALSE;
            }
        }

        /*
        // used to check the value is alphabets  ;
        @param string $inputKey
        @param string $input
        @return string
        */
        public static function isString($inputKey,$input)
        {
            if(is_string($input))
            {
                return "TRUE";
            }
            else
            {
              return "the $inputKey is not  alphabets";
               // return FALSE;
            }
        }   

        /*
        // used to check the value is int  ;
        @param string $inputKey
        @param string $input
        @return string
        */
        public static function numeric($inputKey,$input)
        {
            if(is_int($input))
            {
                return "TRUE";
            }
            else
            {
                //return FALSE;
                return  "the $inputKey is not a  integer";
            }
        }
        
        /*
        // used to check the value has min no of characters is   ;
        @param string $inputKey (or) int $inputKey
        @param string $inputKey
        @param string $size
        @return string
        */
        public static function min($inputKey,$input,$size)
        {
             if(strlen($input) >= $size)
            {
                return "TRUE";
            }
            else
            {
                return  "the $inputKey must be length of $size";  
            }
        }

        /*
        // used to check the value has min no of characters is   ;
        @param string $inputKey (or) int $inputKey
        @param string $inputKey
        @param string $size
        @return string
        */
        public static function max($inputKey,$input,$size)
        {
            if(strlen($input) <= $size)
            {
                return "TRUE";
            }
            else
            {
                return  "the $inputKey must be length of $size";  
            }
        }    

        /*
        // used to check the value match email pattern
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function email($inputKey,$inputValue)
        {
            if (preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+.[a-zA-Z.]{2,5}$/", $inputValue)) 
            {
                return "TRUE";
            }
            else
            {
                return " NOT a valid email address";
            }            
        }

        /*
        // used to check the value has any special characters
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function alphaNumeric($inputKey,$inputValue)
        {
            if(preg_match("/^[a-zA-Z0-9]/", $inputValue))
            {
                return "$inputKey contains special characters";
            }
            else
            {
                 return "TRUE";
            }
        }

        /*
        // used to convert all tags in to string 
        @param array $input 
        @return array
        */
        public static function htmlSpecialChars($input)
        {
            $output = array();
            foreach($input as $key=>$value)
            {
                if(!is_int($value ))
                {
                    $key = htmlspecialchars($key);
                    $value = htmlspecialchars($value);
                    $temp = array($key=>$value);
                    $output = $output + $temp;
                }
                else
                {
                    $temp = array($key=>$value);
                    $output = $output + $temp;                   
                }

            }
            return $output;
        }

        /*
        // used to convert all escape string in to ordinary string 
        @param array $input 
        @return array
        */
        public static function MysqlEscapeString($input)
        {
            $connection = mysqli_connect("localhost", "root", "", "json");
            $output = array();
            foreach($input as $key=>$value)
            {
                if(!is_int($value ))
                {
                $key = mysqli_real_escape_string($connection,$key);
                $value = mysqli_real_escape_string($connection,$value);
                $temp = array($key=>$value);
                $output = $output + $temp;
                }
                else
                {
                $temp = array($key=>$value);
                $output = $output + $temp;                   
                }

            }
            return $output;          
        }

        /*
        // used to check the value has one lower&Uppercase,numeric,min of 8
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function password($inputKey,$inputValue)
        {
            if(strlen($inputValue) >= 8) 
            {
                if(preg_match('@[A-Z]@', $inputValue))
                {
                    if(preg_match('@[a-z]@', $inputValue))
                    {
                        if(preg_match('@[0-9]@', $inputValue))
                        {
                            return "TRUE";
                        }
                        else
                        {
                            return "$inputKey MUST CONTAINS ONE numeric";
                        }
                    }
                    else
                    {
                        return "$inputKey MUST CONTAINS ONE lowercase LETTER";
                    }
                }
                else
                {
                    return "$inputKey must contains atleast one UPPERCASE letter";
                }
            }
            else
            {
                return "$inputKey must contains 8 letters";
            }
        }
        /*
        // used to check the value has atleast one number
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function number($inputKey,$inputValue)
        {
            if(preg_match('@[0-9]@', $inputValue))
            {
                return "TRUE";
            }
            else
            {
                return "$inputKey must contains letter";
            }
        }

        /*
        // used to check the value has atleast one uppercase letter
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function uppercase($inputKey,$inputValue)
        {
            if(preg_match('@[A-Z]@', $inputValue))
            {
                return "TRUE";
            }
            else
            {
                return "$inputKey must contain UPPERCASE letter";
            }
        }

        /*
        // used to check the value has atleast one lowercase letter
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function lowercase($inputKey,$inputValue)
        {
            if(preg_match('@[a-z]@', $inputValue))
            {
                return "TRUE";
            }
            else
            {
                return "$inputKey MUST CONTAIN lowercase LETTER";
            }
        }

        /*
        // used to check the value has atleast one special character
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function specialCharacter($inputKey,$inputValue)
        {

            if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $inputValue))
            {
               return "TRUE";
            }
            else
            {
                
                return  "$inputKey MUST CONTAIN Special Characters";
            }
        }

        /*
        // used to check the value matches phone number pattern
        @param string $inputKey 
        @param string $inputValue
        @return string
        */
        public static function phoneNumber($inputKey,$inputValue)
        {
            if(preg_match('/^[+]?\d{10,14}$/', $inputValue)) 
            {               
               return "TRUE";
            }
            else
            {
                      
                return  "$inputKey is invaild";          
            }

        }

        /*
        // used to execute the user given Validate
        @param string $inputKey 
        @param string $inputValue
        @param string $Validate
        @return string
        */
        public static function custom($inputKey,$inputValue,$Validate)
        {
            $Validate = $Validate;
            if(preg_match($Validate, $inputValue)) 
            {               
               return "TRUE";
            }
            else
            {
                      
                return  "$inputKey is invaild";          
            }            
        }

    } 

    //error_reporting(0);
    $input = array("name"=>"+917200704057", "age"=>28,"email"=>"vickycodename007@gmail.com","password"=>"Vicky$1","phoneNumber"=>"+917200704057");

    $rules = array( "age"=>"alpha|required","name"=>"custom:/^[+]?\d{10,14}$/","email"=>"required|email","phoneNumber"=>"required|phoneNumber");

    $result = Validate::Check($input,$rules);

    echo "<pre>";
    print_r($result);
    var_dump($result);


?>