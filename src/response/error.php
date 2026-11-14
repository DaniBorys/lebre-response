<?php
/**
 * Representação de uma falha nas respostas do LEBRE.
 *
 * Revisão da documentação em 2026-11-11 20:58:18 UTC−03:00.
 */
declare(strict_types=1);

namespace Lebre\Response;
/**
 * Identifica uma falha por um código independente do idioma e pelos
 * dados de contexto associados à ocorrência.
 *
 * As propriedades são somente leitura. Objetos armazenados no contexto
 * mantêm sua própria mutabilidade.
 *
 * @package Lebre\Response
 * @version 1.0.0
 * @since 2026-11-01
 */
final readonly class Error
{
    /**
     * Código que identifica a falha.
     */
    private string $code;

    /**
     * Dados associados à ocorrência.
     *
     * @var array<array-key, mixed>
     */
    private array $context;

    /**
     * Armazena o código e o contexto conforme recebidos.
     *
     * @param string $code Código da falha.
     * @param array<array-key, mixed> $context Dados da ocorrência. O padrão é um array vazio.
     */
    public function __construct(string $code, array $context = [])
    {
        $this->code = $code;
        $this->context = $context;
    }

    /**
     * Retorna o código informado na construção da falha.
     *
     * @return string Código da falha.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Retorna os dados associados à falha.
     *
     * @return array<array-key, mixed> Contexto com as chaves e os valores armazenados.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Retorna a falha como um array associativo, preservando o código
     * e os dados do contexto conforme armazenados.
     *
     * @return array{
     *     code: string,
     *     context: array<array-key, mixed>
     * }
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'context' => $this->context,
        ];
    }
}