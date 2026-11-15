<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Viodev\Fractal;
use Viodev\Pageable;
use Viodev\Pager;
use Viodev\Providers\ReplierServiceProvider;

function same(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException('Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

$validator = new Factory(new Translator(new ArrayLoader(), 'en'));
Request::macro('validate', function (array $rules) use ($validator): array {
    return $validator->make($this->all(), $rules)->validate();
});

class PaginationTransformer
{
    public function create(mixed $data, mixed $sparser): array
    {
        return (array) $data;
    }
}

class PaginationModel extends Model
{
    use Pageable;
}

foreach ([
    Request::create('/accounts'),
    Request::create('/accounts?limit=20&last_id=123'),
    Request::create('/accounts', 'QUERY', ['limit' => 20, 'last_id' => 123]),
] as $index => $request) {
    $pager = new Pager($request, 100, 'desc');
    same($index === 0 ? 100 : 20, $pager->limit);
    same($index === 0 ? null : 123, $pager->lastId);
    same('desc', $pager->order);

    foreach ([[], [['id' => '124']], new Collection([(object) ['id' => '124']])] as $records) {
        $response = Fractal::collection($records, PaginationTransformer::class, $pager);
        $json = json_decode(json_encode($response, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        same($pager->limit, $json['meta']['limit']);
        same(count($records) === 0 ? null : 124, $json['meta']['last_id']);
        same(count($records) === 0 ? [] : [['id' => '124']], $json['data']);
    }
}

foreach ([['limit' => '0'], ['limit' => '101'], ['limit' => 'bad'], ['order' => 'bad'], ['last_id' => 'bad']] as $input) {
    try {
        new Pager(Request::create('/accounts', 'GET', $input), 100, 'desc');
        throw new RuntimeException('Expected pagination validation to fail.');
    } catch (ValidationException $exception) {
        same(array_keys($input), array_keys($exception->errors()));
    }
}

foreach (['asc' => '>', 'desc' => '<'] as $order => $operator) {
    $pager = new Pager(Request::create('/accounts', 'GET', ['limit' => '20', 'last_id' => '0', 'order' => $order]), 100, 'desc');
    $base = new QueryBuilder(new Connection(null));
    $query = (new Builder($base))->setModel(new PaginationModel());
    same($query, (new PaginationModel())->scopePage($query, $pager));
    same(0, $base->wheres[0]['value']);
    same($operator, $base->wheres[0]['operator']);
    same([['column' => 'id', 'direction' => $order]], $base->orders);
    same(20, $base->limit);
}

$pager = new Pager(Request::create('/accounts'), 100, 'desc');
$base = new QueryBuilder(new Connection(null));
(new PaginationModel())->scopePage((new Builder($base))->setModel(new PaginationModel()), $pager);
same([], $base->wheres);

foreach ([[], ['pager' => ['limit' => 25, 'order' => 'asc']]] as $config) {
    $app = new class extends Container {
        public function runningInConsole(): bool
        {
            return false;
        }
    };
    $app->instance('config', new Repository($config));
    (new ReplierServiceProvider($app))->register();
    same($config['pager'] ?? ['limit' => 1000, 'order' => 'desc'], $app['config']->get('pager'));
}

echo "Pagination checks passed.\n";
