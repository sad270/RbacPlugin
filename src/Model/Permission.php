<?php

declare(strict_types=1);

namespace Sylius\RbacPlugin\Model;

use Webmozart\Assert\Assert;

final class Permission
{
    public const CATALOG_MANAGEMENT_PERMISSION = 'catalog_management';
    public const CONFIGURATION_PERMISSION = 'configuration';
    public const CUSTOMERS_MANAGEMENT_PERMISSION = 'customers_management';
    public const MARKETING_MANAGEMENT_PERMISSION = 'marketing_management';
    public const SALES_MANAGEMENT_PERMISSION = 'sales_management';

    /** @var string */
    private $type;

    /** @var array */
    private $accesses;

    public static function catalogManagement(array $accesses): self
    {
        return new self(self::CATALOG_MANAGEMENT_PERMISSION, $accesses);
    }

    public static function configuration(array $accesses): self
    {
        return new self(self::CONFIGURATION_PERMISSION, $accesses);
    }

    public static function customerManagement(array $accesses): self
    {
        return new self(self::CUSTOMERS_MANAGEMENT_PERMISSION, $accesses);
    }

    public static function marketingManagement(array $accesses): self
    {
        return new self(self::MARKETING_MANAGEMENT_PERMISSION, $accesses);
    }

    public static function salesManagement(array $accesses): self
    {
        return new self(self::SALES_MANAGEMENT_PERMISSION, $accesses);
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized);

        return new self($data['type'], $data['accesses']);
    }

    public function __construct(?string $type, ?array $accesses)
    {
        Assert::oneOf(
            $type, [
                self::CATALOG_MANAGEMENT_PERMISSION,
                self::CONFIGURATION_PERMISSION,
                self::CUSTOMERS_MANAGEMENT_PERMISSION,
                self::MARKETING_MANAGEMENT_PERMISSION,
                self::SALES_MANAGEMENT_PERMISSION
            ]
        );

        Assert::allOneOf(
            $accesses, [
                PermissionAccess::READ,
                PermissionAccess::WRITE
            ]
        );

        $this->type = $type;
        $this->accesses = $accesses;
    }

    public function accesses(): array
    {
        return $this->accesses;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function serialize(): array
    {
        return json_encode([
            'type' => $this->type(),
            'accesses' => $this->accesses,
        ]);
    }

    public function equals(self $permission): bool
    {
        return $permission->type() === $this->type();
    }
}
