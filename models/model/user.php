<?php

class User {
  private $nome;
  private $cpf;
  private $email;
  private $telefone;
  private $endereco;
  private $imagem;
  private $id;

  public function __construct($nome, $cpf, $email, $telefone, $endereco, $imagem, $id = null) {
    $this->setNome($nome);
    $this->setCpf($cpf);
    $this->setEmail($email);
    $this->setTelefone($telefone);
    $this->setEndereco($endereco);
    $this->setImagem($imagem);
    $this->setId($id);
    
  }

  public function getNome()  { return $this->nome; }
  public function getCpf() { return $this->cpf; }
  public function getEmail() { return $this->email; }
  public function getTelefone() { return $this->telefone; }
  public function getEndereco() { return $this->endereco; }
  public function getImagem() { return $this->imagem; }
  public function getId() { return $this->id; }

  public function setNome($n)  { $this->nome = trim($n); }
  public function setCpf($c) { $this->cpf = trim($c); }
  public function setEmail($e) { $this->email = trim($e); }
  public function setTelefone($t) { $this->telefone = trim($t); }
  public function setEndereco($end) { $this->endereco = trim($end); }
  public function setImagem($img) { $this->imagem = $img; }
  public function setId($id)  { $this->id = $id; }

  public function __toString() {
    return "{$this->nome} - {$this->cpf} - {$this->email} - {$this->telefone} - {$this->endereco}";
  }

}?>