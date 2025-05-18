<?php
declare(strict_types=1);

namespace framework\clarity\Http;

use framework\clarity\database\interfaces\DataBaseConnectionInterface;
use framework\clarity\Http\interfaces\ResourceWriterInterface;
use InvalidArgumentException;
use RuntimeException;

class JsonResourceWriter implements ResourceWriterInterface
{
    private string $resourceName;

    /**
     * @param DataBaseConnectionInterface $connection
     */
    public function __construct(
        private readonly DataBaseConnectionInterface $connection
    ) {}

    /**
     * @param string $name
     * @return $this
     */
    public function setResourceName(string $name): static
    {
        if ($name === '') {
            throw new InvalidArgumentException('Имя ресурса не передано');
        }

        $this->resourceName = $name;

        return $this;
    }

    /**
     * @param array $values
     */
    public function create(array $values): void
    {
        $this->assertResourceName();

        $this->connection->insert(
            $this->resourceName,
            $values
        );
    }

    /**
     * @param int|string $id
     * @param array      $values
     */
    public function update(int|string $id, array $values): void
    {
        $this->assertResourceName();

        $this->connection->update(
            $this->resourceName,
            $values,
            ['id' => $id]
        );
    }

    /**
     * @param int|string $id
     * @param array      $values
     */
    public function patch(int|string $id, array $values): void
    {
        $this->update($id, $values);
    }

    /**
     * @param int|string $id
     */
    public function delete(int|string $id): void
    {
        $this->assertResourceName();

        $this->connection->delete(
            $this->resourceName,
            ['id' => $id]
        );
    }

    /**
     * @return string
     */
    public function getLastInsertId(): string
    {
        $this->assertResourceName();

        return $this->connection->getLastInsertId();
    }

    /**
     * @return void
     */
    private function assertResourceName(): void
    {
        if (empty($this->resourceName) === true) {
            throw new RuntimeException('Не установлено имя ресурса');
        }
    }
}
