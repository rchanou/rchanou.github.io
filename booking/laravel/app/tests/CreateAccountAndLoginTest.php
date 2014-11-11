<?php


class CreateAccountAndLoginTest extends TestCase
{
    //Club Speed accounts should be create-able
    public function testAccountCreation()
    {
        //Prepare a new customer for creation
        $randomNumber = rand(1,20000);
        $clubSpeedNewCustomerData = array(
            "email" => "testracer" . $randomNumber . "@example.com",
            "password" => "test",
            "donotemail" => 1,
            "Company" => "Test Company",
            "firstname" => "Test",
            "lastname" => "Racer",
            "racername" => "Test Racer " . $randomNumber,
            "birthdate" => "2014-03-12",
            "gender" => 1,
            "howdidyouhearaboutus" => 1,
            "Address" => "Test Address",
            "Address2" => "Test Address 2",
            "City" => "Test City",
            "State" => "Test State",
            "Zip" => "Test Zip",
            "Country" => "Test Country",
            "mobilephone" => "Test Mobile Phone",
            "LicenseNumber" => "Test License Number",
            "Custom1" => "Test Custom 1",
            "Custom2" => "Test Custom 2",
            "Custom3" => "Test Custom 3",
            "Custom4" => "Test Custom 4"
        );

        //Try to create a new customer
        $result = CS_API::createClubSpeedAccount($clubSpeedNewCustomerData);
        $this->assertTrue($result !== null); //The call didn't fail
        $this->assertTrue(is_integer($result)); //The call resulted in a customer ID being returned to us

        //The new user should be able to login
        $result = CS_API::loginToClubSpeed($clubSpeedNewCustomerData['email'],$clubSpeedNewCustomerData['password']);
        $this->assertTrue($result !== null); //The call didn't fail
        $this->assertTrue(isset($result->customerId)); //We received the customer's id
        $this->assertTrue(is_integer($result->customerId)); //The customer ID was an integer
        $this->assertTrue(isset($result->firstName)); //We received the customer's first name
    }
} 