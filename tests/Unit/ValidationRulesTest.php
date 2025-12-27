<?php

namespace Tests\Unit;

use App\Rules\Cnpj;
use App\Rules\Cpf;
use PHPUnit\Framework\TestCase;

class ValidationRulesTest extends TestCase
{
    /** @test */
    public function it_validates_a_valid_cpf()
    {
        $rule = new Cpf;
        
        // CPF válido gerado para teste
        $validCpf = '529.982.247-25'; 
        
        $passed = true;
        $fail = function ($message) use (&$passed) {
            $passed = false;
        };

        $rule->validate('cpf', $validCpf, $fail);
        $this->assertTrue($passed, 'CPF válido deveria passar na validação');
        
        // Teste sem formatação
        $passed = true;
        $rule->validate('cpf', '52998224725', $fail);
        $this->assertTrue($passed, 'CPF sem formatação deveria passar');
    }

    /** @test */
    public function it_rejects_an_invalid_cpf()
    {
        $rule = new Cpf;
        $passed = true;
        $fail = function ($message) use (&$passed) {
            $passed = false;
        };

        // Dígito verificador errado
        $rule->validate('cpf', '529.982.247-00', $fail);
        $this->assertFalse($passed, 'CPF com dígito errado deveria falhar');

        // Todos os dígitos iguais (falso positivo comum em algorítmos ruins)
        $passed = true;
        $rule->validate('cpf', '111.111.111-11', $fail);
        $this->assertFalse($passed, 'CPF com todos dígitos iguais deveria falhar');
        
        // Tamanho errado
        $passed = true;
        $rule->validate('cpf', '123.456.789-0', $fail);
        $this->assertFalse($passed, 'CPF com tamanho errado deveria falhar');
    }

    /** @test */
    public function it_validates_a_valid_cnpj()
    {
        $rule = new Cnpj;
        
        // CNPJ válido (Google Brasil Internet Ltda)
        $validCnpj = '06.990.590/0001-23'; 
        
        $passed = true;
        $fail = function ($message) use (&$passed) {
            $passed = false;
        };

        $rule->validate('cnpj', $validCnpj, $fail);
        $this->assertTrue($passed, 'CNPJ válido deveria passar na validação');
        
        // Teste sem formatação
        $passed = true;
        $rule->validate('cnpj', '06990590000123', $fail);
        $this->assertTrue($passed, 'CNPJ sem formatação deveria passar');
    }

    /** @test */
    public function it_rejects_an_invalid_cnpj()
    {
        $rule = new Cnpj;
        $passed = true;
        $fail = function ($message) use (&$passed) {
            $passed = false;
        };

        // Dígito verificador errado
        $rule->validate('cnpj', '06.990.590/0001-00', $fail);
        $this->assertFalse($passed, 'CNPJ com dígito errado deveria falhar');
        
        // Tamanho errado
        $passed = true;
        $rule->validate('cnpj', '123', $fail);
        $this->assertFalse($passed, 'CNPJ incompleto deveria falhar');
        
        // Sequência repetida
        $passed = true;
        $rule->validate('cnpj', '11.111.111/1111-11', $fail);
        $this->assertFalse($passed, 'CNPJ repetido deveria falhar');
    }
}
