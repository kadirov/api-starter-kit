<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Component\User\CurrentUser;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use LogicException;

class InsertUserAndHideDeletedExtension implements QueryCollectionExtensionInterface
{
    private CurrentUser $currentUser;

    public function __construct(CurrentUser $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $rootTable = $this->getRootTableAlias($queryBuilder);

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

    /**
     * @param QueryBuilder $queryBuilder
     * @return string
     */
    private function getRootTableAlias(QueryBuilder $queryBuilder): string
    {
        return $queryBuilder->getRootAliases()[0];
    }

    private function getUser(): User
    {
        return $this->currentUser->get();
    }

    private function hideDeleted(string $tableName, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere("{$tableName}.isDeleted = false");
    }
}
