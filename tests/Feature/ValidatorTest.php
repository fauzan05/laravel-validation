<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidationValidator;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidatorSuccess()
    {
        $data = [
            "username" => "admin",
            "password" => "12345"
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
        self::assertFalse($validator->fails());
        self::assertTrue($validator->passes());
    }
    public function testValidatorFails()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
        self::assertTrue($validator->fails());
        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
    public function testValidatorValidationException()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
        try{
            $validator->validate();
            self::fail("Validation Exception not thrown");
         }catch(ValidationException $e){
            self::assertNotNull($e->validator);
            $message = $e->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidationRules()
    {
        $data = [
            "username" => "admin",
            "password" => "admin"
        ];
        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];
        $validator = Validator::make($data, $rules);
        // var_dump($validator->fails());
        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidData()
    {
        $data = [
            "username" => "admin@a.com",
            "password" => "admin123",
            "is_admin" => true
        ];
        $rules = [
            "username" => "required|email|max:100",
            "password" => "required|min:6|max:20"
        ];
        $validator = Validator::make($data, $rules);
        try{
            $result = $validator->validate();
            self::assertNotNull($result);
            Log::info(json_encode($result, JSON_PRETTY_PRINT));
        }catch(ValidationException $e){
            Log::error(json_encode($e->validator->errors(), JSON_PRETTY_PRINT));
            self::fail($e->getMessage());
        }
    }

    public function testAdditionalValidation()
    {
        $data = [
            "username" => "admin@a.com",
            "password" => "admin@a.com",
            "is_admin" => true
        ];
        $rules = [
            "username" => "required|email|max:100",
            "password" => "required|min:6|max:20"
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function(ValidationValidator $validator){
            $data = $validator->getData();
            if($data['username'] == $data['password']){
                $validator->errors()->add('password', 'Password tidak boleh sama dengan username!');
            }
        });
        self::assertFalse($validator->passes());
        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
    public function testCustomRulesUppercase()
    {
        $data = [
            "username" => "admin@a.com",
            "password" => "admin@a.com",
            "is_admin" => true
        ];
        $rules = [
            "username" => ["required", "email", "max:100", new Uppercase()],
            "password" => ["required", "min:6", "max:20", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertFalse($validator->passes());
        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testRuleClass()
    {
        $data = [
            "username" => "Admin",
            "password" => "admin@a.com",
            "is_admin" => true
        ];
        $rules = [
            "username" => ["required", new In(["Fauzan", "Admin", "Susi"])],
            "password" => ["required", Password::min(6)->letters()->numbers()->symbols()]
        ];
        $validator = Validator::make($data, $rules);
        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }
}
