**PHP Validator**

## Options
* [required](#required)
* [alpha](#alpha)
* [numeric](#numeric)
* [min](#min)
* [max](#max)
* [email](#email)
* [alphaNumeric](#alphaNumeric)
* [password](#password)
* [number](#number)
* [uppercase](#uppercase)
* [lowercase](#lowercase)
* [specialCharacter](#specialCharacter)
* [phoneNumber](#phoneNumber)
* [isString](#isString)
* [custom](#custom)


## EXAMPLE

sample input
`$input = array("name"=>"vicky", "age"=>20,"email"=>"vicky@gmail.com","password"=>"Vicky007","phoneNumber"=>"+911234567890");`
`$rules = array( "name"=>"required|alpha|min:5|max:10","age"=>"numeric|required|min:1|max:2|","email"=>"required|email","password"=>"required|uppercase|lowercase|number|specialCharacter|min:8","phoneNumber"=>"required|phoneNumber");`

sample output
`stdClass Object`
`(`
`    [name] => vicky`
`    [age] => 20`
`    [email] => vicky@gmail.com`
`    [password] => Vicky007&`
`    [phoneNumber] => +911234567890`
`    [success] => true`
`    [error_message] => null`
`)`
