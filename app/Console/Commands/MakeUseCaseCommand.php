<?php
// app/Console/Commands/MakeUseCaseCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeUseCaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:usecase 
                            {domain : Nome do domínio (ex: User, Vehicle, DriverDocument)} 
                            {action : Ação do UseCase (ex: Create, Update, Delete, Get, List, Respond, Optimize)}
                            {--force : Sobrescrever se já existir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um UseCase para um domínio e ação específicos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = Str::studly($this->argument('domain'));
        $action = Str::studly($this->argument('action'));
        $force = $this->option('force');

        // 👉 MONTAR O NOME DO USECASE
        $useCaseName = "{$action}{$domain}UseCase";
        $path = app_path("Application/{$domain}/UseCases/{$useCaseName}.php");

        // 👉 VERIFICAR SE JÁ EXISTE
        if (File::exists($path) && !$force) {
            $this->error("❌ O UseCase {$useCaseName} já existe!");
            $this->info("   Use --force para sobrescrever.");
            return 1;
        }

        // 👉 CRIAR DIRETÓRIO SE NÃO EXISTIR
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // 👉 GERAR O CONTEÚDO DO USECASE
        $content = $this->generateUseCaseContent($domain, $action, $useCaseName);

        // 👉 SALVAR O ARQUIVO
        File::put($path, $content);

        $this->info("✅ UseCase criado com sucesso!");
        $this->line("   📄 Arquivo: {$path}");
        $this->line("   📦 Classe:  {$useCaseName}");

        return 0;
    }

    /**
     * Gera o conteúdo do UseCase baseado no domínio e ação
     */
    private function generateUseCaseContent(string $domain, string $action, string $useCaseName): string
    {
        // 👉 DEFINIR O CONTEÚDO BASEADO NA AÇÃO
        return match ($action) {
            'Create' => $this->generateCreateUseCase($domain, $useCaseName),
            'Update' => $this->generateUpdateUseCase($domain, $useCaseName),
            'Delete' => $this->generateDeleteUseCase($domain, $useCaseName),
            'Get' => $this->generateGetUseCase($domain, $useCaseName),
            'List' => $this->generateListUseCase($domain, $useCaseName),
            'Respond' => $this->generateRespondUseCase($domain, $useCaseName),
            'Optimize' => $this->generateOptimizeUseCase($domain, $useCaseName),
            default => $this->generateGenericUseCase($domain, $action, $useCaseName),
        };
    }

    /**
     * Gera UseCase de Criação
     */
    private function generateCreateUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Application\\{$domain}\\DTOs\\Create{$domain}Data;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(Create{$domain}Data \$data): {$domain}
    {
        // 👉 CRIAR A ENTIDADE
        \$entity = new {$domain}(
            // Adicione os parâmetros do construtor
        );

        // 👉 SALVAR
        \$this->repository->save(\$entity);

        return \$entity;
    }
}
PHP;
    }

    /**
     * Gera UseCase de Atualização
     */
    private function generateUpdateUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Application\\{$domain}\\DTOs\\Update{$domain}Data;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(int \$id, Update{$domain}Data \$data): {$domain}
    {
        // 👉 BUSCAR A ENTIDADE
        \$entity = \$this->repository->findById(\$id);

        if (!\$entity) {
            throw new \\DomainException('{$domain} não encontrado');
        }

        // 👉 ATUALIZAR OS DADOS
        // \$entity->update(...);

        // 👉 SALVAR
        \$this->repository->save(\$entity);

        return \$entity;
    }
}
PHP;
    }

    /**
     * Gera UseCase de Exclusão
     */
    private function generateDeleteUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(int \$id): void
    {
        // 👉 VERIFICAR SE EXISTE
        \$entity = \$this->repository->findById(\$id);

        if (!\$entity) {
            throw new \\DomainException('{$domain} não encontrado');
        }

        // 👉 DELETAR
        \$this->repository->delete(\$id);
    }
}
PHP;
    }

    /**
     * Gera UseCase de Busca
     */
    private function generateGetUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(int \$id): ?{$domain}
    {
        return \$this->repository->findById(\$id);
    }

    public function list(array \$filters = [], int \$page = 1, int \$perPage = 15): array
    {
        return \$this->repository->findAll(\$filters, \$page, \$perPage);
    }
}
PHP;
    }

    /**
     * Gera UseCase de Listagem
     */
    private function generateListUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Application\\{$domain}\\DTOs\\Filter{$domain}Data;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(Filter{$domain}Data \$data): array
    {
        \$filters = [
            // Adicione os filtros
        ];

        return \$this->repository->findAll(
            \$filters,
            \$data->page,
            \$data->perPage
        );
    }
}
PHP;
    }

    /**
     * Gera UseCase de Resposta
     */
    private function generateRespondUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Application\\{$domain}\\DTOs\\Respond{$domain}Data;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(Respond{$domain}Data \$data): {$domain}
    {
        // 👉 BUSCAR A ENTIDADE
        \$entity = \$this->repository->findById(\$data->id);

        if (!\$entity) {
            throw new \\DomainException('{$domain} não encontrado');
        }

        // 👉 RESPONDER
        \$entity->respond(\$data->status, \$data->message);

        // 👉 SALVAR
        \$this->repository->save(\$entity);

        return \$entity;
    }
}
PHP;
    }

    /**
     * Gera UseCase de Otimização
     */
    private function generateOptimizeUseCase(string $domain, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(int \$id): array
    {
        // 👉 BUSCAR A ENTIDADE
        \$entity = \$this->repository->findById(\$id);

        if (!\$entity) {
            throw new \\DomainException('{$domain} não encontrado');
        }

        // 👉 LÓGICA DE OTIMIZAÇÃO
        // ...

        return [
            'message' => 'Otimização concluída',
        ];
    }
}
PHP;
    }

    /**
     * Gera UseCase Genérico
     */
    private function generateGenericUseCase(string $domain, string $action, string $useCaseName): string
    {
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;

class {$useCaseName}
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository,
    ) {}

    public function execute(): void
    {
        // 👉 IMPLEMENTE A LÓGICA DO USE CASE
    }
}
PHP;
    }
}