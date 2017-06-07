<?php
/*
 * Simple PHP Input Validation Class
 *
 * This is a simple PHP class with the most common input validations.
 * Supported Validations:
 *   - alphaNumeric : [A-Z] and [0-9]
 *   - alphaSpace : [A-Z] plus [SPACE]
 *   - alpha : [A-Z]
 *	 - any : Any character
 *   - numeric, integer, float, email, ip, url, subDomain, domain
 *
 * Usage:
 *
 *   $validations = array(
 *      [KEY] => array(
 *                      [VALIDATION],
 *                      [false/true],   # Is the field required, default is true
 *                      [CUSTOM ERROR], # Your Custom error
 *      ),
 *   );
 *
 *   eg:
 *   require "class.SimpleValidate.php";
 *
 *   $validations = array(
 *     "name"  => array( "alphaSpace" ),
 *     "phone" => array( "integer", false ,"Phone number is invalid" ),
 *   );
 *
 *   $result = SimpleValidate::validate( $_POST, $validations );
 *   if( $result==false )
 *   {
 *     $errors = SimpleValidate::getErrors();
 *   }
 *
 * https://github.com/twizdev/simple-php-input-validation
 *
 * Copyright 2017, TwizDev
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

Class SimpleValidate
{
	private static $errors = array();
	private static $defaultError = "field is invalid";
	private static $defaultErrorRequired = "field required";

	public static function validate( $inputs, $validations )
	{
		if( empty( $validations ) )
		{
			return true;
		}

		// RESET ERRORS
		self::$errors = array();

		// IF INPUT IS NOT ARRAY, CONVERT TO ARRAY
		if( is_array( $inputs )===false )
		{
			$inputs = array( $inputs );
		}

		// ARRAY LOOP
		foreach( $validations as $name=>$validation )
		{
			$custom_error = self::$defaultError;
			$required = true;


			if( is_array( $validation ) )
			{
				$thisValidation = $validation[0];
			}else{
				$thisValidation = $validation;
			}

			// REQUIRED
			if( isset( $validation[1] ) )
			{
				$required = $validation[1];
			}

			// CUSTOM ERROR
			if( isset( $validation[2] ) )
			{
				$custom_error = $validation[2];
			}

			// CHECK REQUIRED
			if( $required && ( !isset( $inputs[ $name ] ) || empty( $inputs[ $name ] ) ) )
			{
				self::$errors[] = array(
					$name => self::$defaultErrorRequired
				);

				continue;

			// NOT REQUIRED CONTINUE
			}elseif( !$required && ( !isset( $inputs[ $name ] ) || empty( $inputs[ $name ] ) ) ){
				continue;
			}

			// CHECK IF VALIDATION EXISTS
			if( is_callable( array( "self", "_{$thisValidation}" ) )===false )
			{
				self::$errors[] = array(
					$name => "Unknown validation"
				);

				continue;
			}

			// VALIDATE
			if( !call_user_func( "self::_{$thisValidation}", $inputs[ $name ] ) )
			{
				self::$errors[] = array(
					$name => $custom_error
				);

				continue;
			}
		}

		if( empty( self::$errors ) )
		{
			return true;
		}else{
			return false;
		}
	}

	public static function getErrors()
	{
		return self::$errors;
	}

	protected static function _any( $input )
	{
		return true;
	}

	protected static function _alphaNumeric( $input )
	{
		return preg_match( "/^[a-z0-9À-ÿ]+$/i", $input );
	}

	protected static function _alphaSpace( $input )
	{
		return preg_match( "/^[ a-zÀ-ÿ]+$/i", $input );
	}

	protected static function _alpha( $input )
	{
		return preg_match( "/^[a-zÀ-ÿ]+$/i", $input );
	}

	protected static function _numeric( $input )
	{
		return is_numeric($input);
	}

	protected static function _integer( $input )
	{
        if(	is_int( $input ) )
		{
            return true;
        }elseif( is_string( $input ) ){
            return ctype_digit( $input );
        }

		return false;
	}

	protected static function _float( $input )
	{
        if(	is_float( $input + 0 ) )
		{
            return true;
        }

		return false;
	}

	protected static function _email( $input )
	{
		return filter_var( $input, FILTER_VALIDATE_EMAIL );
	}

    protected static function _ip( $input )
	{
        return filter_var( $input, FILTER_VALIDATE_IP );
    }

    protected static function _url( $input )
	{
        return filter_var( $input, FILTER_VALIDATE_URL );
    }

    protected static function _subDomain( $input )
	{
		return preg_match( "/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/i", $input );
    }

    protected static function _domain( $input )
	{
		return preg_match( "/^(?!\-)[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.(?!\d+)[a-zA-Z\d]{1,63}$/i", $input );
    }
}
