<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Endereco;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RegisterPage extends Component
{
    // Campos básicos
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    
    // Tipo de pessoa
    public $user_type = 'pf'; // 'pf' = Pessoa Física, 'pj' = Pessoa Jurídica
    
    // Campos Pessoa Física
    public $cpf = '';
    public $celular = '';
    
    // Campos Pessoa Jurídica
    public $cnpj = '';
    public $razao_social = '';
    public $nome_fantasia = '';
    public $inscricao_estadual = '';
    
    // Campos de Endereço (obrigatório para PJ)
    public $cep = '';
    public $rua = '';
    public $numero = '';
    public $complemento = '';
    public $cidade = '';
    public $estado = '';

    public function mount()
    {
        // Se já estiver logado, redireciona
        if (Auth::check()) {
            return redirect()->route('orders.index');
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_type' => 'required|in:pf,pj',
        ];

        // Regras para Pessoa Física
        if ($this->user_type === 'pf') {
            $rules['cpf'] = 'required|string|size:14'; // Formato: XXX.XXX.XXX-XX
            $rules['celular'] = 'nullable|string|max:15';
        }

        // Regras para Pessoa Jurídica
        if ($this->user_type === 'pj') {
            $rules['cnpj'] = 'required|string|size:18'; // Formato: XX.XXX.XXX/XXXX-XX
            $rules['razao_social'] = 'required|string|min:3|max:255';
            $rules['nome_fantasia'] = 'nullable|string|max:255';
            $rules['inscricao_estadual'] = 'nullable|string|max:20';
            
            // Endereço obrigatório para PJ
            $rules['cep'] = 'required|string|size:9'; // Formato: XXXXX-XXX
            $rules['rua'] = 'required|string|min:3|max:255';
            $rules['numero'] = 'required|string|max:20';
            $rules['complemento'] = 'nullable|string|max:100';
            $rules['cidade'] = 'required|string|min:2|max:100';
            $rules['estado'] = 'required|string|size:2';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
        'email.required' => 'O campo e-mail é obrigatório.',
        'email.email' => 'Digite um e-mail válido.',
        'email.unique' => 'Este e-mail já está cadastrado.',
        'password.required' => 'O campo senha é obrigatório.',
        'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        'password.confirmed' => 'As senhas não coincidem.',
        // PF
        'cpf.required' => 'O CPF é obrigatório.',
        'cpf.size' => 'CPF inválido. Use o formato: XXX.XXX.XXX-XX',
        // PJ
        'cnpj.required' => 'O CNPJ é obrigatório para empresas.',
        'cnpj.size' => 'CNPJ inválido. Use o formato: XX.XXX.XXX/XXXX-XX',
        'razao_social.required' => 'A Razão Social é obrigatória para empresas.',
        'razao_social.min' => 'A Razão Social deve ter no mínimo 3 caracteres.',
        // Endereço
        'cep.required' => 'O CEP é obrigatório.',
        'cep.size' => 'CEP inválido. Use o formato: XXXXX-XXX',
        'rua.required' => 'O endereço é obrigatório.',
        'numero.required' => 'O número é obrigatório.',
        'cidade.required' => 'A cidade é obrigatória.',
        'estado.required' => 'O estado é obrigatório.',
        'estado.size' => 'Use a sigla do estado (ex: SP, RJ, AC).',
    ];

    public function setUserType($type)
    {
        $this->user_type = $type;
        // Limpar erros quando trocar o tipo
        $this->resetValidation();
    }

    public function register()
    {
        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'user_type' => $this->user_type,
            'is_admin' => false,
        ];

        // Adicionar campos PF
        if ($this->user_type === 'pf') {
            $userData['cpf'] = $this->cpf;
            $userData['celular'] = $this->celular ?: null;
        }

        // Adicionar campos PJ
        if ($this->user_type === 'pj') {
            $userData['cnpj'] = $this->cnpj;
            $userData['razao_social'] = $this->razao_social;
            $userData['nome_fantasia'] = $this->nome_fantasia ?: null;
            $userData['inscricao_estadual'] = $this->inscricao_estadual ?: null;
        }

        $user = User::create($userData);

        // Criar endereço para PJ
        if ($this->user_type === 'pj') {
            Endereco::create([
                'user_id' => $user->id,
                'cep' => $this->cep,
                'rua' => $this->rua,
                'numero' => $this->numero,
                'complemento' => $this->complemento ?: null,
                'cidade' => $this->cidade,
                'estado' => $this->estado,
            ]);
        }

        Auth::login($user, true);
        session()->regenerate();

        // Enviar código de verificação de email
        $service = new \App\Services\VerificationCodeService();
        $service->createAndSend($user, \App\Models\VerificationCode::TYPE_EMAIL_VERIFICATION);

        // Redirecionar para página de verificação de email
        session()->flash('success', 'Conta criada! Verifique seu email para continuar.');
        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.auth.register-page');
    }
}


