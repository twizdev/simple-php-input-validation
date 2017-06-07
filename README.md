# Simple PHP Input Validation Class

This is a simple PHP class with the most common input validations.


###### VALIDATIONS
* **alphaNumeric** - [A-Z] and [0-9]
* **alphaSpace**   - [A-Z] plus [SPACE]
* **alpha**        - [A-Z]
* **any**          - Any character
* **numeric, integer, float, email, ip, url, subDomain, domain**

## Usage
```
$validations = array(  
   [KEY] => array(
          [VALIDATION],
          [false/true],   # Is the field required, default is true
          [CUSTOM ERROR], # Your Custom error
  ),
);
```

## Example
```
require "class.SimpleValidate.php";

$validations = array(
  "name"   => array( "alphaSpace" ),
  "phone"  => array( "integer", false ,"Phone number is invalid" ),
);

$result = SimpleValidate::validate( $_POST, $validations );
if( $result===false )
{
  $errors = SimpleValidate::getErrors();
}
```
