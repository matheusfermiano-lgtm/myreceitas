<?php
class Recipe {
  private $nome;
  private $descricao;
  private $preco;
  private $estoque;
  private $imagem;
  private $id;

  public function __construct($nome, $descricao, $preco, $estoque, $imagem, $id = null) {
    $this->setNome($nome);
    $this->setDescricao($descricao);
    $this->setPreco($preco);
    $this->setEstoque($estoque);
    $this->setImagem($imagem);
    $this->setId($id);
    
  }

  public function getNome()  { return $this->nome; }
  public function getDescricao() { return $this->descricao; }
  public function getPreco() { return $this->preco; }
  public function getEstoque() { return $this->estoque; }
  public function getImagem() { return $this->imagem; }
  public function getId() { return $this->id; }

  public function setNome($n)  { $this->nome = trim($n); }
  public function setDescricao($d) { $this->descricao = trim($d); }
  public function setPreco($p) { $this->preco = (float)$p; }
  public function setEstoque($e) { $this->estoque = (int)$e; }
  public function setImagem($i) { $this->imagem = $i; }
  public function setId($id)  { $this->id = $id; }

  public function __toString() {
    return "{$this->nome} - {$this->descricao} - R$ {$this->preco} (Estoque: {$this->estoque})";
  }
}

?>