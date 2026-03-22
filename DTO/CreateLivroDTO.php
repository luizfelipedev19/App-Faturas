<?php

class CreateLivroDTO {
    public string $titulo;
    public string $autor;
    public int $ano;
    public ?string $genero;
    public string $status;
    public ?int $avaliacao;
    public ?string $anotacoes;


    public function __construct(array $data)
    {
        $this->titulo = trim($data['titulo'] ?? '');
        $this->autor = trim($data['autor'] ?? '');
        $this->ano = (int) ($data['ano'] ?? 0);
        $this->genero = isset($data['genero']) ? trim($data['genero']) : null;
        $this->status = trim($data['status'] ?? '');
        $this->avaliacao = isset($data['avaliacao']) ? (int) $data['avaliacao'] : null;
        $this->anotacoes = isset($data['anotacoes']) ? trim($data['anotacoes']) : null; 

        $this->validarLivro();
    }

    public function validarLivro(): void {
        if ($this->titulo === ''){
            throw new Exception("Titulo é obrigatório");
        }

        if($this->autor === ''){
            throw new Exception("Autor é obrigatório");
        }

        if($this->ano < 0){
            throw new Exception("Ano inválido");
        }

        if($this->ano > (int) date('Y')){
            throw new Exception("A data não pode ser no futuro");
        }

        $statusValido = ['lendo', 'lido', 'quero_ler'];
        if (!in_array($this->status, $statusValido)){
            throw new Exception("Status inválido. Os status válidos são: " . implode(", ", $statusValido));
        }
        if($this->avaliacao !== null && ($this->avaliacao < 1 || $this->avaliacao > 5)){
            throw new Exception("Avaliação deve ser entre 1 e 5");
        }
    }


}
?>