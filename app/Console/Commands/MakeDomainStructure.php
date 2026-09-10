<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDomainStructure extends Command
{
    protected $signature = 'domain:make {name : Nome do domínio (ex: User, Product, Category)} 
                            {--table= : Nome da tabela (opcional, usa o plural do domínio por padrão)}
                            {--all : Criar estrutura completa com tudo}';

    protected $description = 'Cria a estrutura DDD completa para um domínio';

    public function handle()
    {
        $domain = Str::studly($this->argument('name'));
        $table = $this->option('table') ?: Str::snake(Str::plural($domain));
        $all = $this->option('all');

        $this->info("Criando estrutura DDD para: {$domain}");
        $this->info("Tabela associada: {$table}");

        // Criar estrutura
        $this->createDomainStructure($domain, $table);

        if ($all) {
            $this->createCompleteStructure($domain, $table);
        }

        $this->info("Domínio {$domain} criado com sucesso!");
        $this->info("Localização: app/Domain/{$domain}/");
        
        return 0;
    }

    /**
     * Cria a estrutura básica do domínio
     */
    private function createDomainStructure(string $domain, string $table): void
    {
        // Pastas principais
        $folders = [
            "app/Domain/{$domain}/Entities",
            "app/Domain/{$domain}/ValueObjects",
            "app/Domain/{$domain}/Enums",
            "app/Domain/{$domain}/Repositories",
            "app/Domain/{$domain}/Events",
            "app/Domain/{$domain}/Exceptions",
            "app/Domain/{$domain}/Services",
            "app/Domain/{$domain}/Rules",
            "app/Application/{$domain}/UseCases",
            "app/Application/{$domain}/DTOs",
            "app/Application/{$domain}/Services",
            "app/Infrastructure/{$domain}/Models",
            "app/Infrastructure/{$domain}/Repositories",
            "app/Infrastructure/{$domain}/Mappers",
            "app/Http/Controllers/Api/V1/{$domain}",
        ];

        foreach ($folders as $folder) {
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
                $this->line("   Criado: {$folder}");
            }
        }

        // Criar arquivos básicos
        $this->createEntity($domain);
        $this->createRepositoryInterface($domain);
        $this->createRepositoryEloquent($domain, $table);
        $this->createModel($domain, $table);
        $this->createMapper($domain);
        $this->createDto($domain);
        $this->createUseCases($domain);
        $this->createController($domain);
        // $this->createService($domain);
        $this->createEnum($domain);
        // $this->createFactory($domain);
        // $this->createSeeder($domain, $table);

        // Adicionar ao Service Provider
        $this->registerInServiceProvider($domain);
        
        // Adicionar rotas
        $this->registerRoutes($domain);
    }

    /**
     * Cria a estrutura completa com tudo
     */
    private function createCompleteStructure(string $domain, string $table): void
    {
        $this->info("Criando estrutura completa...");
        
        // Criar Value Objects específicos
        $this->createValueObjects($domain);
        
        // Criar Events
        $this->createEvents($domain);
        
        // Criar Exceptions
        $this->createExceptions($domain);
        
        // Criar Rules
        $this->createRules($domain);
        
        // Criar Tests
        $this->createTests($domain);
    }

    /**
     * Cria a Entity
     */
    private function createEntity(string $domain): void
    {
        $path = "app/Domain/{$domain}/Entities/{$domain}.php";
        
        if (File::exists($path)) {
            $this->line("   ⏭️  Entity já existe: {$domain}");
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Entities;

use DateTimeImmutable;

class {$domain}
{
    private ?int \$id;
    private ?DateTimeImmutable \$createdAt;
    private ?DateTimeImmutable \$updatedAt;
    private ?DateTimeImmutable \$deletedAt;

    public function __construct(?int \$id = null)
    {
        \$this->id = \$id;
        \$this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return \$this->id;
    }

    public function setId(int \$id): self
    {
        \$this->id = \$id;
        return \$this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return \$this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return \$this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable \$updatedAt): self
    {
        \$this->updatedAt = \$updatedAt;
        return \$this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return \$this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable \$deletedAt): self
    {
        \$this->deletedAt = \$deletedAt;
        return \$this;
    }

    public function toArray(): array
    {
        return [];
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Entity criada: {$domain}.php");
    }

    /**
     * Cria o Repository Interface
     */
    private function createRepositoryInterface(string $domain): void
    {
        $path = "app/Domain/{$domain}/Repositories/{$domain}RepositoryInterface.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Repositories;

use App\\Domain\\{$domain}\\Entities\\{$domain};

interface {$domain}RepositoryInterface
{
    public function findById(int \$id): ?{$domain};
    public function findAll(array \$filters = [], int \$page = 1, int \$perPage = 15): array;
    public function findByField(string \$field, mixed \$value): ?{$domain};
    public function findManyByField(string \$field, mixed \$value): array;
    public function save({$domain} \$entity): void;
    public function delete(int \$id): void;
    public function exists(int \$id): bool;
    public function count(array \$filters = []): int;
}
PHP;

        File::put($path, $content);
        $this->line("   Repository Interface criada: {$domain}RepositoryInterface.php");
    }

    /**
     * Cria o Repository Eloquent
     */
    private function createRepositoryEloquent(string $domain, string $table): void
    {
        $path = "app/Infrastructure/{$domain}/Repositories/Eloquent{$domain}Repository.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Infrastructure\\{$domain}\\Repositories;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Infrastructure\\{$domain}\\Models\\{$domain}Model;
use App\\Infrastructure\\{$domain}\\Mappers\\{$domain}Mapper;
use Illuminate\\Pagination\\LengthAwarePaginator;

class Eloquent{$domain}Repository implements {$domain}RepositoryInterface
{
    public function findById(int \$id): ?{$domain}
    {
        \$model = {$domain}Model::find(\$id);
        return \$model ? {$domain}Mapper::toDomain(\$model) : null;
    }

    public function findAll(array \$filters = [], int \$page = 1, int \$perPage = 15): array
    {
        \$query = {$domain}Model::query();

        foreach (\$filters as \$field => \$value) {
            if (\$value !== null) {
                \$query->where(\$field, \$value);
            }
        }

        /** @var LengthAwarePaginator \$paginator */
        \$paginator = \$query->paginate(\$perPage, ['*'], 'page', \$page);

        return [
            'data' => array_map(
                fn(\$model) => {$domain}Mapper::toDomain(\$model),
                \$paginator->items()
            ),
            'total' => \$paginator->total(),
            'current_page' => \$paginator->currentPage(),
            'per_page' => \$paginator->perPage(),
            'last_page' => \$paginator->lastPage(),
        ];
    }

    public function findByField(string \$field, mixed \$value): ?{$domain}
    {
        \$model = {$domain}Model::where(\$field, \$value)->first();
        return \$model ? {$domain}Mapper::toDomain(\$model) : null;
    }

    public function findManyByField(string \$field, mixed \$value): array
    {
        \$models = {$domain}Model::where(\$field, \$value)->get();
        return \$models->map(fn(\$model) => {$domain}Mapper::toDomain(\$model))->toArray();
    }

    public function save({$domain} \$entity): void
    {
        \$data = {$domain}Mapper::toArray(\$entity);
        
        if (\$entity->getId()) {
            \$model = {$domain}Model::find(\$entity->getId());
            if (\$model) {
                \$model->update(\$data);
                return;
            }
        }

        \$model = new {$domain}Model(\$data);
        \$model->save();
        \$entity->setId(\$model->id);
    }

    public function delete(int \$id): void
    {
        {$domain}Model::destroy(\$id);
    }

    public function exists(int \$id): bool
    {
        return {$domain}Model::where('id', \$id)->exists();
    }

    public function count(array \$filters = []): int
    {
        \$query = {$domain}Model::query();

        foreach (\$filters as \$field => \$value) {
            if (\$value !== null) {
                \$query->where(\$field, \$value);
            }
        }

        return \$query->count();
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Repository Eloquent criado: Eloquent{$domain}Repository.php");
    }

    /**
     * Cria o Model
     */
    private function createModel(string $domain, string $table): void
    {
        $path = "app/Infrastructure/{$domain}/Models/{$domain}Model.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Infrastructure\\{$domain}\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class {$domain}Model extends Model
{
    use SoftDeletes;

    protected \$table = '{$table}';

    protected \$fillable = [
        // 'name', 'email', 'status'
    ];

    protected \$casts = [
        // 'status' => 'string'
    ];

    protected \$hidden = [
        // Adicione os campos ocultos aqui
    ];

    protected \$dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relacionamentos
    // public function relacionamento()
    // {
    //     return \$this->belongsTo(OutroModel::class, 'fk_outro_id');
    // }
}
PHP;

        File::put($path, $content);
        $this->line("   Model criado: {$domain}Model.php");
    }

    /**
     * Cria o Mapper
     */
    private function createMapper(string $domain): void
    {
        $path = "app/Infrastructure/{$domain}/Mappers/{$domain}Mapper.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Infrastructure\\{$domain}\\Mappers;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\Domain\\{$domain}\Enums\\{$domain}Status;
use App\\Infrastructure\\{$domain}\\Models\\{$domain}Model;
use DateTimeImmutable;

class {$domain}Mapper
{
    public static function toDomain({$domain}Model \$model): {$domain}
    {
        \$entity = new {$domain}(
            \$model->id
        );

        \$reflection = new \ReflectionClass(\$entity);
        \$statusProperty = \$reflection->getProperty('status');
        \$statusProperty->setAccessible(true);
        \$statusProperty->setValue(\$entity, {$domain}Status::from(\$model->status));

        // Atualizar timestamps
        if (\$model->created_at) {
            \$createdAtProperty = \$reflection->getProperty('createdAt');
            \$createdAtProperty->setAccessible(true);
            \$createdAtProperty->setValue(\$entity, new DateTimeImmutable(\$model->created_at));
        }

        if (\$model->updated_at) {
            \$updatedAtProperty = \$reflection->getProperty('updatedAt');
            \$updatedAtProperty->setAccessible(true);
            \$updatedAtProperty->setValue(\$entity, new DateTimeImmutable(\$model->updated_at));
        }

        if (\$model->deleted_at) {
            \$deletedAtProperty = \$reflection->getProperty('deletedAt');
            \$deletedAtProperty->setAccessible(true);
            \$deletedAtProperty->setValue(\$entity, new DateTimeImmutable(\$model->deleted_at));
        }

        return \$entity;
    }

    public static function toArray({$domain} \$entity): array
    {
        return [];
    }

    public static function toModel({$domain} \$entity): array
    {
        return self::toArray(\$entity);
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Mapper criado: {$domain}Mapper.php");
    }

    /**
     * Cria o DTO
     */
    private function createDto(string $domain): void
    {
        $path = "app/Application/{$domain}/DTOs/Create{$domain}Data.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Application\\{$domain}\\DTOs;

use Illuminate\\Http\\Request;

class Create{$domain}Data
{
    public function __construct(
        public readonly ?int \$id = null,
        // Adicione as propriedades do DTO
        // Exemplo: public readonly string \$name,
        // public readonly string \$email,
    ) {}

    public static function fromRequest(Request \$request): self
    {
        return new self(
            id: \$request->input('id'),
            // Mapeie os campos do request
            // name: \$request->input('name'),
            // email: \$request->input('email'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars(\$this);
    }

    public static function rules(): array
    {
        return [
            // Regras de validação
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email',
        ];
    }

    public static function messages(): array
    {
        return [
            // Mensagens em caso de erro
            // 'file.required' => 'O nome é obrigatório',
            // 'email.unique' => 'Esse email já esta cadastrado',
        ];
    }
}
PHP;

        File::put($path, $content);
        $this->line("   DTO criado: Create{$domain}Data.php");
    }

    /**
     * Cria os UseCases
     */
    private function createUseCases(string $domain): void
    {
        $basePath = "app/Application/{$domain}/UseCases";
        
        // Create
        $path = "{$basePath}/Create{$domain}UseCase.php";
        if (!File::exists($path)) {
            $content = <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Application\\{$domain}\\DTOs\\Create{$domain}Data;

class Create{$domain}UseCase
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository
    ) {}

    public function execute(Create{$domain}Data \$data): {$domain}
    {
        \$entity = new {$domain}(\$data->toArray());
        \$this->repository->save(\$entity);
        return \$entity;
    }
}
PHP;
            File::put($path, $content);
            $this->line("   UseCase criado: Create{$domain}UseCase.php");
        }

        // Update
        $path = "{$basePath}/Update{$domain}UseCase.php";
        if (!File::exists($path)) {
            $content = <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;
use App\\Application\\{$domain}\\DTOs\\{$domain}Data;

class Update{$domain}UseCase
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository
    ) {}

    public function execute(int \$id, {$domain}Data \$data): {$domain}
    {
        \$entity = \$this->repository->findById(\$id);
        if (!\$entity) {
            throw new \\RuntimeException('{$domain} not found');
        }

        \$entity->setData(\$data->toArray());
        \$this->repository->save(\$entity);
        return \$entity;
    }
}
PHP;
            File::put($path, $content);
            $this->line("   UseCase criado: Update{$domain}UseCase.php");
        }

        // Delete
        $path = "{$basePath}/Delete{$domain}UseCase.php";
        if (!File::exists($path)) {
            $content = <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;

class Delete{$domain}UseCase
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository
    ) {}

    public function execute(int \$id): void
    {
        if (!\$this->repository->exists(\$id)) {
            throw new \\RuntimeException('{$domain} not found');
        }

        \$this->repository->delete(\$id);
    }
}
PHP;
            File::put($path, $content);
            $this->line("   UseCase criado: Delete{$domain}UseCase.php");
        }

        // Get
        $path = "{$basePath}/Get{$domain}UseCase.php";
        if (!File::exists($path)) {
            $content = <<<PHP
<?php

namespace App\\Application\\{$domain}\\UseCases;

use App\\Domain\\{$domain}\\Entities\\{$domain};
use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;

class Get{$domain}UseCase
{
    public function __construct(
        private {$domain}RepositoryInterface \$repository
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
            File::put($path, $content);
            $this->line("   UseCase criado: Get{$domain}UseCase.php");
        }
    }

    /**
     * Cria o Controller
     */
    private function createController(string $domain): void
    {
        $path = "app/Http/Controllers/Api/V1/{$domain}/{$domain}Controller.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Http\\Controllers\\Api\\V1\\{$domain};

use App\\Http\\Controllers\\Controller;
use App\\Application\\{$domain}\\UseCases\\Create{$domain}UseCase;
use App\\Application\\{$domain}\\UseCases\\Update{$domain}UseCase;
use App\\Application\\{$domain}\\UseCases\\Delete{$domain}UseCase;
use App\\Application\\{$domain}\\UseCases\\Get{$domain}UseCase;
use App\\Application\\{$domain}\\DTOs\\Create{$domain}Data;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Http\\Request;

class {$domain}Controller extends Controller
{
    public function __construct(
        private Create{$domain}UseCase \$createUseCase,
        private Update{$domain}UseCase \$updateUseCase,
        private Delete{$domain}UseCase \$deleteUseCase,
        private Get{$domain}UseCase \$getUseCase,
    ) {}

    public function index(Request \$request): JsonResponse
    {
        \$filters = \$request->only(['status', 'search']);
        \$page = \$request->input('page', 1);
        \$perPage = \$request->input('per_page', 15);

        \$result = \$this->getUseCase->list(\$filters, \$page, \$perPage);

        return response()->json(\$result);
    }

    public function show(int \$id): JsonResponse
    {
        \$entity = \$this->getUseCase->execute(\$id);

        if (!\$entity) {
            return response()->json(['message' => '{$domain} not found'], 404);
        }

        return response()->json(\$entity->toArray());
    }

    public function store(Request \$request): JsonResponse
    {
        \$request->validate(Create{$domain}Data::rules(), Create{$domain}Data::messages());
        \$data = Create{$domain}Data::fromRequest(\$request);
        \$entity = \$this->createUseCase->execute(\$data);

        return response()->json(\$entity->toArray(), 201);
    }

    // public function update(Request \$request, int \$id): JsonResponse
    // {
    //     \$data = {$domain}Data::fromRequest(\$request);
        
    //     try {
    //         \$entity = \$this->updateUseCase->execute(\$id, \$data);
    //         return response()->json(\$entity->toArray());
    //     } catch (\\RuntimeException \$e) {
    //         return response()->json(['message' => \$e->getMessage()], 404);
    //     }
    // }

    public function destroy(int \$id): JsonResponse
    {
        try {
            \$this->deleteUseCase->execute(\$id);
            return response()->json(null, 204);
        } catch (\\RuntimeException \$e) {
            return response()->json(['message' => \$e->getMessage()], 404);
        }
    }
}
PHP;

        // Criar a pasta se não existir
        $folder = "app/Http/Controllers/Api/V1/{$domain}";
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        File::put($path, $content);
        $this->line("   Controller criado: {$domain}Controller.php");
    }

    /**
     * Cria o Service
     */
    private function createService(string $domain): void
    {
        $path = "app/Domain/{$domain}/Services/{$domain}Service.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Services;

class {$domain}Service
{
    // Implemente as regras de negócio complexas aqui
    // Exemplo: validações, cálculos, processamentos
    
    public function validateData(array \$data): bool
    {
        // Implemente validações específicas do domínio
        return true;
    }

    public function processData(array \$data): array
    {
        // Implemente processamentos de dados
        return \$data;
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Service criado: {$domain}Service.php");
    }

    /**
     * Cria o Enum
     */
    private function createEnum(string $domain): void
    {
        $path = "app/Domain/{$domain}/Enums/{$domain}Status.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Enums;

enum {$domain}Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match(\$this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::PENDING => 'Pendente',
            self::BLOCKED => 'Bloqueado',
        };
    }

    public function color(): string
    {
        return match(\$this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::PENDING => 'yellow',
            self::BLOCKED => 'red',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Enum criado: {$domain}Status.php");
    }

    /**
     * Cria a Factory
     */
    private function createFactory(string $domain): void
    {
        $path = "database/factories/{$domain}Factory.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace Database\\Factories;

use Illuminate\\Database\\Eloquent\\Factories\\Factory;

class {$domain}Factory extends Factory
{
    public function definition(): array
    {
        return [
            // Defina os campos fake aqui
            // Exemplo:
            // 'name' => \$this->faker->name(),
            // 'email' => \$this->faker->unique()->safeEmail(),
            // 'status' => 'active',
        ];
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Factory criada: {$domain}Factory.php");
    }

    /**
     * Cria o Seeder
     */
    private function createSeeder(string $domain, string $table): void
    {
        $path = "database/seeders/{$domain}Seeder.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Infrastructure\\{$domain}\\Models\\{$domain}Model;

class {$domain}Seeder extends Seeder
{
    public function run(): void
    {
        // Crie dados iniciais aqui
        // Exemplo:
        // {$domain}Model::create([
        //     'name' => 'Exemplo',
        //     'email' => 'exemplo@email.com',
        //     'status' => 'active',
        // ]);
        
        // Ou use a factory
        // {$domain}Model::factory(10)->create();
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Seeder criado: {$domain}Seeder.php");
    }

    /**
     * Cria Value Objects (se for --all)
     */
    private function createValueObjects(string $domain): void
    {
        // Exemplo de Value Objects
        $vos = [
            'Email' => 'string',
            'Phone' => 'string',
            'Cpf' => 'string',
        ];

        foreach ($vos as $voName => $voType) {
            $path = "app/Domain/{$domain}/ValueObjects/{$voName}.php";
            
            if (File::exists($path)) {
                continue;
            }

            $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\ValueObjects;

class {$voName}
{
    private string \$value;

    public function __construct(string \$value)
    {
        // Implemente validação aqui
        // if (!filter_var(\$value, FILTER_VALIDATE_EMAIL)) {
        //     throw new \\InvalidArgumentException('Invalid email');
        // }
        
        \$this->value = \$value;
    }

    public function getValue(): string
    {
        return \$this->value;
    }

    public function __toString(): string
    {
        return \$this->value;
    }

    public function equals(self \$other): bool
    {
        return \$this->value === \$other->getValue();
    }
}
PHP;

            File::put($path, $content);
            $this->line("   Value Object criado: {$voName}.php");
        }
    }

    /**
     * Cria Events (se for --all)
     */
    private function createEvents(string $domain): void
    {
        $events = [
            "{$domain}Created",
            "{$domain}Updated",
            "{$domain}Deleted",
        ];

        foreach ($events as $event) {
            $path = "app/Domain/{$domain}/Events/{$event}.php";
            
            if (File::exists($path)) {
                continue;
            }

            $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Events;

use App\\Domain\\{$domain}\\Entities\\{$domain};

class {$event}
{
    public function __construct(
        public readonly {$domain} \$entity
    ) {}
}
PHP;

            File::put($path, $content);
            $this->line("   Event criado: {$event}.php");
        }
    }

    /**
     * Cria Exceptions (se for --all)
     */
    private function createExceptions(string $domain): void
    {
        $exceptions = [
            "{$domain}NotFoundException",
            "{$domain}ValidationException",
        ];

        foreach ($exceptions as $exception) {
            $path = "app/Domain/{$domain}/Exceptions/{$exception}.php";
            
            if (File::exists($path)) {
                continue;
            }

            $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Exceptions;

class {$exception} extends \\Exception
{
    public function __construct(string \$message = "{$exception}")
    {
        parent::__construct(\$message);
    }
}
PHP;

            File::put($path, $content);
            $this->line("   Exception criada: {$exception}.php");
        }
    }

    /**
     * Cria Rules (se for --all)
     */
    private function createRules(string $domain): void
    {
        $path = "app/Domain/{$domain}/Rules/Valid{$domain}Rule.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace App\\Domain\\{$domain}\\Rules;

use Closure;
use Illuminate\\Contracts\\Validation\\ValidationRule;

class Valid{$domain}Rule implements ValidationRule
{
    public function validate(string \$attribute, mixed \$value, Closure \$fail): void
    {
        // Implemente a validação específica do domínio
        // if (!\$this->isValid(\$value)) {
        //     \$fail("O campo :attribute é inválido.");
        // }
    }

    private function isValid(mixed \$value): bool
    {
        // Implemente a lógica de validação
        return true;
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Rule criada: Valid{$domain}Rule.php");
    }

    /**
     * Cria Tests (se for --all)
     */
    private function createTests(string $domain): void
    {
        $path = "tests/Feature/{$domain}Test.php";
        
        if (File::exists($path)) {
            return;
        }

        $content = <<<PHP
<?php

namespace Tests\\Feature;

use Tests\\TestCase;
use App\\Infrastructure\\{$domain}\\Models\\{$domain}Model;

class {$domain}Test extends TestCase
{
    public function test_can_create_{$domain}_(): void
    {
        \$data = [
            // Defina os dados para criar
        ];

        \$response = \$this->postJson('/api/v1/{$domain}', \$data);
        \$response->assertStatus(201);
    }

    public function test_can_list_{$domain}_(): void
    {
        {$domain}Model::factory(3)->create();

        \$response = \$this->getJson('/api/v1/{$domain}');
        \$response->assertStatus(200);
        \$response->assertJsonStructure([
            'data',
            'total',
            'current_page',
            'per_page',
            'last_page',
        ]);
    }

    public function test_can_get_single_{$domain}_(): void
    {
        \$entity = {$domain}Model::factory()->create();

        \$response = \$this->getJson("/api/v1/{$domain}/{\$entity->id}");
        \$response->assertStatus(200);
    }

    public function test_can_update_{$domain}_(): void
    {
        \$entity = {$domain}Model::factory()->create();

        \$data = [
            // Defina os dados para atualizar
        ];

        \$response = \$this->putJson("/api/v1/{$domain}/{\$entity->id}", \$data);
        \$response->assertStatus(200);
    }

    public function test_can_delete_{$domain}_(): void
    {
        \$entity = {$domain}Model::factory()->create();

        \$response = \$this->deleteJson("/api/v1/{$domain}/{\$entity->id}");
        \$response->assertStatus(204);

        \$this->assertDatabaseMissing('{$domain}s', ['id' => \$entity->id]);
    }
}
PHP;

        File::put($path, $content);
        $this->line("   Test criado: {$domain}Test.php");
    }

    /**
     * Registra no Service Provider
     */
    private function registerInServiceProvider(string $domain): void
    {
        $path = app_path("Providers/AppServiceProvider.php");
        
        if (!File::exists($path)) {
            return;
        }

        $content = File::get($path);
        
        // Verificar se já está registrado
        if (str_contains($content, "{$domain}RepositoryInterface")) {
            return;
        }

        // Adicionar use statements APENAS se não existirem
        $useLines = [
            "use App\\Domain\\{$domain}\\Repositories\\{$domain}RepositoryInterface;",
            "use App\\Infrastructure\\{$domain}\\Repositories\\Eloquent{$domain}Repository;",
        ];

        foreach ($useLines as $useLine) {
            if (!str_contains($content, $useLine)) {
                // Adicionar após o último use existente
                $pattern = '/(use [^;]+;)/';
                $content = preg_replace($pattern, "$1\n" . $useLine, $content, 1);
            }
        }

        // Adicionar o bind no método register()
        $bind = "\n        \$this->app->bind(\n            {$domain}RepositoryInterface::class,\n            Eloquent{$domain}Repository::class\n        );";

        // Procurar o método register() e adicionar o bind
        $pattern = '/public function register\(\): void\s*\{([^}]*)\}/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $currentContent = $matches[1];
            
            // Adicionar o bind no final do método
            $newContent = str_replace(
                $matches[0],
                "public function register(): void\n    {\n        {$currentContent}\n        {$bind}\n    }",
                $content
            );
            
            File::put($path, $newContent);
            $this->line("Registrado no AppServiceProvider: {$domain}");
        }
    }

    /**
     * Registra as rotas
     */
    private function registerRoutes(string $domain): void
    {
        $path = base_path("routes/api.php");
        
        if (!File::exists($path)) {
            return;
        }

        $content = File::get($path);
        $routeName = Str::kebab(Str::plural($domain));
        $controllerClass = "App\\Http\\Controllers\\Api\\V1\\{$domain}";
        $controllerName = "{$domain}Controller";

        if (str_contains($content, "{$routeName}")) {
            return;
        }

        $routes = <<<PHP

            // {$domain} Routes
            Route::group(['prefix' => '{$routeName}', 'namespace' => '{$controllerClass}'], function(){
                Route::get('/',['uses' => '{$controllerName}@index', 'as' => '{$routeName}.index'] );
                Route::get('/{id}',['uses' => '{$controllerName}@show', 'as' => '{$routeName}.show'] );
                Route::post('/',['uses' => '{$controllerName}@store', 'as' => '{$routeName}.store'] );
                Route::put('/{id}/update',['uses' => '{$controllerName}@update', 'as' => '{$routeName}.update'] );
                Route::delete('/{id}',['uses' => '{$controllerName}@destroy', 'as' => '{$routeName}.destroy'] );
            });
        PHP;

        $pattern = '/Route::middleware\(\'auth:sanctum\'\)->prefix\(\'v1\'\)->group\(function \(\) \{(.*?)\n\}\);/s';

        if (preg_match($pattern, $content, $matches)) {
            $newContent = str_replace($matches[1], $matches[1] . "\n" . $routes, $content);
            
            File::put($path, $newContent);
            $this->line("Rotas registradas em routes/api.php");
        } else {
            $this->warn("Grupo auth:sanctum não encontrado. Rotas não foram adicionadas.");
        }
    }
}