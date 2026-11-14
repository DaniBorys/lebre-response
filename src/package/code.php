<?php

/**
 * Catálogo de códigos de erro do pacote Lebre\Package.
 *
 * Revisão da documentação em 2026-11-11 21:32:42 UTC−03:00.
 */

declare(strict_types=1);

namespace Lebre\Package;

/**
 * Centraliza os identificadores de erro do repositório, da resolução
 * de dependências e da preparação e execução do plano de carregamento.
 */
class Code
{
    /**
     * Um identificador de pacote foi registrado mais de uma vez nos catálogos.
     */
    public const PACKAGE_DUPLICATE = 'package-duplicate';

    /**
     * O pacote consultado no repositório está ausente dos catálogos registrados.
     */
    public const PACKAGE_NOT_FOUND = 'package-not-found';

    /**
     * O caminho de um manifesto ou de um arquivo de autoload está ausente
     * ou aponta para algo diferente de um arquivo regular.
     */
    public const PACKAGE_FILE_NOT_FOUND = 'package-file-not-found';

    /**
     * A execução do arquivo PHP do manifesto retornou um valor diferente de um array.
     */
    public const PACKAGE_INVALID_MANIFEST = 'package-invalid-manifest';

    /**
     * O percurso de dependências voltou a um pacote que ainda estava em avaliação,
     * formando um ciclo.
     */
    public const PACKAGE_DEPENDENCY_CYCLE = 'package-dependency-cycle';

    /**
     * O manifesto de um pacote visitado durante a resolução está ausente
     * do conjunto de manifestos fornecido.
     */
    public const PACKAGE_DEPENDENCY_NOT_FOUND = 'package-dependency-not-found';

    /**
     * O manifesto visitado tem formato inválido ou sua chave `dependency`
     * está ausente ou contém um valor diferente de uma lista.
     */
    public const PACKAGE_DEPENDENCY_INVALID_MANIFEST = 'package-dependency-invalid-manifest';

    /**
     * Um item da lista `dependency` é uma string vazia ou tem outro tipo.
     */
    public const PACKAGE_DEPENDENCY_INVALID_PACKAGE = 'package-dependency-invalid-package';

    /**
     * A chave `autoload` está ausente, contém um valor diferente de uma lista
     * ou tem um item que é uma string vazia ou de outro tipo.
     */
    public const PACKAGE_INVALID_AUTOLOAD = 'package-invalid-autoload';

    /**
     * A inclusão ou a execução de um arquivo de autoload lançou um Throwable.
     */
    public const PACKAGE_AUTOLOAD_FILE_FAILED = 'package-autoload-file-failed';

    /**
     * Impede a instanciação do catálogo de códigos.
     */
    private function __construct() {}
}