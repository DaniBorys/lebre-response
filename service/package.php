<?php
/**
 * Serviço de planejamento e carregamento dos pacotes do LEBRE.
 *
 * Proposta documentada em 2026-11-11 22:09:12 UTC−03:00.
 */
declare(strict_types=1);

namespace Service;

use Lebre\Package\Autoload;
use Lebre\Package\CatalogInterface;
use Lebre\Package\Mapping;
use Lebre\Package\Repository;
use Lebre\Response\Response;
use RuntimeException;
use Throwable;

/**
 * Reúne os catálogos em um repositório e mantém o registro de pacotes
 * carregados durante a vida da instância do serviço.
 */
final class Package
{
    private readonly Mapping $mapping;

    private readonly Autoload $autoload;

    /**
     * Inicializa o serviço com os catálogos disponíveis.
     *
     * @param CatalogInterface ...$catalogs Catálogos de pacotes a registrar.
     * @throws RuntimeException Se um identificador aparecer mais de uma vez.
     */
    public function __construct(CatalogInterface ...$catalogs)
    {
        $this->mapping = new Mapping(new Repository(...$catalogs));
        $this->autoload = new Autoload();
    }

    /**
     * Resolve em conjunto os pacotes solicitados e suas dependências.
     *
     * @param string ...$packages Identificadores dos pacotes solicitados.
     * @return Response Resposta com a contagem de referências em `mapping`,
     *                  a ordem dos pacotes em `dependency` e os caminhos em
     *                  `autoload`, ou com a falha retornada por Mapping.
     * @throws Throwable Se a inclusão ou a execução de um manifesto lançar
     *                   uma exceção ou um erro.
     */
    public function resolve(string ...$packages): Response
    {
        return $this->mapping->resolve(...$packages);
    }

    /**
     * Executa um plano aprovado por resolve().
     *
     * @param array{
     *     mapping: array<array-key, int>,
     *     dependency: list<string>,
     *     autoload: list<string>
     * } $plan Conteúdo da resposta aprovada.
     * @return Response Resposta com os identificadores dos pacotes do plano
     *                  ou com a falha retornada por Autoload.
     */
    public function load(array $plan): Response
    {
        return $this->autoload->load($plan);
    }
}