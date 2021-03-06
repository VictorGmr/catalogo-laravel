<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Produto;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produto = Produto::paginate(10);
        return view('catalogo.index', array('produto' => $produto, 'busca'=>null));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::check()){
            return view('catalogo.create');
        }else{
            return redirect('/catalogo');
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::check()){
            $produto = new Produto();

            $produto->nome = $request->nome;
            $produto->preco = $request->preco;
            
            if($produto->save()){
                if($request->hasFile('foto')){
                    $imagem = $request->file('foto');
                    $nomedoarquivo = md5($produto->id);
                    $request->file('foto')->move(public_path('img/produtos/'), $nomedoarquivo);
                }
                return redirect('catalogo');
            }
        }else{
            return redirect('/catalogo');
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produto = Produto::find($id);
        return view('catalogo.show', array('produto' => $produto));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::check()){
            $produto = Produto::find($id);
            return view('catalogo.edit', array('produto' => $produto));
        }else{
            return redirect('/catalogo');
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::check()){
            $produto = Produto::find($id);
            $produto->nome = $request->nome;
            $produto->preco = $request->preco;

            if($request->hasFile('foto')){
                $imagem = $request->file('foto');
                $nomedoarquivo = md5($produto->id);
                $request->file('foto')->move(public_path('img/produtos/'), $nomedoarquivo);
            }

            if($produto->save()){
                return redirect('catalogo/'.$produto->id);
            }
        }else{
            return redirect('/catalogo');
        }

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::check()){
            $produto = Produto::find($id);
            $produto->delete();

            return redirect('catalogo');
        }else{
            return redirect('/catalogo');
        }

        
    }


    public function buscar(Request $request){

        $produto = Produto::where('nome', 'LIKE', '%'.$request->input('busca').'%')->paginate(10);

        return view('catalogo.index', array('produto' => $produto, 'busca'=>$request->input('busca')));
    }
}
