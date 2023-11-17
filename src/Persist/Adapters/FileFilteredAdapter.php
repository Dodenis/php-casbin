<?php

declare(strict_types=1);

namespace Casbin\Persist\Adapters;

use Casbin\Exceptions\CannotSaveFilteredPolicy;
use Casbin\Exceptions\CasbinException;
use Casbin\Exceptions\InvalidFilePathException;
use Casbin\Exceptions\InvalidFilterTypeException;
use Casbin\Persist\FilteredAdapter;
use Casbin\Model\Model;

/**
 * FilteredAdapter is the filtered file adapter for Casbin. It can load policy
 * from file or save policy to file and supports loading of filtered policies.
 *
 * @author techlee@qq.com
 */
class FileFilteredAdapter extends FileAdapter implements FilteredAdapter
{
    /**
     * filtered variable.
     *
     * @var bool
     */
    protected $filtered;

    /**
     * FileAdapter constructor.
     */
    public function __construct(string $filePath)
    {
        $this->filtered = true;
        parent::__construct($filePath);
    }

    /**
     * Loads all policy rules from the storage.
     *
     * @throws CasbinException
     */
    public function loadPolicy(Model $model): void
    {
        $this->filtered = false;
        parent::loadPolicy($model);
    }

    /**
     * Loads only policy rules that match the filter.
     *
     * @param mixed $filter
     *
     * @throws CasbinException
     */
    public function loadFilteredPolicy(Model $model, $filter): void
    {
        if (is_null($filter)) {
            $this->loadPolicy($model);

            return;
        }

        if (!file_exists($this->filePath)) {
            throw new InvalidFilePathException('invalid file path, file path cannot be empty');
        }

        if (!$filter instanceof Filter) {
            throw new InvalidFilterTypeException('invalid filter type');
        }

        $this->loadFilteredPolicyFile($model, $filter, [$this, 'loadPolicyLine']);
        $this->filtered = true;
    }

    /**
     * Returns true if the loaded policy has been filtered.
     */
    public function isFiltered(): bool
    {
        return $this->filtered;
    }

    /**
     * SavePolicy saves all policy rules to the storage.
     *
     * @throws CannotSaveFilteredPolicy|CasbinException
     */
    public function savePolicy(Model $model): void
    {
        if ($this->filtered) {
            throw new CannotSaveFilteredPolicy('cannot save a filtered policy');
        }

        parent::savePolicy($model);
    }

    /**
     * LoadFilteredPolicyFile function.
     *
     * @throws InvalidFilePathException
     */
    protected function loadFilteredPolicyFile(Model $model, Filter $filter, callable $handler): void
    {
        $file = fopen($this->filePath, 'rb');

        if (false === $file) {
            throw new InvalidFilePathException(sprintf('Unable to access to the specified path "%s"', $this->filePath));
        }

        while ($line = fgets($file)) {
            $line = trim($line);

            if (self::filterLine($line, $filter)) {
                continue;
            }
            call_user_func($handler, $line, $model);
        }
    }

    /**
     * FilterLine function.
     */
    protected static function filterLine(string $line, Filter $filter): bool
    {
        $p = explode(',', $line);

        if (0 == strlen($p[0])) {
            return true;
        }

        $filterSlice = [];

        switch (trim($p[0])) {
            case 'p':
                $filterSlice = $filter->p;

                break;
            case 'g':
                $filterSlice = $filter->g;

                break;
        }

        return self::filterWords($p, $filterSlice);
    }

    /**
     * FilterWords function.
     */
    protected static function filterWords(array $line, array $filter): bool
    {
        if (count($line) < count($filter) + 1) {
            return true;
        }
        $skipLine = false;

        foreach ($filter as $i => $v) {
            if (strlen($v) > 0 && \trim($v) != trim($line[$i + 1])) {
                $skipLine = true;

                break;
            }
        }

        return $skipLine;
    }
}
