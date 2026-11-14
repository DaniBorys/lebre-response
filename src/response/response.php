<?php
/**
 * Resultado padronizado das operações do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 20:56:57 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Response;
/**
 * Representa uma operação aprovada ou reprovada.
 *
 * Uma resposta aprovada contém um valor opcional e uma coleção de erros
 * vazia. Uma resposta reprovada contém pelo menos um erro e conteúdo nulo.
 * Objetos armazenados no conteúdo mantêm sua própria mutabilidade.
 *
 * @package Lebre\Response
 * @version 1.0.0
 * @since 2026-11-01
 */
final readonly class Response
{
    /**
     * Indica se a operação foi aprovada.
     */
    private bool $approved;

    /**
     * Armazena o conteúdo da resposta.
     */
    private mixed $content;

    /**
     * Armazena os erros na ordem em que foram fornecidos.
     *
     * @var array<array-key, Error>
     */
    private array $errors;

    /**
     * Inicializa a resposta a partir de success() ou error().
     *
     * @param bool $approved Indica se a operação foi aprovada.
     * @param mixed $content Conteúdo da resposta.
     * @param array<array-key, Error> $errors Erros associados à operação.
     */
    private function __construct(
        bool $approved,
        mixed $content,
        array $errors
    ) {
        $this->approved = $approved;
        $this->content = $content;
        $this->errors = $errors;
    }

    /**
     * Cria uma resposta de aprovação com conteúdo opcional.
     *
     * @param mixed $content Conteúdo retornado pela operação. O padrão é null.
     * @return self Resposta aprovada com uma coleção de erros vazia.
     */
    public static function success(mixed $content = null): self
    {
        return new self(true, $content, []);
    }

    /**
     * Cria uma resposta de reprovação com pelo menos um erro.
     *
     * @param Error $error Primeiro erro da operação.
     * @param Error ...$others Erros adicionais. Argumentos nomeados mantêm seus nomes como chaves.
     * @return self Resposta reprovada com conteúdo nulo.
     */
    public static function error(Error $error, Error ...$others): self
    {
        return new self(false, null, [$error, ...$others]);
    }

    /**
     * Indica se a operação foi aprovada.
     *
     * @return bool true para aprovação e false para reprovação.
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * Retorna o conteúdo armazenado na resposta.
     *
     * @return mixed Conteúdo fornecido a success() ou null em caso de reprovação.
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * Retorna os erros com as chaves e a ordem em que foram armazenados.
     *
     * @return array<array-key, Error> Coleção vazia quando a operação foi aprovada.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retorna o estado, o conteúdo e os erros em um array associativo.
     *
     * Preserva o conteúdo como foi armazenado e converte os erros em uma
     * lista sequencial por meio de Error::toArray().
     *
     * @return array{
     *     approved: bool,
     *     content: mixed,
     *     errors: list<array{
     *         code: string,
     *         context: array<string, mixed>
     *     }>
     * }
     */
    public function toArray(): array
    {
        $errors = [];

        foreach ($this->errors as $error) {
            $errors[] = $error->toArray();
        }

        return [
            'approved' => $this->approved,
            'content' => $this->content,
            'errors' => $errors,
        ];
    }
}