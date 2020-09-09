<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Component\User\CurrentUser;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use LogicException;

/**
 * Class uses for change all queries to database.
 *
 * @package App\Controller\ApiPlatform\Extensions
 */
class InsertUserAndHideDeletedExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private CurrentUser $currentUser;

    public function __construct(CurrentUser $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Collection operations without id, like GET /users
     *
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param string|null                 $operationName
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * Item operations with id, like GET /users/{id} or DELETE /users/{id}
     *
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param array                       $identifiers
     * @param string|null                 $operationName
     * @param array                       $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * In this method you can join user table for all queries. So that users can see only their entities.
     * Also you should hide elements that marked as deleted.
     *
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $rootTable = $queryBuilder->getRootAliases()[0];

        switch ($resourceClass) {
//            case Application::class:
//                $this->joinEntityAndAddUser('company', $rootTable, $queryBuilder, $this->getUser());
//                $this->hideDeleted("company", $queryBuilder);
//                break;

//            case Company::class:
//                $this->addUser($queryBuilder, $this->getUser(), $rootTable);
//                $this->hideDeleted($rootTable, $queryBuilder);
//                break;

            case User::class:
//          case Company::class:
                $this->hideDeleted($rootTable, $queryBuilder);
                break;

            default:
                throw new LogicException('Entity is not found');
        }
    }

    /**
     * @param string       $entityTable
     * @param string       $rootTable
     * @param QueryBuilder $queryBuilder
     * @param User         $user
     */
    private function joinEntityAndAddUser(
        string $entityTable,
        string $rootTable,
        QueryBuilder $queryBuilder,
        User $user
    ): void {
        $queryBuilder->join("{$rootTable}.{$entityTable}", $entityTable);

        $this->addUser($queryBuilder, $user, $entityTable);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $tableName
     * @param User         $user
     */
    private function addUser(QueryBuilder $queryBuilder, User $user, string $tableName): void
    {
        $queryBuilder->andWhere("{$tableName}.user = :user");
        $queryBuilder->setParameter('user', $user);
    }

    private function hideDeleted(string $tableName, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere("{$tableName}.isDeleted = false");
    }

    private function getUser(): User
    {
        return $this->currentUser->get();
    }
}
