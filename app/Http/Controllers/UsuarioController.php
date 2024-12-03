<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index() {
        $dados = Usuario::all();
        return view('usuarios.index', [
            'usuarios' => $dados,
        ]);
    }

    public function cadastrar() {
        return view('usuarios.cadastrar');
    }

    public function gravar(Request $form){
        $dados = $form->validate([
            'name' => 'required',
            'email' => 'email|required|unique:usuarios', //não deixa cadastrar o mesmo email
            'username' => 'required|min:3',
            'password' => 'required|min:3',
        ]);

        $dados['password'] = Hash::make($dados['password']);

        Usuario::create($dados);

        return redirect()->route('usuarios');
    }

    public function apagar(Usuario $usuario) { //mostra na tela confirmação
        return view('usuarios.apagar', [
            'usuario' => $usuario,
        ]);
    }

    public function deletar(Usuario $usuario) // mostra na tela a efetivação 
    { 
        $usuario->delete();
        return redirect()->route('usuarios');
    }

    public function editar(Usuario $usuario) {
        return view('usuarios/editar', ['usuario' => $usuario]);
    }

    public function editarGravar(Request $form, Usuario $usuario) {
        $dados = $form->validate([
            'name' => 'required',
            'email' => 'email|required|unique:usuarios', //não deixa cadastrar o mesmo email
            'username' => 'required|min:3',
            'password' => 'required|min:3',
            'admin' => 'boolean',
        ]);

        $usuario->fill($dados);
        $usuario->save();
        return redirect()->route('usuario');
    }

    public function login(Request $form) {
        if ($form->isMethod('POST')) {

            //pega os dados do formulário
            $credenciais = $form->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            // tenta fazer o login
            if(Auth::attempt($credenciais)){
                return redirect()->intended(route('index'));
            }else {
                return redirect()->route('login')
                ->with('erro', 'Usuário ou senha inválidos');
            }
        }

        return view('usuarios.login');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('index');
        
    }
}
