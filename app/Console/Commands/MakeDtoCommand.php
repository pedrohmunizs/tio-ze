<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDtoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto 
                            {domain : Nome do domínio (ex: User, Vehicle, DriverDocument)} 
                            {action : Ação do DTO (ex: Create, Update, Delete, Response, Filter)}
                            {--force : Sobrescrever se já existir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um DTO para um domínio e ação específicos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = Str::studly($this->argument('domain'));
        $action = Str::studly($this->argument('action'));
        $force = $this->option('force');

        // 👉 MONTAR O NOME DO DTO
        $dtoName = "{$action}{$domain}Data";
        $path = app_path("Application/{$domain}/DTOs/{$dtoName}.php");

        // 👉 VERIFICAR SE JÁ EXISTE
        if (File::exists($path) && !$force) {
            $this->error("❌ O DTO {$dtoName} já existe!");
            $this->info("   Use --force para sobrescrever.");
            return 1;
        }

        // 👉 CRIAR DIRETÓRIO SE NÃO EXISTIR
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // 👉 GERAR O CONTEÚDO DO DTO
        $content = $this->generateDtoContent($domain, $action, $dtoName);

        // 👉 SALVAR O ARQUIVO
        File::put($path, $content);

        $this->info("✅ DTO criado com sucesso!");
        $this->line("   📄 Arquivo: {$path}");
        $this->line("   📦 Classe:  {$dtoName}");

        return 0;
    }

    /**
     * Gera o conteúdo do DTO baseado no domínio e ação
     */
    private function generateDtoContent(string $domain, string $action, string $dtoName): string
    {
        // 👉 DEFINIR AS PROPRIEDADES BASEADO NA AÇÃO
        $properties = $this->getPropertiesForAction($domain, $action);
        // $fromRequestMethod = $this->getFromRequestMapping($properties);
        $rules = $this->getRulesForAction($domain, $action);
        $messages = $this->getMessagesForAction($domain, $action);

        // 👉 GERAR STRING DAS PROPRIEDADES
        $propertiesString = '';
        foreach ($properties as $property) {
            $propertiesString .= "        public readonly {$property['type']} \${$property['name']}" . 
                ($property['default'] ? " = {$property['default']}" : '') . ",\n";
        }

        // 👉 GERAR STRING DO FROM REQUEST
        $fromRequestParams = '';
        foreach ($properties as $property) {
            $fromRequestParams .= "            {$property['name']}: {$property['request']},\n";
        }

        // 👉 GERAR STRING DO TO ARRAY
        $toArrayItems = '';
        foreach ($properties as $property) {
            $toArrayItems .= "            '{$property['name']}' => \$this->{$property['name']},\n";
        }

        // 👉 GERAR STRING DAS REGRAS
        $rulesString = '';
        foreach ($rules as $field => $rule) {
            $rulesString .= "            '{$field}' => {$rule},\n";
        }

        // 👉 GERAR STRING DAS MENSAGENS
        $messagesString = '';
        foreach ($messages as $field => $message) {
            $messagesString .= "            '{$field}' => '{$message}',\n";
        }

        // 👉 MONTAR O CONTEÚDO FINAL
        return <<<PHP
<?php

namespace App\\Application\\{$domain}\\DTOs;

use Illuminate\\Http\\Request;

class {$dtoName}
{
    public function __construct(
{$propertiesString}    ) {}

    public static function fromRequest(Request \$request): self
    {
        \$request->validate(self::rules(), self::messages());

        return new self(
{$fromRequestParams}        );
    }

    public function toArray(): array
    {
        return [
{$toArrayItems}        ];
    }

    public static function rules(): array
    {
        return [
{$rulesString}        ];
    }

    public static function messages(): array
    {
        return [
{$messagesString}        ];
    }
}
PHP;
    }

    /**
     * Retorna as propriedades padrão para cada ação
     */
    private function getPropertiesForAction(string $domain, string $action): array
    {
        // 👉 PROPRIEDADES BASE POR AÇÃO
        $propertiesByAction = [
            'Create' => [
                ['name' => 'name', 'type' => 'string', 'default' => null, 'request' => "\$request->input('name')"],
                ['name' => 'description', 'type' => '?string', 'default' => 'null', 'request' => "\$request->input('description')"],
            ],
            'Update' => [
                ['name' => 'id', 'type' => 'int', 'default' => null, 'request' => "(int) \$request->input('id')"],
                ['name' => 'name', 'type' => '?string', 'default' => 'null', 'request' => "\$request->input('name')"],
                ['name' => 'description', 'type' => '?string', 'default' => 'null', 'request' => "\$request->input('description')"],
            ],
            'Delete' => [
                ['name' => 'id', 'type' => 'int', 'default' => null, 'request' => "(int) \$request->input('id')"],
            ],
            'Response' => [
                ['name' => 'id', 'type' => 'int', 'default' => null, 'request' => "(int) \$request->input('id')"],
                ['name' => 'status', 'type' => 'string', 'default' => null, 'request' => "\$request->input('status')"],
                ['name' => 'message', 'type' => '?string', 'default' => 'null', 'request' => "\$request->input('message')"],
            ],
            'Filter' => [
                ['name' => 'search', 'type' => '?string', 'default' => 'null', 'request' => "\$request->input('search')"],
                ['name' => 'status', 'type' => '?string', 'default' => 'null', 'request' => "\$request->input('status')"],
                ['name' => 'page', 'type' => 'int', 'default' => '1', 'request' => "(int) \$request->input('page', 1)"],
                ['name' => 'perPage', 'type' => 'int', 'default' => '15', 'request' => "(int) \$request->input('per_page', 15)"],
            ],
        ];

        return $propertiesByAction[$action] ?? [
            ['name' => 'id', 'type' => 'int', 'default' => null, 'request' => "(int) \$request->input('id')"],
        ];
    }

    /**
     * Retorna as regras de validação para cada ação
     */
    private function getRulesForAction(string $domain, string $action): array
    {
        $rulesByAction = [
            'Create' => [
                'name' => "'required|string|max:255'",
                'description' => "'nullable|string'",
            ],
            'Update' => [
                'id' => "'required|integer|exists:" . Str::snake(Str::plural($domain)) . ",id'",
                'name' => "'nullable|string|max:255'",
                'description' => "'nullable|string'",
            ],
            'Delete' => [
                'id' => "'required|integer|exists:" . Str::snake(Str::plural($domain)) . ",id'",
            ],
            'Response' => [
                'id' => "'required|integer|exists:" . Str::snake(Str::plural($domain)) . ",id'",
                'status' => "'required|in:approved,rejected,pending'",
                'message' => "'nullable|string|max:500'",
            ],
            'Filter' => [
                'search' => "'nullable|string|max:255'",
                'status' => "'nullable|string'",
                'page' => "'nullable|integer|min:1'",
                'per_page' => "'nullable|integer|min:1|max:100'",
            ],
        ];

        return $rulesByAction[$action] ?? [];
    }

    /**
     * Retorna as mensagens de validação para cada ação
     */
    private function getMessagesForAction(string $domain, string $action): array
    {
        $messagesByAction = [
            'Create' => [
                'name.required' => 'O nome é obrigatório',
                'name.max' => 'O nome não pode ter mais que 255 caracteres',
            ],
            'Update' => [
                'id.required' => 'O ID é obrigatório',
                'id.exists' => 'Registro não encontrado',
            ],
            'Delete' => [
                'id.required' => 'O ID é obrigatório',
                'id.exists' => 'Registro não encontrado',
            ],
            'Response' => [
                'id.required' => 'O ID é obrigatório',
                'id.exists' => 'Registro não encontrado',
                'status.required' => 'O status é obrigatório',
                'status.in' => 'Status inválido',
            ],
            'Filter' => [],
        ];

        return $messagesByAction[$action] ?? [];
    }
}