<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Asset\Framework\Core;

use Asset\Framework\Trait\SingletonTrait;

/**
 * Class that handles: Query Builder
 *
 * @package Asset\Framework\Core;
 */
class QueryBuilder
{

    use SingletonTrait;

    /**
     * @var string
     */
    private string $query = '';

    /**
     * @var array
     */
    private array $parameters = [];

    /**
     * @var array
     */
    private array $where = [];

    /**
     * @var array
     */
    private array $joins = [];

    /**
     * @var array
     */
    private array $orderBy = [];

    /**
     * @var array
     */
    private array $groupBy = [];

    /**
     * @var array
     */
    private array $having = [];

    /**
     * @var int
     */
    private int $limit = 0;

    /**
     * @var int
     */
    private int $offset = 0;

    /**
     * @param string $table
     * @return $this
     */
    public function table(string $table): self
    {
        $this->query = "SELECT * FROM `$table`";
        $this->resetParams();

        return $this;
    }

    private function resetParams(): void
    {
        $this->parameters = [];
        $this->where      = [];
        $this->joins      = [];
        $this->orderBy    = [];
        $this->groupBy    = [];
        $this->having     = [];
        $this->limit      = 0;
        $this->offset     = 0;
    }

    public function select(string ...$columns): self
    {
        $cols        = implode(', ', array_map(fn($col) => $this->formatColumn($col), $columns));
        $this->query = str_replace('*', $cols, $this->query);

        return $this;
    }

    private function formatColumn(string $column): string
    {
        if (str_contains($column, '.')) {
            [$table, $col] = explode('.', $column);

            return "`$table`.`$col`";
        }

        return "`$column`";
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = sprintf(
            ' %s JOIN `%s` ON %s %s %s',
            strtoupper($type),
            $table,
            $this->formatColumn($first),
            $operator,
            $this->formatColumn($second)
        );

        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->where[] = [
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value,
            'logic'    => empty($this->where) ? 'WHERE' : 'AND',
        ];

        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        $this->where[] = [
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value,
            'logic'    => 'OR',
        ];

        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders  = rtrim(str_repeat('?,', count($values)), ',');
        $this->where[] = [
            'column'   => $column,
            'operator' => "IN ($placeholders)",
            'value'    => $values,
            'logic'    => empty($this->where) ? 'WHERE' : 'AND',
            'is_array' => true,
        ];

        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupBy = array_merge($this->groupBy, $columns);

        return $this;
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->having[] = [
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value,
        ];

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = $this->formatColumn($column).' '.strtoupper($direction);

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function insert(string $table, array $data): string
    {
        $columns          = implode('`, `', array_keys($data));
        $values           = rtrim(str_repeat('?,', count($data)), ',');
        $this->parameters = array_values($data);

        $this->query = "INSERT INTO `$table` (`$columns`) VALUES ($values)";

        return $this->query;
    }

    public function update(string $table, array $data): string
    {
        $set              = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $this->query      = "UPDATE `$table` SET $set";
        $this->parameters = array_values($data);

        return $this->query;
    }

    public function delete(string $table): string
    {
        $this->query = "DELETE FROM `$table`";

        return $this->query;
    }

    public function getQuery(): string
    {
        $this->buildJoinClauses();
        $this->buildWhereClause();
        $this->buildGroupByClause();
        $this->buildHavingClause();
        $this->buildOrderByClause();
        $this->buildLimitOffsetClause();

        return $this->query;
    }

    private function buildJoinClauses(): void
    {
        if (!empty($this->joins)) {
            $this->query .= implode('', $this->joins);
        }
    }

    private function buildWhereClause(): void
    {
        if (empty($this->where)) {
            return;
        }

        $whereClauses = [];
        foreach ($this->where as $condition) {
            if (isset($condition['is_array']) && $condition['is_array']) {
                $whereClauses[]   = "{$condition['logic']} {$this->formatColumn($condition['column'])} {$condition['operator']}";
                $this->parameters = array_merge($this->parameters, $condition['value']);
            } else {
                $whereClauses[]     = "{$condition['logic']} {$this->formatColumn($condition['column'])} {$condition['operator']} ?";
                $this->parameters[] = $condition['value'];
            }
        }

        $this->query .= ' '.implode(' ', $whereClauses);
    }

    private function buildGroupByClause(): void
    {
        if (!empty($this->groupBy)) {
            $columns     = array_map(fn($col) => $this->formatColumn($col), $this->groupBy);
            $this->query .= ' GROUP BY '.implode(', ', $columns);
        }
    }

    private function buildHavingClause(): void
    {
        if (!empty($this->having)) {
            $havingClauses = [];
            foreach ($this->having as $condition) {
                $havingClauses[]    = "{$this->formatColumn($condition['column'])} {$condition['operator']} ?";
                $this->parameters[] = $condition['value'];
            }
            $this->query .= ' HAVING '.implode(' AND ', $havingClauses);
        }
    }

    private function buildOrderByClause(): void
    {
        if (!empty($this->orderBy)) {
            $this->query .= ' ORDER BY '.implode(', ', $this->orderBy);
        }
    }

    private function buildLimitOffsetClause(): void
    {
        if ($this->limit > 0) {
            $this->query .= " LIMIT $this->limit";
            if ($this->offset > 0) {
                $this->query .= " OFFSET $this->offset";
            }
        }
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

}